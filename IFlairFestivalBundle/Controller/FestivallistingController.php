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

class FestivallistingController extends Controller
{
    /**
     * @Route("/soap/v2/festivallisting", name="festival upcoming listing")
     */
    public function festivallistingAction(Request $request)
    {
        $all = $request->request->get('all');
        $sort_name = $request->request->get('sort_name');
        $music = $request->request->get('music');
        $offset = $request->request->get('offset');
        $limit = $request->request->get('limit');
        $upcoming = $request->request->get('upcoming');
        $country = $request->request->get('country');
        $url_model = new Soapurls();
        $uri = $url_model->getFestivalListingURI($request);
        $location = $url_model->getFestivalListingLocation($request);
        try {
            $client = new \Zend\Soap\Client(null,array('soap_version' => SOAP_1_2));
            $client->setUri($uri);
            $client->setLocation($location);
            $result = $client->setpartyfestivallist($all,$sort_name, $music, $offset, $limit,$upcoming, $country);
            return new Response($result);
        } catch (SoapFault $s) {
          die('ERROR: [' . $s->faultcode . '] ' . $s->faultstring);
        } catch (Exception $e) {
          die('ERROR: ' . $e->getMessage());
        }
        return new Response();
    }
}
