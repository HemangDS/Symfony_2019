<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use Doctrine\ORM\Query\Expr\Join;
use iFlair\LetsBonusAdminBundle\Entity\Shop;
use iFlair\LetsBonusAdminBundle\Entity\Slider;
use iFlair\LetsBonusAdminBundle\Entity\Slug;
use iFlair\LetsBonusAdminBundle\Slug\Constants;
use iFlair\LetsBonusFrontBundle\Entity\Review;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class HomepageController extends Controller
{
    public function sliderAction()
    {
        $image_url = array();
        $provider = $this->container->get('sonata.media.provider.image');
        $em = $this->getDoctrine()->getEntityManager();
        $sliders = $em->createQueryBuilder()
            ->select('s')
            ->from('iFlairLetsBonusAdminBundle:Slider',  's')
            ->where(':date_from >= s.start_date')
            ->andWhere(':date_from <= s.end_date')
            ->andWhere('s.enabled = 1')
            ->andWhere('s.slider_area LIKE :sliderarea')
            ->andWhere('s.show_in_front = 1')
            ->setParameter('date_from', date('Y-m-d H:i:s'))
            ->setParameter('sliderarea', 'homepage')
            ->orderBy('s.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        foreach ($sliders as $slider) {
            $sliderRecord = array();
            $media = $slider->getImage();
            $format = $provider->getFormatName($media, 'banner');
            $sliderRecord['title'] = $slider->getTitle();
            $sliderRecord['url'] = $slider->getUrl();
            $sliderRecord['image'] = $provider->generatePublicUrl($media, $format);
            $image_url[] = $sliderRecord;
        }

        $review_result = $this->getDoctrine()
            ->getRepository('iFlairLetsBonusFrontBundle:Review')
            ->findBy([], ['created' => 'DESC']);

        return $this->render('iFlairLetsBonusFrontBundle:Homepage:index.html.twig', array(
            'slider_image' => $image_url,
            'review' => $review_result,
        ));
    }

    public function productcollectionAction()
    {
        $sm = $this->getDoctrine()->getEntityManager();
        $connection = $sm->getConnection();
        $prodctcollectionshop = $sm->getRepository('iFlairLetsBonusAdminBundle:Shop')->findBy(
            [
                'shopStatus' => Shop::SHOP_ACTIVATED,
                'highlightedHome' => Shop::SHOP_HIGHLIGHTED_HOME,
            ]
        );
        $productcollectiondata = [];
        $count = 1;
        $shopHistoryRepository = $sm->getRepository('iFlairLetsBonusAdminBundle:shopHistory');
        $slugRepository = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug');
        foreach ($prodctcollectionshop as $pdata) {
            if ($pdata->getOffers() === 'product') {
                if ($pdata->getBrand()) {
                    $productcollectiondata[$count]['brand'] = $pdata->getBrand();
                    $statement = $connection->prepare('SELECT vp.* FROM lb_voucher_programs AS vp JOIN lb_shop AS s ON vp.id = s.vprogram_id WHERE s.id = :sid');
                    $statement->bindValue('sid', trim($pdata->getId()));
                    $statement->execute();
                    $shop_data = $statement->fetchAll();

                    if ($shop_data) {
                        foreach ($shop_data as $key => $value) {
                            if ($value['id'] !== null) {
                                $productcollectiondata[$count]['productlogo'] = $value['logo_path'];
                                $productcollectiondata[$count]['brandid'] = $value['id'];
                            }
                        }
                    }
                }
                if ($pdata->getImage()) {
                    $media = $pdata->getImage();
                    $mediaManager = $this->get('sonata.media.pool');
                    $provider = $mediaManager->getProvider($media->getProviderName());
                    $productpublicUrl = $provider->generatePublicUrl($media, 'reference');
                    $productcollectiondata[$count]['productimage'] = $productpublicUrl;
                }
                $product_history = $shopHistoryRepository->findOneBy(array('shop' => trim($pdata->getId())));
                $productcollectiondata[$count]['shop'] = trim($pdata->getId());
                if (!empty($product_history)) {
                    $productcollectiondata[$count]['shop_history'] = $product_history->getId();
                    $slug = $slugRepository->findOneBy(
                        ['categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $product_history->getId()]
                    );
                    if (null !== $slug) {
                        $productcollectiondata[$count]['slug_name'] = $slug->getSlugName();
                    }

                    if ($product_history->getTitle()) {
                        $productcollectiondata[$count]['title'] = $product_history->getTitle();
                        $productcollectiondata[$count]['rating'] = $this->ratingAction(trim($pdata->getId()), $product_history->getId());
                    }

                    if ($product_history->getCashbackPrice()) {
                        $percentage = $product_history->getCashbackPercentage(); // percentage
                        $main_price = $product_history->getCashbackPrice();
                        $UpdatedcashbackPrice = ($main_price - ($main_price * $percentage / 100));
                        $productcollectiondata[$count]['cashbackprice'] = $UpdatedcashbackPrice;
                    }

                    if ($product_history->getCashbackPrice()) {
                        $productcollectiondata[$count]['mainprice'] = $product_history->getCashbackPrice();
                    }

                    if ($product_history->getCashbackPercentage()) {
                        $productcollectiondata[$count]['cashbackpercentage'] = $product_history->getCashbackPercentage();
                    }
                }
                ++$count;
            }
        }
        if (count($productcollectiondata) > 0) {
            return $this->render('iFlairLetsBonusFrontBundle:Homepage:productcollection.html.twig', array(
                'productcollectiondata' => $productcollectiondata,
                'addtofevlist' => $this->addtofevlistAction(),
            ));
        } else {
            return new Response();
        }
    }

    public function collectionAction(Request $request)
    {
        $sm = $this->getDoctrine()->getEntityManager();
        $connection = $sm->getConnection();
        $statement = $connection->prepare('SELECT id,name FROM lb_collection AS clctn JOIN lb_shop_collection AS sc ON clctn.id = sc.collection_id WHERE clctn.status = 1 AND clctn.show_in_front = 1 GROUP BY sc.collection_id HAVING count(sc.collection_id) > 0 ORDER BY name ASC, modified DESC LIMIT 0,1');
        $statement->execute();
        $collection = $statement->fetch();
        $collectionId = $collection['id'];
        $collectionName = $collection['name'];

        $statement = $connection->prepare(
            'SELECT *, sh.id AS id, sh.title AS title, 
            shop AS shop_id,tgs.name AS tag_name, v.isnew, v.exclusive 
            FROM lb_shop_history AS sh
            JOIN lb_shop_collection AS sc ON sc.shop_id = sh.shop
            JOIN lb_shop AS s ON s.id = sh.shop
            LEFT JOIN lb_tags AS tgs ON sh.tag = tgs.id
            LEFT JOIN lb_shop_voucher AS sv ON sv.shop_id = s.id
            LEFT JOIN lb_voucher AS v ON sv.voucher_id = v.id
            JOIN lb_slug AS sl ON sl.categoryId = sh.id AND sl.categoryType = :slugType
            WHERE sc.collection_id = :collectionId AND s.shopStatus = :shopStatus 
            AND s.highlightedHome = :highlightedHome GROUP BY sh.shop ORDER BY sh.title'
        );
        $statement->bindValue('collectionId', $collectionId);
        $statement->bindValue('slugType', Constants::SHOP_IDENTIFIER);
        $statement->bindValue('shopStatus',  Shop::SHOP_ACTIVATED);
        $statement->bindValue('highlightedHome', Shop::SHOP_HIGHLIGHTED_HOME);
        $statement->execute();
        $shop_data = $statement->fetchAll();
        $i = 1;
        $prod_arr = [];
        $brandController = new BrandController();
        $brandController->setContainer($this->container);
        $slugRepository = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug');
        $shopRepository = $sm->getRepository('iFlairLetsBonusAdminBundle:Shop');
        $shopHistoryRepository = $sm->getRepository('iFlairLetsBonusAdminBundle:shopHistory');
        foreach ($shop_data as $key => $value) {
            $prod_arr[$i]['shop_label']= "";
            if($value['offers'] === 'cashback') {
                $prod_arr[$i]['shop_label'] =  $value['tag_name'];
            } elseif($value['offers'] === 'voucher') {
                if($value['exclusive'] && $value['isnew']) {
                    $prod_arr[$i]['shop_label'] = ' *Novedad exclusiva';
                } elseif($value['exclusive'] && !$value['isnew']) {
                    $prod_arr[$i]['shop_label'] = '*exclusivo';
                } elseif(!$value['exclusive'] && $value['isnew']) {
                    $prod_arr[$i]['shop_label'] = '*nuevo';
                }
            }
            
            $prod_arr[$i]['shop_history_id'] = $value['id'];
            $prod_arr[$i]['name'] = $value['title'];
            $prod_arr[$i]['shop_id'] = $value['shop_id'];
            $prod_arr[$i]['shop_affiliate_url'] = $value['urlAffiliate'];
            $prod_arr[$i]['shop_offers'] = $value['offers'];
            $prod_arr[$i]['title'] = $value['title'];
            $prod_arr[$i]['cashback_price'] = (!empty($value['cashbackPrice']) && $value['cashbackPrice'] > 0) ? $value['cashbackPrice'].'€' : $value['cashbackPercentage'].'%';
            $prod_arr[$i]['letsbonus_percentage'] = $value['letsBonusPercentage'];
            $prod_arr[$i]['introduction'] = strip_tags($value['introduction']);
            $prod_arr[$i]['tearms'] = strip_tags($value['tearms']);
            $prod_arr[$i]['rating'] = $this->ratingAction($value['shop_id'], $value['id']);
            $slug = $slugRepository->findOneBy(
                ['categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $value['id']]
            );
            if ($slug) {
                $prod_arr[$i]['slug_name'] = $slug->getSlugName();
            }
            $pdata = $shopRepository->findOneBy(
                [
                    'id' => $value['shop_id'],
                    'shopStatus' => Shop::SHOP_ACTIVATED,
                    'highlightedHome' => Shop::SHOP_HIGHLIGHTED_HOME,
                ]
            );
            $shop_history = $shopHistoryRepository->find($value['id']);

            if (null !== $shop_history) {
                if ($productHistoryId = $shop_history->getId()) {
                    $prod_arr[$i]['shop_history_id'] = $productHistoryId;
                    $prod_arr[$i]['rating'] = $this->ratingAction($value['shop_id'], $productHistoryId);
                }
                //Retrieve variations
                $variationStatement = $connection->prepare(
                    'SELECT v.number, v.title, v.date 
                    FROM lb_variation AS v LEFT JOIN lb_shop_history AS sh ON sh.id = v.shop_history_id 
                    WHERE v.shop_history_id = :shop_history_id ORDER BY v.number DESC'
                );
                $variationStatement->bindValue('shop_history_id', $productHistoryId);
                $variationStatement->execute();
                $variationData = $variationStatement->fetchAll();
                $retrievedVariation = array();
                if ($variationData) {
                    $j = 0;
                    foreach ($variationData as $variation) {
                        $retrievedVariation[$j]['number'] = $variation['number'];
                        $retrievedVariation[$j]['title'] = $variation['title'];
                        $retrievedVariation[$j]['date'] = $variation['date'];
                        ++$j;
                    }
                }
                if (count($retrievedVariation) > 0) {
                    $prod_arr[$i]['variations'] = $retrievedVariation;
                }
            }

            $affiliationArgs = new DefaultController();
            $affiliationArgs->setContainer($this->container);
            $affiliationUrlArgs = $affiliationArgs->getAffiliation($pdata, $shop_history, $sm);

            if (!empty($shop_history->getUrlAffiliate())) {
                $prod_arr[$i]['shop_affiliate_url'] = $shop_history->getUrlAffiliate().$affiliationUrlArgs;
            }

            if (null !== $pdata && $pdata->getTabImage()) {
                $media = $pdata->getTabImage();
                $mediaManager = $this->get('sonata.media.pool');
                $provider = $mediaManager->getProvider($media->getProviderName());
                $format = $provider->getFormatName($media, 'shop');
                $productpublicUrl = $provider->generatePublicUrl($media, $format);
                $prod_arr[$i]['image'] = $productpublicUrl;
            }

            // brand_logo
            $shopBrand = $sm->createQueryBuilder()
                ->select('vp')
                ->from('iFlairLetsBonusAdminBundle:VoucherPrograms',  'vp')
                ->join('iFlairLetsBonusAdminBundle:Shop', 's', Join::WITH, 'vp.id = s.vprogram')
                ->where('s.id = :sid')
                ->setParameter('sid', trim($value['shop_id']))
                ->setFirstResult(0)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
            $prod_arr[$i]['brand_logo'] = '';
            $prod_arr[$i]['brand_name'] = '';
            if ($shopBrand) {
                $prod_arr[$i]['brand_name'] = $shopBrand->getProgramName();
                $uploadedLogo = $shopBrand->getImage();
                $popUpLogo = $shopBrand->getPopUpImage();
                $networkProvidedLogo = $shopBrand->getLogoPath();
                if (!empty($uploadedLogo)) {
                    $mediaManager = $this->get('sonata.media.pool');
                    $provider = $mediaManager->getProvider($uploadedLogo->getProviderName());
                    $format = $provider->getFormatName($uploadedLogo, 'brand_on_shop');
                    $prod_arr[$i]['brand_logo'] = $provider->generatePublicUrl($uploadedLogo, $format);
                } elseif (!empty($networkProvidedLogo)) {
                    $prod_arr[$i]['brand_logo'] = $networkProvidedLogo;
                }
                if (!empty($popUpLogo)) {
                    $mediaManager = $this->get('sonata.media.pool');
                    $provider = $mediaManager->getProvider($popUpLogo->getProviderName());
                    $format = $provider->getFormatName($popUpLogo, 'cashback_voucher_popup');
                    $prod_arr[$i]['brand_logo_popup'] = $provider->generatePublicUrl($popUpLogo, $format);
                } elseif (!empty($networkProvidedLogo)) {
                    $prod_arr[$i]['brand_logo_popup'] = $networkProvidedLogo;
                }
                $prod_arr[$i]['brand_id'] = $shopBrand->getId();
                
            }

            // voucher_id
            $statement = $connection->prepare('SELECT b.code FROM lb_voucher b JOIN lb_shop_voucher s ON b.id = s.voucher_id WHERE s.shop_id = :shopid');
            $statement->bindValue('shopid', $value['shop_id']);
            $statement->execute();
            $shop_data_voucher = $statement->fetchAll();

            if ($shop_data_voucher) {
                $prod_arr[$i]['voucher_code'] = $shop_data_voucher;
                $prod_arr[$i]['voucher_code_count'] = count($shop_data_voucher);
            } else {
                $prod_arr[$i]['voucher_code'] = '';
                $prod_arr[$i]['voucher_code_count'] = 0;
            }
            $voucherFinal = $brandController->getVoucherByShopId($pdata->getId(), $pdata->getVprogram(), $connection);
            if (count($voucherFinal) > 0) {
                $prod_arr[$i]['voucher_id'] = $voucherFinal[0]['voucher_id'];
                $prod_arr[$i]['voucher_code'] = $voucherFinal[0]['voucher_code'];
                $prod_arr[$i]['voucher_name'] = $voucherFinal[0]['voucher_name'];
                $dat = '';

                if (strtotime($voucherFinal[0]['voucher_expire_date']) > strtotime('-30 days')) {
                    $date = strtotime($voucherFinal[0]['voucher_expire_date']);
                    $dat = date('d/m/y', $date);
                }

                $prod_arr[$i]['voucher_expire_date'] = $dat;
                $prod_arr[$i]['discount_amount'] = $voucherFinal[0]['discount_amount'];
                $prod_arr[$i]['is_percentage'] = $voucherFinal[0]['is_percentage'];
                $prod_arr[$i]['exclusive'] = $voucherFinal[0]['exclusive'];
                $prod_arr[$i]['short_description'] = $voucherFinal[0]['short_description'];
                $prod_arr[$i]['default_track_uri'] = $voucherFinal[0]['default_track_uri'];
                $prod_arr[$i]['description'] = $voucherFinal[0]['description'];
                $prod_arr[$i]['voucher_program_name'] = $pdata->getVprogram()->getProgramName();
            }
            ++$i;
        }
        /* view more button */
        $collection_slug_name = '';
        $connection = $sm->getConnection();
        $statement = $connection->prepare(
            'SELECT id,name FROM lb_collection AS clctn JOIN lb_shop_collection AS sc ON clctn.id = sc.collection_id
            WHERE clctn.status = 1 AND clctn.mark_special = 1 
            GROUP BY sc.collection_id HAVING count(sc.collection_id) > 0 
            ORDER BY name ASC, modified DESC LIMIT 0,1'
        );
        $statement->execute();
        $collection = $statement->fetch();
        $collectionId = $collection['id'];
        $collectionName = $collection['name'];

        if (!empty($collectionId)) {
            $slug = $slugRepository->findOneBy(
                [
                    'categoryType' => Constants::COLLECTION_IDENTIFIER,
                    'categoryId' => $collectionId,
                ]
            );
            if ($slug) {
                $collection_slug_name = $slug->getSlugName();
            }
        }
        /* view more button */
        if (count($prod_arr) > 0) {
            return $this->render(
                'iFlairLetsBonusFrontBundle:Homepage:collection.html.twig',
                [
                    'collection' => $prod_arr,
                    'collectionname' => $collectionName,
                    'collectionSlugName' => $collection_slug_name,
                ]
            );
        } else {
            return new Response();
        }
    }

    /**
     * @TODO be sure I can delete, changed per Ofertasespeciales:getHomeDailyOffer
     * @param Request $request
     *
     * @return Response
     */
    public function homepagedoublecashbackAction(Request $request)
    {
        $provider = $this->container->get('sonata.media.provider.image');

        /* ONLY Oferta del día SELECTED DATA :: START */
        $offer_em = $this->getDoctrine()->getEntityManager();
        $offer_shop_entity = $offer_em->getRepository('iFlairLetsBonusAdminBundle:Shop');
        $offer_shop_collection = $offer_shop_entity->findBy(array(
            'shopStatus' => Shop::SHOP_ACTIVATED,
            // 'highlightedHome' => Shop::SHOP_HIGHLIGHTED_HOME,
            'highlightedOffer' => Shop::OFFER_ACTIVATED,
        ));
        $offer_voucher_entity = $offer_em->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms');
        $offer_shop_data = array();
        $cnt = 0;

        if (!empty($offer_shop_collection)):
            foreach ($offer_shop_collection as $offer_shop) {
                $shop_id = $offer_shop->getId();
                /* BRAND LOGO & BRAND URL */
                $offer_shop_data[$cnt]['shop_id'] = $shop_id;
                $shop_history = $offer_em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('shop' => $shop_id), array('startDate'=>'DESC'), 1);
                $voucher_offer_collection = $offer_voucher_entity->findBy(array('id' => $offer_shop->getVprogram()->getId())); // Get Logo from Voucher Program
                if ($voucher_offer_collection) {
                    foreach ($voucher_offer_collection as $offer_brand) {
                        $uploadedLogo = $offer_brand->getImage(); // Uploaded Brand Logo
                        $networkProvidedLogo = $offer_brand->getLogoPath(); // IF Uploaded Image logo not available then image url wil be used
                        if (!empty($uploadedLogo)) {
                            $format = $provider->getFormatName($uploadedLogo, 'brand_on_shop');
                            $offer_shop_data[$cnt]['brand_logo'] = $provider->generatePublicUrl($uploadedLogo, $format);
                        } elseif (!empty($networkProvidedLogo)) {
                            $offer_shop_data[$cnt]['brand_logo'] = $networkProvidedLogo;
                        } else {
                            $offer_shop_data[$cnt]['brand_logo'] = '';
                        }
                        $offer_shop_data[$cnt]['brand_id'] = $offer_brand->getId();
                        if (!empty($offer_brand->getId())) {                            
                            $slug1 = $offer_em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $shop_history->getId()));
                            if ($slug1) {
                                $offer_shop_data[$cnt]['slug_name'] = $slug1->getSlugName();
                            }
                        }
                    }
                }
                /* SHOP IMAGE */
                $offer_media = $offer_shop->getImage();
                if ($offer_media) {
                    $format = $provider->getFormatName($offer_media, 'offertas_del_dia_type');
                    $image_url = $provider->generatePublicUrl($offer_media, $format);
                    $offer_shop_data[$cnt]['shop_image'] = $image_url;
                }

                /* SHOP END DATE & CASHBACK SETTINGS ID AND OFFER LIKE DOUBLE OR TRIPPLE */
                if($offer_shop->getOffers() == "cashback" || $offer_shop->getOffers() == "cashback/coupons")
                {
                    $shop_history = $offer_em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findBy(array('shop' => $offer_shop->getId()), array('created'=>'DESC'), 1);
                    foreach ($shop_history as $shop_history_key => $shop_history_value) 
                    {
                        if($shop_history_value->getTag())
                        {
                            $offer_shop_data[$cnt]['type'] = $shop_history_value->getTag()->getName();
                        }
                        else
                        {
                            $offer_shop_data[$cnt]['type'] = 'Cashback';
                        }
                    }
                    $offer_shop_data[$cnt]['offer_type'] = 'cashback';
                }
                elseif ($offer_shop->getOffers() == "voucher" || $offer_shop->getOffers() == "cashback/coupons") {
                    $connection = $offer_em->getConnection();
                    $brandController = new BrandController();
                    $brandController->setContainer($this->container);
                    $voucherData = $brandController->getVoucherByShopId($offer_shop->getId(),$offer_shop->getVprogram(),$connection);
                  
                    if($voucherData)
                    {   $new = "";
                        if($voucherData[0]["exclusive"] == 1)
                        {
                            $string = "Exclusive";
                        }
                        else if($voucherData[0]["exclusive"] == 0)
                        {
                             $string = "Non Exclusive";
                        }

                        if($voucherData[0]["isnew"] == 1)
                        {
                            $new = "New";
                        }
                        $offer_shop_data[$cnt]['type'] =  $string." ".$new;
                    }
                    else
                    {
                        $offer_shop_data[$cnt]['type'] = 'Voucher';
                    }
                     $offer_shop_data[$cnt]['offer_type'] = 'voucher';
                }
                elseif ($offer_shop->getOffers() == "offer") {
                    $offer_shop_data[$cnt]['type'] = 'Offer';
                    $offer_shop_data[$cnt]['offer_type'] = 'offer';
                }
                    $offer_shop_data[$cnt]['enddate'] = '0000-00-00 00:00:00';
                   
                
               /* $offer_shop_connection = $offer_em->getConnection();
                $offer_statement = $offer_shop_connection->prepare('SELECT * FROM lb_cachback_settings_shop where `shop_id` ='.$offer_shop->getId().' LIMIT 1');
                $offer_statement->execute();
                $selected_shops = $offer_statement->fetchAll();
                if (!empty($selected_shops)) {
                    $cashbacksetting_id = '';
                    foreach ($selected_shops as $cback) {
                        $cashbacksetting_id = $cback['cashback_settings_id'];
                    }
                    $cashback_entities = $offer_em->getRepository('iFlairLetsBonusAdminBundle:cashbackSettings');
                    $cashback_collection = $cashback_entities->findBy(array('id' => trim($cashbacksetting_id)));
                    foreach ($cashback_collection as $cb_collection) {
                        $cbkstatus = $cb_collection->getStatus();
                        if ($cbkstatus == 1) {
                            $offer_shop_data[$cnt]['type'] = $cb_collection->getType().' Cashback';
                            if ($cb_collection->getEndDate()) {
                                $offer_shop_data[$cnt]['enddate'] = $cb_collection->getEndDate()->format('Y-m-d H:i:s');
                            } else {
                                $offer_shop_data[$cnt]['enddate'] = '0000-00-00 00:00:00';
                            }
                        }
                    }
                } else {
                    $offer_shop_data[$cnt]['type'] = 'Cashback';
                    if (empty($offer_shop->getEndDate()->format('Y-m-d H:i:s'))):
                        $offer_shop_data[$cnt]['enddate'] = '0000-00-00 00:00:00'; else:
                        $offer_shop_data[$cnt]['enddate'] = $offer_shop->getEndDate()->format('Y-m-d H:i:s');
                    endif;
                }*/
                /* SHOP HISTORY DATA DESCRIPTION AND PERCENTAGE */
                $offer_shophistory_entities = $offer_em->getRepository('iFlairLetsBonusAdminBundle:ShopHistory');
                $shophistory_collection = $offer_shophistory_entities->findBy(array('shop' => $shop_id));
                foreach ($shophistory_collection as $sphstry_collection) {
                    if (strlen($sphstry_collection->getTitle()) <= 50) {
                        $offer_shop_data[$cnt]['description'] = $sphstry_collection->getTitle();
                    } else {
                        $shop_desc = substr(strip_tags($sphstry_collection->getTitle()), 0, 50);
                        $offer_shop_data[$cnt]['description'] = $shop_desc.'...';
                    }
                    if ($sphstry_collection->getCashbackPrice() > 0):
                        $offer_shop_data[$cnt]['cashback_euro'] = $sphstry_collection->getCashbackPrice().'€'; // Cashback Price
                    else:
                        if (!empty($sphstry_collection->getCashbackPercentage())):
                            $offer_shop_data[$cnt]['cashback_percentage'] = $sphstry_collection->getCashbackPercentage(); // Cashback Percentage
                        endif;
                    endif;
                }
                ++$cnt;
            }
        endif;
        /* ONLY Oferta del día SELECTED DATA :: END */

        /* SHOP DATA */
        $shop_em = $this->getDoctrine()->getEntityManager();
        $shop_connection = $shop_em->getConnection();
        $statement = $shop_connection->prepare('SELECT * FROM lb_cachback_settings_shop');
        $statement->execute();
        $selected_shops = $statement->fetchAll();

        $count = 1;
        $all_required_data = array();
        $shop_entities = $shop_em->getRepository('iFlairLetsBonusAdminBundle:Shop');
        $voucher_p_entities = $shop_em->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms');

        foreach ($selected_shops as $selected_product) {
            $shop_id = $selected_product['shop_id'];
            $shop_datecheck = $shop_entities->findOneBy(array('id' => $shop_id));
            if (date('Y-m-d h:i:s') <= $shop_datecheck->getEndDate()->format('Y-m-d H:i:s')):
                $shop_collection = $shop_entities->findBy(array(
                    'id' => $selected_product['shop_id'],
                    'shopStatus' => Shop::SHOP_ACTIVATED,
                    'highlightedHome' => Shop::SHOP_HIGHLIGHTED_HOME,
                ));

            if (!empty($shop_collection)):
                $all_required_data[$count]['shop_id'] = $selected_product['shop_id'];
                $shop_history = $offer_em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('shop' => $selected_product['shop_id']), array('startDate'=>'DESC'), 1);
            foreach ($shop_collection as $sp_collection) {
                $voucher_p_collection = $voucher_p_entities->findBy(array('id' => $sp_collection->getVprogram())); // Get Logo from Voucher Program
                        if ($voucher_p_collection) {
                            foreach ($voucher_p_collection as $brand) {
                                $uploadedLogo = $brand->getImage(); // Uploaded Brand Logo
                                $networkProvidedLogo = $brand->getLogoPath(); // IF Uploaded Image logo not available then image url wil be used
                                if (!empty($uploadedLogo)) {
                                    $format = $provider->getFormatName($uploadedLogo, 'brand_on_shop');
                                    $all_required_data[$count]['brand_logo'] = $provider->generatePublicUrl($uploadedLogo, $format);
                                } elseif (!empty($networkProvidedLogo)) {
                                    $all_required_data[$count]['brand_logo'] = $networkProvidedLogo;
                                } else {
                                    $all_required_data[$count]['brand_logo'] = '';
                                }
                                $all_required_data[$count]['brand_id'] = $brand->getId();
                                if (!empty($brand->getId())) {
                                    $em = $this->getDoctrine()->getEntityManager();
                                    $slug1 = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                                        array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $shop_history->getId()));
                                    if ($slug1) {
                                        $all_required_data[$count]['slug_name'] = $slug1->getSlugName();
                                    }
                                }
                            }
                        }
                $shopStatus = $sp_collection->getShopStatus();
                if ($shopStatus == 1) {
                    $media = $sp_collection->getImage();
                    if ($media) {
                        $format = $provider->getFormatName($media, 'offertas_del_dia_type');
                        $image_url = $provider->generatePublicUrl($media, $format);
                        $all_required_data[$count]['shop_image'] = $image_url;
                    }
                }
            }
                    // Get Date from ShopHistory through Shop id
            $shophistory_entities = $shop_em->getRepository('iFlairLetsBonusAdminBundle:ShopHistory');
            $shophistory_collection = $shophistory_entities->findBy(array('shop' => $selected_product['shop_id']));
            foreach ($shophistory_collection as $sphstry_collection) {

                if($sphstry_collection->getTag())
                {
                    $all_required_data[$count]['type'] = $sphstry_collection->getTag()->getName();
                }
                else
                {
                    $all_required_data[$count]['type'] = 'Cashback';
                }
                $all_required_data[$count]['offer_type'] = 'cashback';       
                $all_required_data[$count]['enddate'] = '0000-00-00 00:00:00';
                if (strlen($sphstry_collection->getTitle()) <= 50) {
                    $all_required_data[$count]['description'] = $sphstry_collection->getTitle();
                } else {
                    $shop_desc = substr(strip_tags($sphstry_collection->getTitle()), 0, 50);
                    $all_required_data[$count]['description'] = $shop_desc.'...';// SHOP Description
                }
                if ($sphstry_collection->getCashbackPrice() > 0):
                            $all_required_data[$count]['cashback_euro'] = $sphstry_collection->getCashbackPrice().'€'; // Cashback Price
                        else:
                            if (!empty($sphstry_collection->getCashbackPercentage())):
                                $all_required_data[$count]['cashback_percentage'] = $sphstry_collection->getCashbackPercentage(); // Cashback Percentage
                            endif;
                endif;
            }

            //  Cashback Settings ID ::
          /*  $cash_em = $this->getDoctrine()->getManager();
            $cashback_entities = $cash_em->getRepository('iFlairLetsBonusAdminBundle:cashbackSettings');
            $cashback_collection = $cashback_entities->findBy(array('id' => $selected_product['cashback_settings_id']));
            foreach ($cashback_collection as $cb_collection) {
                $cbkstatus = $cb_collection->getStatus();
                if ($cbkstatus == 1) {
                    $all_required_data[$count]['type'] = $cb_collection->getType().' Cashback';
                    if ($cb_collection->getEndDate()) {
                        $all_required_data[$count]['enddate'] = $cb_collection->getEndDate()->format('Y-m-d H:i:s');
                    } else {
                        $all_required_data[$count]['enddate'] = '0000-00-00 00:00:00';
                    }
                }
            }*/
              
            ++$count;
            endif;
            endif;
        }

        /* VOUCHER DATA */
        $voucherstatement = $shop_connection->prepare('SELECT * FROM lb_shop_voucher');
        $voucherstatement->execute();
        $selected_vouchers = $voucherstatement->fetchAll();

        $countt = 1;
        $all_required_voucherdata = array();
        foreach ($selected_vouchers as $sel_vouchers) {
            $shop_history = $offer_em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('shop' => $sel_vouchers['shop_id']), array('startDate'=>'DESC'), 1);
            $vouchershop_id = trim($sel_vouchers['shop_id']);

            $vouchershop_datecheck = $shop_entities->findOneBy(array('id' => $vouchershop_id));
            if (date('Y-m-d h:i:s') <= $vouchershop_datecheck->getEndDate()->format('Y-m-d H:i:s')):
                $voucher_shop_em = $this->getDoctrine()->getManager();
            $VouchershopRepository = $voucher_shop_em->getRepository('iFlairLetsBonusAdminBundle:Shop');
            $vouchershop_collection = $VouchershopRepository->findBy(array(
                    'id' => $vouchershop_id,
                    'shopStatus' => Shop::SHOP_ACTIVATED,
                    'highlightedHome' => Shop::SHOP_HIGHLIGHTED_HOME,
                ));
            $all_required_voucherdata[$countt]['shop_id'] = $vouchershop_id;
            if (!empty($vouchershop_collection)):
                    foreach ($vouchershop_collection as $voucher_shop_collection) {
                        $shopStatus = $voucher_shop_collection->getShopStatus();
                        if ($voucher_shop_collection->getShopStatus() == Shop::SHOP_ACTIVATED) {
                            $media = $voucher_shop_collection->getImage();
                            if ($media) {
                                $format = $provider->getFormatName($media, 'offertas_del_dia_type');
                                $image_url = $provider->generatePublicUrl($media, $format);
                                $all_required_voucherdata[$countt]['shop_image'] = $image_url;
                            }
                        }
                    }
            $image_url = '';
            if (!empty($vouchershop_id)) {
                $shophistory_entities = $shop_em->getRepository('iFlairLetsBonusAdminBundle:ShopHistory');
                $voucher_shophistory_collection = $shophistory_entities->findBy(array('shop' => $vouchershop_id));
                foreach ($voucher_shophistory_collection as $voucher_hstry_collection) {
                    if (strlen($voucher_hstry_collection->getTitle()) <= 50) {
                        $all_required_voucherdata[$countt]['description'] = $voucher_hstry_collection->getTitle();
                    } else {
                        $voucher_desc = substr(strip_tags($voucher_hstry_collection->getTitle()), 0, 50);
                        $all_required_voucherdata[$countt]['description'] = $voucher_desc.'...';
                    }
                }
            }
            $voucher_id = trim($sel_vouchers['voucher_id']);

            $voucher_entities = $shop_em->getRepository('iFlairLetsBonusAdminBundle:Voucher');
            $voucher_collection = $voucher_entities->findBy(array('id' => $voucher_id));

            foreach ($voucher_collection as $voucher_coll) {
                /* VOUCHER PROGRAM LOGO */
                        $programId = $voucher_coll->getProgram()->getId();
                $voucherShopBrand = $shop_em->createQueryBuilder()
                            ->select('vp')
                            ->from('iFlairLetsBonusAdminBundle:VoucherPrograms',  'vp')
                            ->where('vp.id = :vpid')
                            ->setParameter('vpid', $programId)
                            ->getQuery()
                            ->getOneOrNullResult();

                $voucher_logo_path = '';
                if ($voucherShopBrand) {
                    $uploadedLogo = $voucherShopBrand->getImage(); // Voucher Image AS  LOGO :: BRAND LOGO
                            $networkProvidedLogo = $voucherShopBrand->getLogoPath();
                    if (!empty($uploadedLogo)) {
                        $format = $provider->getFormatName($uploadedLogo, 'brand_on_shop');
                        $voucher_logo_path = $provider->generatePublicUrl($uploadedLogo, $format);
                    } elseif (!empty($networkProvidedLogo)) {
                        $voucher_logo_path = $networkProvidedLogo;
                    } else {
                        $voucher_logo_path = '';
                    }
                    $all_required_voucherdata[$countt]['brand_logo'] = $voucher_logo_path;
                    $voucher_logo_path = '';
                    $all_required_voucherdata[$countt]['brand_id'] = $voucherShopBrand->getId();
                    if (!empty($voucherShopBrand->getId())) {
                        $em = $this->getDoctrine()->getEntityManager();
                        $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                                    array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $shop_history->getId()));
                        if ($slug) {
                            $all_required_voucherdata[$countt]['slug_name'] = $slug->getSlugName();
                        }
                    }
                }

                $all_required_voucherdata[$countt]['enddate'] = $voucher_coll->getPublishEndDate()->format('Y-m-d H:i:s');
                if ($voucher_coll->getIsPercentage() == 1) {
                    $all_required_voucherdata[$countt]['cashback_percentage'] = $voucher_coll->getDiscountAmount();
                } else {
                    $all_required_voucherdata[$countt]['cashback_euro'] = $voucher_coll->getDiscountAmount().'€';
                }

                if ($voucher_coll->getExclusive() == 1) {
                    $all_required_voucherdata[$countt]['type'] = 'Exclusive';
                } else {
                    $all_required_voucherdata[$countt]['type'] = 'No Exclusive';
                }
                $all_required_voucherdata[$countt]['offer_type'] = 'voucher';  
            }
            $cashbacksettings_statement = $shop_connection->prepare("SELECT `cashback_settings_id` FROM lb_cachback_settings_shop WHERE `shop_id` = '".trim($vouchershop_id)."'");
            $cashbacksettings_statement->execute();
            $selected_cashback_settings = $cashbacksettings_statement->fetchAll();

            $selected_cashback_settings_id = '';
            foreach ($selected_cashback_settings as $cashback_setting) {
                $selected_cashback_settings_id = $cashback_setting['cashback_settings_id'];
            }
            ++$countt;
            endif;
            endif;
        }
      
        /* SHOW DEFAULT CASHBACK AND VOUCHER DATA */
        if (empty($offer_shop_data)):
        
            $Offer_Product_Collection_all = array_merge($all_required_data, $all_required_voucherdata);
        $Offer_Product_Collection = array();
        foreach ($Offer_Product_Collection_all as $offer_data) {
            if (array_key_exists('cashback_euro', $offer_data)) {
                if ($offer_data['cashback_euro'] > 0) {
                    $Offer_Product_Collection[] = $offer_data;
                }
            }
            if (array_key_exists('cashback_percentage', $offer_data)) {
                if ($offer_data['cashback_percentage'] > 0) {
                    $Offer_Product_Collection[] = $offer_data;
                }
            }
        }
        $offer_array = array();
        if (count($Offer_Product_Collection) > 0) {
            $offer_key = array_rand($Offer_Product_Collection);
            $offer_array = $Offer_Product_Collection[ $offer_key ];

                // $offer_array['enddate']
                if (!empty($offer_array['enddate'])) {
                    $hourdiff = (strtotime($offer_array['enddate']) - strtotime(date('Y-m-d H:i:s')));
                    $hourdiff = date('H', strtotime($hourdiff));

                    $expire_status = '';
                    if ($hourdiff < 23) {
                        $expire_status = 'Expires in '.$hourdiff.' hours';
                    } elseif ($hourdiff > 23 && $hourdiff <= 47) {
                        $expire_status = 'Expires tomorrow';
                    } elseif ($hourdiff >= 48 && $hourdiff <= 71) {
                        $expire_status = 'Expires in 2 days';
                    } elseif ($hourdiff >= 72 && $hourdiff <= 168) {
                        $date1 = $now = date('Y-m-d H:i:s');
                        $newnew_date = date('Y-m-d H:i:s', strtotime($date1." +$hourdiff hours"));
                        $expire_date = date('d F', strtotime($newnew_date));
                        $expire_status = "Expires on $expire_date";
                    } else {
                        $expire_status = '';
                    }
                } else {
                    $expire_status = '';
                }

            if (empty($offer_array['shop_image'])) {
                $offer_array['shop_image'] = $request->getScheme().'://'.$request->getHttpHost().$request->getBasePath().'/bundles/iflairletsbonusfront/images/shoppiday-placeholder.png';
            }
            if (empty($offer_array['brand_logo'])) {
                $offer_array['brand_logo'] = '';
            }
            if (empty($offer_array['type'])) {
                $offer_array['type'] = '';
                $offer_array['offer_type'] = '';
            }
            if (empty($offer_array['description'])) {
                $offer_array['description'] = '';
            }
            if (empty($offer_array['cashback_percentage'])) {
                $offer_array['cashback_percentage'] = 0;
            }

            return $this->render('::right_offer_block.html.twig', array(
                    'offer_array' => $offer_array,
                    'expire_status' => $expire_status,
                ));
        } else {
            return new Response();
        } else:

            $shop_offer_collection = array();
        foreach ($offer_shop_data as $offer_data) {
            if (array_key_exists('cashback_euro', $offer_data)) {
                if ($offer_data['cashback_euro'] > 0) {
                    $shop_offer_collection[] = $offer_data;
                }
            }
            if (array_key_exists('cashback_percentage', $offer_data)) {
                if ($offer_data['cashback_percentage'] > 0) {
                    $shop_offer_collection[] = $offer_data;
                }
            }
        }

        $shop_offer_array = array();
        if (count($shop_offer_collection) >= 1) {
            $offerkey = array_rand($shop_offer_collection);
            $offer_array = $shop_offer_collection[$offerkey];

            if (!empty($offer_array['enddate'])) {
                $hourdiff = (strtotime($offer_array['enddate']) - strtotime(date('Y-m-d H:i:s')));
                $hourdiff = date('H', strtotime($hourdiff));

                $expire_status = '';
                if ($hourdiff < 23) {
                    $expire_status = 'Expires in '.$hourdiff.' hours';
                } elseif ($hourdiff > 23 && $hourdiff <= 47) {
                    $expire_status = 'Expires tomorrow';
                } elseif ($hourdiff >= 48 && $hourdiff <= 71) {
                    $expire_status = 'Expires in 2 days';
                } elseif ($hourdiff >= 72 && $hourdiff <= 168) {
                    $date1 = $now = date('Y-m-d H:i:s');
                    $newnew_date = date('Y-m-d H:i:s', strtotime($date1." +$hourdiff hours"));
                    $expire_date = date('d F', strtotime($newnew_date));
                    $expire_status = "Expires on $expire_date";
                } else {
                    $expire_status = '';
                }
            } else {
                $expire_status = '';
            }
            if (empty($offer_array['shop_image'])) {
                $offer_array['shop_image'] = $request->getScheme().'://'.$request->getHttpHost().$request->getBasePath().'/bundles/iflairletsbonusfront/images/shoppiday-placeholder.png';
            }
            if (empty($offer_array['brand_logo'])) {
                $offer_array['brand_logo'] = '';
            }
            if (empty($offer_array['type'])) {
                $offer_array['type'] = '';
                $offer_array['offer_type'] = '';
            }
            if (empty($offer_array['description'])) {
                $offer_array['description'] = '';
            }
            if (array_key_exists('cashback_percentage', $offer_array)) {
                if (empty($offer_array['cashback_percentage'])) {
                    $offer_array['cashback_percentage'] = 0;
                }
            }

            return $this->render('::right_offer_block.html.twig', array(
                    'offer_array' => $offer_array,
                    'expire_status' => $expire_status,
                ));
        } else {
            return new Response();
        }
        endif;
    }

    public function facebookcountAction()
    {
        return new Response();
        //  echo "No Of Facebook Page LIKERS ::".fbLikeCount('coffeecupweb','__my_app_id__','__my_secret_key__');
        /*$facebookcount = $this->fbLikeCount($this->container->getParameter('facebook_page_id'), $this->container->getParameter('facebook_client_id'), $this->container->getParameter('facebook_client_secret'));

        echo 'Facebook Counts :: '.$facebookcount;

        return $this->render('iFlairLetsBonusFrontBundle:Homepage:facebookmembercount.html.twig', array(
            'facebookcount' => $facebookcount,
        ));*/
    }

    public function fbLikeCount($id, $appid, $appsecret)
    {
        $json_url = 'https://graph.facebook.com/'.$id.'?access_token='.$appid.'|'.$appsecret;
        $json = file_get_contents($json_url);
        $json_output = json_decode($json);

        //Extract the likes count from the JSON object
        $likes = 0;
        if ($json_output->likes) {
            $likes = $json_output->likes;
        }

        return $likes;
    }

    /**
     * @return Response
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function twittercountAction()
    {
        $tw_username = $this->container->getParameter('twitter_username');
        $data = file_get_contents('https://cdn.syndication.twimg.com/widgets/followbutton/info.json?screen_names='.$tw_username);
        $parsed = json_decode($data, true);
        $twittercount = $parsed[0]['followers_count'];

        return $this->render('iFlairLetsBonusFrontBundle:Homepage:twittermembercount.html.twig', array(
            'twittercount' => $twittercount,
        ));
    }

    /**
     * @return Response
     * @throws \LogicException
     */
    public function offercountAction()
    {
        $cashbackcount = $this->getDoctrine()->getRepository('iFlairLetsBonusAdminBundle:Shop')->countActive();
        $vouchercount = $this->getDoctrine()->getRepository('iFlairLetsBonusAdminBundle:Voucher')->countActive();

        return $this->render(
            'iFlairLetsBonusFrontBundle:Homepage:offercount.html.twig',
            [
                'count' => $vouchercount + $cashbackcount,
                'cashback' => $cashbackcount,
                'voucher' => $vouchercount,
            ]
        );
    }

    public function bestproductAction(Request $request)
    {
        $sm = $this->getDoctrine()->getEntityManager();
        $connection = $sm->getConnection();
        $statement = $connection->prepare('SELECT * FROM lb_shop_voucher ');
        $statement->execute();
        $shop_data = $statement->fetchAll();

        $prod_arr = array();

        if (!empty($shop_data)) {
            $voucherArray = array();
            $vouchercounterArray = array();

            foreach ($shop_data as $key => $val) {
                $voucherArray[$val['shop_id']][] = $val['voucher_id'];
            }

            foreach ($voucherArray as $key => $value) {
                $vouchercounterArray[$key] = count($value);
            }

            if (!empty($vouchercounterArray)) {
                arsort($vouchercounterArray);
            }

            $i = 1;
            $entities = $sm->getRepository('iFlairLetsBonusAdminBundle:Shop');
            foreach ($vouchercounterArray as $key => $value) {
                $pdata = $entities->findOneBy(array(
                    'id' => $key,
                    'shopStatus' => Shop::SHOP_ACTIVATED,
                    'highlightedHome' => Shop::SHOP_HIGHLIGHTED_HOME,
                ));

                if (!empty($pdata)) {
                    $shopId = $pdata->getId();
                    $prod_arr[$i]['shop_id'] = $shopId;
                    $prod_arr[$i]['shop_offers'] = $pdata->getOffers();
                    $prod_arr[$i]['shop_affiliate_url'] = $pdata->getUrlAffiliate();
                    $prod_arr[$i]['title'] = $pdata->getTitle();
                    $prod_arr[$i]['shop_offers'] = $pdata->getOffers();
                    /*  $prod_arr[$i]['cashback_price'] =
                       (!empty($pdata->getCashbackPrice) && $pdata->getCashbackPrice > 0) ? $pdata->getCashbackPrice.'€' : $pdata->getCashbackPercentage().'%';*/

                    if ($shopId) {
                         $cashback_type =  new TiendasController();
                     $prod_arr[$i]['cashback_type_value'] = $cashback_type->getCashbackSettingsByShopId($shopId, $connection);

                        if ($i <= count($vouchercounterArray)) {
                            $prod_arr[$i]['image'] = '';
                            if ($pdata->getTabImage()) {
                                $media = $pdata->getTabImage();
                                $mediaManager = $this->get('sonata.media.pool');
                                $provider = $mediaManager->getProvider($media->getProviderName());
                                $format = $provider->getFormatName($media, 'shop');
                                $productpublicUrl = $provider->generatePublicUrl($media, $format);
                                $prod_arr[$i]['image'] = $productpublicUrl;
                            }

                            $product_history = $sm->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array(
                                'shop' => $shopId,
                            ));

                            $prod_arr[$i]['shop_history_id'] = '';
                            $prod_arr[$i]['rating'] = '';
                            // $prod_arr[$i]['title'] = '';
                            //$prod_arr[$i]['cashback_price'] = 0;
                            $prod_arr[$i]['letsbonus_percentage'] = 0;
                            $prod_arr[$i]['introduction'] = '';
                            $prod_arr[$i]['tearms'] = '';
                            $prod_arr[$i]['variations'] = array();

                            if (!empty($product_history)) {
                                if ($productHistoryId = $product_history->getId()) {
                                    $prod_arr[$i]['shop_history_id'] = $productHistoryId;
                                    $prod_arr[$i]['rating'] = $this->ratingAction($shopId, $productHistoryId);

                                    $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                                    array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $productHistoryId));
                                        if ($slug) {
                                            $prod_arr[$i]['slug_name'] = $slug->getSlugName();
                                        }
                                }

                                //Retrieve variations
                                $variationStatement = $connection->prepare('SELECT v.number, v.title, v.date FROM lb_variation AS v LEFT JOIN lb_shop_history AS sh ON sh.id = v.shop_history_id WHERE v.shop_history_id = :shop_history_id ORDER BY v.number DESC');
                                $variationStatement->bindValue('shop_history_id', $productHistoryId);
                                $variationStatement->execute();
                                $variationData = $variationStatement->fetchAll();
                                $retrievedVariation = array();
                                if ($variationData) {
                                    $j = 0;
                                    foreach ($variationData as $variation) {
                                        $retrievedVariation[$j]['number'] = $variation['number'];
                                        $retrievedVariation[$j]['title'] = $variation['title'];
                                        $retrievedVariation[$j]['date'] = $variation['date'];
                                        ++$j;
                                    }
                                }
                                if (count($retrievedVariation) > 0) {
                                    $prod_arr[$i]['variations'] = $retrievedVariation;
                                }

                                /*   if ($productHistoryTitle = $product_history->getTitle()) {
                                       $prod_arr[$i]['title'] = $productHistoryTitle;
                                   }*/

                                // if ($productHistoryCashbackPrice = $product_history->getCashbackPrice()) {
                                $cashbackPrice = $product_history->getCashbackPrice();
                                $cashbackPercentage = $product_history->getCashbackPercentage();
                                if ($cashbackPrice > 0) {
                                    $prod_arr[$i]['cashback_price'] = $cashbackPrice.'€';
                                } elseif ($cashbackPercentage > 0) {
                                    $prod_arr[$i]['cashback_price'] = $cashbackPercentage.'%';
                                }

                                /* if(!empty($product_history->getCashbackPrice()) && $product_history->getCashbackPrice() > 0)
                                 {

                                      $prod_arr[$i]['cashback_price'] = $product_history->getCashbackPrice().'€' ;
                                 }
                                 else
                                 {
                                      $prod_arr[$i]['cashback_price'] = $product_history->getCashbackPercentage().'%';
                                 }*/

                                //  }

                                $prod_arr[$i]['letsbonus_percentage'] = 0;
                                if ($productHistoryLetsBonusPercentage = $product_history->getLetsBonusPercentage()) {
                                    $prod_arr[$i]['letsbonus_percentage'] = $productHistoryLetsBonusPercentage;
                                }

                                if ($productHistoryIntroduction = $product_history->getIntroduction()) {
                                    $prod_arr[$i]['introduction'] = strip_tags($productHistoryIntroduction);
                                }

                                if ($productHistoryTerms = $product_history->getTearms()) {
                                    $prod_arr[$i]['tearms'] = $productHistoryTerms;
                                }
                            }

                            // brand_logo
                            $shopBrand = $sm->createQueryBuilder()
                                ->select('vp')
                                ->from('iFlairLetsBonusAdminBundle:VoucherPrograms',  'vp')
                                ->join('iFlairLetsBonusAdminBundle:Shop', 's', Join::WITH, 'vp.id = s.vprogram')
                                ->where('s.id = :sid')
                                ->setParameter('sid', $shopId)
                                ->setFirstResult(0)
                                ->setMaxResults(1)
                                ->getQuery()
                                ->getOneOrNullResult()
                            ;
                            $prod_arr[$i]['brand_logo'] = '';
                            $prod_arr[$i]['brand_name'] = '';
                            if ($shopBrand) {
                                $prod_arr[$i]['brand_name'] = $shopBrand->getProgramName();
                                $uploadedLogo = $shopBrand->getImage();
                                $networkProvidedLogo = $shopBrand->getLogoPath();
                                if (!empty($uploadedLogo)) {
                                    $mediaManager = $this->get('sonata.media.pool');
                                    $provider = $mediaManager->getProvider($uploadedLogo->getProviderName());
                                    $format = $provider->getFormatName($uploadedLogo, 'brand_on_shop');
                                    $prod_arr[$i]['brand_logo'] = $provider->generatePublicUrl($uploadedLogo, $format);
                                } elseif (!empty($networkProvidedLogo)) {
                                    $prod_arr[$i]['brand_logo'] = $networkProvidedLogo;
                                }
                                $prod_arr[$i]['brand_id'] = $shopBrand->getId();
                               
                            }

                            // voucher_id
                            $statement = $connection->prepare('SELECT
											b.code
											FROM lb_voucher b
											JOIN lb_shop_voucher s ON b.id = s.voucher_id
											WHERE s.shop_id = :shopid');
                            $statement->bindValue('shopid', $shopId);
                            $statement->execute();
                            $shop_data = $statement->fetchAll();

                            $prod_arr[$i]['voucher_code'] = '';
                            $prod_arr[$i]['voucher_code_count'] = 0;
                            if ($shop_data) {
                                $prod_arr[$i]['voucher_code'] = $shop_data;
                                $prod_arr[$i]['voucher_code_count'] = count($shop_data);
                                foreach ($shop_data as $key => $value) {
                                    $date = strtotime($value['publish_end_date']);
                                    $dat = date('d/m/y', $date);
                                    if(strtotime($value['publish_end_date']) > strtotime('-30 days')) {
                                                $date = strtotime($value['publish_end_date']);
                                                $dat = date('d/m/y', $date);
                                         }
                                        else
                                        {
                                            $dat = "";
                                        }


                                    $prod_arr[$i]['voucher_expire_date'] = $dat;
                                }
                            }
                            $voucherFinal = $brandController->getVoucherByShopId($pdata->getId(), $pdata->getVprogram(), $connection);
                            if (count($voucherFinal) != 0) {
                                $prod_arr[$i]['voucher_id'] = $voucherFinal[0]['voucher_id'];
                                $prod_arr[$i]['voucher_code'] = $voucherFinal[0]['voucher_code'];
                                $prod_arr[$i]['voucher_name'] = $voucherFinal[0]['voucher_name'];
                               
                                if(strtotime($voucherFinal[0]['voucher_expire_date']) > strtotime('-30 days')) {
                                         $date = strtotime($voucherFinal[0]['voucher_expire_date']);
                                         $dat = date('d/m/y', $date);
                                 }
                                else
                                {
                                    $dat = "";
                                }

                                $prod_arr[$i]['voucher_expire_date'] = $dat;
                                $prod_arr[$i]['discount_amount'] = $voucherFinal[0]['discount_amount'];
                                $prod_arr[$i]['is_percentage'] = $voucherFinal[0]['is_percentage'];
                                $prod_arr[$i]['exclusive'] = $voucherFinal[0]['exclusive'];
                                $prod_arr[$i]['short_description'] = $voucherFinal[0]['short_description'];
                                $prod_arr[$i]['default_track_uri'] = $voucherFinal[0]['default_track_uri'];
                                $prod_arr[$i]['description'] = $voucherFinal[0]['description'];
                                $prod_arr[$i]['voucher_program_name'] = $pdata->getVprogram()->getProgramName();
                            }
                        }
                    }
                    ++$i;
                }
            }
        }

        if (count($prod_arr) > 0) {
            return $this->render('iFlairLetsBonusFrontBundle:Homepage:bestProduct.html.twig', array(
                'bestprodctcollection' => $prod_arr,
                'addtofevlist' => $this->addtofevlistAction(),
            ));
        } else {
            return new Response();
        }
    }

    public function bestproductResponsiveAction(Request $request)
    {
        $sm = $this->getDoctrine()->getEntityManager();
        $connection = $sm->getConnection();
        $statement = $connection->prepare('SELECT * FROM lb_shop_voucher ');
        $statement->execute();
        $shop_data = $statement->fetchAll();

        $prod_arr = array();
        if (!empty($shop_data)) {
            $voucherArray = array();
            $vouchercounterArray = array();

            foreach ($shop_data as $key => $val) {
                $voucherArray[$val['shop_id']][] = $val['voucher_id'];
            }

            foreach ($voucherArray as $key => $value) {
                $vouchercounterArray[$key] = count($value);
            }

            if (!empty($vouchercounterArray)) {
                arsort($vouchercounterArray);
            }

            $i = 1;
            $entities = $sm->getRepository('iFlairLetsBonusAdminBundle:Shop');
            foreach ($vouchercounterArray as $key => $value) {
                $pdata = $entities->findOneBy(array(
                    'id' => $key,
                    'shopStatus' => Shop::SHOP_ACTIVATED,
                    'highlightedHome' => Shop::SHOP_HIGHLIGHTED_HOME,
                ));

                if (!empty($pdata)) {
                    $shopId = $pdata->getId();
                    $prod_arr[$i]['shop_id'] = $shopId;
                    $prod_arr[$i]['shop_offers'] = $pdata->getOffers();
                    $prod_arr[$i]['shop_affiliate_url'] = $pdata->getUrlAffiliate();
                    $prod_arr[$i]['title'] = $pdata->getTitle();
                    /* $prod_arr[$i]['cashback_price'] =
                      (!empty($pdata->getCashbackPrice) && $pdata->getCashbackPrice > 0) ? $pdata->getCashbackPrice.'€' : $pdata->getCashbackPercentage().'%';*/

                    if ($shopId) {
                         $cashback_type =  new TiendasController();
                     $prod_arr[$i]['cashback_type_value'] = $cashback_type->getCashbackSettingsByShopId($shopId, $connection);

                        if ($i <= count($vouchercounterArray)) {
                            $prod_arr[$i]['image'] = '';
                            if ($pdata->getTabImage()) {
                                $media = $pdata->getTabImage();
                                $mediaManager = $this->get('sonata.media.pool');
                                $provider = $mediaManager->getProvider($media->getProviderName());
                                $format = $provider->getFormatName($media, 'shop');
                                $productpublicUrl = $provider->generatePublicUrl($media, $format);
                                $prod_arr[$i]['image'] = $productpublicUrl;
                            }

                            $product_history = $sm->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array(
                                'shop' => $shopId,
                            ));

                            $prod_arr[$i]['shop_history_id'] = '';
                            $prod_arr[$i]['rating'] = '';
                            // $prod_arr[$i]['title'] = '';
                            //$prod_arr[$i]['cashback_price'] = 0;
                            $prod_arr[$i]['letsbonus_percentage'] = 0;
                            $prod_arr[$i]['introduction'] = '';
                            $prod_arr[$i]['tearms'] = '';
                            $prod_arr[$i]['variations'] = array();

                            if (!empty($product_history)) {
                                if ($productHistoryId = $product_history->getId()) {
                                    $prod_arr[$i]['shop_history_id'] = $productHistoryId;
                                    $prod_arr[$i]['rating'] = $this->ratingAction($shopId, $productHistoryId);

                                    $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                                    array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $productHistoryId));
                                        if ($slug) {
                                            $prod_arr[$i]['slug_name'] = $slug->getSlugName();
                                        }
                                }

                                //Retrieve variations
                                $variationStatement = $connection->prepare('SELECT v.number, v.title, v.date FROM lb_variation AS v LEFT JOIN lb_shop_history AS sh ON sh.id = v.shop_history_id WHERE v.shop_history_id = :shop_history_id');
                                $variationStatement->bindValue('shop_history_id', $productHistoryId);
                                $variationStatement->execute();
                                $variationData = $variationStatement->fetchAll();
                                $retrievedVariation = array();
                                if ($variationData) {
                                    $j = 0;
                                    foreach ($variationData as $variation) {
                                        $retrievedVariation[$j]['number'] = $variation['number'];
                                        $retrievedVariation[$j]['title'] = $variation['title'];
                                        $retrievedVariation[$j]['date'] = $variation['date'];
                                        ++$j;
                                    }
                                }
                                if (count($retrievedVariation) > 0) {
                                    $prod_arr[$i]['variations'] = $retrievedVariation;
                                }

                                /* if ($productHistoryTitle = $product_history->getTitle()) {
                                     $prod_arr[$i]['title'] = $productHistoryTitle;
                                 }*/

                                $cashbackPrice = $product_history->getCashbackPrice();
                                $cashbackPercentage = $product_history->getCashbackPercentage();
                                if ($cashbackPrice > 0) {
                                    $prod_arr[$i]['cashback_price'] = $cashbackPrice.'€';
                                } elseif ($cashbackPercentage > 0) {
                                    $prod_arr[$i]['cashback_price'] = $cashbackPercentage.'%';
                                }

                                if ($productHistoryLetsBonusPercentage = $product_history->getLetsBonusPercentage()) {
                                    $prod_arr[$i]['letsbonus_percentage'] = $productHistoryLetsBonusPercentage;
                                }

                                if ($productHistoryIntroduction = $product_history->getIntroduction()) {
                                    $prod_arr[$i]['introduction'] = strip_tags($productHistoryIntroduction);
                                }

                                if ($produtHistoryTerms = $product_history->getTearms()) {
                                    $prod_arr[$i]['tearms'] = $produtHistoryTerms;
                                }
                            }

                            // brand_logo
                            $shopBrand = $sm->createQueryBuilder()
                                ->select('vp')
                                ->from('iFlairLetsBonusAdminBundle:VoucherPrograms',  'vp')
                                ->join('iFlairLetsBonusAdminBundle:Shop', 's', Join::WITH, 'vp.id = s.vprogram')
                                ->where('s.id = :sid')
                                ->setParameter('sid', $shopId)
                                ->setFirstResult(0)
                                ->setMaxResults(1)
                                ->getQuery()
                                //->getSql()
                                ->getOneOrNullResult()
                            ;

                            $prod_arr[$i]['brand_logo'] = '';
                            $prod_arr[$i]['brand_name'] = '';
                            if ($shopBrand) {
                                $prod_arr[$i]['brand_name'] = $shopBrand->getProgramName();
                                $uploadedLogo = $shopBrand->getImage();
                                $networkProvidedLogo = $shopBrand->getLogoPath();
                                if (!empty($uploadedLogo)) {
                                    $mediaManager = $this->get('sonata.media.pool');
                                    $provider = $mediaManager->getProvider($uploadedLogo->getProviderName());
                                    $format = $provider->getFormatName($uploadedLogo, 'brand_on_shop');
                                    $prod_arr[$i]['brand_logo'] = $provider->generatePublicUrl($uploadedLogo, $format);
                                } elseif (!empty($networkProvidedLogo)) {
                                    $prod_arr[$i]['brand_logo'] = $networkProvidedLogo;
                                }
                                $prod_arr[$i]['brand_id'] = $shopBrand->getId();
                               
                            }

                            // voucher_id
                            $statement = $connection->prepare('SELECT
															b.code
															FROM lb_voucher b
															JOIN lb_shop_voucher s ON b.id = s.voucher_id
															where s.shop_id = :shopid');
                            $statement->bindValue('shopid', $shopId);
                            $statement->execute();
                            $shop_data = $statement->fetchAll();

                            $prod_arr[$i]['voucher_code'] = '';
                            $prod_arr[$i]['voucher_code_count'] = 0;
                            if ($shop_data) {
                                $prod_arr[$i]['voucher_code'] = $shop_data;
                                $prod_arr[$i]['voucher_code_count'] = count($shop_data);
                                foreach ($shop_data as $key => $value) {
                                    if(strtotime($value['publish_end_date']) > strtotime('-30 days')) {
                                        $date = strtotime($value['publish_end_date']);
                                        $dat = date('d/m/y', $date);
                                     }
                                    else
                                    {
                                        $dat = "";
                                    }

                                    $prod_arr[$i]['voucher_expire_date'] = $dat;
                                }
                            }
                            $voucherFinal = $brandController->getVoucherByShopId($pdata->getId(), $pdata->getVprogram(), $connection);
                            if (count($voucherFinal) != 0) {
                                $prod_arr[$i]['voucher_id'] = $voucherFinal[0]['voucher_id'];
                                $prod_arr[$i]['voucher_code'] = $voucherFinal[0]['voucher_code'];
                                $prod_arr[$i]['voucher_name'] = $voucherFinal[0]['voucher_name'];
                              
                                if(strtotime($voucherFinal[0]['voucher_expire_date']) > strtotime('-30 days')) {
                                         $date = strtotime($voucherFinal[0]['voucher_expire_date']);
                                            $dat = date('d/m/y', $date);
                                     }
                                    else
                                    {
                                        $dat = "";
                                    }


                                $prod_arr[$i]['voucher_expire_date'] = $dat;
                                $prod_arr[$i]['discount_amount'] = $voucherFinal[0]['discount_amount'];
                                $prod_arr[$i]['is_percentage'] = $voucherFinal[0]['is_percentage'];
                                $prod_arr[$i]['exclusive'] = $voucherFinal[0]['exclusive'];
                                $prod_arr[$i]['short_description'] = $voucherFinal[0]['short_description'];
                                $prod_arr[$i]['default_track_uri'] = $voucherFinal[0]['default_track_uri'];
                                $prod_arr[$i]['description'] = $voucherFinal[0]['description'];
                                $prod_arr[$i]['voucher_program_name'] = $pdata->getVprogram()->getProgramName();
                            }
                        }
                    }
                    ++$i;
                }
            }
        }

        if (!empty($prod_arr)):
            $prod_arr = array_chunk($prod_arr, 4);

        if (count($prod_arr[0]) > 0) {
            return $this->render('iFlairLetsBonusFrontBundle:Homepage:bestProductResponsive.html.twig', array(
                    'bestprodctcollection' => $prod_arr[0],
                    'addtofevlist' => $this->addtofevlistAction(),
                ));
        } else {
            return new Response();
        } else:
            return new Response();
        endif;
    }

    public function bestcashbackAction(Request $request,$isResponsive = false)
    {        
        $sm = $this->getDoctrine()->getEntityManager();
        $connection = $sm->getConnection();
        $shopData = $sm->createQueryBuilder()
                                ->select('partial s.{id}')
                                ->from('iFlairLetsBonusAdminBundle:Shop',  's')
                                ->where('s.shopStatus = :shopStatus')
                                ->setParameter('shopStatus', Shop::SHOP_ACTIVATED)
                                ->andWhere('s.highlightedHome = :highlightedHome')
                                ->setParameter('highlightedHome', Shop::SHOP_HIGHLIGHTED_HOME)
                                ->andWhere('(s.offers = \'cashback\' OR s.offers = \'cashback/coupons\')')
                                ->setFirstResult(0)
                                ->setMaxResults(6)
                                ->getQuery()
                                //->getSql()
                                ->getResult()
                                ;

        $prod_arr = array();
        $i = 0;
        foreach ($shopData as $shop) {
            $shopHistoryData = $sm->createQueryBuilder()
                ->select(
                    'partial sh.{id,title,shop,cashbackPercentage,cashbackPrice,urlAffiliate,introduction,letsBonusPercentage,tearms}'
                )
                ->from('iFlairLetsBonusAdminBundle:shopHistory', 'sh')
                ->where('sh.shop = :shop')
                ->setParameter('shop', $shop->getId())
                ->join(
                    'iFlairLetsBonusAdminBundle:Slug',
                    'sl',
                    Join::WITH,
                    'sl.categoryId = sh.id'
                )
                ->andWhere('sl.categoryType = :shopType')
                ->setParameter('shopType', Constants::SHOP_IDENTIFIER)
                ->setFirstResult(0)
                ->setMaxResults(1)
                ->addOrderBy('sh.startDate', 'DESC')
                ->addOrderBy('sh.id', 'DESC')
                ->getQuery()
                ->getResult();
                            
            
            if($shopHistoryData) {
                foreach($shopHistoryData as $shopHistory) {
                    $pdata = $shop;
                    if (!empty($pdata)) {
                        $shopId = $pdata->getId();
                        $prod_arr[$i]['shop_id'] = $shopId;
                        $prod_arr[$i]['shop_offers'] = $pdata->getOffers();
                        $prod_arr[$i]['title'] = $shopHistory->getTitle();
                        $prod_arr[$i]['program_id'] = $pdata->getProgramId();

                        if ($shopId) {                        
                            $prod_arr[$i]['image'] = '';
                            if ($pdata->getTabImage()) {
                                $media = $pdata->getTabImage();
                                $mediaManager = $this->get('sonata.media.pool');
                                $provider = $mediaManager->getProvider($media->getProviderName());
                                $format = $provider->getFormatName($media, 'shop');
                                $productpublicUrl = $provider->generatePublicUrl($media, $format);
                                $prod_arr[$i]['image'] = $productpublicUrl;
                            }

                            $cashback_type =  new TiendasController();
                            $prod_arr[$i]['cashback_type_value'] = $cashback_type->getCashbackSettingsByShopId($shopHistory->getId(), $connection);
                            $prod_arr[$i]['shop_history_id'] = '';
                            $prod_arr[$i]['rating'] = '';
                            // $prod_arr[$i]['title'] = '';
                            // $prod_arr[$i]['cashback_price'] = 0;
                            $prod_arr[$i]['letsbonus_percentage'] = 0;
                            $prod_arr[$i]['introduction'] = '';
                            $prod_arr[$i]['tearms'] = '';
                            $prod_arr[$i]['shop_affiliate_url'] = '';
                            $prod_arr[$i]['shop_affiliate_url_origin'] = '';
                            $prod_arr[$i]['discount_amount'] = 0;
                            $prod_arr[$i]['variations'] = [];
                            if ($productHistoryId = $shopHistory->getId()) {
                                $prod_arr[$i]['shop_history_id'] = $productHistoryId;
                                $prod_arr[$i]['rating'] = $this->ratingAction($shopId, $productHistoryId);

                                $affiliationArgs = new DefaultController();
                                $affiliationArgs->setContainer($this->container);
                                $affiliationUrlArgs = $affiliationArgs->getAffiliation($pdata, $shopHistory, $sm);

                                if (!empty($pdata->getUrlAffiliate())) {
                                    $prod_arr[$i]['shop_affiliate_url_origin'] = $shopHistory->getUrlAffiliate();
                                    $redirect_url = $shopHistory->getUrlAffiliate().$affiliationUrlArgs;
                                    $prod_arr[$i]['shop_affiliate_url'] = $redirect_url;
                                }

                                $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                                    array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $productHistoryId));
                                    if ($slug) {
                                        $prod_arr[$i]['slug_name'] = $slug->getSlugName();
                                    }
                            }

                            //Retrieve variations
                            $variationStatement = $connection->prepare('SELECT v.number, v.title, v.date FROM lb_variation AS v LEFT JOIN lb_shop_history AS sh ON sh.id = v.shop_history_id WHERE v.shop_history_id = :shop_history_id ORDER BY v.number DESC');
                            $variationStatement->bindValue('shop_history_id', $productHistoryId);
                            $variationStatement->execute();
                            $variationData = $variationStatement->fetchAll();
                            $retrievedVariation = array();
                            if ($variationData) {
                                $j = 0;
                                foreach ($variationData as $variation) {
                                    $retrievedVariation[$j]['number'] = $variation['number'];
                                    $retrievedVariation[$j]['title'] = $variation['title'];
                                    $retrievedVariation[$j]['date'] = $variation['date'];
                                    ++$j;
                                }
                            }
                            if (count($retrievedVariation) > 0) {
                                $prod_arr[$i]['variations'] = $retrievedVariation;
                            }
                                    
                            $cashbackPrice = $shopHistory->getCashbackPrice();
                            $cashbackPercentage = $shopHistory->getCashbackPercentage();
                            if ($cashbackPrice > 0) {
                                $prod_arr[$i]['cashback_price'] = $cashbackPrice.'€';
                            } elseif ($cashbackPercentage > 0) {
                                $prod_arr[$i]['cashback_price'] = $cashbackPercentage.'%';
                            }

                            if ($productHistoryLetsBonusPercentage = $shopHistory->getLetsBonusPercentage()) {
                                $prod_arr[$i]['letsbonus_percentage'] = $productHistoryLetsBonusPercentage;
                            }

                            if ($productHistoryIntroduction = $shopHistory->getIntroduction()) {
                                $prod_arr[$i]['introduction'] = strip_tags($productHistoryIntroduction);
                            }

                            if ($productHistoryTerms = $shopHistory->getTearms()) {
                                $prod_arr[$i]['tearms'] = $productHistoryTerms;
                            }                        

                            // brand_logo
                            $shopBrand = $sm->createQueryBuilder()
                                ->select('partial vp.{id,programName,image,logoPath}')
                                ->from('iFlairLetsBonusAdminBundle:VoucherPrograms',  'vp')
                                ->join('iFlairLetsBonusAdminBundle:Shop', 's', Join::WITH, 'vp.id = s.vprogram')
                                ->where('s.id = :sid')
                                ->andWhere('s.vprogram IS NOT NULL')
                                ->setParameter('sid', $shopId)
                                ->setFirstResult(0)
                                ->setMaxResults(1)
                                ->getQuery()
                                ->getOneOrNullResult()
                            ;
                            $prod_arr[$i]['brand_id'] = '';
                            $prod_arr[$i]['brand_logo'] = '';
                            $prod_arr[$i]['brand_name'] = '';
                            $prod_arr[$i]['brand_logo_popup'] = '';
                            if ($shopBrand) {
                                $prod_arr[$i]['brand_id'] = $shopBrand->getId();
                                $prod_arr[$i]['brand_name'] = $shopBrand->getProgramName();
                                $uploadedLogo = $shopBrand->getImage();
                                $popUpLogo = $shopBrand->getPopUpImage();
                                $networkProvidedLogo = $shopBrand->getLogoPath();
                                if (!empty($uploadedLogo)) {
                                    $mediaManager = $this->get('sonata.media.pool');
                                    $provider = $mediaManager->getProvider($uploadedLogo->getProviderName());
                                    $format = $provider->getFormatName($uploadedLogo, 'brand_on_shop');
                                    $prod_arr[$i]['brand_logo'] = $provider->generatePublicUrl($uploadedLogo, $format);
                                } elseif (!empty($networkProvidedLogo)) {
                                    $prod_arr[$i]['brand_logo'] = $networkProvidedLogo;
                                }
                                if (!empty($popUpLogo)) {
                                    $mediaManager = $this->get('sonata.media.pool');
                                    $provider = $mediaManager->getProvider($popUpLogo->getProviderName());
                                    $format = $provider->getFormatName($popUpLogo, 'cashback_voucher_popup');
                                    $prod_arr[$i]['brand_logo_popup'] = $provider->generatePublicUrl($popUpLogo, $format);
                                } elseif (!empty($networkProvidedLogo)) {
                                    $prod_arr[$i]['brand_logo_popup'] = $networkProvidedLogo;
                                }
                                $prod_arr[$i]['brand_id'] = $shopBrand->getId();

                            }

                            // voucher_id
                            $statement = $connection->prepare('SELECT
                                                                b.code
                                                                FROM lb_voucher b
                                                                JOIN lb_shop_voucher s ON b.id = s.voucher_id
                                                                where s.shop_id = :shopid');
                            $statement->bindValue('shopid', $shopId);
                            $statement->execute();
                            $shop_data = $statement->fetchAll();
                            $prod_arr[$i]['voucher_code'] = '';
                            $prod_arr[$i]['voucher_code_count'] = 0;
                            if ($shop_data) {
                                $prod_arr[$i]['voucher_code'] = $shop_data;

                                $prod_arr[$i]['voucher_code_count'] = count($shop_data);
                            }                        
                        }
                        ++$i;
                    }
                }
            }
        }
       
        if (count($prod_arr) > 0) {
            if($isResponsive) {
                return $this->render('iFlairLetsBonusFrontBundle:Homepage:bestCashbackResponsive.html.twig', array(
                    'bestprodctcollection' => $prod_arr,
                    'addtofevlist' => $this->addtofevlistAction(),
                ));
            } else {
                return $this->render('iFlairLetsBonusFrontBundle:Homepage:bestCashback.html.twig', array(
                    'bestprodctcollection' => $prod_arr,
                    'addtofevlist' => $this->addtofevlistAction(),
                ));
            }
        } else {
            return new Response();
        }
    }

    public function bestcouponAction(Request $request, $isResponsive = false)
    {
        $sm = $this->getDoctrine()->getEntityManager();
        $connection = $sm->getConnection();

        $shopDataQuery = $connection->prepare('SELECT v.*,s.id AS shop_id,sh.id AS shop_history_id FROM lb_voucher AS v INNER JOIN lb_shop_voucher AS sv ON sv.voucher_id = v.id INNER JOIN lb_shop AS s ON s.id = sv.shop_id LEFT JOIN lb_voucher_programs AS vp ON vp.id = v.program_id LEFT JOIN lb_shop_history AS sh ON sh.shop = s.id JOIN lb_slug AS sl ON sl.categoryId = sh.id AND sl.categoryType = :categoryType WHERE s.shopStatus = :shopStatus AND s.highlightedHome = :highlightedHome AND (s.offers = \'voucher\' OR s.offers = \'cashback/coupons\') GROUP BY sh.shop ORDER BY v.discount_amount DESC LIMIT 6');
        $shopDataQuery->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        $shopDataQuery->bindValue('highlightedHome', Shop::SHOP_HIGHLIGHTED_HOME);
        $shopDataQuery->bindValue('categoryType', Constants::SHOP_IDENTIFIER);
        $shopDataQuery->execute();
        $shopData = $shopDataQuery->fetchAll();        

        $brandController = new BrandController();
        $brandController->setContainer($this->container);
        $prod_arr = array();        
        if($shopData) {
            $i = 0;
            $entities = $sm->getRepository('iFlairLetsBonusAdminBundle:Shop');
            foreach($shopData as $shop) {
                
                $shopHistoryId = $shop['shop_history_id'];
                
                $pdata = $entities->findOneBy(array(
                    'id' => $shop['shop_id']
                ));

                if (!empty($pdata)) {
                    $shopId = $pdata->getId();
                    $prod_arr[$i]['shop_id'] = $shopId;
                    $prod_arr[$i]['shop_offers'] = $pdata->getOffers();
                    $prod_arr[$i]['shop_affiliate_url'] = $pdata->getUrlAffiliate();
                    $prod_arr[$i]['title'] = $pdata->getTitle();
                    /*$prod_arr[$i]['cashback_price'] =
                     (!empty($pdata->getCashbackPrice) && $pdata->getCashbackPrice > 0) ? $pdata->getCashbackPrice.'€' : $pdata->getCashbackPercentage().'%';*/
                    


                    if ($shopId) {                       
                        $prod_arr[$i]['image'] = '';
                        if ($pdata->getTabImage()) {
                            $media = $pdata->getTabImage();
                            $mediaManager = $this->get('sonata.media.pool');
                            $provider = $mediaManager->getProvider($media->getProviderName());
                            $format = $provider->getFormatName($media, 'shop');
                            $productpublicUrl = $provider->generatePublicUrl($media, $format);
                            $prod_arr[$i]['image'] = $productpublicUrl;
                        }

                        $cashback_type =  new TiendasController();
                        $prod_arr[$i]['cashback_type_value'] = $cashback_type->getCashbackSettingsByShopId($shopHistoryId, $connection);
                        //$product_history = $sm->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('shop' => $shopId));
                        $product_history = $sm->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('shop' => $shopId), array('startDate'=>'DESC'), 1);
                        $prod_arr[$i]['shop_history_id'] = '';
                        $prod_arr[$i]['rating'] = '';
                        // $prod_arr[$i]['title'] = '';
                        // $prod_arr[$i]['cashback_price'] = 0;
                        $prod_arr[$i]['letsbonus_percentage'] = 0;
                        $prod_arr[$i]['introduction'] = '';
                        $prod_arr[$i]['tearms'] = '';
                        $prod_arr[$i]['variations'] = array();

                        if (!empty($product_history)) {
                            if ($productHistoryId = $product_history->getId()) {
                                $prod_arr[$i]['shop_history_id'] = $productHistoryId;
                                $prod_arr[$i]['rating'] = $this->ratingAction($shopId, $productHistoryId);
                                $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                                    array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $productHistoryId));
                                if ($slug) {
                                    $prod_arr[$i]['slug_name'] = $slug->getSlugName();
                                }
                            }

                            //Retrieve variations
                            $variationStatement = $connection->prepare('SELECT v.number, v.title, v.date FROM lb_variation AS v LEFT JOIN lb_shop_history AS sh ON sh.id = v.shop_history_id WHERE v.shop_history_id = :shop_history_id ORDER BY v.number DESC');
                            $variationStatement->bindValue('shop_history_id', $productHistoryId);
                            $variationStatement->execute();
                            $variationData = $variationStatement->fetchAll();
                            $retrievedVariation = array();
                            if ($variationData) {
                                $j = 0;
                                foreach ($variationData as $variation) {
                                    $retrievedVariation[$j]['number'] = $variation['number'];
                                    $retrievedVariation[$j]['title'] = $variation['title'];
                                    $retrievedVariation[$j]['date'] = $variation['date'];
                                    ++$j;
                                }
                            }
                            if (count($retrievedVariation) > 0) {
                                $prod_arr[$i]['variations'] = $retrievedVariation;
                            }

                            /* if ($productHistoryTitle = $product_history->getTitle()) {
                                    $prod_arr[$i]['title'] = $productHistoryTitle;
                            }*/

                            $cashbackPrice = $product_history->getCashbackPrice();
                            $cashbackPercentage = $product_history->getCashbackPercentage();
                            if ($cashbackPrice > 0) {
                                $prod_arr[$i]['cashback_price'] = $cashbackPrice.'€';
                            } elseif ($cashbackPercentage > 0) {
                                $prod_arr[$i]['cashback_price'] = $cashbackPercentage.'%';
                            }

                            if ($productHistoryLetsBonusPercentage = $product_history->getLetsBonusPercentage()) {
                                $prod_arr[$i]['letsbonus_percentage'] = $productHistoryLetsBonusPercentage;
                            }

                            if ($productHistoryIntroduction = $product_history->getIntroduction()) {
                                $prod_arr[$i]['introduction'] = strip_tags($productHistoryIntroduction);
                            }

                            if ($productHistoryTerms = $product_history->getTearms()) {
                                $prod_arr[$i]['tearms'] = $productHistoryTerms;
                            }
                        }

                        // brand_logo
                        $shopBrand = $sm->createQueryBuilder('vp')
                            ->select('partial vp.{id,programName,image,logoPath}')
                            ->from('iFlairLetsBonusAdminBundle:VoucherPrograms',  'vp')
                            ->join('iFlairLetsBonusAdminBundle:Shop', 's', Join::WITH, 'vp.id = s.vprogram')
                            ->where('s.id = :sid')
                            ->setParameter('sid', $shopId)
                            ->setFirstResult(0)
                            ->setMaxResults(1)
                            ->getQuery()
                            //->getSql()
                            ->getOneOrNullResult()
                        ;
                        $prod_arr[$i]['brand_id'] = '';
                        $prod_arr[$i]['brand_logo_popup'] = '';
                        $prod_arr[$i]['brand_logo'] = '';
                        $prod_arr[$i]['brand_name'] = '';
                        if ($shopBrand) {
                            $prod_arr[$i]['brand_id'] = $shopBrand->getId();
                            $prod_arr[$i]['brand_name'] = $shopBrand->getProgramName();
                            $uploadedLogo = $shopBrand->getImage();
                            $networkProvidedLogo = $shopBrand->getLogoPath();
                            if (!empty($uploadedLogo)) {
                                $mediaManager = $this->get('sonata.media.pool');
                                $provider = $mediaManager->getProvider($uploadedLogo->getProviderName());
                                $format = $provider->getFormatName($uploadedLogo, 'brand_on_shop');
                                $prod_arr[$i]['brand_logo'] = $provider->generatePublicUrl($uploadedLogo, $format);
                            } elseif (!empty($networkProvidedLogo)) {
                                $prod_arr[$i]['brand_logo'] = $networkProvidedLogo;
                            }
                            if (!empty($popUpLogo)) {
                                $mediaManager = $this->get('sonata.media.pool');
                                $provider = $mediaManager->getProvider($popUpLogo->getProviderName());
                                $format = $provider->getFormatName($popUpLogo, 'cashback_voucher_popup');
                                $prod_arr[$i]['brand_logo_popup'] = $provider->generatePublicUrl($popUpLogo, $format);
                            } elseif (!empty($networkProvidedLogo)) {
                                $prod_arr[$i]['brand_logo_popup'] = $networkProvidedLogo;
                            }
                            $prod_arr[$i]['brand_id'] = $shopBrand->getId();
                               
                        }

                        // voucher_id
                        $statement = $connection->prepare('SELECT
                                                                b.code, b.publish_end_date
                                                                FROM lb_voucher b
                                                                JOIN lb_shop_voucher s ON b.id = s.voucher_id
                                                                where s.shop_id = :shopid');
                        $statement->bindValue('shopid', $shopId);
                        $statement->execute();
                        $shop_data = $statement->fetchAll();

                        $prod_arr[$i]['voucher_code'] = '';
                        $prod_arr[$i]['voucher_code_count'] = 0;
                        $prod_arr[$i]['voucher_expire_date'] = '';

                        if ($shop_data) {
                            $prod_arr[$i]['voucher_code'] = $shop_data;
                            $prod_arr[$i]['voucher_code_count'] = count($shop_data);
                            foreach ($shop_data as $key => $value) {
                               
                                if(strtotime($value['publish_end_date']) > strtotime('-30 days')) {
                                         $date = strtotime($value['publish_end_date']);
                                        $dat = date('d/m/y', $date);

                                }
                                else
                                {
                                    $dat = "";
                                }



                                $prod_arr[$i]['voucher_expire_date'] = $dat;
                            }
                        }
                        $voucherFinal = $brandController->getVoucherByShopId($pdata->getId(), $pdata->getVprogram(), $connection);
                        if (count($voucherFinal) != 0) {
                            $prod_arr[$i]['voucher_id'] = $voucherFinal[0]['voucher_id'];
                            $prod_arr[$i]['voucher_code'] = $voucherFinal[0]['voucher_code'];
                            $prod_arr[$i]['voucher_name'] = $voucherFinal[0]['voucher_name'];
                           

                             if(strtotime($voucherFinal[0]['voucher_expire_date']) > strtotime('-30 days')) {
                                    $date = strtotime($voucherFinal[0]['voucher_expire_date']);
                                    $dat = date('d/m/y', $date);
                                }
                                else
                                {
                                    $dat = "";
                                }


                            $prod_arr[$i]['voucher_expire_date'] = $dat;
                            $prod_arr[$i]['discount_amount'] = $voucherFinal[0]['discount_amount'];
                            $prod_arr[$i]['is_percentage'] = $voucherFinal[0]['is_percentage'];
                            $prod_arr[$i]['exclusive'] = $voucherFinal[0]['exclusive'];
                            $prod_arr[$i]['short_description'] = $voucherFinal[0]['short_description'];
                            $prod_arr[$i]['default_track_uri'] = $voucherFinal[0]['default_track_uri'];
                            $prod_arr[$i]['description'] = $voucherFinal[0]['description'];
                            $prod_arr[$i]['voucher_program_name'] = $pdata->getVprogram()->getProgramName();
                        }                        
                    }
                    ++$i;
                }
            }
        }


        if (count($prod_arr) > 0) {
            if($isResponsive) {
                return $this->render('iFlairLetsBonusFrontBundle:Homepage:bestCouponsResonsive.html.twig', array(
                    'bestprodctcollection' => $prod_arr,
                    'addtofevlist' => $this->addtofevlistAction(),
                ));
            } else {
                return $this->render('iFlairLetsBonusFrontBundle:Homepage:bestCoupons.html.twig', array(
                    'bestprodctcollection' => $prod_arr,
                    'addtofevlist' => $this->addtofevlistAction(),
                ));
            }
        } else {
            return new Response();
        }
    }

    /**
     * @return array
     * @throws \LogicException
     */
    public function addtofevlistAction()
    {
        $session = new Session();
        $fev_shop = [];
        if ($session->get('user_id')) {
            $userId = $session->get('user_id');
            $fev_list = $this->getDoctrine()
                ->getRepository('iFlairLetsBonusAdminBundle:AddtoFev')
                ->findBy(['userId' => $userId]);
            $i = 0;
            foreach ($fev_list as $key => $value) {
                $fev_shop[$i] = $value->getShopId()->getId();
                ++$i;
            }
        }

        return $fev_shop;
    }

    /**
     * @param $shop_id
     * @param $shop_history_id
     *
     * @return float
     * @throws \UnexpectedValueException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function ratingAction($shop_id, $shop_history_id)
    {
        $em = $this->getDoctrine()->getManager();
        $entities_rating = $em->getRepository('iFlairLetsBonusFrontBundle:Review');
        $pdata_rating = $entities_rating->findBy(['shopId' => $shop_id, 'shopHistoryId' => $shop_history_id]);
        $countUserRating = count($pdata_rating);
        if ($countUserRating === 0) {
            $countUserRating = 1;
        }
        $rating = 0;
        foreach ($pdata_rating as $key => $value) {
            $rating += $value->getRating();
        }
        $rating /=  $countUserRating;

        return ($rating * 100) / 5;
    }
}
