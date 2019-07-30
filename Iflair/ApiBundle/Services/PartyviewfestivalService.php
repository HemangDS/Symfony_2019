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

class PartyviewfestivalService
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
    public function partyviewfestival($festival_id,$user_id,$search)
    {
        $em = $this->doctrine->getManager();

        /* Search data insert into database ::  */
        $user = $this->doctrine->getRepository('AppBundle:User')->findOneBy(array('id' => $user_id));
        $festival = $this->doctrine->getRepository('IFlairFestivalBundle:festival')->findOneBy(array('id' => $festival_id));
        if($search == 'true')
        {   
            $festival_search = new festival_search();
            $festival_search->setUserId($user);
            $festival_search->setFestivalId($festival);
            $em->persist($festival_search);
            $em->flush();
        }
        /* Search data insert into database */

        /* Insert data into database using user id : View festival :Start */
        if($user_id)
        {
            $festival_view = new festival_view();
            $festival_view->setUserId($user);
            $festival_view->setFestivalId($festival);
            $festival_view->setViewedDate(new \DateTime());
            $em->persist($festival_view);
            $em->flush();
        }
        /* Insert data into database using user id : View festival :End */

        $request = $this->request->getCurrentRequest();
        $queryBuilder = $em->createQueryBuilder();

        $partyfestival = $em->getRepository('IFlairFestivalBundle:festival')->findById($festival_id);
        $title = ''; $type = ''; $held_since = ''; $stages = ''; $festival_info = ''; $address = ''; $latitude = ''; $longitude = '';
        $city = ''; $country = ''; $country_logo = ''; $attendes = ''; $startdate = ''; $enddate = ''; $remaining_days_to_go = '';
        $music_genre = array(); $festival_curncy = array(); $festival_logo = ''; $festival_banner = ''; $avg_rating = 0; $artists[] = array();

        $is_admin = 'false';
        foreach($partyfestival as $festival)
        {
            $title = $festival->getTitle();
            $type = $festival->getType();
            $held_since = $festival->getHeldSince();
            $stages = $festival->getStages();
            $festival_info = $festival->getDescription();
            
            if($festival->getUserAdmin()->getEmail() == $user->getEmail()){
                $is_admin = 'true';
            }
            $city = $festival->getFestivalLocationId()->getCityId()->getCityName();
            $country = $festival->getFestivalLocationId()->getCityId()->getCountryId()->getCountryName();

            $housenumber = $festival->getFestivalLocationId()->getHousenumber();
            $street = $festival->getFestivalLocationId()->getStreet();
            $zipcode = $festival->getFestivalLocationId()->getZipcode();
        }

        $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/country/';
        $country_logo = $image_path.strtolower(str_replace(' ', '_', $country)).'.png';

        $festival_attendes = $em->getRepository('IFlairFestivalBundle:festival_attendees')->findByFestivalId($festival_id);
        
        foreach($festival_attendes as $attend)
        {
            $attendes = $attend->getAttendees();
        }

        $festival_dates = $em->getRepository('IFlairFestivalBundle:festival_dates')->findByFestivalId($festival_id);
        
        foreach($festival_dates as $dates)
        {
            $startdate = $dates->getStartDate()->format('Y-m-d H:i:s');
            $enddate = $dates->getEndDate()->format('Y-m-d H:i:s');
        }
        $from = strtotime($startdate);
        $today = time();
        $difference = $from - $today;
        $remaining_days_to_go = floor($difference / 86400);
        if($remaining_days_to_go == 1)
        {
            $remaining_days_to_go = $remaining_days_to_go.' day';
        }else{
            $remaining_days_to_go = $remaining_days_to_go.' days';
        }

        $festival_music = $em->getRepository('IFlairFestivalBundle:festival_musicgenre')->findByFestivalId($festival_id);
        
        foreach($festival_music as $music)
        {
            $music_genre[] = $music->getMusicGenreId()->getName();
        }
        
        $festival_currency = $em->getRepository('IFlairFestivalBundle:festival_currency')->findByFestivalId($festival_id);
        
        foreach($festival_currency as $currency)
        {
            $festival_curncy[] = $currency->getCurrencyId()->getCurrencyCode();
        }
        

        $path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/';
        $festival_logos = $em->getRepository('IFlairFestivalBundle:festival_image')->findBy(array("festivalId" => $festival_id, "imageType" => 'logo'));
        
        foreach($festival_logos as $logos)
        {
            $festival_logo = $path.$logos->getImageName();
        }

        /* Uploaded Images */
        $festivaluploadedimage = $em->getRepository('IFlairFestivalBundle:FestivalUploadedImages')->findBy(array('festivalId' => $festival_id));
        $uploaded_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/uploaded_images/';
        $uploaded_images = array();
        $count = 0;
        foreach($festivaluploadedimage as $image)
        {
            $uploaded_images[$count]['image_name'] = $uploaded_path.$image->getImageName();
            $uploaded_images[$count]['uploaded_by'] = $image->getUserId()->getUsername();
            $uploaded_images[$count]['date'] = $image->getTimestamp()->format('Y-m-d H:i:s');
            $uploaded_images[$count]['views'] = $image->getView();
            $count++;
        }
        $uploaded_images = array_values($uploaded_images);
        /* Uploaded Images : END */

        $festival_banners = $em->getRepository('IFlairFestivalBundle:festival_image')->findBy(array("festivalId" => $festival_id, "imageType" => 'banner'));
        
        foreach($festival_banners as $banner)
        {
            $festival_banner = $path.$banner->getImageName();
        }
        $festival_ratings = $em->getRepository('IFlairFestivalBundle:festival_type_ratings')->findByFestivalId($festival_id);
        $count = 0; $festival_rating = 0;
        foreach($festival_ratings as $rating)
        {
            $festival_rating += $rating->getUserRatings();
            $count++;
        }
        if($count == 0)
            $count = 1;
        $avg_rating = $festival_rating / $count;

        $festival_atrists = $em->getRepository('IFlairFestivalBundle:festival_artist')->findByFestivalId($festival_id);
        $count = 0;
        foreach($festival_atrists as $artist)
        {
            $artists[$count][] = $artist->getArtistId()->getName();
            $artists[$count][] = $artist->getArtistId()->getSubtitle();
            $count++;
        }

        $festival_features = $em->getRepository('IFlairFestivalBundle:festival_features')->findByFestivalId($festival_id);
        $count = 0; $features = array();
        foreach($festival_features as $feature)
        {
            $features[$feature->getFeatureId()->getType()][$count]['name'] = $feature->getFeatureId()->getName();
            $features[$feature->getFeatureId()->getType()][$count]['status'] = $feature->getStatus();
            $count++;
        }

        $festival_payments = $em->getRepository('IFlairFestivalBundle:Festival_Payment')->findByFestivalId($festival_id);
        $count = 0; $payments = array();
        foreach($festival_payments as $payment)
        {
            $payments[$count]['name'] = $payment->getPaymentId()->getName();
            $payments[$count]['status'] = $payment->getStatus();
            $count++;
        }

        /* view count */
        $feativalcount = $em->getRepository('IFlairFestivalBundle:festival_view')->findByFestivalId($festival_id);
        $featival_count = 0;
        foreach($feativalcount as $counts)
        {
            $featival_count++;
        }
        
        $festival_organiser = $em->getRepository('IFlairFestivalBundle:festival_organizer')->findByFestivalId($festival_id);
        $count = 0; $contacts = array();
        foreach($festival_organiser as $organize)
        {
            $contacts['street'] = $organize->getStreet();
            $contacts['zipcode'] = $organize->getZipcode();
            $festival_city = $em->getRepository('IFlairSoapBundle:Partyfindercity')->findOneById($organize->getCity());
            $contacts['city'] = $festival_city->getCityName();
            $festival_country = $em->getRepository('IFlairSoapBundle:Partyfindercountry')->findOneById($organize->getCountry());
            $contacts['country'] = $festival_country->getCountryName();
            $contacts['phone_no'] = $organize->getPhoneNo();
            $contacts['website'] = $organize->getWebsite();
            $contacts['organizer_name'] = $organize->getOrganizerName();
            $contacts['latitude'] = $organize->getLatitude();
            $contacts['longitude'] = $organize->getLongitude();            
        }
        
        $general = array();
        $general["title"] = $title;
        $general["type"] = $type;
        $general["held since"] = $held_since;
        $general['stages'] = $stages;
        $general["featival_count"] = $featival_count;
        $general["festival_info"] = $festival_info;
        $general['city'] = $city;
        $general['country'] = $country;
        $general['country_logo'] = $country_logo;
        $general['attendes'] = $attendes;
        $general['startdate'] = $startdate;
        $general['enddate'] = $enddate;
        $general['remaining_days_to_go'] = $remaining_days_to_go;
        $general['festival_logo'] = $festival_logo;
        $general['festival_banner'] = $festival_banner;
        $general['avg_rating'] = $avg_rating;
            
        /* Code for favourite */
        $favourite = $em->getRepository('IFlairFestivalBundle:festivalFavourite')->findBy(array('userId' => $user_id, 'festivalId' => $festival_id));
        if($favourite)
        {
            foreach($favourite as $fvurite)
            {
                $favourite_id = $fvurite->getId();
            }
            $favourite = 'true';
        }else{
            $favourite = 'false';
        }
        
        $myresponse = array(
            'message' => 'festival view successfully..',
            'success' => true,
            'status' => Response::HTTP_OK,
            'general' => $general,
            'music_genre' => $music_genre,
            'festival_curncy' => $festival_curncy,
            'artists' => $artists,
            'features' => $features,
            'payments' => $payments,
            'favourite' => $favourite,
            'contacts' => $contacts,
            'uploaded_images' => $uploaded_images,
            'is_admin' => $is_admin
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}