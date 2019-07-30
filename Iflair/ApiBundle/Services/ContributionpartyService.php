<?php
namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Settings;
use IFlairSoapBundle\Entity\PartyfinderInprogress;
use IFlairSoapBundle\Entity\PartyfinderInprogressRatings;
use IFlairSoapBundle\Entity\PartyfinderInprogressFeatures;
use IFlairSoapBundle\Entity\PartyfinderInprogressMusicgenre;
use IFlairFestivalBundle\Entity\ContributionStatus;

class ContributionpartyService
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
    public function getcontributionaddparty($location_name,$user_id,$country_id,$city_id,$club_type,$feature_id,$rating_id,$music_genre,$upload_image_path)
    {
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();

        $music_data = json_decode($music_genre, true);
        $ratings = array(json_decode($rating_id, true));
        $features = array(json_decode($feature_id, true));

        // Party Finder added
        $contribution_status = $em->getRepository('IFlairSoapBundle:ContributionStatus')->findOneById(2);
        $user = $em->getRepository('AppBundle:User')->findOneById($user_id);
        $country = $em->getRepository('IFlairSoapBundle:Partyfindercountry')->findOneById($country_id);
        $city = $em->getRepository('IFlairSoapBundle:Partyfindercity')->findOneById($city_id);        
        $club_type = $em->getRepository('IFlairSoapBundle:Partyfindertype')->findOneByName($club_type);
        $partyfinderInprogress = new PartyfinderInprogress();
        $partyfinderInprogress->setName($location_name);
        $partyfinderInprogress->setHeader($upload_image_path);
        $partyfinderInprogress->setLogo(NULL);
        $partyfinderInprogress->setDancefloor(NULL);
        $partyfinderInprogress->setWebsite(NULL);
        $partyfinderInprogress->setOpenedIn(NULL);
        $partyfinderInprogress->setEmail(NULL);
        $partyfinderInprogress->setManagement(NULL);
        $partyfinderInprogress->setManager(NULL);
        $partyfinderInprogress->setManagementWebsite(NULL);
        $partyfinderInprogress->setFacebook(NULL);
        $partyfinderInprogress->setSnapchat(NULL);
        $partyfinderInprogress->setTwitter(NULL);
        $partyfinderInprogress->setInstagram(NULL);
        $partyfinderInprogress->setYoutube(NULL);
        $partyfinderInprogress->setCountryId($country);
        $partyfinderInprogress->setCityId($city);
        $partyfinderInprogress->setTypeId($club_type);
        $partyfinderInprogress->setUserId($user);
        $partyfinderInprogress->setCreatedDate(new \DateTime());
        $partyfinderInprogress->setStatusId($contribution_status);
        $em->persist($partyfinderInprogress);
        $em->flush();
        $last_inserted_id = $partyfinderInprogress->getId();

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
            $partyfinderInprogress_id = $em->getRepository('IFlairSoapBundle:PartyfinderInprogress')->findOneById($last_inserted_id);
            $partyfinderInprogressRatings = new PartyfinderInprogressRatings();
            $partyfinderInprogressRatings->setPartyfinderInprogressId($partyfinderInprogress_id);
            $partyfinderInprogressRatings->setRatingId($ratingid);
            $partyfinderInprogressRatings->setUserRating($ratng['rating']);
            $partyfinderInprogressRatings->setUserId($user);
            $em->persist($partyfinderInprogressRatings);
            $em->flush();
            $rating_inserted_id = $partyfinderInprogressRatings->getId();
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
            $partyfinderInprogress_id = $em->getRepository('IFlairSoapBundle:PartyfinderInprogress')->findOneById($last_inserted_id);
            $featureid = $em->getRepository('IFlairFestivalBundle:features')->findOneById($feature['id']);
            $partyfinderInprogressFeatures = new PartyfinderInprogressFeatures();
            $partyfinderInprogressFeatures->setPartyfinderInprogressId($partyfinderInprogress_id);
            $partyfinderInprogressFeatures->setFeatureId($featureid);
            $partyfinderInprogressFeatures->setStatus($feature['is_selected']);
            $em->persist($partyfinderInprogressFeatures);
            $em->flush();
            $feature_inserted_id = $partyfinderInprogressFeatures->getId();
        }

        // music genre
        foreach($music_data as $music)
        {
            $partyfinderInprogress_id = $em->getRepository('IFlairSoapBundle:PartyfinderInprogress')->findOneById($last_inserted_id);
            $musicgenre = $em->getRepository('IFlairSoapBundle:Musicgenre')->findOneById($music);
            $partyfinderInprogressMusicgenre = new PartyfinderInprogressMusicgenre();
            $partyfinderInprogressMusicgenre->setPartyfinderInprogressId($partyfinderInprogress_id);
            $partyfinderInprogressMusicgenre->setMusicgenreId($musicgenre);
            $em->persist($partyfinderInprogressMusicgenre);
            $em->flush();
            $music_inserted_id = $partyfinderInprogressMusicgenre->getId();
        }

        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'Contribution Party Location Inprogress Added.'
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}
