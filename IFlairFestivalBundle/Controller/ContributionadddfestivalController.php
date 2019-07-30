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
use IFlairFestivalBundle\Entity\FestivalInprogressRatings;
use IFlairFestivalBundle\Entity\Festival_inprogress_status;

class ContributionadddfestivalController extends Controller
{
    /**
     * @Route("/soap/v2/contributionadddfestival", name="Contribution Addd Festival")
     */
    public function contributionadddfestivalAction(Request $request)
    {

        $user_id = $request->request->get('user_id');
        $festival_name = $request->request->get('festival_name');
        $country_id = $request->request->get('country_id');
        $city_id = $request->request->get('city_id');
        $start_date = $request->request->get('start_date');
        $end_date = $request->request->get('end_date');
        $feature_id = $request->request->get('feature_id');
        $rating_id = $request->request->get('rating_id');
        $music_genre = $request->request->get('music_genre');
        
        $url_model = new Soapurls();
        $uri = $url_model->getContributionadddfestivalURI($request);
        $location = $url_model->getContributionadddfestivalLocation($request);

        $files = $_FILES;
        $uploadDir = $this->container->getParameter('upload_dir');
        $upload_image_path = $uploadDir . time().$files['image_path']['name'];
        if(!move_uploaded_file($files['image_path']['tmp_name'], $upload_image_path)){
            die('Error while upload file');
        }
        try {
            $client = new \Zend\Soap\Client(null,array('soap_version' => SOAP_1_2));
            $client->setUri($uri);
            $client->setLocation($location);
            $result = $client->getcontributionadddfestival($user_id,$festival_name,$country_id,$city_id,$start_date,$end_date,$feature_id,$rating_id,$music_genre,$upload_image_path);
            return new Response($result);
        } catch (SoapFault $s) {
          die('ERROR: [' . $s->faultcode . '] ' . $s->faultstring);
        } catch (Exception $e) {
          die('ERROR: ' . $e->getMessage());
        }
    }
}
