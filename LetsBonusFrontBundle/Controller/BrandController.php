<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use iFlair\LetsBonusAdminBundle\Entity\Settings;
use iFlair\LetsBonusAdminBundle\Entity\Shop;
use iFlair\LetsBonusAdminBundle\Entity\Slug;
use iFlair\LetsBonusAdminBundle\Entity\Voucher;
use iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms;
use iFlair\LetsBonusAdminBundle\Slug\Constants;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends Controller
{
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
                                           JOIN lb_shop_voucher s
                                           ON b.id = s.voucher_id
                                           WHERE s.shop_id = :shopid');
        $statement->bindValue('shopid', $shopId);
        $statement->execute();

        return $statement->fetchAll();
    }
    public function getVoucherByShopId($shopId, $voucherProgramId, $connection)
    {
        $final = array();
        $statement = $connection->prepare('SELECT b.id AS voucher_id,
                                                  b.code AS voucher_code,
                                                  b.title AS voucher_name,
                                                  b.publish_end_date AS voucher_expire_date,
                                                  b.discount_amount AS discount_amount,
                                                  b.is_percentage AS is_percentage,
                                                  b.exclusive AS exclusive,
                                                  b.isnew AS isnew,
                                                  b.short_description AS short_description,
                                                  b.default_track_uri AS default_track_uri,
                                                  b.description AS description
                                           FROM lb_voucher b
                                           JOIN lb_shop_voucher s
                                           ON b.id = s.voucher_id
                                           WHERE s.shop_id = :shopid
                                           AND b.isdisplayonfront = :isdisplayonfront
                                           AND b.status = :status
                                           ORDER BY modified
                                           DESC LIMIT 1;');
        $statement->bindValue('shopid', $shopId);
        $statement->bindValue('isdisplayonfront', Voucher::YES);
        $statement->bindValue('status', Voucher::YES);
        $statement->execute();
        $final = $statement->fetchAll();
        if (count($final) == 0) {
            $statement = $connection->prepare('SELECT b.id AS voucher_id,
                                                      b.code AS voucher_code,
                                                      b.title AS voucher_name,
                                                      b.publish_end_date AS voucher_expire_date,
                                                      b.discount_amount AS discount_amount,
                                                      b.is_percentage AS is_percentage,
                                                      b.exclusive AS exclusive,
                                                      b.isnew AS isnew,
                                                      b.short_description AS short_description,
                                                      b.default_track_uri AS default_track_uri,
                                                      b.description AS description
                                               FROM lb_voucher b
                                               JOIN lb_shop_voucher s
                                               ON b.id = s.voucher_id
                                               WHERE s.shop_id = :shopid
                                               AND b.status = :status
                                               ORDER BY modified
                                               DESC LIMIT 1;');
            $statement->bindValue('shopid', $shopId);
            $statement->bindValue('status', Voucher::YES);
            $statement->execute();
            $final = $statement->fetchAll();
            if (count($final) == 0) {
                $statement = $connection->prepare('SELECT b.id AS voucher_id,
                                                          b.code AS voucher_code,
                                                          b.title AS voucher_name,
                                                          b.publish_end_date AS voucher_expire_date,
                                                          b.discount_amount AS discount_amount,
                                                          b.is_percentage AS is_percentage,
                                                          b.exclusive AS exclusive,
                                                          b.isnew AS isnew,
                                                          b.short_description AS short_description,
                                                          b.default_track_uri AS default_track_uri,
                                                          b.description AS description
                                                   FROM lb_voucher b
                                                   WHERE b.program_id = :voucherProgramId
                                                   AND b.isdisplayonfront = :isdisplayonfront
                                                   AND b.status = :status
                                                   ORDER BY modified
                                                   DESC LIMIT 1;');
                $statement->bindValue('voucherProgramId', $voucherProgramId);
                $statement->bindValue('isdisplayonfront', Voucher::YES);
                $statement->bindValue('status', Voucher::YES);
                $final = $statement->fetchAll();
                if (count($final) == 0) {
                    $statement = $connection->prepare('SELECT b.id AS voucher_id,
                                                              b.code AS voucher_code,
                                                              b.title AS voucher_name,
                                                              b.publish_end_date AS voucher_expire_date,
                                                              b.discount_amount AS discount_amount,
                                                              b.is_percentage AS is_percentage,
                                                              b.exclusive AS exclusive,
                                                              b.isnew AS isnew,
                                                              b.short_description AS short_description,
                                                              b.default_track_uri AS default_track_uri,
                                                              b.description AS description
                                                       FROM lb_voucher b
                                                       WHERE b.program_id = :voucherProgramId
                                                       AND b.status = :status
                                                       ORDER BY modified
                                                       DESC LIMIT 1;');
                    $statement->bindValue('voucherProgramId', $voucherProgramId);
                    $statement->bindValue('status', Voucher::YES);
                    $statement->execute();
                    $final = $statement->fetchAll();
                }
            }
        }

        return $final;
    }
    public function getCashbackSettingsByShopId($shopId, $connection)
    {
        $query = $connection->prepare('SELECT cs.type AS cashback_type
                                       FROM lb_cashbackSettings AS cs
                                       JOIN lb_cachback_settings_shop AS css ON cs.id = css.cashback_settings_id
                                       JOIN lb_shop AS s ON s.id = css.shop_id
                                       WHERE s.id = :id AND s.shopStatus = :shopStatus AND cs.status = 1');
        $query->bindValue('id', $shopId);
        $query->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        $query->execute();
        $cashback_type = $query->fetchAll();
        if (!empty($cashback_type)) {
            $cashback_type = $cashback_type[0]['cashback_type'];
        } else {
            $cashback_type = '';
        }

        return $cashback_type;
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
    public function brandDetailAction(Request $request, $slug_name = null)
    {
        $em = $this->getDoctrine()->getManager();
        /*
         *  NOTE    :: Its an common method to show top banner for all sections
         *  DEFINED IN  ::  CategoryController
         *  ARGS    :: type_of_media_image, code_of_banner_for_any_section, em
         */
        $cashbackBannerTitle = '';
        $cashbackBannerDescription = '';
        $cuponesBannerTitle = '';
        $cuponesBannerDescription = '';
        $connection = $em->getConnection();
        $bannerFromCategoryController = new CategoryController();
        $bannerFromCategoryController->setContainer($this->container);
        $cashbackTopBanner = $bannerFromCategoryController->getTopBanner('default_list_page_type', Settings::CASHBACKTOPBANNER, $em);
        $cuponesTopBanner = $bannerFromCategoryController->getTopBanner('default_list_page_type', Settings::CUPONESTOPBANNER, $em);

        $cashbackBanner = $bannerFromCategoryController->getBannerTitleDescription(Settings::CASHBACKTOPBANNER, $connection);
        $cuponesBanner = $bannerFromCategoryController->getBannerTitleDescription(Settings::CUPONESTOPBANNER, $connection);

        if (isset($cashbackBanner[0]['bannertitle']) && isset($cashbackBanner[0]['bannerdescription'])) {
            $cashbackBannerTitle = $cashbackBanner[0]['bannertitle'];
            $cashbackBannerDescription = $cashbackBanner[0]['bannerdescription'];
        }
        if (isset($cuponesBanner[0]['bannertitle']) && isset($cuponesBanner[0]['bannerdescription'])) {
            $cuponesBannerTitle = $cuponesBanner[0]['bannertitle'];
            $cuponesBannerDescription = $cuponesBanner[0]['bannerdescription'];
        }

        $session = $this->getRequest()->getSession();
        $network_id = '';
        $homepageController = new HomepageController();
        $homepageController->setContainer($this->container);

        $affiliationArgs = new DefaultController();
        $affiliationArgs->setContainer($this->container);

        $brand_image['brand_image'] = '';

       /* $entities = $em->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms');
        $pdata = $entities->findAll();*/
        $category_id = '';
        $category_type = '';
        $cat_name = '';
        $cat_id = '';
        $voucherProgramsEntity = $em->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms');
        if ($slug_name != null) {
            $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('slugName' => $slug_name));
            if ($slug) {
                $category_id = $slug->getCategoryId();
                $category_type = $slug->getCategoryType();
            }
        }
        if (!empty($category_type) && !empty($category_id)) {
            if ($category_type == Constants::PARENT_CATEGORY_IDENTIFIER) {
                $CategoryRepository = $em->getRepository('iFlairLetsBonusAdminBundle:parentCategory');
            } elseif ($category_type == Constants::MIDDLE_CATEGORY_IDENTIFIER) {
                $CategoryRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Category');
            } elseif ($category_type == Constants::CHILD_CATEGORY_IDENTIFIER) {
                $CategoryRepository = $em->getRepository('iFlairLetsBonusAdminBundle:childCategory');
            }

            $Category = $CategoryRepository->findOneBy(array('id' => $category_id, 'status' => 1));
            if ($Category) {
                $cat_id = $Category->getId();
                $cat_name = $Category->getName();
            }
        }

        $currentRoute = $request->attributes->get('_route');        

        $i = 0;
        $max_letsBonusPercentage = array();        
        if ($currentRoute == 'i_flair_lets_bonus_front_brand_cashback_category' || $currentRoute == 'i_flair_lets_bonus_front_brand_cashback') {

            $query = $connection->prepare('SELECT s.*, 
                      s.id AS shop, v.id AS voucher_id, v.code AS voucher_code, v.title AS voucher_name, v.publish_end_date AS voucher_expire_date, v.discount_amount AS voucher_discount_amount, v.is_percentage AS voucher_is_percentage, v.exclusive AS voucher_exclusive, v.isnew AS voucher_isnew, v.short_description AS voucher_short_description, v.default_track_uri AS voucher_default_track_uri, v.description AS voucher_description,
                      s.image_id AS shop_image, 
                      s.offers AS offers, 
                      sh.id AS shop_history_id,
                      sh.title AS shop_history_title, 
                      sh.tearms AS shop_terms, 
                      sh.cashbackPercentage as cashbackPercentage,
                      sh.cashbackPrice as cashbackPrice,
                      sh.introduction  AS shop_history_shop_description, 
                      s.endDate AS shop_expiry_date, 
                      vp.program_name AS voucher_names, 
                      v.*,
                      vp.image_id AS brand_logo,
                      vp.pop_up_image_id AS brand_logo_popup,
                      vp.logo_path AS default_logo_path, 
                      vp.id AS brand_id 
                      FROM lb_shop_history as sh JOIN lb_shop as s ON sh.shop = s.id JOIN lb_slug AS sl ON sl.categoryId = sh.id AND sl.categoryType = :slugType JOIN lb_voucher_programs as vp ON vp.id = s.vprogram_id LEFT JOIN lb_voucher AS v ON vp.id = v.program_id WHERE s.shopStatus = :shopStatus GROUP BY sh.shop');
            $query->bindValue('slugType', Constants::SHOP_IDENTIFIER);
            $query->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
            $query->execute();
            $max_letsBonusPercentage = $query->fetchAll();
        } elseif ($currentRoute == 'i_flair_lets_bonus_front_brand_cupones_category' || $currentRoute == 'i_flair_lets_bonus_front_brand_cupones') {            
            $query = $connection->prepare('SELECT v.id AS voucher_id, v.code AS voucher_code, v.title AS voucher_name, v.publish_end_date AS voucher_expire_date, v.discount_amount AS voucher_discount_amount, v.is_percentage AS voucher_is_percentage, v.exclusive AS voucher_exclusive, v.isnew AS voucher_isnew, v.short_description AS voucher_short_description, v.default_track_uri AS voucher_default_track_uri, v.description AS voucher_description, s.*, s.id AS shop, s.image_id AS shop_image, s.offers AS offers, sh.id AS shop_history_id,sh.title AS shop_history_title, sh.tearms AS shop_terms,sh.cashbackPercentage as cashbackPercentage, sh.cashbackPrice as cashbackPrice, sh.introduction  AS shop_history_shop_description, vp.program_name AS voucher_names, v.*,vp.image_id AS brand_logo,vp.pop_up_image_id AS brand_logo_popup,vp.logo_path AS default_logo_path, vp.id AS brand_id FROM lb_shop_voucher AS sv LEFT JOIN lb_shop AS s ON sv.shop_id = s.id LEFT JOIN lb_shop_history AS sh ON sh.shop = s.id JOIN lb_slug AS sl ON sl.categoryId = sh.id AND sl.categoryType = :slugType LEFT JOIN lb_voucher AS v ON sv.voucher_id = v.id LEFT JOIN lb_voucher_programs as vp ON vp.id = s.vprogram_id WHERE s.shopStatus = :shopStatus GROUP BY v.id ORDER BY sh.id DESC, sh.startDate DESC');
              $query->bindValue('slugType', Constants::SHOP_IDENTIFIER);
              $query->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
              $query->execute();
              $max_letsBonusPercentage = $query->fetchAll();
        }

        if (!empty($max_letsBonusPercentage)) {
            foreach ($max_letsBonusPercentage as $key => $cat_value) {
                $shopHistoryId = $cat_value['shop_history_id'];
                $variations = $this->getShopHistoryVariationByShopHistoryId($shopHistoryId, $em);
                $brand_arr[$i]['shop_history_variation'] = $variations;

                $voucher_count = $this->getVoucherCountByShopId($cat_value['shop'], $connection);
                $brand_arr[$i]['voucher_code_count'] = 0;
                if ($voucher_count) {
                    $brand_arr[$i]['voucher_code_count'] = count($voucher_count);
                }

                $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $cat_value['shop_history_id']));

                if ($slug) {
                    $brand_arr[$i]['slug_name'] = $slug->getSlugName();
                } else {
                    $brand_arr[$i]['slug_name'] = '';
                }
                
                $brand_arr[$i]['shop_id'] = $cat_value['shop'];
                $brand_arr[$i]['shop_offers'] = $cat_value['offers'];
                if($cat_value['offers'] === 'cashback' || $cat_value['offers'] === 'cashback/coupons') {
                  $brand_arr[$i]['shop_type'] = 'cashback';
                }
                if($cat_value['offers'] === 'offer') {
                  $brand_arr[$i]['shop_type'] = 'oferta';
                }
                $brand_arr[$i]['shop_image'] = $cat_value['shop_image'];
                $brand_arr[$i]['shop_history_id'] = $cat_value['shop_history_id'];
                $brand_arr[$i]['max_letsBonusPercentage'] = $cat_value['letsBonusPercentage'];
                $brand_arr[$i]['shop_history_shop_title'] = $cat_value['shop_history_title'];
                $brand_arr[$i]['voucher_id'] = $cat_value['id'];
                $brand_arr[$i]['voucher_name'] = $cat_value['voucher_name'];
                $brand_arr[$i]['brand_id'] = $cat_value['brand_id'];

                $brand_arr[$i]['voucher_expire_date'] = '';
                if (isset($cat_value['shop_expiry_date']) && !empty($cat_value['shop_expiry_date'])) {

                    if (strtotime($cat_value['shop_expiry_date']) > strtotime('-30 days')) {
                        $date = strtotime($cat_value['shop_expiry_date']);
                        $dat = date('d/m/y', $date);
                    } else {
                        $dat = '';
                    }

                    $brand_arr[$i]['voucher_expire_date'] = $dat;
                }
                $brand_arr[$i]['voucher_description'] = $cat_value['voucher_description'];
                $brand_arr[$i]['discount_amount'] = $cat_value['discount_amount'];
                $brand_arr[$i]['is_percentage'] = $cat_value['is_percentage'];
               // $brand_arr[$i]['brand_logo'] = $cat_value['brand_logo'];
                $brand_arr[$i]['exclusive'] = $cat_value['exclusive'];
                $brand_arr[$i]['short_description'] = strip_tags($cat_value['short_description']);
                $brand_arr[$i]['description'] = strip_tags($cat_value['description']);
                $brand_arr[$i]['shop_terms'] = $cat_value['shop_terms'];
                
                $cashback_type =  new TiendasController();
                if($cat_value['offers'] === 'cashback') {
                  $brand_arr[$i]['cashback_type_value'] = $cashback_type->getCashbackSettingsByShopId($shopHistoryId, $connection);
                } elseif($cat_value['offers'] === 'voucher') {
                  if($cat_value['voucher_exclusive'] && $cat_value['voucher_isnew']) {
                    $brand_arr[$i]['cashback_type_value'] = ' *Novedad exclusiva';
                  } elseif($cat_value['voucher_exclusive'] && !$cat_value['voucher_isnew']) {
                    $brand_arr[$i]['cashback_type_value'] = '*exclusivo';
                  } elseif(!$cat_value['voucher_exclusive'] && $cat_value['voucher_isnew']) {
                    $brand_arr[$i]['cashback_type_value'] = '*nuevo';
                  }
                } elseif($cat_value['offers'] == 'offer') {
                  $brand_arr[$i]['cashback_type_value'] = '';
                }

                $brand_arr[$i]['rating'] = $homepageController->ratingAction($cat_value['shop'], $cat_value['shop_history_id']);
                $brand_arr[$i]['shop_history_shop_description'] = strip_tags($cat_value['shop_history_shop_description']);
                $brand_arr[$i]['voucher_program_name'] = $cat_value['voucher_name'];

                $brand_arr[$i]['cashbackPrice'] = $cat_value['cashbackPrice'];
                $brand_arr[$i]['cashbackPercentage'] = $cat_value['cashbackPercentage'];

                if (!empty($cat_value['brand_logo'])) {
                    $brand_arr[$i]['brand_logo'] = $this->getImageUrl($voucherProgramsEntity, $cat_value['brand_logo'], 'brand_on_shop');
                } else {
                    $brand_arr[$i]['brand_logo'] = $cat_value['default_logo_path'];
                }

                if (!empty($cat_value['brand_logo_popup'])) {
                    $brand_arr[$i]['brand_logo_popup'] = $this->getImageUrl($voucherProgramsEntity, $cat_value['brand_logo_popup'], 'cashback_voucher_popup');
                } else {
                    $brand_arr[$i]['brand_logo_popup'] = $cat_value['default_logo_path'];
                }

                $entities = $em->getRepository('iFlairLetsBonusAdminBundle:Shop');
                $pdata = $entities->findOneBy(array('id' => $cat_value['shop'], 'shopStatus' => Shop::SHOP_ACTIVATED));

                $shopHistory = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('shop' => $cat_value['shop']));
                $affiliationUrlArgs = $affiliationArgs->getAffiliation($pdata, $shopHistory, $sm);
                $brand_arr[$i]['shop_affiliate_url_origin'] = '';
                $brand_arr[$i]['shop_affiliate_url'] = '';
                if (isset($shopHistory) && !empty($shopHistory)) {
                    if (!empty($shopHistory->getUrlAffiliate())) {
                        $brand_arr[$i]['shop_affiliate_url_origin'] = $shopHistory->getUrlAffiliate();
                        $redirect_url = $shopHistory->getUrlAffiliate().$affiliationUrlArgs;
                        $brand_arr[$i]['shop_affiliate_url'] = $redirect_url;
                    }
                }
                $brand_arr[$i]['program_id'] = $pdata->getProgramId();

                //if($currentRoute == 'i_flair_lets_bonus_front_brand_cashback_category' || $currentRoute == 'i_flair_lets_bonus_front_brand_cashback') {
                if($currentRoute == 'i_flair_lets_bonus_front_brand_cashback_category') {
                    $voucherFinal = $this->getVoucherByShopId($cat_value['shop'], $cat_value['vprogram_id'], $connection);
                    if (count($voucherFinal) != 0) {
                        $brand_arr[$i]['voucher_id'] = $voucherFinal[0]['voucher_id'];
                        if($cat_value['offers'] == 'voucher') {
                          if(isset($voucherFinal[0]['voucher_code']) && !empty($voucherFinal[0]['voucher_code'])) {
                              $brand_arr[$i]['shop_type'] = 'coupon';
                          } else {
                              $brand_arr[$i]['shop_type'] = 'oferta';
                          }
                        }
                        if($cat_value['offers'] == 'offer') {
                              $brand_arr[$i]['shop_type'] = 'oferta';
                        }

                        if($cat_value['offers'] == 'cashback/coupons') {
                          if(isset($voucherFinal[0]['voucher_code']) && !empty($voucherFinal[0]['voucher_code'])) {
                              $brand_arr[$i]['shop_type'] = 'coupon';
                          } else {
                              $brand_arr[$i]['shop_type'] = 'oferta';
                          }
                        }

                        $brand_arr[$i]['voucher_code'] = $voucherFinal[0]['voucher_code'];
                        $brand_arr[$i]['voucher_name'] = $voucherFinal[0]['voucher_name'];


                         if(strtotime($voucherFinal[0]['voucher_expire_date']) > strtotime('-30 days')) 
                         {
                                $date = strtotime($voucherFinal[0]['voucher_expire_date']);
                                $dat = date('d/m/y', $date);
                         }
                        else
                        {
                            $dat = "";
                        }

                        $brand_arr[$i]['voucher_expire_date'] = $dat;
                        $brand_arr[$i]['discount_amount'] = $voucherFinal[0]['discount_amount'];
                        $brand_arr[$i]['is_percentage'] = $voucherFinal[0]['is_percentage'];
                        $brand_arr[$i]['exclusive'] = $voucherFinal[0]['exclusive'];
                        $brand_arr[$i]['short_description'] = $voucherFinal[0]['short_description'];
                        $brand_arr[$i]['description'] = $voucherFinal[0]['description'];
                        $brand_arr[$i]['default_track_uri'] = $voucherFinal[0]['default_track_uri'];
                        $brand_arr[$i]['voucher_program_name'] = $pdata->getVprogram()->getProgramName();
                        $brand_arr[$i]['brand_name'] = $pdata->getVprogram()->getProgramName();
                    }
                } 
                else {
                    $brand_arr[$i]['voucher_id'] = $cat_value['voucher_id'];
                    if($cat_value['offers'] == 'voucher') {
                        if(isset($cat_value['voucher_code']) && !empty($cat_value['voucher_code'])) {
                            $brand_arr[$i]['shop_type'] = 'coupon';
                        } else {
                            $brand_arr[$i]['shop_type'] = 'oferta';
                        }
                    }
                     if($cat_value['offers'] == 'offer') {
                      $brand_arr[$i]['shop_type'] = 'oferta';
                    } else if($cat_value['offers'] == 'cashback/coupons') {
                        if(isset($cat_value['voucher_code']) && !empty($cat_value['voucher_code'])) {
                            $brand_arr[$i]['shop_type'] = 'coupon';
                        } else {
                            $brand_arr[$i]['shop_type'] = 'oferta';
                        }
                    }
                    $brand_arr[$i]['voucher_code'] = $cat_value['voucher_code'];
                    $brand_arr[$i]['voucher_name'] = $cat_value['voucher_name'];
                   

                     if(strtotime($cat_value['voucher_expire_date']) > strtotime('-30 days')) 
                         {
                                $date = strtotime($cat_value['voucher_expire_date']);
                                $dat = date('d/m/y', $date);
                         }
                        else
                        {
                            $dat = "";
                        }


                    $brand_arr[$i]['voucher_expire_date'] = $dat;
                    $brand_arr[$i]['discount_amount'] = $cat_value['voucher_discount_amount'];
                    $brand_arr[$i]['is_percentage'] = $cat_value['voucher_is_percentage'];
                    $brand_arr[$i]['exclusive'] = $cat_value['voucher_exclusive'];
                    $brand_arr[$i]['short_description'] = $cat_value['voucher_short_description'];
                    $brand_arr[$i]['description'] = $cat_value['voucher_description'];
                    $brand_arr[$i]['default_track_uri'] = $cat_value['voucher_default_track_uri'];
                    $brand_arr[$i]['voucher_program_name'] = $pdata->getVprogram()->getProgramName();
                    $brand_arr[$i]['brand_name'] = $pdata->getVprogram()->getProgramName();
                }

                if (!empty($pdata)) {
                    $prod_arr[$i]['shop_id'] = $pdata->getId();
                    if ($pdata->getCategoryImage()) {
                        $media = $pdata->getCategoryImage();
                        $mediaManager = $this->get('sonata.media.pool');
                        $provider = $mediaManager->getProvider($media->getProviderName());
                        $format = $provider->getFormatName($media, 'default_shop');
                        $productpublicUrl = $provider->generatePublicUrl($media, $format);
                        $brand_arr[$i]['shop_image'] = $productpublicUrl;
                      } else {
                        $brand_arr[$i]['shop_image'] = '';
                     }
                     if ($pdata->getHighlineofferImage()) {
                        $media = $pdata->getHighlineofferImage();
                        $mediaManager = $this->get('sonata.media.pool');
                        $provider = $mediaManager->getProvider($media->getProviderName());
                        $format = $provider->getFormatName($media, 'default_highline_offer_image');
                        $productpublicUrl = $provider->generatePublicUrl($media, $format);
                        $brand_arr[$i]['top_shop_image'] = $productpublicUrl;
                    } else {
                        $brand_arr[$i]['top_shop_image'] = '';
                    }
                }
                ++$i;
            }
            if (!empty($session->get('user_id'))) {
                $userAddToFav = $homepageController->addtofevlistAction();
            } else {
                $userAddToFav = array();
            }
        }

        if (!empty($brand_arr)) {
            // for max_voucher 
            $max_voucher = array();
            foreach ($brand_arr as $key => $value) {
                if (!empty($value['shop_id']) && $value['shop_offers'] == 'voucher') {
                    $max_voucher[] = $value;
                }
            }

            if (!empty($max_voucher)) {
                foreach ($max_voucher as $key => $value) {
                    $voucher1[$key] = $value['voucher_code_count'];
                }

                array_multisort($voucher1, SORT_DESC, $max_voucher);
            } else {
                $max_voucher = array();
                $max_voucher[0] = array();
                $max_voucher[0]['voucher_code_count'] = '';
            }
             //End max_voucher 
            // for max_cashback 
            $max_cashback = array();
            foreach ($brand_arr as $key => $value) {
                if (!empty($value['shop_id']) && $value['shop_offers'] == 'cashback') {
                    $max_cashback[] = $value;
                }
            }

            if (!empty($max_cashback)) {
                foreach ($max_cashback as $key => $value) {
                    $cashback1[$key] = $value['max_letsBonusPercentage'];
                }

                array_multisort($cashback1, SORT_DESC, $max_cashback);
            } else {
                $max_cashback = array();
                $max_cashback[0] = array();
            }
            //End max_cashback 
            // for max_voucher_cashback 
             $max_voucher_cashback = array();
            foreach ($brand_arr as $key => $value) {
                if (!empty($value['shop_id']) && $value['shop_offers'] == 'offer') {
                    $max_voucher_cashback[] = $value;
                }
            }

            if (!empty($max_voucher_cashback)) {
                foreach ($max_voucher_cashback as $key => $value) {
                    $offer[$key] = $value['max_letsBonusPercentage'];
                }

                array_multisort($offer, SORT_DESC, $max_voucher_cashback);
            } else {
                $max_voucher_cashback = array();
                $max_voucher_cashback[0] = array();
            }

            //End max_voucher_cashback 
        } else {
            $max_voucher[0] = array();
            $max_cashback[0] = array();
            $max_voucher_cashback[0] = array();
        }

        $cashback = array();
        $product = array();
        $voucher = array();

        /*
         * NOTE :: with removing repeated shops, should becomes more improvable.
         */
        if (!empty($brand_arr)) {
            foreach ($brand_arr as $key => $value) {
                if (!empty($value['shop_id'])) {
                    if ($value['shop_offers'] == 'cashback' || $value['shop_offers'] == 'offer' || $value['shop_offers'] == 'cashback/coupons') {
                        $cashback[$value['shop_id']] = $value;
                    } 
                    if ($value['shop_offers'] == 'voucher' || $value['shop_offers'] == 'offer' || $value['shop_offers'] == 'cashback/coupons') {
                        $voucher[$value['shop_history_id']][$value['voucher_id']]= $value;
                    }
                }
            }
        }
        $cashbackFinal = $cashback;
        $voucherFinal = [];
        foreach ($voucher as $voucherkey => $voucher_value) {
            foreach ($voucher_value as $voucher_valuekey => $voucher_value_value) {

                $voucherFinal[] = $voucher_value_value;
            }
        }
       

        if ($currentRoute == 'i_flair_lets_bonus_front_brand_cashback_category' || $currentRoute == 'i_flair_lets_bonus_front_brand_cashback') {
            $brand_details = $cashbackFinal;
        } elseif ($currentRoute == 'i_flair_lets_bonus_front_brand_cupones_category' || $currentRoute == 'i_flair_lets_bonus_front_brand_cupones') {
            $brand_details = $voucherFinal;
        }

        $product_count = count($brand_details);
        $init_count = 12;
        $final_count = count($brand_details);
        $remove_load_more = 0;

        $categoryFilterController = new CategoryFilterController();
        $categoryFilterController->setContainer($this->container);
        if($request->get('offer')){
            $offerFilter=$request->get('offer');
            $filterNavigationCounter = $categoryFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$brand_details,$request);
            $offerFilterData = $filterNavigationCounter['offerFilterData'];
            $brand_details = $filterNavigationCounter['data_details'];
            $remove_load_more = $filterNavigationCounter['remove_load_more'];
            $target_count = $filterNavigationCounter['target_count'];
            if($request->get('alphabet')){
                $alphabetFilter=$request->get('alphabet');
                $filterNavigationCounter = $categoryFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$offerFilterData,$request);
                $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                $brand_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('category_id_string')){
                    $catagoryFilter=$request->get('category_id_string');
                    $filterNavigationCounter = $categoryFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$alphabetFilterData,$request);
                    $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                    $brand_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }elseif($request->get('category_id_string')){
                $catagoryFilter=$request->get('category_id_string');
                $filterNavigationCounter = $categoryFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$offerFilterData,$request);
                $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                $brand_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('alphabet')){
                    $alphabetFilter=$request->get('alphabet');
                    $filterNavigationCounter = $categoryFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$catagoryFilterData,$request);
                    $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                    $brand_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }
            return $this->filterCategoryPage($brand_details,$userAddToFav,$brand_image['brand_image'],$cashback,$product,$voucher,$max_voucher[0],$max_cashback[0],$max_voucher_cashback[0],$cat_name,$cashbackTopBanner,$cuponesTopBanner,$cashbackBannerTitle,$cashbackBannerDescription,$cuponesBannerTitle,$cuponesBannerDescription,$currentRoute,$final_count,$init_count,$target_count,$remove_load_more,$request->get('offer'),$request->get('alphabet'),$request->get('category_id_string'), $product_count);
        }
        elseif($request->get('alphabet')){
            $alphabetFilter=$request->get('alphabet');
            $filterNavigationCounter = $categoryFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$brand_details,$request);
            $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
            $brand_details = $filterNavigationCounter['data_details'];
            $remove_load_more = $filterNavigationCounter['remove_load_more'];
            $target_count = $filterNavigationCounter['target_count'];
            if($request->get('offer')){
                $offerFilter=$request->get('offer');
                $filterNavigationCounter = $categoryFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$alphabetFilterData,$request);
                $offerFilterData = $filterNavigationCounter['offerFilterData'];
                $brand_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('category_id_string')){
                    $catagoryFilter=$request->get('category_id_string');
                    $filterNavigationCounter = $categoryFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$offerFilterData,$request);
                    $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                    $brand_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }elseif($request->get('category_id_string')){
                $catagoryFilter=$request->get('category_id_string');
                $filterNavigationCounter = $categoryFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$alphabetFilterData,$request);
                $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                $brand_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('offer')){
                    $offerFilter=$request->get('offer');
                    $filterNavigationCounter = $categoryFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$catagoryFilterData,$request);
                    $offerFilterData = $filterNavigationCounter['offerFilterData'];
                    $brand_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }
          return $this->filterCategoryPage($brand_details,$userAddToFav,$brand_image['brand_image'],$cashback,$product,$voucher,$max_voucher[0],$max_cashback[0],$max_voucher_cashback[0],$cat_name,$cashbackTopBanner,$cuponesTopBanner,$cashbackBannerTitle,$cashbackBannerDescription,$cuponesBannerTitle,$cuponesBannerDescription,$currentRoute,$final_count,$init_count,$target_count,$remove_load_more,$request->get('offer'),$request->get('alphabet'),$request->get('category_id_string'), $product_count);

        }elseif($request->get('category_id_string')){
            $catagoryFilter=$request->get('category_id_string');
            $filterNavigationCounter = $categoryFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$brand_details,$request);
            $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
            $brand_details = $filterNavigationCounter['data_details'];
            $remove_load_more = $filterNavigationCounter['remove_load_more'];
            $target_count = $filterNavigationCounter['target_count'];
            if($request->get('offer')){
                $offerFilter=$request->get('offer');
                $filterNavigationCounter = $categoryFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$catagoryFilterData,$request);
                $offerFilterData = $filterNavigationCounter['offerFilterData'];
                $brand_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('alphabet')){
                    $alphabetFilter=$request->get('alphabet');
                    $filterNavigationCounter = $categoryFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$offerFilterData,$request);
                    $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                    $brand_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }elseif($request->get('alphabet')){
                $alphabetFilter=$request->get('alphabet');
                $filterNavigationCounter = $categoryFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$catagoryFilterData,$request);
                $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                $brand_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('offer')){
                    $offerFilter=$request->get('offer');
                    $filterNavigationCounter = $categoryFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$alphabetFilterData,$request);
                    $offerFilterData = $filterNavigationCounter['offerFilterData'];
                    $brand_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }

            return $this->filterCategoryPage($brand_details,$userAddToFav,$brand_image['brand_image'],$cashback,$product,$voucher,$max_voucher[0],$max_cashback[0],$max_voucher_cashback[0],$cat_name,$cashbackTopBanner,$cuponesTopBanner,$cashbackBannerTitle,$cashbackBannerDescription,$cuponesBannerTitle,$cuponesBannerDescription,$currentRoute,$final_count,$init_count,$target_count,$remove_load_more,$request->get('offer'),$request->get('alphabet'),$request->get('category_id_string'), $product_count);

        }
        else {
            if ($request->get('target_count')) {
                $target_count = $request->get('target_count');
                if ($request->get('alphabet') == 'TODAS') {
                    $target_count = 1;
                }
                $filterNavigationCounter = $categoryFilterController->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$brand_details);
                $brand_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];

                return $this->filterCategoryPage($brand_details,$userAddToFav,$brand_image['brand_image'],$cashback,$product,$voucher,$max_voucher[0],$max_cashback[0],$max_voucher_cashback[0],$cat_name,$cashbackTopBanner,$cuponesTopBanner,$cashbackBannerTitle,$cashbackBannerDescription,$cuponesBannerTitle,$cuponesBannerDescription,$currentRoute,$final_count,$init_count,$target_count,$remove_load_more,$request->get('offer'),$request->get('alphabet'),$request->get('category_id_string'), $product_count);


            } else {
                $target_count = $execute_count = 1;
                $filterNavigationCounter = $categoryFilterController->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$brand_details);
                
                $brand_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                return $this->renderCategoryPage($brand_details,$userAddToFav,$brand_image['brand_image'],$cashback,$product,$voucher,$max_voucher[0],$max_cashback[0],$max_voucher_cashback[0],$cat_name,$cashbackTopBanner,$cuponesTopBanner,$cashbackBannerTitle,$cashbackBannerDescription,$cuponesBannerTitle,$cuponesBannerDescription,$currentRoute,$final_count,$init_count,$execute_count, $target_count, $remove_load_more);
            }
        }
    }

    public function filterCategoryPage($brand_details,$userAddToFav,$brand_image,$cashback,$product,$voucher,$max_voucher,$max_cashback,$max_voucher_cashback,$cat_name,$cashbackTopBanner,$cuponesTopBanner,$cashbackBannerTitle,$cashbackBannerDescription,$cuponesBannerTitle,$cuponesBannerDescription,$currentRoute,$final_count,$init_count,$target_count,$remove_load_more,$OF,$AF,$CF,$product_count){

        $render_data = [
            'brand_detail' => $brand_details,
            'addtofevlist' => $userAddToFav,
            'brand_image' => $brand_image,
            'cashback_shop' => $cashback,
            'product_shop' => $product,
            'voucher_shop' => $voucher,
            'max_voucher_data' => $max_voucher,
            'max_cashback_percentage' => $max_cashback,
            'max_voucher_cashback' => $max_voucher_cashback,
            'slug_name' => $cat_name,
            'cashback_top_banner' => $cashbackTopBanner,
            'cupones_top_banner' => $cuponesTopBanner,
            'cashbackBannerTitle' => $cashbackBannerTitle,
            'cashbackBannerDescription' => $cashbackBannerDescription,
            'cuponesBannerTitle' => $cuponesBannerTitle,
            'cuponesBannerDescription' => $cuponesBannerDescription,
            'currentRoute' => $currentRoute,
            'final_count' => $final_count,
            'init_count' => $init_count,
            'target_count' => $target_count,
            'target_count_category_filter' => $target_count,
            'remove_load_more' => $remove_load_more,
            'OF' => json_encode($OF),
            'AF' => $AF,
            'CF' => $CF,
            'tabType' => '',
            'tabId' => '',
        ];
        $arr = array('product_count'=>$product_count,'html' => $this->render('iFlairLetsBonusFrontBundle:Brand:brand-page-loadmore.html.twig',$render_data)->getContent());
        return new Response(json_encode($arr));
    }

    public function renderCategoryPage($brand_details,$userAddToFav,$brand_image,$cashback,$product,$voucher,$max_voucher,$max_cashback,$max_voucher_cashback,$cat_name,$cashbackTopBanner,$cuponesTopBanner,$cashbackBannerTitle,$cashbackBannerDescription,$cuponesBannerTitle,$cuponesBannerDescription,$currentRoute,$final_count,$init_count,$execute_count,$target_count,$remove_load_more){
        $render_data = [
            'brand_detail' => $brand_details,
            'addtofevlist' => $userAddToFav,
            'brand_image' => $brand_image,
            'cashback_shop' => $cashback,
            'product_shop' => $product,
            'voucher_shop' => $voucher,
            'max_voucher_data' => $max_voucher,
            'max_cashback_percentage' => $max_cashback,
            'max_voucher_cashback' => $max_voucher_cashback,
            'slug_name' => $cat_name,
            'cashback_top_banner' => $cashbackTopBanner,
            'cupones_top_banner' => $cuponesTopBanner,
            'cashbackBannerTitle' => $cashbackBannerTitle,
            'cashbackBannerDescription' => $cashbackBannerDescription,
            'cuponesBannerTitle' => $cuponesBannerTitle,
            'cuponesBannerDescription' => $cuponesBannerDescription,
            'currentRoute' => $currentRoute,
            'execute_count' => $execute_count,
            'final_count' => $final_count,
            'init_count' => $init_count,
            'target_count' => $target_count,
            'remove_load_more' => $remove_load_more,
            'OF' => '',
            'AF' => '',
            'tabType' => '',
            'tabId' => '',
        ];

        return $this->render('iFlairLetsBonusFrontBundle:Brand:brand-page.html.twig', $render_data);
    }
}
