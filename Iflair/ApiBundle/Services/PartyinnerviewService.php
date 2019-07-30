<?php

namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
// ENTITY
use AppBundle\Entity\User;
use IFlairFestivalBundle\Entity\festival_search;
use IFlairFestivalBundle\Entity\festival_view;
use IFlairSoapBundle\Entity\Partyfindertiming;

class PartyinnerviewService
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
    public function viewinnerparty($party_finder_id,$user_id)
    {
        $em = $this->doctrine->getManager();

        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $user_id));
        $partyfinder = $em->getRepository('IFlairSoapBundle:Partyfinder')->findById($party_finder_id);
        $type = ''; $title = ''; $city = '';$country = '';$website='';$email='';
        $club_info = '';$dance_floor = '';$management='';$manager='';$management_website='';
        $opened_in = '';
        $music_genre = array();
        foreach($partyfinder as $party)
        {
            $type = $party->getClubTypeId()->getName();
            $title = $party->getClubTitle();
            $city_id = $party->getClubLocationId()->getCityId()->getId();
            $country_id = $party->getClubLocationId()->getCityId()->getCountryId()->getId();
            $city = $party->getClubLocationId()->getCityId()->getCityName();
            $country = $party->getClubLocationId()->getCityId()->getCountryId()->getCountryName();
            $club_info = $party->getClubDescription();
            $dance_floor = $party->getClubDancefloor();
            $website = $party->getClubWebsite();
            $email = $party->getClubEmail();
            $management = $party->getClubManagement();
            $manager = $party->getClubManager();
            $opened_in = $party->getClubSinceYear();
            $management_website = $party->getClubManagementWebsite();
        }
        $party_music = $em->getRepository('IFlairSoapBundle:Partyfindermusicgenre')->findBypartyFinderId($party_finder_id);
        foreach($party_music as $music)
        {
            $music_genre['name'][] = $music->getMusicGenreId()->getName();
            $music_genre['id'][] = $music->getMusicGenreId()->getId();
        }
        
        $party_features = $em->getRepository('IFlairSoapBundle:Partyfinderfeatures')->findBypartyFinderId($party_finder_id);
        $count = 0; $features = array();
        foreach($party_features as $feature)
        {
            $features[$feature->getFeatureId()->getType()]['name'][] = $feature->getFeatureId()->getName();
            $features[$feature->getFeatureId()->getType()]['id'][] = $feature->getFeatureId()->getId();
            $count++;
        }
        
        $party_payment_methods = $em->getRepository('IFlairSoapBundle:Partyfinder_Payment')->findBypartyFinderId($party_finder_id);
        $party_payments = array();
        foreach($party_payment_methods as $method)
        {
            $party_payment_methods = $em->getRepository('IFlairSoapBundle:Partyfinder_Payment')->findOneById($method->getId());
            $party_payments['name'][] = $party_payment_methods->getPaymentId()->getName();
            $party_payments['id'][] = $party_payment_methods->getPaymentId()->getId();
        }

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

        if($party_timing)
            $timing = json_encode($party_timing);
        else
            $timing = '';  
        
        $general = array();
        $general['type'] = $type;
        $general['title'] = $title;        
        $general['city_id'] = $city_id;
        $general['country_id'] = $country_id;
        $general['city'] = $city;
        $general['country'] = $country;
        $general['club_info'] = $club_info;
        $general['dance_floor'] = $dance_floor;
        $general['website'] = $website;
        $general['email'] = $email;
        $general['management'] = $management;
        $general['manager'] = $manager;
        $general['management_website'] = $management_website;
        $general['facebook'] = $club_social_facebook;
        $general['twitter'] = $club_social_Twitter;
        $general['snapchat'] = $club_social_Snapchat;
        $general['instagram'] = $club_social_Instagram;
        $general['youtube'] = $club_social_Youtube;
        $general['opened_in'] = $opened_in;
        $general['openinghours'] = $timing;

        $myresponse = array(
            'message' => 'party view successfully..',
            'success' => true,
            'status' => Response::HTTP_OK,
            'general' => $general,
            'music_genre' => $music_genre,
            'features' => $features,
            'payments' => $party_payments
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}
