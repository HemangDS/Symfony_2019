<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use iFlair\LetsBonusAdminBundle\Entity\Advertisement;
use iFlair\LetsBonusAdminBundle\Entity\AdvertisementType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdvertisementController extends Controller
{
    public function getHeaderAdvertisementAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();

        $query = $connection->prepare('SELECT * FROM lb_advertisement AS a join lb_advertisement_type as at ON a.adv_type=at.id where at.adv_type_name = :headerType ORDER BY a.created DESC LIMIT 1');
        $query->bindValue('headerType', AdvertisementType::ADTYPEHEADER);
        $query->execute();
        $advertisement = $query->fetchAll();

        $advertisementData = array();

        foreach ($advertisement as $mainKey => $advData) {
            foreach ($advData as $advDatakey => $advDatavalue) {
                if ($advDatakey == 'image_id') {
                    $em = $this->getDoctrine()->getManager();
                    $entities = $em->getRepository('iFlairLetsBonusAdminBundle:Advertisement');
                    $media = $entities->findOneBy(array('image' => $advData[$advDatakey]));
                    $advertisementData[$mainKey]['cashback_adv_url'] = $media->getUrl();
                    $advertisementData[$mainKey]['cashback_adv_name'] = $media->getAdvName();
                    if (!empty($media) && !empty($advData['image_id'])) {
                        $media = $media->getImage();
                        $mediaManager = $this->get('sonata.media.pool');
                        $provider = $mediaManager->getProvider($media->getProviderName());
                        $format = $provider->getFormatName($media, 'big');
                        $productpublicUrl = $provider->generatePublicUrl($media, $format);
                        $advertisementData[$mainKey]['cashback_adv_image_path'] = $productpublicUrl;
                    }
                }
                $advertisementData[$mainKey][$advDatakey] = $advDatavalue;
            }
        }

        return $this->render('iFlairLetsBonusFrontBundle:Advertisement:menuAdvertisement.html.twig', array('advertisementDatas' => $advertisementData));
    }

    public function getContentAdvertisementAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();

        $query = $connection->prepare('SELECT * FROM lb_advertisement AS a join lb_advertisement_type as at ON a.adv_type=at.id where at.adv_type_name = :middleContentType ORDER BY a.created DESC LIMIT 2');
        $query->bindValue('middleContentType', AdvertisementType::ADTYPEMIDDLECONTENT);
        $query->execute();
        $advertisement = $query->fetchAll();

        $advertisementData = array();

        foreach ($advertisement as $mainKey => $advData) {
            foreach ($advData as $advDatakey => $advDatavalue) {
                if ($advDatakey == 'image_id') {
                    $em = $this->getDoctrine()->getManager();
                    $entities = $em->getRepository('iFlairLetsBonusAdminBundle:Advertisement');
                    $media = $entities->findOneBy(array('image' => $advData[$advDatakey]));
                    $advertisementData[$mainKey]['cashback_adv_url'] = $media->getUrl();
                    $advertisementData[$mainKey]['cashback_adv_name'] = $media->getAdvName();
                    if (!empty($media) && !empty($advData['image_id'])) {
                        $media = $media->getImage();
                        $mediaManager = $this->get('sonata.media.pool');
                        $provider = $mediaManager->getProvider($media->getProviderName());
                        $format = $provider->getFormatName($media, 'ad_middle_type');
                        $productpublicUrl = $provider->generatePublicUrl($media, $format);
                        $advertisementData[$mainKey]['cashback_adv_image_path'] = $productpublicUrl;
                    }
                }
                $advertisementData[$mainKey][$advDatakey] = $advDatavalue;
            }
        }

        return $this->render('iFlairLetsBonusFrontBundle:Advertisement:contentAdvertisement.html.twig', array('advertisementContentDatas' => $advertisementData));
    }

    public function getFooterAdvertisementAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();

        $query = $connection->prepare('SELECT * FROM lb_advertisement AS a join lb_advertisement_type as at ON a.adv_type=at.id where at.adv_type_name = :footerType ORDER BY a.created DESC LIMIT 1');
        $query->bindValue('footerType', AdvertisementType::ADTYPEFOOTER);
        $query->execute();
        $advertisement = $query->fetchAll();

        $advertisementData = array();

        foreach ($advertisement as $mainKey => $advData) {
            foreach ($advData as $advDatakey => $advDatavalue) {
                if ($advDatakey == 'image_id') {
                    $em = $this->getDoctrine()->getManager();
                    $entities = $em->getRepository('iFlairLetsBonusAdminBundle:Advertisement');
                    $media = $entities->findOneBy(array('image' => $advData[$advDatakey]));
                    $advertisementData[$mainKey]['cashback_adv_url'] = $media->getUrl();
                    $advertisementData[$mainKey]['cashback_adv_name'] = $media->getAdvName();
                    if (!empty($media) && !empty($advData['image_id'])) {
                        $media = $media->getImage();
                        $mediaManager = $this->get('sonata.media.pool');
                        $provider = $mediaManager->getProvider($media->getProviderName());
                        $format = $provider->getFormatName($media, 'ad_footer_type');
                        $productpublicUrl = $provider->generatePublicUrl($media, $format);
                        $advertisementData[$mainKey]['cashback_adv_image_path'] = $productpublicUrl;
                    }
                }
                $advertisementData[$mainKey][$advDatakey] = $advDatavalue;
            }
        }

        return $this->render('iFlairLetsBonusFrontBundle:Advertisement:footerAdvertisement.html.twig', array('advertisementFooterDatas' => $advertisementData));
    }
}
