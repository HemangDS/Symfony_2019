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
use IFlairFestivalBundle\Entity\FestivalInprogress;
use IFlairFestivalBundle\Entity\festival;
use IFlairFestivalBundle\Entity\FestivalInprogressMultipleImageUpload;
use IFlairFestivalBundle\Entity\FestivalInprogressMusicgenre;
use IFlairFestivalBundle\Entity\FestivalInprogressCurrency;
use IFlairFestivalBundle\Entity\FestivalInprogressFeatures;
use IFlairFestivalBundle\Entity\FestivalInprogressPayments;
use IFlairFestivalBundle\Entity\FestivalInprogressDates;
use IFlairFestivalBundle\Entity\festival_attendees;


class FestivalinprogresssaveService
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
    public function festivalinprogresssaveService($dates, $music_genre, $currency, $highlights, $payments, $country_id, $city_id, $name, $attendies, $attendies_year, $stages, $heldSince, $website, $email, $host, $manager, $hostWebsite, $statusId, $user_id, $festival_id, $festivalInfo, $logo, $header)
    {
        
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();
        $festival = $em->getRepository('IFlairFestivalBundle:festival')->findOneById($festival_id);
        $user = $em->getRepository('AppBundle:User')->findOneById($user_id);
        $country = $em->getRepository('IFlairSoapBundle:Partyfindercountry')->findOneById($country_id);
        $city = $em->getRepository('IFlairSoapBundle:Partyfindercity')->findOneById($city_id);
        $status = $em->getRepository('IFlairSoapBundle:ContributionStatus')->findOneById($statusId);
        
        $festival_inprogress = new FestivalInprogress();
        $festival_inprogress->setName($name);
        $festival_inprogress->setCountryId($country);
        $festival_inprogress->setCityId($city);
        $festival_inprogress->setFestivalId($festival);
        $festival_inprogress->setUserId($user);
        $festival_inprogress->setLogo($logo);
        $festival_inprogress->setHeader($header);
        $festival_inprogress->setAttendies($attendies);
        $festival_inprogress->setStages($stages);
        $festival_inprogress->setHeldSince($heldSince);
        $festival_inprogress->setWebsite($website);
        $festival_inprogress->setEmail($email);
        $festival_inprogress->setHost($host);
        $festival_inprogress->setManager($manager);
        $festival_inprogress->setHostWebsite($hostWebsite);
        $festival_inprogress->setStatus($status);
        $festival_inprogress->setCreatedDate(new \DateTime());
        $em->persist($festival_inprogress);
        $em->flush();
        $last_inserted_id = $festival_inprogress->getId();

        // Music Genre 
        if(!empty($music_genre))
        {
            $music_genre_array = explode(',', $music_genre);
            foreach($music_genre_array as $music)
            {
                $music_genre_id = $em->getRepository('IFlairSoapBundle:Musicgenre')->findOneById($music);
                $festival_inprogress = $em->getRepository('IFlairFestivalBundle:FestivalInprogress')->findOneById($last_inserted_id);
                $festivalinprogressmusicgenre = new FestivalInprogressMusicgenre();
                $festivalinprogressmusicgenre->setMusicgenreId($music_genre_id);
                $festivalinprogressmusicgenre->setFestivalInprogressId($festival_inprogress);
                $em->persist($festivalinprogressmusicgenre);
                $em->flush();
            }
        }
        // Currency 
        $cmp_cur = $currency;
        if(!empty($currency))
        {
            $currency_array = explode(',', $currency);
            foreach($currency_array as $currency)
            {
                $currency_id = $em->getRepository('IFlairFestivalBundle:currency')->findOneById($currency);
                $festival_inprogress = $em->getRepository('IFlairFestivalBundle:FestivalInprogress')->findOneById($last_inserted_id);
                $festivalinprogresscurrency = new FestivalInprogressCurrency();
                $festivalinprogresscurrency->setCurrencyId($currency_id);
                $festivalinprogresscurrency->setFestivalInprogressId($festival_inprogress);
                $em->persist($festivalinprogresscurrency);
                $em->flush();
            }
        }
        // highlights ( Features ) 
        $cmp_hlt = $highlights;
        if(!empty($highlights))
        {
            $highlights_array = explode(',', $highlights);
            foreach($highlights_array as $highlights)
            {
                $highlights_id = $em->getRepository('IFlairFestivalBundle:features')->findOneById($highlights);
                $festival_inprogress = $em->getRepository('IFlairFestivalBundle:FestivalInprogress')->findOneById($last_inserted_id);
                $festivalinprogressfeatures = new FestivalInprogressFeatures();
                $festivalinprogressfeatures->setFeatureId($highlights_id);
                $festivalinprogressfeatures->setFestivalInprogressId($festival_inprogress);
                $em->persist($festivalinprogressfeatures);
                $em->flush();
            }
        }
        // Payments 
        $cmp_pmt = $payments;
        if(!empty($payments))
        {
            $payments_array = explode(',', $payments);
            foreach($payments_array as $payments)
            {
                $payments_id = $em->getRepository('IFlairSoapBundle:Payments')->findOneById($payments);
                $festival_inprogress = $em->getRepository('IFlairFestivalBundle:FestivalInprogress')->findOneById($last_inserted_id);
                $festivalinprogresspayments = new FestivalInprogressPayments();
                $festivalinprogresspayments->setPaymentId($payments_id);
                $festivalinprogresspayments->setFestivalInprogressId($festival_inprogress);
                $em->persist($festivalinprogresspayments);
                $em->flush();
            }
        }
        if(!empty($dates))
        {
            $dates_array = json_decode($dates, true);
            foreach($dates_array as $date)
            {
                $festivalinprogressdates = new FestivalInprogressDates();
                $festival_inprogress = $em->getRepository('IFlairFestivalBundle:FestivalInprogress')->findOneById($last_inserted_id);
                $festivalinprogressdates->setStartDates(new \DateTime($date['start']));
                $festivalinprogressdates->setEndDates(new \DateTime($date['end']));
                $festivalinprogressdates->setFestivalInprogressId($festival_inprogress);
                $em->persist($festivalinprogressdates);
                $em->flush();
            }
        }
        $new_attendies = 'false';
        $festival_attendies = $em->getRepository('IFlairFestivalBundle:festival_attendees')->findByFestivalId($festival_id);
        $attendies_array = array();
        foreach($festival_attendies as $attend)
        {
            if($attendies_year == $attend->getAttendeesYear())
            {
                $festival_attendiesyear = $em->getRepository('IFlairFestivalBundle:festival_attendees')->findOneByAttendeesYear($attend->getAttendeesYear());
                if($attend->getAttendeesYear())
                {
                    $new_attendies = 'true';
                    $festival_attendiesyear->setAttendees($attendies);
                    $em->persist($festival_attendiesyear);
                    $em->flush();
                }
            }else{
                $queryBuilder1 = $em->createQueryBuilder();
                $queryBuilder1->select('fa.id')
                    ->from('IFlairFestivalBundle\Entity\festival_attendees', 'fa')
                    ->where("fa.festivalId = $festival_id")->andwhere("fa.attendeesYear = $attendies_year");
                $festival_attendiesyearr = $queryBuilder1->getQuery()->getResult();
                if(!$festival_attendiesyearr)
                {
                    $new_attendies = 'true';
                    $festival = $em->getRepository('IFlairFestivalBundle:festival')->findOneById($festival_id);
                    $attendees = new festival_attendees();                    
                    $attendees->setFestivalId($festival);
                    $attendees->setAttendees($attendies);
                    $attendees->setAttendeesYear($attendies_year);
                    $em->persist($attendees);
                    $em->flush();
                }
            }
        }

/* Edited Fields update */
        $festival = $em->getRepository('IFlairFestivalBundle:festival')->findOneById($festival_id);
        if($festival)
        {
            if(!empty($festival->getTitle()))
                $compare_name = $festival->getTitle();
            else
                $compare_name = '';
        }
        if($festival)
        {
            if(!empty($festival->getHeldSince()))
                $compare_heldsince = $festival->getHeldSince();
            else
                $compare_heldsince = '';
        }
        if($festival)
        {
            if(!empty($festival->getStages()))
                $compare_stages = $festival->getStages();
            else
                $compare_stages = '';
        }
        $festival_organiser = $em->getRepository('IFlairFestivalBundle:festival_organizer')->findOneByFestivalId($festival_id);
        if($festival_organiser)
        {
            if(!empty($festival_organiser->getWebsite()))
                $compare_website = $festival_organiser->getWebsite();
            else
                $compare_website = '';
        }
        if($festival_organiser)
        {
            if(!empty($festival_organiser->getEmail()))
                $compare_email = $festival_organiser->getEmail();
            else
                $compare_email = '';
        }
        if($festival_organiser)
        {
            if(!empty($festival_organiser->getManager()))
                $compare_manager = $festival_organiser->getManager();
            else
                $compare_manager = '';
        }
        if($festival_organiser)
        {
            if(!empty($festival_organiser->getCity()->getId()))
                $compare_city = $festival_organiser->getCity()->getId();
            else
                $compare_city = '';
        }
        if($festival_organiser)
        {
            if(!empty($festival_organiser->getCountry()->getId()))
                $compare_country = $festival_organiser->getCountry()->getId();
            else
                $compare_country = '';
        }
        if($festival_organiser)
        {
            if(!empty($festival_organiser->getHost()))
                $compare_host = $festival_organiser->getHost();
            else
                $compare_host = '';
        }
        
        $festival_musicgenre = $em->getRepository('IFlairFestivalBundle:festival_musicgenre')->findByFestivalId($festival_id);
        $music_array = array();
        if($festival_musicgenre)
        {
            foreach($festival_musicgenre as $music)
            {
                $music_array[] = $music->getMusicGenreId()->getId();
            }
        }
        $festival_payments = $em->getRepository('IFlairFestivalBundle:Festival_Payment')->findByFestivalId($festival_id);
        $payment_array = array();
        foreach($festival_payments as $payment)
        {
            $payment_array[] = $payment->getPaymentId()->getId();
        }
        $festival_currency = $em->getRepository('IFlairFestivalBundle:festival_currency')->findByFestivalId($festival_id);
        $currency_array = array();
        foreach($festival_currency as $currency)
        {
            $currency_array[] = $currency->getCurrencyId()->getId();
        }
        $festival_features = $em->getRepository('IFlairFestivalBundle:festival_features')->findByFestivalId($festival_id);
        $feature_array = array();
        foreach($festival_features as $feature)
        {
            $feature_array[] = $feature->getFeatureId()->getId();
        }

        $festival_features = $em->getRepository('IFlairFestivalBundle:festival_features')->findByFestivalId($festival_id);
        $feature_array = array();
        foreach($festival_features as $feature)
        {
            $feature_array[] = $feature->getFeatureId()->getId();
        }

        $festival_dates = $em->getRepository('IFlairFestivalBundle:festival_dates')->findByFestivalId($festival_id);
        $dates1 = array(); $count = 0;
        if($festival_dates)
        {
            foreach($festival_dates as $date)
            {
                $dates1[$count]['start'] = $date->getStartDate()->format('d.m.Y');
                $dates1[$count]['end'] = $date->getEndDate()->format('d.m.Y');
                $count++;
            }
        }
        $json_obj = json_encode($dates1);
        $json_obj = preg_replace('/\s+/', '', $json_obj);        
        $compare_music = implode(',', $music_array);
        $compare_payment = implode(',', $payment_array);
        $compare_currency = implode(',', $currency_array);
        $compare_feature = implode(',', $feature_array);

        $compare_festinfo = $festival->getDescription();

        $edited_info = array();

        $json_obj = json_decode($json_obj, true);
        $dates = json_decode($dates, true);
        if($json_obj != $dates)
            $edited_info[] = 'dates';

        if($compare_name != $name)
            $edited_info[] = 'name';
        if(trim($compare_heldsince) != trim($heldSince))
            $edited_info[] = 'heldsince';
        if(trim($compare_stages) != trim($stages))
            $edited_info[] = 'stages';
        if(trim($compare_website) != trim($website))
            $edited_info[] = 'website';
        if(trim($compare_email) != trim($email))
            $edited_info[] = 'email';
        if(trim($compare_manager) != trim($manager))
            $edited_info[] = 'manager';
        if(trim($compare_city) != trim($city_id))
            $edited_info[] = 'city_id';
        if(trim($compare_country) != trim($country_id))
            $edited_info[] = 'country_id';
        if(trim($compare_host) != trim($host))
            $edited_info[] = 'host';
        if(trim($compare_music) != trim($music_genre))
            $edited_info[] = 'music_genre';
        if(trim($compare_payment) != trim($cmp_pmt))
            $edited_info[] = 'payments';
        if(trim($compare_currency) != trim($cmp_cur))
            $edited_info[] = 'currency';
        if(trim($compare_feature) != trim($cmp_hlt))
            $edited_info[] = 'highlights';
        if(trim($compare_festinfo) != trim($festivalInfo))
            $edited_info[] = 'festivalInfo';
        if($new_attendies == 'true')
            $edited_info[] = 'attendees';


        $festival_inprogress = $em->getRepository('IFlairFestivalBundle:FestivalInprogress')->findOneById($last_inserted_id);
        $edited_data = json_encode($edited_info);
        $festival_inprogress->setUpdatedFields($edited_data);
        $em->persist($festival_inprogress);
        $em->flush();

/* Edited Fields update */

        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'Festival Inprogress saved.'
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}
