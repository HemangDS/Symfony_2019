<?php
namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use IFlairSoapBundle\Entity\Settings;
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
use IFlairFestivalBundle\Entity\ContributionStatus;
use IFlairFestivalBundle\Entity\FestivalInprogress;
use IFlairFestivalBundle\Entity\festival;
use IFlairFestivalBundle\Entity\FestivalInprogressMultipleImageUpload;
use IFlairFestivalBundle\Entity\FestivalInprogressMusicgenre;
use IFlairFestivalBundle\Entity\FestivalInprogressCurrency;
use IFlairFestivalBundle\Entity\FestivalInprogressFeatures;
use IFlairFestivalBundle\Entity\FestivalInprogressPayments;
use IFlairFestivalBundle\Entity\FestivalInprogressDates;
use IFlairFestivalBundle\Entity\FestivalInprogressRatings;
use IFlairFestivalBundle\Entity\Festival_inprogress_status;

class ContributionadddfestivalService
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
    public function getcontributionadddfestival($user_id,$festival_name,$country_id,$city_id,$start_date,$end_date,$feature_id,$rating_id,$music_genre,$upload_image_path)
    {
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();

        $music_data = json_decode($music_genre, true);
        $ratings = array(json_decode($rating_id, true));
        $features = array(json_decode($feature_id, true));

        $user = $em->getRepository('AppBundle:User')->findOneById($user_id);
        $country = $em->getRepository('IFlairSoapBundle:Partyfindercountry')->findOneById($country_id);
        $city = $em->getRepository('IFlairSoapBundle:Partyfindercity')->findOneById($city_id);        
        $contribution_status = $em->getRepository('IFlairSoapBundle:ContributionStatus')->findOneById(2);

        $festival_inprogress = new FestivalInprogress();
        $festival_inprogress->setName($festival_name);
        $festival_inprogress->setCountryId($country);
        $festival_inprogress->setCityId($city);
        $festival_inprogress->setFestivalId(NULL);
        $festival_inprogress->setUserId($user);
        $festival_inprogress->setLogo(NULL);
        $festival_inprogress->setHeader($upload_image_path);
        $festival_inprogress->setAttendies(NULL);
        $festival_inprogress->setStages(NULL);
        $festival_inprogress->setHeldSince(NULL);
        $festival_inprogress->setWebsite(NULL);
        $festival_inprogress->setEmail(NULL);
        $festival_inprogress->setHost(NULL);
        $festival_inprogress->setManager(NULL);
        $festival_inprogress->setHostWebsite(NULL);
        $festival_inprogress->setStatus($contribution_status);
        $festival_inprogress->setCreatedDate(new \DateTime());
        $em->persist($festival_inprogress);
        $em->flush();
        $last_inserted_id = $festival_inprogress->getId();


        
        $festival_inprogress = $em->getRepository('IFlairFestivalBundle:FestivalInprogress')->findOneById($last_inserted_id);
        $Festival_inprogress_status = new Festival_inprogress_status();
        $Festival_inprogress_status->setFestivalInprogressId($festival_inprogress);
        $Festival_inprogress_status->setStatusId($contribution_status);
        $em->persist($Festival_inprogress_status);
        $em->flush();

        // Music Genre 
        // $music_genre = '1,2,3';
        
        foreach($music_data as $music)
        {
            $music_genre_id = $em->getRepository('IFlairSoapBundle:Musicgenre')->findOneById($music);
            $festival_inprogress = $em->getRepository('IFlairFestivalBundle:FestivalInprogress')->findOneById($last_inserted_id);
            $festivalinprogressmusicgenre = new FestivalInprogressMusicgenre();
            $festivalinprogressmusicgenre->setMusicgenreId($music_genre_id);
            $festivalinprogressmusicgenre->setFestivalInprogressId($festival_inprogress);
            $em->persist($festivalinprogressmusicgenre);
            $em->flush();
        }

        // highlights ( Features ) 
        
        $highlights_array = explode(',', $highlights);
        foreach($highlights_array as $highlights)
        {
            
        }
        // Feature Data :: Start 

        $feature_data = array();$count = 1;
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
            $featureid = $em->getRepository('IFlairFestivalBundle:features')->findOneById($feature['id']);
            $festival_inprogress = $em->getRepository('IFlairFestivalBundle:FestivalInprogress')->findOneById($last_inserted_id);
            $festivalinprogressfeatures = new FestivalInprogressFeatures();
            $festivalinprogressfeatures->setFeatureId($featureid);
            $festivalinprogressfeatures->setFestivalInprogressId($festival_inprogress);
            $festivalinprogressfeatures->setStatus($feature['is_selected']);
            $em->persist($festivalinprogressfeatures);
            $em->flush();
        }

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
            $festival_inprogress = $em->getRepository('IFlairFestivalBundle:FestivalInprogress')->findOneById($last_inserted_id);
            $FestivalInprogressRatings = new FestivalInprogressRatings();
            $FestivalInprogressRatings->setFestivalInprogressId($festival_inprogress);
            $FestivalInprogressRatings->setRatingId($ratingid);
            $FestivalInprogressRatings->setUserRatings($ratng['rating']);
            $em->persist($FestivalInprogressRatings);
            $em->flush();
            $rating_inserted_id = $FestivalInprogressRatings->getId();
        }

        $festivalinprogressdates = new FestivalInprogressDates();
        $festival_inprogress = $em->getRepository('IFlairFestivalBundle:FestivalInprogress')->findOneById($last_inserted_id);
        $festivalinprogressdates->setStartDates(new \DateTime($start_date));
        $festivalinprogressdates->setEndDates(new \DateTime($end_date));
        $festivalinprogressdates->setFestivalInprogressId($festival_inprogress);
        $em->persist($festivalinprogressdates);
        $em->flush();

        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'Contribution Added in Festival Inprogress.'
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}
