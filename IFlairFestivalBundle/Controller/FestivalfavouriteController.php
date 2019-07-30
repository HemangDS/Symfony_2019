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
use IFlairFestivalBundle\Entity\festivalFavourite;

class FestivalfavouriteController extends Controller
{
    /**
     * @Route("/soap/v2/festivalsetfavourite", name="set favourite")
     */
    public function setfavouriteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user_id = $request->request->get('user_id');
        $festival_id = $request->request->get('festival_id');
        $status = $request->request->get('status');
        $em = $this->getDoctrine()->getManager();
        
        $url_model = new Soapurls();
        $uri = $url_model->getSFestivalFavouriteURI($request);
        $location = $url_model->getSFestivalFavouriteLocation($request);
        try {
            $client = new \Zend\Soap\Client(null,array('soap_version' => SOAP_1_2));
            $client->setUri($uri);
            $client->setLocation($location);
            $result = $client->setfestivalfavourite($user_id, $festival_id, $status);
            return new Response($result);
        } catch (SoapFault $s) {
          die('ERROR: [' . $s->faultcode . '] ' . $s->faultstring);
        } catch (Exception $e) {
          die('ERROR: ' . $e->getMessage());
        }
    }
}