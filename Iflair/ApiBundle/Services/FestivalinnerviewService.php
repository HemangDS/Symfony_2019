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

class FestivalinnerviewService
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
    public function viewinnerfestival($festival_id,$user_id)
    {
        $em = $this->doctrine->getManager();
        $partyfestival = $em->getRepository('IFlairFestivalBundle:festival')->findById($festival_id);
        $title = ''; $type = ''; $held_since = ''; $stages = ''; $festival_info = ''; $city = ''; $country = ''; $attendes = ''; $startdate = ''; $enddate = '';
        $music_genre = array(); $festival_curncy = array();

        $type = 'Festival';
        foreach($partyfestival as $festival)
        {
            $title = $festival->getTitle();
            $held_since = $festival->getHeldSince();
            $stages = $festival->getStages();
            $festival_info = $festival->getDescription();
            $city_id = $festival->getFestivalLocationId()->getCityId()->getId();
            $country_id = $festival->getFestivalLocationId()->getCityId()->getCountryId()->getId();
            $city = $festival->getFestivalLocationId()->getCityId()->getCityName();
            $country = $festival->getFestivalLocationId()->getCityId()->getCountryId()->getCountryName();
        }

        $festival_attendes = $em->getRepository('IFlairFestivalBundle:festival_attendees')->findByFestivalId($festival_id);
        foreach($festival_attendes as $attend)
        {
            $attendes = $attend->getAttendees();
        }

        $festival_dates = $em->getRepository('IFlairFestivalBundle:festival_dates')->findByFestivalId($festival_id);
        foreach($festival_dates as $dates)
        {
            $startdate = $dates->getStartDate()->format('d.m.Y');
            $enddate = $dates->getEndDate()->format('d.m.Y');
        }
        
        $festival_music = $em->getRepository('IFlairFestivalBundle:festival_musicgenre')->findByFestivalId($festival_id);
        foreach($festival_music as $music)
        {
            $music_genre['name'][] = $music->getMusicGenreId()->getName();
            $music_genre['id'][] = $music->getMusicGenreId()->getId();
        }
        
        $festival_currency = $em->getRepository('IFlairFestivalBundle:festival_currency')->findByFestivalId($festival_id);
        foreach($festival_currency as $currency)
        {
            $festival_curncy['code'][] = $currency->getCurrencyId()->getCurrencyCode();
            $festival_curncy['id'][] = $currency->getCurrencyId()->getId();
        }

        $festival_payment_methods = $em->getRepository('IFlairFestivalBundle:Festival_Payment')->findByFestivalId($festival_id);
        $festival_payments = array();
        foreach($festival_payment_methods as $method)
        {
            $festival_payment = $em->getRepository('IFlairFestivalBundle:Festival_Payment')->findOneById($method->getId());
            $festival_payments['name'][] = $festival_payment->getPaymentId()->getName();
            $festival_payments['id'][] = $festival_payment->getPaymentId()->getId();
        }

        $festival_organiser = $em->getRepository('IFlairFestivalBundle:festival_organizer')->findByFestivalId($festival_id);
        $count = 0; $contacts = array();
        foreach($festival_organiser as $organize)
        {
            $contacts['website'] = $organize->getWebsite();
            $contacts['manager'] = $organize->getManager();
            $contacts['email'] = $organize->getEmail();
            $contacts['host'] = $organize->getHost();
        }

        $festival_features = $em->getRepository('IFlairFestivalBundle:festival_features')->findByFestivalId($festival_id);
        $count = 0; $features = array();
        foreach($festival_features as $feature)
        {
            $features[$feature->getFeatureId()->getType()]['name'][] = $feature->getFeatureId()->getName();
            $features[$feature->getFeatureId()->getType()]['id'][] = $feature->getFeatureId()->getId();
            $count++;
        }

        $per_count = 0;
        if (!empty($title)){ $per_count++; }
        if (!empty($held_since)){ $per_count++; }
        if (!empty($stages)){ $per_count++; }
        if (!empty($festival_info)){ $per_count++; }
        if (!empty($attendes)){ $per_count++; }
        if (!empty($contacts['website'])){ $per_count++; }
        if (!empty($contacts['manager'])){ $per_count++; }
        if (!empty($contacts['email'])){ $per_count++; }
        if (!empty($contacts['host'])){ $per_count++; }
        if (!empty($features)){ $per_count++; }
        if (!empty($city)){ $per_count++; }
        if (!empty($country)){ $per_count++; }
        if (!empty($festival_attendes)){ $per_count++; }
        if (!empty($festival_dates)){ $per_count++; }
        if (!empty($festival_music)){ $per_count++; }
        if (!empty($festival_currency)){ $per_count++; }
        if (!empty($festival_payments)){ $per_count++; }        
        if (!empty($festival_features)){ $per_count++; }
        $percentage = ($per_count*100)/18;

        $general = array();
        $general["title"] = $title;
        $general["type"] = $type;
        $general["held_since"] = $held_since;
        $general['stages'] = $stages;
        $general["festival_info"] = $festival_info;
        $general['city'] = $city;
        $general['city_id'] = $city_id;
        $general['country_id'] = $country_id;
        $general['country'] = $country;
        $general['attendes'] = $attendes;
        $general['startdate'] = $startdate;
        $general['enddate'] = $enddate;

        $myresponse = array(
            'message' => 'festival view successfully..',
            'success' => true,
            'status' => Response::HTTP_OK,
            'general' => $general,
            'music_genre' => $music_genre,
            'festival_curncy' => $festival_curncy,
            'features' => $features,
            'contacts' => $contacts,
            'payments' => $festival_payments,
            'percentage' => $percentage,
            'percentage_filled_count' => $per_count,
            'percentage_total_count' => 18,
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}