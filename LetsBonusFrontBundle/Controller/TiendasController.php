<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use iFlair\LetsBonusAdminBundle\Slug\Constants;
use iFlair\LetsBonusAdminBundle\Entity\Shop;
use iFlair\LetsBonusAdminBundle\Entity\Settings;
use Symfony\Component\HttpFoundation\Response;

class TiendasController extends Controller
{
    public function getMediaUrlByCategoryId($shop_id, $mediaImageType, &$sm)
    {
        if (!empty($shop_id)) {
            $id = $shop_id;
            $entity = 'Shop';
        } else {
            $id = null;
            $entity = '';
        }

        if (!empty($id)) {
            $shopRepository = $sm->getRepository('iFlairLetsBonusAdminBundle:'.$entity);
            $shop = $shopRepository->findOneBy(array('id' => $id, 'shopStatus' => Shop::SHOP_ACTIVATED));
            if ($shop) {
                $shopMedia = $shop->getImage();
                if ($shopMedia) {
                    return $this->getMediaURL($shopMedia, $mediaImageType);
                }
            }
        }
    }
    public function getMediaUrlByShopId($shopId, $mediaImageType, &$sm)
    {
        $shopRepository = $sm->getRepository('iFlairLetsBonusAdminBundle:Shop');
        $shop = $shopRepository->findOneBy(array(
            'id' => $shopId,
            'shopStatus' => Shop::SHOP_ACTIVATED,
        ));
        if ($shop) {
            $shopMedia = $shop->getImage();
            if ($shopMedia) {
                return $this->getMediaURL($shopMedia, $mediaImageType);
            }
        }
    }
      public function getMediaForHighLineTab($shopId, $mediaImageType, &$sm)
    {
        $shopRepository = $sm->getRepository('iFlairLetsBonusAdminBundle:Shop');
        $shop = $shopRepository->findOneBy(array(
            'id' => $shopId,
            'shopStatus' => Shop::SHOP_ACTIVATED,
        ));
        if ($shop) {
            $shopMedia = $shop->getHighlineofferImage();
            if ($shopMedia) {
                return $this->getMediaURL($shopMedia, $mediaImageType);
            }
        }
    }
    
    public function getMediaURL($media, $mediaImageType)
    {
        $mediaManager = $this->get('sonata.media.pool');
        $mediaprovider = $mediaManager->getProvider($media->getProviderName());
        $mediaUrl = $mediaprovider->generatePublicUrl($media, $mediaImageType);

        return $mediaUrl;
    }

    public function getShopDetailsByCategoryId($id, $connection)
    {
        $statement = $connection->prepare('SELECT vp.logo_path AS brand_logo, vp.id AS brand_id,s.urlAffiliate AS urlAffiliate, s.* ,vp.program_name AS brand_name, MAX(v.discount_amount), v.*,s.id AS shop_id, vp.image_id as brand_image, vp.pop_up_image_id as brand_popup_image
                                           FROM lb_shop AS s
                                           LEFT JOIN lb_shop_voucher AS sv ON sv.shop_id = s.id
                                           LEFT JOIN lb_voucher_programs AS vp ON vp.id = s.vprogram_id
                                           LEFT JOIN lb_voucher AS v ON v.id = sv.voucher_id
                                           WHERE v.status = 1 AND s.id = :shopid AND s.shopStatus = :shopStatus');
        $statement->bindValue('shopid', $id);
        $statement->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        $statement->execute();

        return $statement->fetchAll();
    }
    public function getShopHistoryVariationByShopHistoryId($shopHistoryId, &$sm)
    {
        $variations = array();
        $i = 0;
        $variationRepository = $sm->getRepository('iFlairLetsBonusAdminBundle:Variation')->findBy(array('shopHistory' => $shopHistoryId));
        foreach ($variationRepository as $variation) {
            $variations[$i]['number'] = $variation->getNumber();
            $variations[$i]['title'] = $variation->getTitle();
            ++$i;
        }

        return $variations;
    }
    public function getVoucherCountByShopId($shopId, $connection)
    {
        $statement = $connection->prepare('SELECT b.code
                                           FROM lb_voucher b
                                           JOIN lb_shop_voucher s ON b.id = s.voucher_id
                                           WHERE s.shop_id = :shopid');
        $statement->bindValue('shopid', $shopId);
        $statement->execute();

        return $statement->fetchAll();
    }
    public function getCashbackSettingsByShopId($shopHistoryId, $connection)
    {
        $query = $connection->prepare('SELECT tgs.name AS tag_name, s.offers AS offer, s.id AS shop_id, s.vprogram_id FROM lb_shop_history AS sh LEFT JOIN lb_tags AS tgs ON sh.tag = tgs.id LEFT JOIN lb_shop AS s ON s.id=sh.shop WHERE sh.id = :id LIMIT 1');

        $query->bindValue('id', $shopHistoryId);
        $query->execute();
        $cashback_type = $query->fetchAll();
        $cashback_type_label = "";
        
        if (!empty($cashback_type)) {
            if($cashback_type[0]['offer'] == "voucher") {
                $brand = new BrandController();
                $voucher = $brand->getVoucherByShopId($cashback_type[0]['shop_id'], $cashback_type[0]['vprogram_id'], $connection);
                
                if($voucher[0]['exclusive'] && $voucher[0]['isnew']) {
                    $cashback_type_label = "*Novedad exclusiva";
                } elseif($voucher[0]['exclusive'] && !$voucher[0]['isnew']) {
                    $cashback_type_label = "*Exclusivo";
                } elseif(!$voucher[0]['exclusive'] && $voucher[0]['isnew']) {
                    $cashback_type_label = "*Nuevo";
                }
                
            } elseif($cashback_type[0]['offer'] == "cashback") {
                $cashback_type_label = $cashback_type[0]['tag_name'];
            }
        }

        return $cashback_type_label;
    }
    public function getFilter($cat_arr)
    {
        $categories_details = array();
        foreach ($cat_arr as $key => $value) {
            foreach ($value as $key_data => $value_data) {
                $categories_details[$key_data] = $value_data;
            }
        }

        return $categories_details;
    }
    public function getCahbackOfferFilter($categories_details)
    {
        $cashback = array();
        if (!empty($categories_details)) {
            foreach ($categories_details as $key => $value) {
                if (!empty($value['shop_id'])) {
                    if ($value['shop_offers'] == 'cashback' || $value['shop_offers'] == 'cashback/coupons') {
                        return $cashback[] = $value;
                    }
                }
            }
        }
    }
    public function getProductOfferFilter($categories_details)
    {
        $product = array();
        if (!empty($categories_details)) {
            foreach ($categories_details as $key => $value) {
                if (!empty($value['shop_id'])) {
                    if ($value['shop_offers'] == 'product') {
                        return $product[] = $value;
                    }
                }
            }
        }
    }
    public function getVoucherOfferFilter($categories_details)
    {
        $voucher = array();
        if (!empty($categories_details)) {
            foreach ($categories_details as $key => $value) {
                if (!empty($value['shop_id'])) {
                    if ($value['shop_offers'] == 'voucher' || $value['shop_offers'] == 'cashback/coupons') {
                        return $voucher[] = $value;
                    }
                }
            }
        }
    }
    public function getMaxVoucherFilter($categories_details)
    {
        $max_voucher = array();
        foreach ($categories_details as $key => $value) {
            if (!empty($value['shop_id']) && $value['shop_offers'] == 'voucher' || $value['shop_offers'] == 'cashback/coupons') {
                $max_voucher[] = $value;
            }
        }

        if (!empty($max_voucher)) {
            foreach ($max_voucher as $key => $value) {
                $voucher[$key] = $value['voucher_count'];
            }
            array_multisort($voucher, SORT_DESC, $max_voucher);
        } else {
            $max_voucher[0] = array();
        }

        return $max_voucher;
    }
    public function getMaxCashbackFilter($categories_details)
    {
        foreach ($categories_details as $key => $value) {
            if (!empty($value['shop_id']) && $value['shop_offers'] == 'cashback' || $value['shop_offers'] == 'cashback/coupons') {
                $max_cashback[] = $value;
            }
        }

        if (!empty($max_cashback)) {
            foreach ($max_cashback as $key => $value) {
                $cashback[$key] = $value['max_letsBonusPercentage'];
            }

            array_multisort($cashback, SORT_DESC, $max_cashback);
        } else {
            $max_cashback[0] = array();
        }

        return $max_cashback;
    }
    public function getMaxVoucherCashback($categories_details)
    {
        foreach ($categories_details as $key => $value) {
            if (!empty($value['shop_id']) && $value['shop_offers'] == 'offer') {
                $max_voucher_cashback[] = $value;
            }
        }

        if (!empty($max_voucher_cashback)) {
            foreach ($max_voucher_cashback as $key => $value) {
                $voucher_cashback[$key] = $value['max_letsBonusPercentage'];
            }

            array_multisort($voucher_cashback, SORT_DESC, $max_voucher_cashback);
        } else {
            $max_voucher_cashback[0] = array();
        }

        return $max_voucher_cashback;
    }
    public function getShopOffersByCategoryId($shop_id, $connection)
    {
        $statement = '';
        if (!empty($shop_id)) {
            $statement = $connection->prepare('SELECT s.*, s.id AS shop_id
                                           FROM lb_shop AS s WHERE s.shopStatus = :shopStatus AND s.id = :shop_id');
            $statement->bindValue('shop_id', $shop_id);
            $statement->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        } else {
            $statement = $connection->prepare('SELECT s.*, s.id AS shop_id
                                           FROM lb_shop AS s WHERE s.shopStatus = :shopStatus');
            $statement->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        }
        $statement->execute();

        return $statement->fetchAll();
    }
    public function tiendasPageAction(Request $request)
    {
        $sm = $this->getDoctrine()->getEntityManager();
        /*
         *  NOTE    :: Its an common method to show top banner for all sections
         *  DEFINED IN  ::  CategoryController
         *  ARGS    :: type_of_media_image, code_of_banner_for_any_section, em
         */
        $tiendasBannerTitle = '';
        $tiendasBannerDescription = '';

        $connection = $sm->getConnection();
        $bannerFromCategoryController = new CategoryController();
        $bannerFromCategoryController->setContainer($this->container);
        $tiendaTopBanner = $bannerFromCategoryController->getTopBanner('default_list_page_type', Settings::TIENDASTOPBANNER, $sm);

        $tiendaBanner = $bannerFromCategoryController->getBannerTitleDescription(Settings::TIENDASTOPBANNER, $connection);

        if (isset($tiendaBanner[0]['bannertitle']) && isset($tiendaBanner[0]['bannerdescription'])) {
            $tiendasBannerTitle = $tiendaBanner[0]['bannertitle'];
            $tiendasBannerDescription = $tiendaBanner[0]['bannerdescription'];
        }

        $affiliationArgs = new DefaultController();
        $affiliationArgs->setContainer($this->container);
        $brandController = new BrandController();
        $brandController->setContainer($this->container);
        $id = '';
        $userAddToFav = array();

        $connection = $sm->getConnection();
        $shop_id = '';

        $session = $this->getRequest()->getSession();
        $cat_arr = array();

        $category_image['cat_image'] = $this->getMediaUrlByCategoryId($shop_id, 'default_list_page_type', $sm);
        $shop_data = $this->getShopOffersByCategoryId($shop_id, $connection);

        $homepageController = new HomepageController();
        $homepageController->setContainer($this->container);

        if (!empty($shop_data)) {
            $i = 0;
            $voucherProgramsEntity = $sm->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms');
            foreach ($shop_data as $key => $shopdata) {
                $shopId = $shopdata['shop_id'];
                $shop_data_record = $this->getShopDetailsByCategoryId($shopId, $connection);
                //echo "<pre>";
                //print_r($shop_data_record);
                /*
                 * FOR CASHBACK :: CLICKS :: AFFILIATION MANAGEMENT
                 */
                $shopRepository = $sm->getRepository('iFlairLetsBonusAdminBundle:Shop');
                $shop = $shopRepository->findOneBy(array(
                    'id' => $shopId,
                    'shopStatus' => Shop::SHOP_ACTIVATED,
                ));
                // if condition for cashback && voucher is not exclusive
                if ((isset($shopId) && $shopdata['offers'] == 'cashback') || (isset($shopId) && $shopdata['offers'] == 'cashback/coupons') || (isset($shopId) && $shop_data_record[0]['exclusive'] == 0 && $shopdata['offers'] == 'voucher')) {
                    $shopHistoryRepo = $sm->getRepository('iFlairLetsBonusAdminBundle:shopHistory');
                    $query = $shopHistoryRepo->createQueryBuilder('sh')
                        ->join('iFlairLetsBonusAdminBundle:Slug', 'sl', \Doctrine\ORM\Query\Expr\Join::WITH, 'sl.categoryId = sh.id')
                        ->where('sh.shop = :shopId')
                        ->setParameter('shopId', $shopId)
                        ->andWhere('sl.categoryType = :shopType')
                        ->setParameter('shopType', Constants::SHOP_IDENTIFIER)                        
                        ->getQuery();
                    $shop_history = $query->getResult();
                    //$shop_history = $sm->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findBy(array('shop' => $shopId), array('startDate'=>'DESC'), 1);
                    //echo "<br> shop_history: ".count($shop_history);
                    if($shop_history) {
                        foreach ($shop_history as $key => $shop_value) {
                            $shopHistory = $shop_value;
                            $shopHistoryId = $shop_value->getId();
                            $affiliationUrlArgs = $affiliationArgs->getAffiliation($shop, $shopHistory, $sm);

                            if (!empty($shop_value->getUrlAffiliate())) {
                               // $cat_arr[$i]['shop_affiliate_url_origin'] = $shop_value->getUrlAffiliate();
                                $redirect_url = $shop_value->getUrlAffiliate().$affiliationUrlArgs;
                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['shop_affiliate_url'] = $redirect_url;
                            }
                          //  $cat_arr[$i]['program_id'] = $shop->getProgramId();
                            $voucher_count = $this->getVoucherCountByShopId($shopId, $connection);
                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['voucher_count'] = 0;
                            if ($voucher_count) {
                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['voucher_count'] = count($voucher_count);
                            }
                            $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $shopHistoryId));
                            if ($slug) {
                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['slug_name'] = $slug->getSlugName();
                            } else {
                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['slug_name'] = '';
                            }
                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['cashback_type_value'] = $this->getCashbackSettingsByShopId($shopId, $connection);
                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['shop_offers'] = $shopdata['offers'];
                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['shop_history_id'] = $shopHistoryId;
                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['shop_history_shop_title'] = $shop_value->getTitle();
                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['shop_history_shop_description'] = strip_tags($shop_value->getIntroduction());
                            //$cat_arr[$i][$shop_data_record[0]['brand_id']]['shop_history_shop_end_date'] = $shop_value->getEndDate();
                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['shop_terms'] = $shop_value->getTearms();
                            //Actual cashback instead of letsBonusPercentage
                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['max_letsBonusPercentage'] = 0;
                            $cashbackPrice = $shop_value->getCashbackPrice();
                            $cashbackPercentage = $shop_value->getCashbackPercentage();
                            if ($cashbackPrice > 0) {
                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['max_letsBonusPercentage'] = $cashbackPrice.'€'; //TO-DO :: Update dynamic currency symbol
                            } elseif ($cashbackPercentage > 0) {
                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['max_letsBonusPercentage'] = $cashbackPercentage.'%';
                            }
                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['shop_image'] = $this->getMediaUrlByShopId($shopId, 'reference', $sm);
                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['top_shop_image'] = $this->getMediaForHighLineTab($shopId, 'default_highline_offer_image', $sm);
                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['voucher_id'] = $shop_data_record[0]['id'];
                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['voucher_name'] = $shop_data_record[0]['title'];
                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['shop_id'] = $shopId;
                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['discount_amount'] = $shop_data_record[0]['discount_amount'];
                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['is_percentage'] = $shop_data_record[0]['is_percentage'];
                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['short_description'] = strip_tags($shop_data_record[0]['short_description']);
                            

                             if(strtotime($shop_data_record[0]['publish_end_date']) > strtotime('-30 days')) {
                                    $date = strtotime($shop_data_record[0]['publish_end_date']);
                                    $dat = date('d/m/y', $date);
                                 } else {
                                    $dat = "";
                                }


                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['voucher_expire_date'] = $dat;
                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['exclusive'] = $shop_data_record[0]['exclusive'];
                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['logo_image'] = $shop_data_record[0]['brand_logo'];
                            if (!empty($shop_data_record[0]['brand_image'])) {
                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['logo_image'] = $this->getImageUrl($voucherProgramsEntity, $shop_data_record[0]['brand_image'], 'brand_on_shop');
                            }
                            if (!empty($shop_data_record[0]['brand_popup_image'])) {
                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['brand_logo_popup'] = $this->getImageUrl($voucherProgramsEntity, $shop_data_record[0]['brand_popup_image'], 'cashback_voucher_popup');
                            } else {
                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['brand_logo_popup'] = $shop_data_record[0]['brand_logo'];
                            }
                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['brand_name'] = $shop_data_record[0]['brand_name'];

                            //$cat_arr[$i][$shop_data_record[0]['brand_id']]['shop_affiliate_url'] = $shop_data_record[0]['urlAffiliate'];

                            $cat_arr[$i][$shop_data_record[0]['brand_id']]['rating_percentage'] = $homepageController->ratingAction($shopId, $shopHistoryId);
                            $voucherFinal = $brandController->getVoucherByShopId($shop->getId(), $shop->getVprogram(), $connection);
                            if (count($voucherFinal) != 0) {
                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['voucher_id'] = $voucherFinal[0]['voucher_id'];
                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['voucher_code'] = $voucherFinal[0]['voucher_code'];
                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['voucher_name'] = $voucherFinal[0]['voucher_name'];
                               

                                if(strtotime($voucherFinal[0]['voucher_expire_date']) > strtotime('-30 days')) {
                                     $date = strtotime($voucherFinal[0]['voucher_expire_date']);
                                    $dat = date('d/m/y', $date);
                                 } else {
                                    $dat = "";
                                }


                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['voucher_expire_date'] = $dat;
                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['discount_amount'] = $voucherFinal[0]['discount_amount'];
                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['is_percentage'] = $voucherFinal[0]['is_percentage'];
                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['exclusive'] = $voucherFinal[0]['exclusive'];
                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['short_description'] = $voucherFinal[0]['short_description'];
                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['default_track_uri'] = $voucherFinal[0]['default_track_uri'];
                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['description'] = $voucherFinal[0]['description'];
                                $cat_arr[$i][$shop_data_record[0]['brand_id']]['voucher_program_name'] = $shop->getVprogram()->getProgramName();
                            }
                        }
                    }
                    // end if condition for cashback

                } 
                elseif ((isset($shopId) && $shopdata['offers'] == 'offer') || (isset($shopId) && $shop_data_record[0]['exclusive'] == 1 && $shopdata['offers'] == 'voucher')) {
                    // checking offer type voucher

                    $shop_history = $sm->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('shop' => $shopId), array('startDate' => 'DESC'), 1);
                    $shopHistoryId = $shop_history->getId();
                    $voucher_count = $this->getVoucherCountByShopId($shopId, $connection);
                    $cat_arr[$i][$shopHistoryId]['voucher_count'] = 0;
                    if ($voucher_count) {
                        $cat_arr[$i][$shopHistoryId]['voucher_count'] = count($voucher_count);
                    }
                    $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $shopHistoryId));
                    if ($slug) {
                        $cat_arr[$i][$shopHistoryId]['slug_name'] = $slug->getSlugName();
                    } else {
                        $cat_arr[$i][$shopHistoryId]['slug_name'] = '';
                    }

                    $cat_arr[$i][$shopHistoryId]['shop_offers'] = $shopdata['offers'];
                    $cat_arr[$i][$shopHistoryId]['shop_history_id'] = $shopHistoryId;
                    $cat_arr[$i][$shopHistoryId]['shop_history_shop_title'] = $shop_history->getTitle();
                    $cat_arr[$i][$shopHistoryId]['shop_history_shop_description'] = strip_tags($shop_history->getIntroduction());
                   // $cat_arr[$i][$shopHistoryId]['shop_history_shop_end_date'] = $shop_history->getEndDate();
                    $cat_arr[$i][$shopHistoryId]['shop_terms'] = $shop_history->getTearms();
                    //Actual cashback instead of letsBonusPercentage
                    $cat_arr[$i][$shopHistoryId]['max_letsBonusPercentage'] = 0;
                    $cashbackPrice = $shop_history->getCashbackPrice();
                    $cashbackPercentage = $shop_history->getCashbackPercentage();
                    if ($cashbackPrice > 0) {
                        $cat_arr[$i][$shopHistoryId]['max_letsBonusPercentage'] = $cashbackPrice.'€'; //TO-DO :: Update dynamic currency symbol
                    } elseif ($cashbackPercentage > 0) {
                        $cat_arr[$i][$shopHistoryId]['max_letsBonusPercentage'] = $cashbackPercentage.'%';
                    }
                    $cat_arr[$i][$shopHistoryId]['shop_image'] = $this->getMediaUrlByShopId($shopId, 'reference', $sm);
                    $cat_arr[$i][$shopHistoryId]['top_shop_image'] = $this->getMediaForHighLineTab($shopId, 'default_highline_offer_image', $sm);
                    $cat_arr[$i][$shopHistoryId]['voucher_id'] = $shop_data_record[0]['id'];
                    $cat_arr[$i][$shopHistoryId]['voucher_name'] = $shop_data_record[0]['title'];
                    $cat_arr[$i][$shopHistoryId]['shop_id'] = $shopId;
                    $cat_arr[$i][$shopHistoryId]['discount_amount'] = $shop_data_record[0]['discount_amount'];
                    $cat_arr[$i][$shopHistoryId]['is_percentage'] = $shop_data_record[0]['is_percentage'];
                    $cat_arr[$i][$shopHistoryId]['short_description'] = strip_tags($shop_data_record[0]['short_description']);
                   

                     if(strtotime($shop_data_record[0]['publish_end_date']) > strtotime('-30 days')) {
                                 $date = strtotime($shop_data_record[0]['publish_end_date']);
                                $dat = date('d/m/y', $date);
                             }
                            else
                            {
                                $dat = "";
                            }

                    $cat_arr[$i][$shopHistoryId]['voucher_expire_date'] = $dat;
                    $cat_arr[$i][$shopHistoryId]['exclusive'] = $shop_data_record[0]['exclusive'];
                    $cat_arr[$i][$shopHistoryId]['logo_image'] = $shop_data_record[0]['brand_logo'];
                    if (!empty($shop_data_record[0]['brand_image'])) {
                        $cat_arr[$i][$shopHistoryId]['logo_image'] = $this->getImageUrl($voucherProgramsEntity, $shop_data_record[0]['brand_image'], 'brand_on_shop');
                    }
                    if (!empty($shop_data_record[0]['brand_popup_image'])) {
                        $cat_arr[$i][$shopHistoryId]['brand_logo_popup'] = $this->getImageUrl($voucherProgramsEntity, $shop_data_record[0]['brand_popup_image'], 'cashback_voucher_popup');
                    } else {
                        $cat_arr[$i][$shopHistoryId]['brand_logo_popup'] = $shop_data_record[0]['brand_logo'];
                    }
                    $cat_arr[$i][$shopHistoryId]['brand_name'] = $shop_data_record[0]['brand_name'];
                    $cat_arr[$i][$shopHistoryId]['shop_affiliate_url'] = $shop_data_record[0]['urlAffiliate'];
                    $cat_arr[$i][$shopHistoryId]['rating_percentage'] = $homepageController->ratingAction($shopId, $shopHistoryId);
                    $voucherFinal = $brandController->getVoucherByShopId($shop->getId(), $shop->getVprogram(), $connection);
                    if (count($voucherFinal) != 0) {
                        $cat_arr[$i][$shopHistoryId]['voucher_id'] = $voucherFinal[0]['voucher_id'];
                        $cat_arr[$i][$shopHistoryId]['voucher_code'] = $voucherFinal[0]['voucher_code'];
                        $cat_arr[$i][$shopHistoryId]['voucher_name'] = $voucherFinal[0]['voucher_name'];
                        

                        if(strtotime($voucherFinal[0]['voucher_expire_date']) > strtotime('-30 days')) {
                                $date = strtotime($voucherFinal[0]['voucher_expire_date']);
                                $dat = date('d/m/y', $date);
                         }
                        else
                        {
                            $dat = "";
                        }

                        $cat_arr[$i][$shopHistoryId]['voucher_expire_date'] = $dat;
                        $cat_arr[$i][$shopHistoryId]['discount_amount'] = $voucherFinal[0]['discount_amount'];
                        $cat_arr[$i][$shopHistoryId]['is_percentage'] = $voucherFinal[0]['is_percentage'];
                        $cat_arr[$i][$shopHistoryId]['exclusive'] = $voucherFinal[0]['exclusive'];
                        $cat_arr[$i][$shopHistoryId]['short_description'] = $voucherFinal[0]['short_description'];
                        $cat_arr[$i][$shopHistoryId]['default_track_uri'] = $voucherFinal[0]['default_track_uri'];
                        $cat_arr[$i][$shopHistoryId]['description'] = $voucherFinal[0]['description'];
                        $cat_arr[$i][$shopHistoryId]['voucher_program_name'] = $shop->getVprogram()->getProgramName();
                    }
                }
                // end elseif checking offer type voucher
                ++$i;
            }
            if (!empty($session->get('user_id'))) {
                $userAddToFav = $homepageController->addtofevlistAction();
            } else {
                $userAddToFav = array();
            }
        }
      
        $tiendas_details = $this->getFilter($cat_arr);
        $max_voucher = $this->getMaxVoucherFilter($tiendas_details);
        $max_cashback = $this->getMaxCashbackFilter($tiendas_details);
        $max_voucher_cashback = $this->getMaxVoucherCashback($tiendas_details);
        $cashback = $this->getCahbackOfferFilter($tiendas_details);
        $product = $this->getProductOfferFilter($tiendas_details);
        $voucher = $this->getVoucherOfferFilter($tiendas_details);

        $product_count = count($tiendas_details);
        $init_count = 12;
        $final_count = count($tiendas_details);
        $remove_load_more = 0;

        $tiendasFilterController = new TiendasFilterController();
        $tiendasFilterController->setContainer($this->container);
        if($request->get('offer')){
            $offerFilter=$request->get('offer');
            $filterNavigationCounter = $tiendasFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$tiendas_details,$request);
            $offerFilterData = $filterNavigationCounter['offerFilterData'];
            $tiendas_details = $filterNavigationCounter['data_details'];
            $remove_load_more = $filterNavigationCounter['remove_load_more'];
            $target_count = $filterNavigationCounter['target_count'];
            if($request->get('alphabet')){
                $alphabetFilter=$request->get('alphabet');
                $filterNavigationCounter = $tiendasFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$offerFilterData,$request);
                $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                $tiendas_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('category_id_string')){
                    $catagoryFilter=$request->get('category_id_string');
                    $filterNavigationCounter = $tiendasFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$alphabetFilterData,$request);
                    $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                    $tiendas_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }elseif($request->get('category_id_string')){
                $catagoryFilter=$request->get('category_id_string');
                $filterNavigationCounter = $tiendasFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$offerFilterData,$request);
                $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                $tiendas_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('alphabet')){
                    $alphabetFilter=$request->get('alphabet');
                    $filterNavigationCounter = $tiendasFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$catagoryFilterData,$request);
                    $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                    $tiendas_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }
            return $this->filterTiendasPage($tiendas_details, $userAddToFav, $category_image['cat_image'], $cashback, $product, $voucher, $max_voucher[0], $max_cashback[0], $max_voucher_cashback[0], $tiendaTopBanner,$tiendasBannerTitle,$tiendasBannerDescription, $final_count, $init_count, $target_count, $remove_load_more, $request->get('offer'), $request->get('alphabet'), $request->get('category_id_string'), $product_count);
        }elseif($request->get('alphabet')){
            $alphabetFilter=$request->get('alphabet');
            $filterNavigationCounter = $tiendasFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$tiendas_details,$request);
            $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
            $tiendas_details = $filterNavigationCounter['data_details'];
            $remove_load_more = $filterNavigationCounter['remove_load_more'];
            $target_count = $filterNavigationCounter['target_count'];
            if($request->get('offer')){
                $offerFilter=$request->get('offer');
                $filterNavigationCounter = $tiendasFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$alphabetFilterData,$request);
                $offerFilterData = $filterNavigationCounter['offerFilterData'];
                $tiendas_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('category_id_string')){
                    $catagoryFilter=$request->get('category_id_string');
                    $filterNavigationCounter = $tiendasFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$offerFilterData,$request);
                    $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                    $tiendas_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }elseif($request->get('category_id_string')){
                $catagoryFilter=$request->get('category_id_string');
                $filterNavigationCounter = $tiendasFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$alphabetFilterData,$request);
                $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                $tiendas_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('offer')){
                    $offerFilter=$request->get('offer');
                    $filterNavigationCounter = $tiendasFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$catagoryFilterData,$request);
                    $offerFilterData = $filterNavigationCounter['offerFilterData'];
                    $tiendas_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }
           return $this->filterTiendasPage($tiendas_details, $userAddToFav, $category_image['cat_image'], $cashback, $product, $voucher, $max_voucher[0], $max_cashback[0], $max_voucher_cashback[0], $tiendaTopBanner,$tiendasBannerTitle,$tiendasBannerDescription, $final_count, $init_count, $target_count, $remove_load_more, $request->get('offer'), $request->get('alphabet'), $request->get('category_id_string'), $product_count);
        }elseif($request->get('category_id_string')){
            $catagoryFilter=$request->get('category_id_string');
            $filterNavigationCounter = $tiendasFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$tiendas_details,$request);
            $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
            $tiendas_details = $filterNavigationCounter['data_details'];
            $remove_load_more = $filterNavigationCounter['remove_load_more'];
            $target_count = $filterNavigationCounter['target_count'];
            if($request->get('offer')){
                $offerFilter=$request->get('offer');
                $filterNavigationCounter = $tiendasFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$catagoryFilterData,$request);
                $offerFilterData = $filterNavigationCounter['offerFilterData'];
                $tiendas_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('alphabet')){
                    $alphabetFilter=$request->get('alphabet');
                    $filterNavigationCounter = $tiendasFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$offerFilterData,$request);
                    $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                    $tiendas_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }elseif($request->get('alphabet')){
                $alphabetFilter=$request->get('alphabet');
                $filterNavigationCounter = $tiendasFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$catagoryFilterData,$request);
                $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                $tiendas_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('offer')){
                    $offerFilter=$request->get('offer');
                    $filterNavigationCounter = $tiendasFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$alphabetFilterData,$request);
                    $offerFilterData = $filterNavigationCounter['offerFilterData'];
                    $tiendas_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }
           return $this->filterTiendasPage($tiendas_details, $userAddToFav, $category_image['cat_image'], $cashback, $product, $voucher, $max_voucher[0], $max_cashback[0], $max_voucher_cashback[0], $tiendaTopBanner,$tiendasBannerTitle,$tiendasBannerDescription, $final_count, $init_count, $target_count, $remove_load_more, $request->get('offer'), $request->get('alphabet'), $request->get('category_id_string'), $product_count);
        }else {
            if ($request->get('target_count')) {
                $target_count = $request->get('target_count');
                if ($request->get('alphabet') == 'TODAS') {
                    $target_count = 1;
                }
                $filterNavigationCounter = $tiendasFilterController->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$tiendas_details);
                $tiendas_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                return $this->filterTiendasPage($tiendas_details, $userAddToFav, $category_image['cat_image'], $cashback, $product, $voucher, $max_voucher[0], $max_cashback[0], $max_voucher_cashback[0], $tiendaTopBanner,$tiendasBannerTitle,$tiendasBannerDescription, $final_count, $init_count, $target_count, $remove_load_more, $request->get('offer'), $request->get('alphabet'), $request->get('category_id_string'), $product_count);
            } else {
                $target_count = $execute_count = 1;
                $filterNavigationCounter = $tiendasFilterController->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$tiendas_details);
                 
                $tiendas_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                return $this->renderTiendasPage($tiendas_details, $userAddToFav, $category_image['cat_image'], $cashback, $product, $voucher, $max_voucher[0], $max_cashback[0], $max_voucher_cashback[0], $tiendaTopBanner,$tiendasBannerTitle,$tiendasBannerDescription, $final_count, $init_count, $execute_count, $target_count, $remove_load_more);
            }
        }
    }

     public function filterTiendasPage($tiendas_details,$userAddToFav,$category_image,$cashback,$product,$voucher,$max_voucher,$max_cashback,$max_voucher_cashback,$tiendaTopBanner,$tiendasBannerTitle,$tiendasBannerDescription,$final_count,$init_count,$target_count,$remove_load_more,$OF,$AF,$CF,$product_count){
        $render_data = array(
            'category_detail' => $tiendas_details,
            'base_category_detail' => $tiendas_details,
            'addtofevlist' => $userAddToFav,
            'cat_image' => $category_image['cat_image'],
            'cashback_shop' => $cashback,
            'product_shop' => $product,
            'voucher_shop' => $voucher,
            'max_voucher_data' => $max_voucher,
            'max_cashback_percentage' => $max_cashback,
            'max_voucher_cashback' => $max_voucher_cashback,
            'cupones_top_banner' => $tiendaTopBanner,
            'tiendasTitle' => $tiendasBannerTitle,
            'tiendasBannerDescription' => $tiendasBannerDescription,
            'final_count' => $final_count,
            'init_count' => $init_count,
            'target_count' => $target_count,
            'target_count_category_filter' => $target_count,
            'remove_load_more' => $remove_load_more,
            'OF' => json_encode($OF),
            'AF' => $AF,
            'alphabet' => $AF,
            'CF' => $CF
        );
        $arr = array('product_count'=>$product_count,'html' => $this->render('iFlairLetsBonusFrontBundle:Tiendas:tiendas-page-loadmore.html.twig',$render_data)->getContent());
        return new Response(json_encode($arr));
    }

    public function renderTiendasPage($tiendas_details,$userAddToFav,$category_image,$cashback,$product,$voucher,$max_voucher,$max_cashback,$max_voucher_cashback,$tiendaTopBanner,$tiendasBannerTitle,$tiendasBannerDescription,$final_count,$init_count,$execute_count,$target_count,$remove_load_more){

        $render_data = array(
            'category_detail' => $tiendas_details,
            'base_category_detail' => $tiendas_details,
            'addtofevlist' => $userAddToFav,
            'cat_image' => $category_image['cat_image'],
            'cashback_shop' => $cashback,
            'product_shop' => $product,
            'voucher_shop' => $voucher,
            'max_voucher_data' => $max_voucher,
            'max_cashback_percentage' => $max_cashback,
            'max_voucher_cashback' => $max_voucher_cashback,
            'cupones_top_banner' => $tiendaTopBanner,
            'tiendasTitle' => $tiendasBannerTitle,
            'tiendasBannerDescription' => $tiendasBannerDescription,
            'execute_count' => $execute_count,
            'final_count' => $final_count,
            'init_count' => $init_count,
            'target_count' => $target_count,
            'remove_load_more' => $remove_load_more,
            'alphabet' => '',
        );
        return $this->render('iFlairLetsBonusFrontBundle:Tiendas:tiendas-page.html.twig', $render_data);
    }

    public function getImageUrl($entity, $imageId, $imageType = 'preview')
    {
        $fieldName = 'image';
        if ($imageType == 'brand_on_shop') {
            $fieldName = 'image';
        }
        if ($imageType == 'cashback_voucher_popup') {
            $fieldName = 'popUpImage';
        }
        $media = $entity->findOneBy(array($fieldName => $imageId));
        $imageUrl = '';
        if (!empty($media) && !empty($imageId)) {
            if ($imageType == 'brand_on_shop') {
                $media = $media->getImage();
            }
            if ($imageType == 'cashback_voucher_popup') {
                $media = $media->getPopUpImage();
            }
            $mediaManager = $this->get('sonata.media.pool');
            $provider = $mediaManager->getProvider($media->getProviderName());
            $format = $provider->getFormatName($media, $imageType);
            $imageUrl = $provider->generatePublicUrl($media, $format);
        }

        return $imageUrl;
    }

    public function tiendasLetterAction($letter, $sm, $connection,$request)
    {
        $id = '';
        $userAddToFav = array();

        $shop_id = '';
        $tiendasBannerTitle = '';
        $tiendasBannerDescription = '';
        $session = $this->getRequest()->getSession();
        $cat_arr = array();

        $category_image['cat_image'] = $this->getMediaUrlByCategoryId($shop_id, 'default_list_page_type', $sm);
        $shop_data = $this->getShopOffersByCategoryId($shop_id, $connection);

        $connection = $sm->getConnection();
        $bannerFromCategoryController = new CategoryController();
        $bannerFromCategoryController->setContainer($this->container);
        $tiendaTopBanner = $bannerFromCategoryController->getTopBanner('default_list_page_type', Settings::TIENDASTOPBANNER, $sm);

        $tiendaBanner = $bannerFromCategoryController->getBannerTitleDescription(Settings::TIENDASTOPBANNER, $connection);

        if (isset($tiendaBanner[0]['bannertitle']) && isset($tiendaBanner[0]['bannerdescription'])) {
            $tiendasBannerTitle = $tiendaBanner[0]['bannertitle'];
            $tiendasBannerDescription = $tiendaBanner[0]['bannerdescription'];
        }

        $homepageController = new HomepageController();
        $homepageController->setContainer($this->container);
        $affiliationArgs = new DefaultController();
        $affiliationArgs->setContainer($this->container);
        $brandController = new BrandController();
        $brandController->setContainer($this->container);
        if (!empty($shop_data)) {
            $i = 0;
            $voucherProgramsEntity = $sm->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms');
            foreach ($shop_data as $key => $shopdata) {
                $shopId = $shopdata['shop_id'];
                $shop_data_record = $this->getShopDetailsByCategoryId($shopId, $connection);
                /*
                 * FOR CASHBACK :: CLICKS :: AFFILIATION MANAGEMENT
                 */
                $shopRepository = $sm->getRepository('iFlairLetsBonusAdminBundle:Shop');
                $shop = $shopRepository->findOneBy(array(
                    'id' => $shopId,
                    'shopStatus' => Shop::SHOP_ACTIVATED,
                ));
                // if condition for cashback && voucher is not exclusive
               
                if ((isset($shopId) && $shopdata['offers'] == 'cashback') ||
                    (isset($shopId) && $shop_data_record[0]['exclusive'] == 0 && $shopdata['offers'] == 'voucher')) 
                        {
                    $shop_history = $sm->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findBy(array('shop' => $shopId), array('startDate'=>'DESC'), 1);
                    foreach ($shop_history as $key => $shop_value) {
                        $shopHistory = $shop_value;
                        $shopHistoryId = $shop_value->getId();
                        $affiliationUrlArgs = $affiliationArgs->getAffiliation($shop, $shopHistory, $sm);

                        if (!empty($shop_value->getUrlAffiliate())) {
                            $cat_arr[$i][$shopHistoryId]['shop_affiliate_url'] = $shop_value->getUrlAffiliate().$affiliationUrlArgs;
                        }
                        $voucher_count = $this->getVoucherCountByShopId($shopId, $connection);
                        $cat_arr[$i][$shopHistoryId]['voucher_count'] = 0;
                        if ($voucher_count) {
                            $cat_arr[$i][$shopHistoryId]['voucher_count'] = count($voucher_count);
                        }
                        $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $shopHistoryId));
                        if ($slug) {
                            $cat_arr[$i][$shopHistoryId]['slug_name'] = $slug->getSlugName();
                        } else {
                            $cat_arr[$i][$shopHistoryId]['slug_name'] = '';
                        }

                        $cat_arr[$i][$shopHistoryId]['cashback_type_value'] = $this->getCashbackSettingsByShopId($shopId, $connection);
                        $cat_arr[$i][$shopHistoryId]['shop_offers'] = $shopdata['offers'];
                        $cat_arr[$i][$shopHistoryId]['shop_history_id'] = $shopHistoryId;
                        $cat_arr[$i][$shopHistoryId]['shop_history_shop_title'] = $shop_value->getTitle();
                        $cat_arr[$i][$shopHistoryId]['shop_history_shop_description'] = strip_tags($shop_value->getIntroduction());
                        //$cat_arr[$i][$shopHistoryId]['shop_history_shop_end_date'] = $shop_value->getEndDate();
                        $cat_arr[$i][$shopHistoryId]['shop_terms'] = $shop_value->getTearms();
                        //Actual cashback instead of letsBonusPercentage
                        $cat_arr[$i][$shopHistoryId]['max_letsBonusPercentage'] = 0;
                        $cashbackPrice = $shop_value->getCashbackPrice();
                        $cashbackPercentage = $shop_value->getCashbackPercentage();
                        if ($cashbackPrice > 0) {
                            $cat_arr[$i][$shopHistoryId]['max_letsBonusPercentage'] = $cashbackPrice.'€'; //TO-DO :: Update dynamic currency symbol
                        } elseif ($cashbackPercentage > 0) {
                            $cat_arr[$i][$shopHistoryId]['max_letsBonusPercentage'] = $cashbackPercentage.'%';
                        }
                        $cat_arr[$i][$shopHistoryId]['shop_image'] = $this->getMediaUrlByShopId($shopId, 'reference', $sm);
                        $cat_arr[$i][$shopHistoryId]['top_shop_image'] = $this->getMediaForHighLineTab($shopId, 'default_highline_offer_image', $sm);
                        $cat_arr[$i][$shopHistoryId]['voucher_id'] = $shop_data_record[0]['id'];
                        $cat_arr[$i][$shopHistoryId]['voucher_name'] = $shop_data_record[0]['title'];
                        $cat_arr[$i][$shopHistoryId]['shop_id'] = $shopId;
                        $cat_arr[$i][$shopHistoryId]['discount_amount'] = $shop_data_record[0]['discount_amount'];
                        $cat_arr[$i][$shopHistoryId]['is_percentage'] = $shop_data_record[0]['is_percentage'];
                        $cat_arr[$i][$shopHistoryId]['short_description'] = strip_tags($shop_data_record[0]['short_description']);
                      
                        if(strtotime($shop_data_record[0]['publish_end_date']) > strtotime('-30 days')) {
                              $date = strtotime($shop_data_record[0]['publish_end_date']);
                                $dat = date('d/m/y', $date);
                         }
                        else
                        {
                            $dat = "";
                        }

                        $cat_arr[$i][$shopHistoryId]['voucher_expire_date'] = $dat;
                        $cat_arr[$i][$shopHistoryId]['exclusive'] = $shop_data_record[0]['exclusive'];
                        $cat_arr[$i][$shopHistoryId]['logo_image'] = $shop_data_record[0]['brand_logo'];
                        $cat_arr[$i][$shopHistoryId]['brand_name'] = $shop_data_record[0]['brand_name'];
                        if (!empty($shop_data_record[0]['brand_popup_image'])) {
                            $cat_arr[$i][$shopHistoryId]['brand_logo_popup'] = $this->getImageUrl($voucherProgramsEntity, $shop_data_record[0]['brand_popup_image'], 'cashback_voucher_popup');
                        } else {
                            $cat_arr[$i][$shopHistoryId]['brand_logo_popup'] = $shop_data_record[0]['brand_logo'];
                        }
                        //$cat_arr[$i][$shopHistoryId]['shop_affiliate_url'] = $shop_data_record[0]['urlAffiliate'];
                        $cat_arr[$i][$shopHistoryId]['rating_percentage'] = $homepageController->ratingAction($shopId, $shopHistoryId);
                        $voucherFinal = $brandController->getVoucherByShopId($shop->getId(), $shop->getVprogram(), $connection);
                        if (count($voucherFinal) != 0) {
                            $cat_arr[$i][$shopHistoryId]['voucher_id'] = $voucherFinal[0]['voucher_id'];
                            $cat_arr[$i][$shopHistoryId]['voucher_code'] = $voucherFinal[0]['voucher_code'];
                            $cat_arr[$i][$shopHistoryId]['voucher_name'] = $voucherFinal[0]['voucher_name'];
                          

                            if(strtotime($voucherFinal[0]['voucher_expire_date']) > strtotime('-30 days')) {
                                $date = strtotime($voucherFinal[0]['voucher_expire_date']);
                                $dat = date('d/m/y', $date);
                             }
                            else
                            {
                                $dat = "";
                            }

                            $cat_arr[$i][$shopHistoryId]['voucher_expire_date'] = $dat;
                            $cat_arr[$i][$shopHistoryId]['discount_amount'] = $voucherFinal[0]['discount_amount'];
                            $cat_arr[$i][$shopHistoryId]['is_percentage'] = $voucherFinal[0]['is_percentage'];
                            $cat_arr[$i][$shopHistoryId]['exclusive'] = $voucherFinal[0]['exclusive'];
                            $cat_arr[$i][$shopHistoryId]['short_description'] = $voucherFinal[0]['short_description'];
                            $cat_arr[$i][$shopHistoryId]['default_track_uri'] = $voucherFinal[0]['default_track_uri'];
                            $cat_arr[$i][$shopHistoryId]['description'] = $voucherFinal[0]['description'];
                            $cat_arr[$i][$shopHistoryId]['voucher_program_name'] = $shop->getVprogram()->getProgramName();
                        }
                    }
                    // end if condition for cashback
                }  elseif ((isset($shopId) && $shopdata['offers'] == 'offer') || (isset($shopId) && $shop_data_record[0]['exclusive'] == 1 && $shopdata['offers'] == 'voucher')) {
                    // checking offer type voucher
                    $shop_history = $sm->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('shop' => $shopId), array('startDate' => 'DESC'), 1);
                    
                    $shopHistoryId = $shop_history->getId();
                    $voucher_count = $this->getVoucherCountByShopId($shopId, $connection);
                    $cat_arr[$i][$shopHistoryId]['voucher_count'] = 0;
                    if ($voucher_count) {
                        $cat_arr[$i][$shopHistoryId]['voucher_count'] = count($voucher_count);
                    }
                    $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $shopHistoryId));
                    if ($slug) {
                        $cat_arr[$i][$shopHistoryId]['slug_name'] = $slug->getSlugName();
                    } else {
                        $cat_arr[$i][$shopHistoryId]['slug_name'] = '';
                    }

                    $cat_arr[$i][$shopHistoryId]['shop_offers'] = $shopdata['offers'];
                    $cat_arr[$i][$shopHistoryId]['shop_history_id'] = $shopHistoryId;
                    $cat_arr[$i][$shopHistoryId]['shop_history_shop_title'] = $shop_history->getTitle();
                    $cat_arr[$i][$shopHistoryId]['shop_history_shop_description'] = strip_tags($shop_history->getIntroduction());
                    //$cat_arr[$i][$shopHistoryId]['shop_history_shop_end_date'] = $shop_history->getEndDate();
                    $cat_arr[$i][$shopHistoryId]['shop_terms'] = $shop_history->getTearms();
                    //Actual cashback instead of letsBonusPercentage
                    $cat_arr[$i][$shopHistoryId]['max_letsBonusPercentage'] = 0;
                    $cashbackPrice = $shop_history->getCashbackPrice();
                    $cashbackPercentage = $shop_history->getCashbackPercentage();
                    if ($cashbackPrice > 0) {
                        $cat_arr[$i][$shopHistoryId]['max_letsBonusPercentage'] = $cashbackPrice.'€'; //TO-DO :: Update dynamic currency symbol
                    } elseif ($cashbackPercentage > 0) {
                        $cat_arr[$i][$shopHistoryId]['max_letsBonusPercentage'] = $cashbackPercentage.'%';
                    }
                    $cat_arr[$i][$shopHistoryId]['shop_image'] = $this->getMediaUrlByShopId($shopId, 'reference', $sm);
                    $cat_arr[$i][$shopHistoryId]['top_shop_image'] = $this->getMediaForHighLineTab($shopId, 'default_highline_offer_image', $sm);
                    $cat_arr[$i][$shopHistoryId]['voucher_id'] = $shop_data_record[0]['id'];
                    $cat_arr[$i][$shopHistoryId]['voucher_name'] = $shop_data_record[0]['title'];
                    $cat_arr[$i][$shopHistoryId]['shop_id'] = $shopId;
                    $cat_arr[$i][$shopHistoryId]['discount_amount'] = $shop_data_record[0]['discount_amount'];
                    $cat_arr[$i][$shopHistoryId]['is_percentage'] = $shop_data_record[0]['is_percentage'];
                    $cat_arr[$i][$shopHistoryId]['short_description'] = strip_tags($shop_data_record[0]['short_description']);
                  
                     if(strtotime($shop_data_record[0]['publish_end_date']) > strtotime('-30 days')) {
                                  $date = strtotime($shop_data_record[0]['publish_end_date']);
                                    $dat = date('d/m/y', $date);

                     }
                    else
                    {
                        $dat = "";
                    }

                    $cat_arr[$i][$shopHistoryId]['voucher_expire_date'] = $dat;
                    $cat_arr[$i][$shopHistoryId]['exclusive'] = $shop_data_record[0]['exclusive'];
                    $cat_arr[$i][$shopHistoryId]['logo_image'] = $shop_data_record[0]['brand_logo'];
                    $cat_arr[$i][$shopHistoryId]['brand_name'] = $shop_data_record[0]['brand_name'];
                    $cat_arr[$i][$shopHistoryId]['shop_affiliate_url'] = $shop_data_record[0]['urlAffiliate'];
                    if (!empty($shop_data_record[0]['brand_popup_image'])) {
                        $cat_arr[$i][$shopHistoryId]['brand_logo_popup'] = $this->getImageUrl($voucherProgramsEntity, $shop_data_record[0]['brand_popup_image'], 'cashback_voucher_popup');
                    } else {
                        $cat_arr[$i][$shopHistoryId]['brand_logo_popup'] = $shop_data_record[0]['brand_logo'];
                    }
                    $cat_arr[$i][$shopHistoryId]['rating_percentage'] = $homepageController->ratingAction($shopId, $shopHistoryId);
                    $voucherFinal = $brandController->getVoucherByShopId($shop->getId(), $shop->getVprogram(), $connection);
                    if (count($voucherFinal) != 0) {
                        $cat_arr[$i][$shopHistoryId]['voucher_id'] = $voucherFinal[0]['voucher_id'];
                        $cat_arr[$i][$shopHistoryId]['voucher_code'] = $voucherFinal[0]['voucher_code'];
                        $cat_arr[$i][$shopHistoryId]['voucher_name'] = $voucherFinal[0]['voucher_name'];
                       
                        if(strtotime($voucherFinal[0]['voucher_expire_date']) > strtotime('-30 days')) {
                                $date = strtotime($voucherFinal[0]['voucher_expire_date']);
                                $dat = date('d/m/y', $date);
                             }
                            else
                            {
                                $dat = "";
                            }

                        $cat_arr[$i][$shopHistoryId]['voucher_expire_date'] = $dat;
                        $cat_arr[$i][$shopHistoryId]['discount_amount'] = $voucherFinal[0]['discount_amount'];
                        $cat_arr[$i][$shopHistoryId]['is_percentage'] = $voucherFinal[0]['is_percentage'];
                        $cat_arr[$i][$shopHistoryId]['exclusive'] = $voucherFinal[0]['exclusive'];
                        $cat_arr[$i][$shopHistoryId]['short_description'] = $voucherFinal[0]['short_description'];
                        $cat_arr[$i][$shopHistoryId]['default_track_uri'] = $voucherFinal[0]['default_track_uri'];
                        $cat_arr[$i][$shopHistoryId]['description'] = $voucherFinal[0]['description'];
                        $cat_arr[$i][$shopHistoryId]['voucher_program_name'] = $shop->getVprogram()->getProgramName();
                    }
                }
                // end elseif checking offer type voucher
                ++$i;
            }
            if (!empty($session->get('user_id'))) {
                $userAddToFav = $homepageController->addtofevlistAction();
            } else {
                $userAddToFav = array();
            }
        }

        $tiendas_details = $this->getFilter($cat_arr);
        $max_voucher = $this->getMaxVoucherFilter($tiendas_details);
        $max_cashback = $this->getMaxCashbackFilter($tiendas_details);
        $max_voucher_cashback = $this->getMaxVoucherCashback($tiendas_details);
        $cashback = $this->getCahbackOfferFilter($tiendas_details);
        $product = $this->getProductOfferFilter($tiendas_details);
        $voucher = $this->getVoucherOfferFilter($tiendas_details);

        $base_tiendas_details = $tiendas_details;
        $filter_data = array();

        if ($letter != null) {
            foreach ($tiendas_details as $key => $value) {
                if ($letter == '0TO9') {
                    foreach (range(0, 9) as $key => $value_digit) {
                        if (0 === strpos($value['shop_history_shop_title'], (string) $value_digit)) {
                            $filter_data[] = $value;
                        }
                    }
                } elseif ($letter == 'TODAS') {
                    $filter_data[] = $value;
                } else {
                    if (0 === strpos($value['shop_history_shop_title'], $letter)) {
                        $filter_data[] = $value;
                    }
                }
            }

            $tiendas_details = $filter_data;
        }

      
        $product_count = count($tiendas_details);
        $init_count = 12;
        $final_count = count($tiendas_details);
        $remove_load_more = 0;

        $tiendasFilterController = new TiendasFilterController();
        $tiendasFilterController->setContainer($this->container);
        if($request->get('offer')){
            $offerFilter=$request->get('offer');
            $filterNavigationCounter = $tiendasFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$tiendas_details,$request);
            $offerFilterData = $filterNavigationCounter['offerFilterData'];
            $tiendas_details = $filterNavigationCounter['data_details'];
            $remove_load_more = $filterNavigationCounter['remove_load_more'];
            $target_count = $filterNavigationCounter['target_count'];
            if($request->get('alphabet')){
                $alphabetFilter=$request->get('alphabet');
                $filterNavigationCounter = $tiendasFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$offerFilterData,$request);
                $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                $tiendas_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('category_id_string')){
                    $catagoryFilter=$request->get('category_id_string');
                    $filterNavigationCounter = $tiendasFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$alphabetFilterData,$request);
                    $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                    $tiendas_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }elseif($request->get('category_id_string')){
                $catagoryFilter=$request->get('category_id_string');
                $filterNavigationCounter = $tiendasFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$offerFilterData,$request);
                $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                $tiendas_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('alphabet')){
                    $alphabetFilter=$request->get('alphabet');
                    $filterNavigationCounter = $tiendasFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$catagoryFilterData,$request);
                    $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                    $tiendas_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }
            return $this->filterTiendasLetterPage($tiendas_details,$base_tiendas_details, $userAddToFav, $category_image['cat_image'], $cashback, $product, $voucher, $max_voucher[0], $max_cashback[0], $max_voucher_cashback[0], $tiendaTopBanner,$tiendasBannerTitle,$tiendasBannerDescription, $final_count, $init_count, $target_count, $remove_load_more, $request->get('offer'), $request->get('alphabet'), $request->get('category_id_string'), $product_count);
        }elseif($request->get('alphabet')){
            $alphabetFilter=$request->get('alphabet');
            $filterNavigationCounter = $tiendasFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$tiendas_details,$request);
            $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
            $tiendas_details = $filterNavigationCounter['data_details'];
            $remove_load_more = $filterNavigationCounter['remove_load_more'];
            $target_count = $filterNavigationCounter['target_count'];
            if($request->get('offer')){
                $offerFilter=$request->get('offer');
                $filterNavigationCounter = $tiendasFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$alphabetFilterData,$request);
                $offerFilterData = $filterNavigationCounter['offerFilterData'];
                $tiendas_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('category_id_string')){
                    $catagoryFilter=$request->get('category_id_string');
                    $filterNavigationCounter = $tiendasFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$offerFilterData,$request);
                    $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                    $tiendas_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }elseif($request->get('category_id_string')){
                $catagoryFilter=$request->get('category_id_string');
                $filterNavigationCounter = $tiendasFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$alphabetFilterData,$request);
                $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                $tiendas_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('offer')){
                    $offerFilter=$request->get('offer');
                    $filterNavigationCounter = $tiendasFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$catagoryFilterData,$request);
                    $offerFilterData = $filterNavigationCounter['offerFilterData'];
                    $tiendas_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }
           return $this->filterTiendasLetterPage($tiendas_details,$base_tiendas_details, $userAddToFav, $category_image['cat_image'], $cashback, $product, $voucher, $max_voucher[0], $max_cashback[0], $max_voucher_cashback[0], $tiendaTopBanner,$tiendasBannerTitle,$tiendasBannerDescription, $final_count, $init_count, $target_count, $remove_load_more, $request->get('offer'), $request->get('alphabet'), $request->get('category_id_string'), $product_count);
        }elseif($request->get('category_id_string')){
            $catagoryFilter=$request->get('category_id_string');
            $filterNavigationCounter = $tiendasFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$tiendas_details,$request);
            $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
            $tiendas_details = $filterNavigationCounter['data_details'];
            $remove_load_more = $filterNavigationCounter['remove_load_more'];
            $target_count = $filterNavigationCounter['target_count'];
            if($request->get('offer')){
                $offerFilter=$request->get('offer');
                $filterNavigationCounter = $tiendasFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$catagoryFilterData,$request);
                $offerFilterData = $filterNavigationCounter['offerFilterData'];
                $tiendas_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('alphabet')){
                    $alphabetFilter=$request->get('alphabet');
                    $filterNavigationCounter = $tiendasFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$offerFilterData,$request);
                    $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                    $tiendas_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }elseif($request->get('alphabet')){
                $alphabetFilter=$request->get('alphabet');
                $filterNavigationCounter = $tiendasFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$catagoryFilterData,$request);
                $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                $tiendas_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('offer')){
                    $offerFilter=$request->get('offer');
                    $filterNavigationCounter = $tiendasFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$alphabetFilterData,$request);
                    $offerFilterData = $filterNavigationCounter['offerFilterData'];
                    $tiendas_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }
           return $this->filterTiendasLetterPage($tiendas_details,$base_tiendas_details, $userAddToFav, $category_image['cat_image'], $cashback, $product, $voucher, $max_voucher[0], $max_cashback[0], $max_voucher_cashback[0], $tiendaTopBanner,$tiendasBannerTitle,$tiendasBannerDescription, $final_count, $init_count, $target_count, $remove_load_more, $request->get('offer'), $request->get('alphabet'), $request->get('category_id_string'), $product_count);
        }else {
            if ($request->get('target_count')) {
                $target_count = $request->get('target_count');
                if ($request->get('alphabet') == 'TODAS') {
                    $target_count = 1;
                }
                $filterNavigationCounter = $tiendasFilterController->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$tiendas_details);
                $tiendas_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                return $this->filterTiendasLetterPage($tiendas_details,$base_tiendas_details, $userAddToFav, $category_image['cat_image'], $cashback, $product, $voucher, $max_voucher[0], $max_cashback[0], $max_voucher_cashback[0], $tiendaTopBanner,$tiendasBannerTitle,$tiendasBannerDescription, $final_count, $init_count, $target_count, $remove_load_more, $request->get('offer'), $request->get('alphabet'), $request->get('category_id_string'), $product_count);
            } else {
                $target_count = $execute_count = 1;
                $filterNavigationCounter = $tiendasFilterController->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$tiendas_details);
                 
                $tiendas_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                return $this->renderTiendasLetterPage($tiendas_details,$base_tiendas_details, $userAddToFav, $category_image['cat_image'], $cashback, $product, $voucher, $max_voucher[0], $max_cashback[0], $max_voucher_cashback[0], $tiendaTopBanner,$tiendasBannerTitle,$tiendasBannerDescription, $final_count, $init_count, $execute_count, $target_count, $remove_load_more);
            }
        }
    }   

     public function filterTiendasLetterPage($tiendas_details,$base_tiendas_details,$userAddToFav,$category_image,$cashback,$product,$voucher,$max_voucher,$max_cashback,$max_voucher_cashback,$tiendaTopBanner,$tiendasBannerTitle,$tiendasBannerDescription,$final_count,$init_count,$target_count,$remove_load_more,$OF,$AF,$CF,$product_count){
        $render_data = array(
            'category_detail' => $tiendas_details,
            'base_category_detail' => $base_tiendas_details,
            'addtofevlist' => $userAddToFav,
            'cat_image' => $category_image['cat_image'],
            'cashback_shop' => $cashback,
            'product_shop' => $product,
            'voucher_shop' => $voucher,
            'max_voucher_data' => $max_voucher,
            'max_cashback_percentage' => $max_cashback,
            'max_voucher_cashback' => $max_voucher_cashback,
            'cupones_top_banner' => $tiendaTopBanner,
            'tiendasTitle' => $tiendasBannerTitle,
            'tiendasBannerDescription' => $tiendasBannerDescription,
            'final_count' => $final_count,
            'init_count' => $init_count,
            'target_count' => $target_count,
            'target_count_category_filter' => $target_count,
            'remove_load_more' => $remove_load_more,
            'OF' => json_encode($OF),
            'AF' => $AF,
            'alphabet' => $AF,
            'CF' => $CF
        );
        $arr = array('product_count'=>$product_count,'html' => $this->render('iFlairLetsBonusFrontBundle:Tiendas:tiendas-page-loadmore.html.twig',$render_data)->getContent());
        return new Response(json_encode($arr));
    }

    public function renderTiendasLetterPage($tiendas_details,$base_tiendas_details,$userAddToFav,$category_image,$cashback,$product,$voucher,$max_voucher,$max_cashback,$max_voucher_cashback,$tiendaTopBanner,$tiendasBannerTitle,$tiendasBannerDescription,$final_count,$init_count,$execute_count,$target_count,$remove_load_more){

        $render_data = array(
            'category_detail' => $tiendas_details,
            'base_category_detail' => $base_tiendas_details,
            'addtofevlist' => $userAddToFav,
            'cat_image' => $category_image['cat_image'],
            'cashback_shop' => $cashback,
            'product_shop' => $product,
            'voucher_shop' => $voucher,
            'max_voucher_data' => $max_voucher,
            'max_cashback_percentage' => $max_cashback,
            'max_voucher_cashback' => $max_voucher_cashback,
            'cupones_top_banner' => $tiendaTopBanner,
            'tiendasTitle' => $tiendasBannerTitle,
            'tiendasBannerDescription' => $tiendasBannerDescription,
            'execute_count' => $execute_count,
            'final_count' => $final_count,
            'init_count' => $init_count,
            'target_count' => $target_count,
            'remove_load_more' => $remove_load_more
        );
        return $this->render('iFlairLetsBonusFrontBundle:Tiendas:tiendas-page.html.twig', $render_data);
    }

}
