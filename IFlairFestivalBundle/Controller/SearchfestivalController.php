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

class SearchfestivalController extends Controller
{
    /**
     * @Route("/soap/v2/searchfestival", name="search festival listing")
     */
    public function searchfestivalAction(Request $request)
    {
        $url_model = new Soapurls();
        $uri = $url_model->getSearchfestivalURI($request);
        $location = $url_model->getSearchfestivalLocation($request);
        try {
            $client = new \Zend\Soap\Client(null,array('soap_version' => SOAP_1_2));
            $client->setUri($uri);
            $client->setLocation($location);
            $result = $client->setpartysearchfestival();
            return new Response($result);
        } catch (SoapFault $s) {
          die('ERROR: [' . $s->faultcode . '] ' . $s->faultstring);
        } catch (Exception $e) {
          die('ERROR: ' . $e->getMessage());
        }
        return new Response();
    }
}