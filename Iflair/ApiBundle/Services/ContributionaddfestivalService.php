<?php
namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use IFlairSoapBundle\Entity\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Zend\Soap\Client;
use Zend\Soap;
use IFlairFestivalBundle\Models\Soapurls;
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Partyfindercountry;
use IFlairSoapBundle\Entity\Partyfindercity;
use IFlairSoapBundle\Entity\Musicgenre;
use IFlairFestivalBundle\Entity\ContributionAdddFestival;
use IFlairFestivalBundle\Entity\ContributionAddRating;
use IFlairFestivalBundle\Entity\festivalFavourite;
use IFlairFestivalBundle\Entity\features;
use IFlairFestivalBundle\Entity\ContributionAddImage;
use IFlairFestivalBundle\Entity\ContributionAddFeature;
use IFlairFestivalBundle\Entity\ContributionAddMusic;


class ContributionaddfestivalService
{
    protected $doctrine;
    public function __construct(RequestStack $request,Registry $doctrine)
    {
        $this->request = $request;
        $this->doctrine = $doctrine;
    }
    /**
     * User Registartion method details, user will be register when called.
     * @param string $name 
     * @return mixed
     */
    public function getcontributionaddfestival($user_id,$festival_name,$country_id,$city_id,$start_date,$end_date,$feature_id,$rating_id,$music_genre,$uploadDir,$upload_image_path)
    {
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();

        $music_data = json_decode($music_genre, true);
        $ratings = array(json_decode($rating_id, true));
        $features = array(json_decode($feature_id, true));

        $user = $em->getRepository('AppBundle:User')->findOneById($user_id);
        $country = $em->getRepository('IFlairSoapBundle:Partyfindercountry')->findOneById($country_id);
        $city = $em->getRepository('IFlairSoapBundle:Partyfindercity')->findOneById($city_id);        
        $contributionAddFestival = new ContributionAdddFestival();
        $contributionAddFestival->setFestivalName($festival_name);
        $contributionAddFestival->setStartDate(new \DateTime($start_date));
        $contributionAddFestival->setEndDate(new \DateTime($end_date));
        //echo "user id : ".$user->getId();
        $contributionAddFestival->setUserId($user);
        $contributionAddFestival->setCountryId($country);
        $contributionAddFestival->setCityId($city);
        $em->persist($contributionAddFestival);
        $em->flush();
        $last_inserted_id = $contributionAddFestival->getId();

        // Rating Data :: Start
        $rating_data = array(); $count = 0;
        foreach($ratings as $rating)
        {
            foreach($rating as $rate)
            {
                $rating_data[$count]['id'] = $rate['id'];
                $rating_data[$count]['rating'] = $rate['rating'];
                $count++;
            }
        }

        foreach($rating_data as $ratng)
        {
            $ratingid = $em->getRepository('IFlairFestivalBundle:festival_rating_type')->findOneById($ratng['id']);
            $contribution_id = $em->getRepository('IFlairFestivalBundle:ContributionAdddFestival')->findOneById($last_inserted_id);
            $contributionAddRating = new ContributionAddRating();
            $contributionAddRating->setContributionId($contribution_id);
            $contributionAddRating->setRatingId($ratingid);
            $contributionAddRating->setUserRatings($ratng['rating']);
            $em->persist($contributionAddRating);
            $em->flush();
            $rating_inserted_id = $contributionAddRating->getId();
        }
        // Rating Data :: end 
        // Feature Data :: Start 
        $feature_data = array();$count = 0;
        foreach($features as $feature)
        {
            foreach($feature as $featur)
            {
                $feature_data[$count]['id'] = $featur['id'];
                $feature_data[$count]['is_selected'] = $featur['isSelected'];
                $count++;
            }
        }
        foreach($feature_data as $feature)
        {
            $contribution_id = $em->getRepository('IFlairFestivalBundle:ContributionAdddFestival')->findOneById($last_inserted_id);
            $featureid = $em->getRepository('IFlairFestivalBundle:features')->findOneById($feature['id']);
            $contributionAddFeature = new ContributionAddFeature();
            $contributionAddFeature->setContributionId($contribution_id);
            $contributionAddFeature->setFeatureId($featureid);
            $contributionAddFeature->setStatus($feature['is_selected']);
            $em->persist($contributionAddFeature);
            $em->flush();
            $feature_inserted_id = $contributionAddFeature->getId();
        }
        // Feature Data :: end
        // music genre data insert
        foreach($music_data as $music)
        {
            $contribution_id = $em->getRepository('IFlairFestivalBundle:ContributionAdddFestival')->findOneById($last_inserted_id);
            $musicgenre = $em->getRepository('IFlairSoapBundle:Musicgenre')->findOneById($music);
            $contributionAddMusic = new ContributionAddMusic();
            $contributionAddMusic->setContributionId($contribution_id);
            $contributionAddMusic->setMusicGenreId($musicgenre);
            $em->persist($contributionAddMusic);
            $em->flush();
            $music_inserted_id = $contributionAddMusic->getId();
        }
        // music Data :: End 
        // image upload data :: start
        
        
        $contribution_id = $em->getRepository('IFlairFestivalBundle:ContributionAdddFestival')->findOneById($last_inserted_id);
        $ContributionAddImage = new ContributionAddImage();
        $ContributionAddImage->setContributionId($contribution_id);
        $ContributionAddImage->setImagePath($upload_image_path);
        $em->persist($ContributionAddImage);
        $em->flush();
        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'Contribution Festival Added.',
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}
