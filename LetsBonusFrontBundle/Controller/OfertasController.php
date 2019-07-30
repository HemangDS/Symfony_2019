<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use iFlair\LetsBonusAdminBundle\Entity\Shop;

class OfertasController extends Controller
{

    public function getHighestCashbackRelatedShopDataAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        unset($connection);
        $connection = $em->getConnection();
        $query = $connection->prepare('SELECT vp.*,v.*,sv.*,s.*,css.*,cs.*,sh.*
                                        FROM lb_voucher_programs AS vp
										join lb_voucher as v ON vp.id=v.program_id
										join lb_shop_voucher AS sv ON v.id=sv.voucher_id 
										join lb_shop AS s ON s.id=sv.shop_id
										join lb_cachback_settings_shop AS css ON css.shop_id=s.id
										join lb_cashbackSettings AS cs ON cs.id=css.cashback_settings_id
										join lb_shop_history AS sh ON s.id=sh.shop
										WHERE s.shopStatus = :shopStatus
										GROUP BY s.id ORDER BY sh.cashbackPercentage DESC LIMIT 1');
        $query->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        $query->execute();
        $highestCashbackRelatedShopData = $query->fetchAll();
        $nwData = array();
        if (!empty($highestCashbackRelatedShopData)) {
            foreach ($highestCashbackRelatedShopData as $key => $data) {
                foreach ($data as $subdatakey => $subdatavalue) {
                    if ($subdatakey == 'image_id') {
                        $em = $this->getDoctrine()->getManager();
                        $entities = $em->getRepository('iFlairLetsBonusAdminBundle:Shop');
                        $media = $entities->findOneBy(array('image' => $subdatavalue));

                        if (!empty($media) && !empty($subdatavalue)) {
                            $media = $media->getImage();
                            $mediaManager = $this->get('sonata.media.pool');
                            $provider = $mediaManager->getProvider($media->getProviderName());
                            $productpublicUrl = $provider->generatePublicUrl($media, 'default_big');
                            $nwData[$key]['shop_image_path'] = $productpublicUrl;
                        }
                    } else {
                        $nwData[$key][$subdatakey] = $subdatavalue;
                    }
                }
            }
            if (isset($nwData[0])) {
                $highestCashbackRelatedShopData = $nwData[0];
            }
        }
        /*echo "<pre>";
        print_r($highestCashbackRelatedShopData);
        exit;*/
        return $this->render('iFlairLetsBonusFrontBundle:Ofertas:highestCashbackRelatedShop.html.twig', array('highestCashbackRelatedShopDatas' => $highestCashbackRelatedShopData));
    }

    public function getHighestDiscountRelatedShopDataAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        unset($connection);
        $connection = $em->getConnection();
        $query = $connection->prepare('SELECT vp.*,v.*,sv.*,s.*,css.*,cs.*,sh.*
                                        FROM lb_voucher_programs AS vp
										join lb_voucher as v ON vp.id=v.program_id
										join lb_shop_voucher AS sv ON v.id=sv.voucher_id
										join lb_shop AS s ON s.id=sv.shop_id
										join lb_cachback_settings_shop AS css ON css.shop_id=s.id
										join lb_cashbackSettings AS cs ON cs.id=css.cashback_settings_id
										join lb_shop_history AS sh ON s.id=sh.shop
										WHERE s.shopStatus = :shopStatus
										GROUP BY s.id ORDER BY v.discount_amount DESC LIMIT 1');
        $query->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        $query->execute();
        $highestDiscountRelatedShopData = $query->fetchAll();

        if (!empty($highestDiscountRelatedShopData)) {
            $nwData = array();
            foreach ($highestDiscountRelatedShopData as $key => $data) {
                foreach ($data as $subdatakey => $subdatavalue) {
                    if ($subdatakey == 'image_id') {
                        $em = $this->getDoctrine()->getManager();
                        $entities = $em->getRepository('iFlairLetsBonusAdminBundle:Shop');
                        $media = $entities->findOneBy(array('image' => $subdatavalue));

                        if (!empty($media) && !empty($subdatavalue)) {
                            $media = $media->getImage();
                            $mediaManager = $this->get('sonata.media.pool');
                            $provider = $mediaManager->getProvider($media->getProviderName());
                            $productpublicUrl = $provider->generatePublicUrl($media, 'default_big');
                            $nwData[$key]['shop_image_path'] = $productpublicUrl;
                        }
                    } else {
                        $nwData[$key][$subdatakey] = $subdatavalue;
                    }
                }
            }
            if (isset($nwData[0])) {
                $highestDiscountRelatedShopData = $nwData[0];
            }
        }

        return $this->render('iFlairLetsBonusFrontBundle:Ofertas:highestDiscountRelatedShop.html.twig', array('highestDiscountRelatedShopDatas' => $highestDiscountRelatedShopData));
    }
}
