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
use IFlairFestivalBundle\Entity\ContributionAddRating;
use IFlairFestivalBundle\Entity\festivalFavourite;
use IFlairFestivalBundle\Entity\features;
use IFlairFestivalBundle\Entity\ContributionAddImage;
use IFlairFestivalBundle\Entity\ContributionAddFeature;
use IFlairFestivalBundle\Entity\ContributionAddMusic;
use IFlairFestivalBundle\Entity\ContributionStatus;
use IFlairFestivalBundle\Entity\FestivalInfoEditConfirmation;

class FestivalinfoeditconfirmationController extends Controller
{
    /**
     * @Route("/soap/v2/festivalinfoeditconfirmation", name="Festival Info Edit Confirmation")
     */
    public function festivalinfoeditconfirmationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user_id = $request->request->get('user_id');
        $festival_info = $request->request->get('festival_info');
        $festival_id = $request->request->get('festival_id');
        
        $url_model = new Soapurls();
        $uri = $url_model->getFestivalinfoeditconfirmationURI($request);
        $location = $url_model->getFestivalinfoeditconfirmationLocation($request);
        
        try {
            $client = new \Zend\Soap\Client(null,array('soap_version' => SOAP_1_2));
            $client->setUri($uri);
            $client->setLocation($location);
            $result = $client->festivalinfoeditconfirmation($user_id,$festival_id,$festival_info);
            return new Response($result);
        } catch (SoapFault $s) {
          die('ERROR: [' . $s->faultcode . '] ' . $s->faultstring);
        } catch (Exception $e) {
          die('ERROR: ' . $e->getMessage());
        }
    }
}
