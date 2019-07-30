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
use IFlairSoapBundle\Entity\Partyfinderfavorite;
use IFlairSoapBundle\Entity\Partyfinder;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\Expr;
use DoctrineExtensions\Tests\Query\Mysql;

class FestivalinnerviewController extends Controller
{
    /**
     * @Route("/soap/v2/festivalinnerview", name="festival inner view")
     */
    public function festivalinnerview(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $festival_id = $request->request->get('festival_id');
        $user_id = $request->request->get('user_id');

        $url_model = new Soapurls();
        $uri = $url_model->getFestivalinnverviewURI($request);
        $location = $url_model->getFestivalinnverviewLocation($request);
        try {
            $client = new \Zend\Soap\Client(null,array('soap_version' => SOAP_1_2));
            $client->setUri($uri);
            $client->setLocation($location);
            $result = $client->viewinnerfestival($festival_id,$user_id);
            return new Response($result);
        } catch (SoapFault $s) {
          die('ERROR: [' . $s->faultcode . '] ' . $s->faultstring);
        } catch (Exception $e) {
          die('ERROR: ' . $e->getMessage());
        }
        return new Response();
    }
}