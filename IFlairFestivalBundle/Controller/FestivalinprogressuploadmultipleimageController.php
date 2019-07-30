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
use IFlairFestivalBundle\Entity\FestivalInprogressMultipleImageUpload;

class FestivalinprogressuploadmultipleimageController extends Controller
{
    /**
     * @Route("/soap/v2/festivalinprogressuploadmultipleimage", name="Festival In progress upload multiple image")
     */
    public function festivalinprogressuploadmultipleimageAction(Request $request)
    {
        $files = $_FILES;
        $em = $this->getDoctrine()->getManager();
        $url_model = new Soapurls();
        $uri = $url_model->getFestivalinprogressuploadmultipleimageURI($request);
        $location = $url_model->getFestivalinprogressuploadmultipleimageLocation($request);
        $user_id = $request->request->get('user_id');
        $festival_id = $request->request->get('festival_id');
        $uploadDir = $this->container->getParameter('upload_dir');
        $images_path = array();
        foreach($files as $image)
        {
            $count_image = count($image['name']);
            for($i = 0; $i < $count_image; $i++)
            {
                $extension = explode('.',$image['name'][$i]);
                $images_path[] = $upload_image_path = $uploadDir . time().$i.'.'.$extension[1];;
                if(!move_uploaded_file($image['tmp_name'][$i], $upload_image_path)){
                    die('Error while upload file');
                }
            }
        }
        try {
            $client = new \Zend\Soap\Client(null,array('soap_version' => SOAP_1_2));
            $client->setUri($uri);
            $client->setLocation($location);
            $result = $client->festivalinprogressuploadmultipleimageService($user_id,$festival_id,$images_path);
            return new Response($result);
        } catch (SoapFault $s) {
          die('ERROR: [' . $s->faultcode . '] ' . $s->faultstring);
        } catch (Exception $e) {
          die('ERROR: ' . $e->getMessage());
        }
    }
}
