<?php

namespace IFlairFestivalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Settings;
use Zend\Soap\Client;
use Zend\Soap;
use IFlairFestivalBundle\Models\Soapurls;
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Partyfindercountry;
use IFlairSoapBundle\Entity\Partyfindercity;
use IFlairSoapBundle\Entity\Musicgenre;
use IFlairFestivalBundle\Entity\ContributionAdddFestival;
use IFlairFestivalBundle\Entity\features;
use IFlairFestivalBundle\Entity\FestivalInprogress;
use IFlairFestivalBundle\Entity\festival;
use IFlairFestivalBundle\Entity\FestivalInprogressMusicgenre;
use IFlairFestivalBundle\Entity\FestivalInprogressCurrency;
use IFlairFestivalBundle\Entity\FestivalInprogressFeatures;
use IFlairFestivalBundle\Entity\FestivalInprogressPayments;
use IFlairFestivalBundle\Entity\FestivalInprogressDates;
use IFlairFestivalBundle\Entity\festival_attendees;

class FestivalinprogresssaveController extends Controller
{
    /**
     * @Route("/soap/v2/festivalinprogresssave", name="Festival Inprogress save")
     */
    public function festivalinprogresssaveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();     
        $dates = $request->request->get('dates');
        $music_genre = $request->request->get('music_genre');
        $currency = $request->request->get('currency');
        $highlights = $request->request->get('highlights');
        $payments = $request->request->get('payments');
        $country_id = $request->request->get('country_id');
        $city_id = $request->request->get('city_id');
        $name = $request->request->get('name');
        $attendies = $request->request->get('attendies');
        $attendies_year = $request->request->get('attendies_year');
        $stages = $request->request->get('stages');
        $heldSince = $request->request->get('heldSince');
        $website = $request->request->get('website');
        $email = $request->request->get('email');
        $host = $request->request->get('host');
        $manager = $request->request->get('manager');
        $hostWebsite = $request->request->get('hostWebsite');
        $statusId = 2;
        $user_id = $request->request->get('user_id');        
        $festival_id = $request->request->get('festival_id');
        $festivalInfo = $request->request->get('festivalInfo');

        $files = $_FILES;
        $logo = '';
        if(isset($files['header']))
        {
            $uploadDir = $this->container->getParameter('upload_dir');
            $logo = $uploadDir . time().$files['header']['name'];
            if(!move_uploaded_file($files['header']['tmp_name'], $logo)){
                die('Error while upload file');
            }
        }
        $header = '';
        if(isset($files['logo']))
        {
            $uploadDir = $this->container->getParameter('upload_dir');
            $header = $uploadDir . time().$files['logo']['name'];
            if(!move_uploaded_file($files['logo']['tmp_name'], $header)){
                die('Error while upload file');
            }
        }        
        $url_model = new Soapurls();
        $uri = $url_model->getFestivalinprogresssaveURI($request);
        $location = $url_model->getFestivalinprogresssaveLocation($request);

        try {
            $client = new \Zend\Soap\Client(null,array('soap_version' => SOAP_1_2));
            $client->setUri($uri);
            $client->setLocation($location);
            $result = $client->festivalinprogresssaveService($dates, $music_genre, $currency, $highlights, $payments, $country_id, $city_id, $name, $attendies, $attendies_year, $stages, $heldSince, $website, $email, $host, $manager, $hostWebsite, $statusId, $user_id, $festival_id,$festivalInfo, $logo, $header);
            return new Response($result);
        } catch (SoapFault $s) {
          die('ERROR: [' . $s->faultcode . '] ' . $s->faultstring);
        } catch (Exception $e) {
          die('ERROR: ' . $e->getMessage());
        }
    }
}
