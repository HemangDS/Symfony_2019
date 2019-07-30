<?php
namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use IFlairSoapBundle\Models\Soapurls;
use IFlairSoapBundle\Models\compareDatetime;
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\PartyfinderInprogress;
use IFlairSoapBundle\Entity\PartyfinderInprogressMusicgenre;
use IFlairSoapBundle\Entity\PartyfinderInprogressTiming;
use IFlairSoapBundle\Entity\PartyfinderInprogressFeatures;
use IFlairSoapBundle\Entity\PartyfinderInprogressPayments;
use IFlairSoapBundle\Entity\partyInfoEditConfirmation;

class PartyfinderinprogresssaveService
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
    public function partyfinderinprogresssaveService($name, $country_id, $city_id, $website, $email, $manager, $management, $management_website, $dancefloor, $user_id, $type, $party_finder_id, $music_genre, $days_time, $payments, $highlights, $party_info, $logo, $header,$openedIn, $facebook, $snapchat, $twitter, $instagram, $youtube)
    {
        $instagram1 = $instagram;
        $snapchat1 = $snapchat;
        $facebook1 =$facebook;
        $payments1 = $payments;
        $highlights1 = $highlights;
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();

        $user = $em->getRepository('AppBundle:User')->findOneById($user_id);
        $partyfinderId = $em->getRepository('IFlairSoapBundle:Partyfinder')->findOneBy(array('id' => $party_finder_id));
        $country = $em->getRepository('IFlairSoapBundle:Partyfindercountry')->findOneById($country_id);
        $city = $em->getRepository('IFlairSoapBundle:Partyfindercity')->findOneById($city_id);
        $status = $em->getRepository('IFlairSoapBundle:ContributionStatus')->findOneById(2);
        $type_id = $em->getRepository('IFlairSoapBundle:Partyfindertype')->findOneByName(trim($type));

        $partyfinder_inprogress = new PartyfinderInprogress();
        $partyfinder_inprogress->setName($name);
        $partyfinder_inprogress->setHeader($header);
        $partyfinder_inprogress->setLogo($logo);
        $partyfinder_inprogress->setDancefloor($dancefloor);
        $partyfinder_inprogress->setWebsite($website);
        $partyfinder_inprogress->setOpenedIn($openedIn);
        $partyfinder_inprogress->setEmail($email);
        $partyfinder_inprogress->setManagement($management);
        $partyfinder_inprogress->setManager($manager);
        $partyfinder_inprogress->setManagementWebsite($management_website);
        $partyfinder_inprogress->setFacebook($facebook);
        $partyfinder_inprogress->setSnapchat($snapchat);
        $partyfinder_inprogress->setTwitter($twitter);
        $partyfinder_inprogress->setInstagram($instagram);
        $partyfinder_inprogress->setYoutube($youtube);
        $partyfinder_inprogress->setCountryId($country);
        $partyfinder_inprogress->setCityId($city);
        $partyfinder_inprogress->setTypeId($type_id);
        $partyfinder_inprogress->setUserId($user);
        $partyfinder_inprogress->setPartyFinderId($partyfinderId);
        $partyfinder_inprogress->setCreatedDate(new \DateTime());
        $partyfinder_inprogress->setStatusId($status);
        $em->persist($partyfinder_inprogress);
        $em->flush();
        $last_inserted_id = $partyfinder_inprogress->getId();

        // Music Genre 
        if(!empty($music_genre))
        {
            $music_genre_array = explode(',', $music_genre);
            foreach($music_genre_array as $music)
            {
                $music_genre_id = $em->getRepository('IFlairSoapBundle:Musicgenre')->findOneById($music);
                $partyfinderInprogress = $em->getRepository('IFlairSoapBundle:PartyfinderInprogress')->findOneById($last_inserted_id);
                $partyfinderinprogressmusicgenre = new PartyfinderInprogressMusicgenre();
                $partyfinderinprogressmusicgenre->setMusicgenreId($music_genre_id);
                $partyfinderinprogressmusicgenre->setPartyfinderInprogressId($partyfinderInprogress);
                $em->persist($partyfinderinprogressmusicgenre);
                $em->flush();
            }
        }
        // Party finder timing
        $json = array(json_decode($days_time, true));
        $count = 0;
        foreach($json as $dayname => $day_time)
        {
            foreach($day_time as $key => $day)
            {
                if(!empty($day[$count]['from']) && !empty($day[$count]['to']))
                {
                    $partyfinderInprogress = $em->getRepository('IFlairSoapBundle:PartyfinderInprogress')->findOneById($last_inserted_id);
                    $partyfinderInprogressTiming = new PartyfinderInprogressTiming();
                    $partyfinderInprogressTiming->setStartTime($day[$count]['from']);
                    $partyfinderInprogressTiming->setEndTime($day[$count]['to']);
                    $partyfinderInprogressTiming->setPartyfinderInprogressId($partyfinderInprogress);                
                    $partyfinderInprogressTiming->setTimingDay($dayname);
                    $em->persist($partyfinderInprogressTiming);
                    $em->flush();
                    $count ++;
                }
            }
        }
        // Payments
        if(!empty($payments))
        {
            $payments_array = explode(',', $payments);
            foreach($payments_array as $payments)
            {
                $payments_id = $em->getRepository('IFlairSoapBundle:Payments')->findOneById($payments);
                $partyfinderInprogress = $em->getRepository('IFlairSoapBundle:PartyfinderInprogress')->findOneById($last_inserted_id);
                $partyfinderInprogressPayments = new PartyfinderInprogressPayments();
                $partyfinderInprogressPayments->setPaymentId($payments_id);
                $partyfinderInprogressPayments->setPartyfinderInprogressId($partyfinderInprogress);
                $em->persist($partyfinderInprogressPayments);
                $em->flush();
            }
        }
        // highlights
        if(!empty($highlights))
        {
            $highlights_array = explode(',', $highlights);
            foreach($highlights_array as $highlights)
            {
                $highlights_id = $em->getRepository('IFlairFestivalBundle:features')->findOneById($highlights);
                $partyfinderInprogress = $em->getRepository('IFlairSoapBundle:PartyfinderInprogress')->findOneById($last_inserted_id);
                $partyfinderInprogressFeatures = new PartyfinderInprogressFeatures();
                $partyfinderInprogressFeatures->setFeatureId($highlights_id);
                $partyfinderInprogressFeatures->setPartyfinderInprogressId($partyfinderInprogress);
                $em->persist($partyfinderInprogressFeatures);
                $em->flush();
            }
        }
        // party finder information
        $partyfinder = $em->getRepository('IFlairSoapBundle:Partyfinder')->findOneBy(array('id' => $party_finder_id));
        $partyInformation = new partyInfoEditConfirmation();
        $partyInformation->setPartyInfo($party_info);
        $partyInformation->setStatus(0);
        $partyInformation->setUserId($user);
        $partyInformation->setPartyFinderId($partyfinder);
        $em->persist($partyInformation);
        $em->flush();

        // Edited Fields 
        $partyfinder = $em->getRepository('IFlairSoapBundle:Partyfinder')->findOneBy(array('id' => $party_finder_id));
        $club_city_id = $partyfinder->getClubLocationId()->getCityId()->getId();
        $club_country_id = $partyfinder->getClubLocationId()->getCityId()->getCountryId()->getId();
        $club_title = $partyfinder->getClubTitle();
        $club_type = $partyfinder->getClubTypeId()->getName();
        $club_openedin = $partyfinder->getClubSinceYear(); // opened in 
        $club_dancefloor = $partyfinder->getClubDancefloor();
        $club_website = $partyfinder->getClubWebsite();
        $club_email = $partyfinder->getClubEmail();
        $club_management = $partyfinder->getClubManagement();
        $club_manager = $partyfinder->getClubManager();
        
        // party_info
        $club_partyinfo = $partyfinder->getClubDescription();

        $club_management_website = $partyfinder->getClubManagementWebsite();
        $partyfinder_social = $em->getRepository('IFlairSoapBundle:Partysocialmedia')->findBy(array('partyFinderId' => $party_finder_id));
        $club_social_facebook = ''; $club_social_Twitter = ''; $club_social_Snapchat = ''; $club_social_Instagram = ''; $club_social_Youtube = '';
        foreach($partyfinder_social as $social)
        {   
            if($social->getSocialMediaId()->getSocialMedia() == 'Facebook'){
                $club_social_facebook = $social->getSocialUrl();
            }else if($social->getSocialMediaId()->getSocialMedia() == 'Twitter'){
                $club_social_Twitter = $social->getSocialUrl();
            }else if($social->getSocialMediaId()->getSocialMedia() == 'Snapchat'){
                $club_social_Snapchat = $social->getSocialUrl();
            }else if($social->getSocialMediaId()->getSocialMedia() == 'Instagram'){
                $club_social_Instagram = $social->getSocialUrl();
            }else if($social->getSocialMediaId()->getSocialMedia() == 'Youtube'){
                $club_social_Youtube = $social->getSocialUrl();
            }
        }

        $partyfinder_musicgenre = $em->getRepository('IFlairSoapBundle:Partyfindermusicgenre')->findBy(array('partyFinderId' => $party_finder_id));
        $party_musicgenre = array();
        foreach($partyfinder_musicgenre as $musicgenre)
        {
            $party_musicgenre[] = $musicgenre->getMusicGenreId()->getId();
        }
        $club_party_musicgenre = implode(',', $party_musicgenre);

        $partyfinder_payment = $em->getRepository('IFlairSoapBundle:Partyfinder_Payment')->findBy(array('partyFinderId' => $party_finder_id));
        $party_payment = array();
        foreach($partyfinder_payment as $payment)
        {
            $party_payment[] = $payment->getPaymentId()->getId();
        }
        $club_party_payment = implode(',', $party_payment);

        $partyfinder_features = $em->getRepository('IFlairSoapBundle:Partyfinderfeatures')->findBy(array('partyFinderId' => $party_finder_id));
        $party_features = array();
        foreach($partyfinder_features as $feature)
        {
            $party_features[] = $feature->getFeatureId()->getId();
        }
        $club_party_features = implode(',', $party_features);

        $partyfinder_timing = $em->getRepository('IFlairSoapBundle:Partyfindertiming')->findBy(array('partyFinderId' => $party_finder_id));
        $party_timing = array();
        foreach($partyfinder_timing as $timing)
        {
            if(array_key_exists($timing->getTimingDay(), $party_timing))
            {
                $count = count($party_timing[$timing->getTimingDay()]);
                $party_timing[$timing->getTimingDay()][$count]['from'] = $timing->getStartTime();
                $party_timing[$timing->getTimingDay()][$count]['to'] = $timing->getEndTime();
            }else{
                $party_timing[$timing->getTimingDay()][0]['from'] = $timing->getStartTime();
                $party_timing[$timing->getTimingDay()][0]['to'] = $timing->getEndTime();
            }
        }
        $compare_timing = json_encode($party_timing);
        $compare_timing = strtolower($compare_timing);
        $days_time = strtolower($days_time);
        $json = array(json_decode($days_time, true));
        $json1 = array(json_decode($compare_timing, true));
        $json = $json[0];
        $json1 = $json1[0];
        ksort($json);
        ksort($json1);
        $obj11 = new compareDatetime();
        $result = $obj11->partyfinderinprogresssaverecursive1($json, $json1);
        $edited_info = array();
        if($result != 0)
        { $edited_info[] = 'timing'; }

        if($club_title != $name)
            $edited_info[] = 'name';
        if($club_country_id != $country_id)
            $edited_info[] = 'country';
        if($club_city_id != $city_id)
            $edited_info[] = 'city';
        if($club_website != $website)
            $edited_info[] = 'website';
        if($club_email != $email)
            $edited_info[] = 'email';
        if($club_manager != $manager)
            $edited_info[] = 'manager';
        if($club_management != $management)
            $edited_info[] = 'management';
        if($club_management_website != $management_website)
            $edited_info[] = 'management_website';
        if($club_dancefloor != $dancefloor)
            $edited_info[] = 'dancefloor';
        /*if($club_type != $type)
            $edited_info[] = 'type';*/
        if($club_party_musicgenre != $music_genre)
            $edited_info[] = 'music_genre';
        if($club_party_payment != $payments1)
            $edited_info[] = 'payments';
        if($club_party_features != $highlights1)
            $edited_info[] = 'highlights';


        $rerfb = $club_social_facebook.' != '.$facebook1;
        if($club_social_facebook != $facebook1)
            $edited_info[] = 'facebook';

        $snaptcht = $club_social_Snapchat.' != '.$snapchat1;
        if($club_social_Snapchat != $snapchat1)
            $edited_info[] = 'snapchat';
        if($club_social_Twitter != $twitter)
            $edited_info[] = 'twitter';

        $clubisnta = $club_social_Instagram.' != '.$instagram1;
        if($club_social_Instagram != $instagram1)
            $edited_info[] = 'instagram';
        if($club_social_Youtube != $youtube)
            $edited_info[] = 'youtube';
        if($party_info != $club_partyinfo)
            $edited_info[] = 'description';

        $partyfinderInprogress = $em->getRepository('IFlairSoapBundle:PartyfinderInprogress')->findOneById($last_inserted_id);
        $edited_data = json_encode($edited_info);
        $partyfinderInprogress->setUpdatedFields($edited_data);
        $em->persist($partyfinderInprogress);
        $em->flush();

        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'Party finder Inprogress saved.'
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}