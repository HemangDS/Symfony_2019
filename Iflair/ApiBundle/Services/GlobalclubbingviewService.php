<?php

namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
// ENTITY
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Partyfindertiming;
use IFlairSoapBundle\Entity\Partyfinderviews;

class GlobalclubbingviewService
{
    protected $doctrine;
    public function __construct(RequestStack $request,Registry $doctrine)
    {
        $this->request = $request;
        $this->doctrine = $doctrine;
    }
    /**
     * View NightClub or Bar when called
     * @param string $name 
     * @return mixed
     */
    public function globalclubbingview($party_id, $user_id)
    {
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();
        $user = $this->doctrine->getRepository('AppBundle:User')->findOneBy(array('id' => $user_id));
        $partyfinder = $em->getRepository('IFlairSoapBundle:Partyfinder')->findById($party_id);
        $is_admin = 'false';
        foreach($partyfinder as  $club)
        {
            $club_title = $club->getClubTitle();
            $club_location_id = $club->getClubLocationId();
            $club_city = $club->getClubLocationId()->getCityId()->getCityName();
            $club_country = $club->getClubLocationId()->getCityId()->getCountryId()->getCountryName();
            $club_type_id = $club->getClubTypeId()->getId();
            $club_dancefloor = $club->getClubDancefloor();
            $club_sinceyear = $club->getClubSinceYear();
            $club_info = $club->getClubDescription();
            $club_website = $club->getClubWebsite();
            $club_email = $club->getClubEmail();
            $club_management = $club->getClubManagement();
            $club_manager = $club->getClubManager();
            $club_management_website = $club->getClubManagementWebsite();
            /* if($club->getUserAdmin()->getEmail() == $user->getEmail()){*/
                $is_admin = 'true';
            /*}*/
        }
        $image_name = $em->getRepository('IFlairSoapBundle:Partyfinder')->getImage($em, $party_id, $request);

        if($club_location_id)
        {
            $partyfinderlocation = $em->getRepository('IFlairSoapBundle:Partyfinderlocation')->findById($club_location_id);
            foreach($partyfinderlocation as $location)
            {
                $address = $location->getAddress();
                $latitude = $location->getLatitude();
                $longitude = $location->getLongitude();
            }
        }
        $partymusic = $em->getRepository('IFlairSoapBundle:Partyfindermusicgenre')->findBy(array('partyFinderId' => $partyfinder));
        foreach($partymusic as $music)
        {
            $partymusic = $em->getRepository('IFlairSoapBundle:Musicgenre')->findById($music->getMusicGenreId()->getId());
            foreach($partymusic as $musicname)
            {
                $music_name[] = $musicname->getName();
            }
        }
        $social_name = array();
        $partysocial = $em->getRepository('IFlairSoapBundle:Partysocialmedia')->findBy(array('partyFinderId' => $partyfinder));
        $count = 0;
        foreach($partysocial as $social_m)
        {
            $party = $em->getRepository('IFlairSoapBundle:Socialmedia')->findById($social_m->getSocialMediaId()->getId());
            foreach($party as $social)
            {
                $social_name[$count]['name'] = $social->getSocialMedia();
            }
            $social_name[$count]['url'] = $social_m->getSocialUrl();
            $count++;
        }
        if($club_type_id)
        {
            $partyfindertype = $em->getRepository('IFlairSoapBundle:Partyfindertype')->findById($club_type_id);
            foreach($partyfindertype as $type)
            {
                $club_type = $type->getName();
            }
        }
        $partyfinderatings = $em->getRepository('IFlairSoapBundle:Partyfinderratings')->findBy(array('partyFinderId' => $partyfinder));
        foreach($partyfinderatings as $rating)
        {
            $total += $rating->getUserRatings();
            $average_rating++;
        }
        if($total >= 1 && $average_rating >= 1)
            $average_rating = $total/$average_rating;
        else
            $average_rating = 0;
        

        $party_features = $em->getRepository('IFlairSoapBundle:Partyfinderfeatures')->findByPartyFinderId($party_id);
        $count = 0; $features = array();
        foreach($party_features as $feature)
        {
            $features[$feature->getFeatureId()->getType()][$count]['name'] = $feature->getFeatureId()->getName();
            $features[$feature->getFeatureId()->getType()][$count]['status'] = $feature->getStatus();
            $count++;
        }

        $party_payment = $em->getRepository('IFlairSoapBundle:Partyfinder_Payment')->findByPartyFinderId($party_id);
        $count = 0; $payments = array();
        foreach($party_payment as $payment)
        {
            $payments[$payment->getPaymentId()->getType()][$count]['name'] = $payment->getPaymentId()->getName();
            $payments[$payment->getPaymentId()->getType()][$count]['status'] = $payment->getStatus();
            $count++;
        }


        $partyfindeimage = $em->getRepository('IFlairSoapBundle:Partyfinderimage')->findBy(array('partyFinderId' => $partyfinder,  'imageType' => 'banner'));
        $banner_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairsoap/images/';
        foreach($partyfindeimage as $image)
        {
            $banner_path = $banner_path.$image->getImageName();
        }

        /* Favourite */
        $partyfindefavourite = $em->getRepository('IFlairSoapBundle:Partyfinderfavorite')->findBy(array('partyFinderId' => $party_id,'userId' => $user_id));
        if($partyfindefavourite)
        {
            foreach($partyfindefavourite as $favor)
            {
                $favourite = 'true';
            }

        }else{
            $favourite = 'false';
        }

        /* Country logo */
        $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/country/';
        $country_logo = $image_path.strtolower(str_replace(' ', '_', $club_country)).'.png';

        /* view count */
        $partyfindecount = $em->getRepository('IFlairSoapBundle:Partyfinderviews')->findBy(array('partyFinderId' => $party_id));
        $party_count = 0;
        foreach($partyfindecount as $counts)
        {
            $party_count++;
        }

        /* Opening Times */
        $partyfindertime = $em->getRepository('IFlairSoapBundle:Partyfindertiming')->findBy(array('partyFinderId' => $party_id));
        $timings = array();
        $club_status = '';
        foreach($partyfindertime as $times)
        {
            if(strtolower(date ('l')) == $times->getTimingDay())
            {
                $start_date = $times->getStartTime();
                $end_date = $times->getEndTime();
                date_default_timezone_set("Asia/Kolkata");
                if (time() >= strtotime($start_date) && time() <= strtotime($end_date)) {
                    $club_status = 'Opened Today';
                }else{
                    $club_status = 'Closed Today';
                }
            }
            $timings[$times->getTimingDay()] = $times->getStartTime().' - '.$times->getEndTime();
        }
        if(!$timings){ $timings = [];}
        /* Uploaded Images */
        $partyfinderuploadedimage = $em->getRepository('IFlairSoapBundle:PartyUploadedImages')->findBy(array('partyFinderId' => $partyfinder));
        $uploaded_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairsoap/uploaded_images/';
        $uploaded_images = array();
        $count = 0;
        foreach($partyfinderuploadedimage as $image)
        {
            $uploaded_images[$count]['image_name'] = $uploaded_path.$image->getImageName();
            $uploaded_images[$count]['uploaded_by'] = $image->getUserId()->getUsername();
            $uploaded_images[$count]['date'] = $image->getTimestamp()->format('Y-m-d H:i:s');
            $uploaded_images[$count]['views'] = $image->getView();
            $count++;
        }
        $uploaded_images = array_values($uploaded_images);

        $general = array();
        $general['banner_path'] = $banner_path;
        $general['title'] = $club_title;
        $general['type'] = $club_type;
        $general['location'] = $club_city;
        $general['opened'] = $club_sinceyear;
        $general['genre'] = $music_name;
        $general['dancefloor'] = $club_dancefloor;
        $general['rating'] = $average_rating;
        $general['club_logo'] = $image_name;
        $general['country_logo'] = $country_logo;
        $general['party_count'] = $party_count;
        $general['club_status'] = $club_status;
        $general['favourite'] = $favourite;
        $general['latitude'] = $latitude;
        $general['longitude'] = $longitude;
        $club_times = array();
        $club_times['week_times'] = $timings;
        $clubinfo = array();
        $clubinfo['club_info'] = $club_info;
        $feature = array();
        $feature['description'] = $club_info;
        $feature['highlights'] = $features;
        $contact = array();
        $contact['address'] = $address;
        $contact['club_website'] = $club_website;
        $contact['club_email'] = $club_email;
        $contact['club_management'] = $club_management;
        $contact['club_manager'] = $club_manager;
        $contact['club_management_website'] = $club_management_website;
        $contact['club_social'] = $social_name;
        
        $myresponse = array(
            'message' => 'global club view successfully..',
            'success' => true,
            'status' => Response::HTTP_OK,
            'content' => array(
                'general'          =>  $general,
                'club_times'       =>  $club_times,
                'clubinfo'         =>  $clubinfo,
                'feature'          =>  $feature,
                'payments'         =>  $payments,
                'contact'          =>  $contact,
                'uploaded_images'  =>  $uploaded_images,
                'is_admin' => $is_admin
            )
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}