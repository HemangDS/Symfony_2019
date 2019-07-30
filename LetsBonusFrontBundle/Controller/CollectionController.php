<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use iFlair\LetsBonusAdminBundle\Entity\Category;
use iFlair\LetsBonusAdminBundle\Entity\childCategory;
use iFlair\LetsBonusAdminBundle\Entity\parentCategory;
use iFlair\LetsBonusAdminBundle\Entity\Settings;
use iFlair\LetsBonusAdminBundle\Entity\Shop;
use iFlair\LetsBonusAdminBundle\Entity\Slug;
use iFlair\LetsBonusAdminBundle\Slug\Constants;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CollectionController extends Controller
{
    public function getSepcialCollectionAction(Request $request, $position = 'header')
    {
        $collection_slug_name = '';
        $sm = $this->getDoctrine()->getEntityManager();
        $connection = $sm->getConnection();
        $statement = $connection->prepare('SELECT id,name FROM lb_collection AS clctn JOIN lb_shop_collection AS sc ON clctn.id = sc.collection_id WHERE clctn.status = 1 AND clctn.mark_special = 1 GROUP BY sc.collection_id HAVING count(sc.collection_id) > 0 ORDER BY name ASC, modified DESC LIMIT 0,1');
        $statement->execute();
        $collection = $statement->fetch();
        $collectionId = $collection['id'];
        $collectionName = $collection['name'];

        if (!empty($collectionId)) {
            $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryType' => Constants::COLLECTION_IDENTIFIER,
                                                                                        'categoryId' => $collectionId, ));
            if ($slug) {
                $collection_slug_name = $slug->getSlugName();
            }
        }

        if ($collectionId) {
            return $this->render('iFlairLetsBonusFrontBundle:Collection:collection.html.twig', array(
                'collectionId' => $collectionId,
                'collectionName' => $collectionName,
                'collectionSlugName' => $collection_slug_name,
                'position' => $position,
            ));
        } else {
            return new Response();
        }
    }

     public function getImageUrl($entities, $imageId, $imageType = 'big', $definedType = 'category')
    {
        $fieldName = 'nimage';
        if ($definedType == 'voucherprogram') {
            if ($imageType == 'brand_on_shop') {
                $fieldName = 'image';
            }
            if ($imageType == 'cashback_voucher_popup') {
                $fieldName = 'popUpImage';
            }
        }
        $media = $entities->findOneBy(array($fieldName => $imageId));
        $imageUrl = '';
        if (!empty($media) && !empty($imageId)) {
            if ($definedType == 'category') {
                $media = $media->getnImage();
            } elseif ($definedType == 'voucherprogram') {
                if ($imageType == 'brand_on_shop') {
                    $media = $media->getImage();
                }
                if ($imageType == 'cashback_voucher_popup') {
                    $media = $media->getPopUpImage();
                }
            }
            $mediaManager = $this->get('sonata.media.pool');
            $provider = $mediaManager->getProvider($media->getProviderName());
            $format = $provider->getFormatName($media, $imageType);
            $imageUrl = $provider->generatePublicUrl($media, $format);
        }

        return $imageUrl;
    }

    public function getMediaUrlByShopId($shopId, $mediaImageType, &$sm)
    {
        $shopRepository = $sm->getRepository('iFlairLetsBonusAdminBundle:Shop');
        $shop = $shopRepository->findOneBy(array(
            'id' => $shopId,
            'shopStatus' => Shop::SHOP_ACTIVATED,
        ));
        if ($shop) {
            $shopMedia = $shop->getCategoryImage();
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
        $statement = $connection->prepare('SELECT vp.logo_path AS brand_logo, vp.id AS brand_id,s.urlAffiliate AS urlAffiliate, s.* ,vp.program_name AS brand_name, MAX(v.discount_amount), v.*,s.id AS shop_id, vp.image_id AS brand_image, vp.pop_up_image_id AS brand_popup_image, v.publish_end_date AS voucher_expire_date
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

    public function slugType($slug_name, $sm)
    {
        $slug_type = array();
        $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('slugName' => $slug_name));
        if ($slug) {
            $slug_type['type'] = $slug->getCategoryType();
            $slug_type['slug_type_id'] = $slug->getCategoryId();
        }

        return $slug_type;
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
                    if ($value['shop_offers'] == 'cashback' ||  $value['shop_offers'] == 'cashback/coupons') {
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
                    if ($value['shop_offers'] == 'voucher' ||  $value['shop_offers'] == 'cashback/coupons') {
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
            if (!empty($value['shop_id']) && $value['shop_offers'] == 'voucher' ||  $value['shop_offers'] == 'cashback/coupons') {
                $max_voucher[] = $value;
            }
        }

        if (!empty($max_voucher)) {
            foreach ($max_voucher as $key => $value) {
                $voucher[$key] = $value['voucher_code_count'];
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
            if (!empty($value['shop_id']) && $value['shop_offers'] == 'cashback' ||  $value['shop_offers'] == 'cashback/coupons') {
                $max_cashback[] = $value;
            }
        }

        if (!empty($max_cashback)) {
            foreach ($max_cashback as $key => $value) {
                $cashback[$key] = $value['letsBonusPercentage'];
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
                $max_offer[] = $value;
            }
        }

        if (!empty($max_offer)) {
            foreach ($max_offer as $key => $value) {
                $offer[$key] = $value['letsBonusPercentage'];
            }
            array_multisort($offer, SORT_DESC, $max_offer);
        } else {
            $max_offer[0] = array();
        }

        return $max_offer;
    }

      //public function collectionType($collection_id, $sm, $connection,$request)
    public function collectionpageAction(Request $request, $collection_name = null, $parent_category = null, $category = null, $child_category = null)
    
    {
        $sm = $this->getDoctrine()->getEntityManager();
        $connection = $sm->getConnection();
        $collection_id ="";
        $parent_cat_id = '';
        $cat_id = '';
        $child_cat_id = '';
        $parent_cat_name = '';
        $cat_name = '';
        $child_cat_name = '';
        $collection_details = array();
        $userAddToFav = array();
       



        /*
         *  NOTE    :: Its an common method to show top banner for all sections
         *  DEFINED IN  ::  CategoryController
         *  ARGS    :: type_of_media_image, code_of_banner_for_any_section, em
         */
        $collectionsBannerTitle = '';
        $collectionsBannerDescription = '';

        
        $collectionFromCategoryController = new CategoryController();
        $collectionFromCategoryController->setContainer($this->container);
        $collectionTopBanner = $collectionFromCategoryController->getTopBanner('default_list_page_type', Settings::COLLECTIONTOPBANNER, $sm);

        $collectionBanner = $collectionFromCategoryController->getBannerTitleDescription(Settings::COLLECTIONTOPBANNER, $connection);

        if (isset($collectionBanner[0]['bannertitle']) && isset($collectionBanner[0]['bannerdescription'])) {
            $collectionsBannerTitle = $collectionBanner[0]['bannertitle'];
            $collectionsBannerDescription = $collectionBanner[0]['bannerdescription'];
        }



        $slug_type = $this->slugType($collection_name, $sm);
        if (!empty($slug_type)) {
            if ($slug_type['type'] == Constants::COLLECTION_IDENTIFIER) {
        
                $collection_id = $slug_type['slug_type_id'];
            }
        }

         if (!empty($parent_category)) {
            $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                            array('categoryType' => Constants::PARENT_CATEGORY_IDENTIFIER, 'slugName' => $parent_category));
            if ($slug) {
                $parent = $slug->getCategoryId();
            }
            else
            {
                 return $this->render('iFlairLetsBonusFrontBundle:Error:error404.html.twig');
            }

            $parentCategoryRepository = $sm->getRepository('iFlairLetsBonusAdminBundle:parentCategory');
            $parentCategory = $parentCategoryRepository->findOneBy(array('id' => $parent, 'status' => 1));
            if ($parentCategory) {
                $parent_cat_id = $parentCategory->getId();
                $parent_cat_name = $parentCategory->getName();
            }
        }

        if (!empty($category) && $category != null) {
            $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                            array('categoryType' => Constants::MIDDLE_CATEGORY_IDENTIFIER, 'slugName' => $category));
            if ($slug) {
                $cat_id = $slug->getCategoryId();
            }
              else
            {
                 return $this->render('iFlairLetsBonusFrontBundle:Error:error404.html.twig');
            }
            $CategoryRepository = $sm->getRepository('iFlairLetsBonusAdminBundle:Category');
            $Category = $CategoryRepository->findOneBy(array('parentCategory' => $parent_cat_id, 'id' => $cat_id, 'status' => 1));
            if ($Category) {
                $cat_id = $Category->getId();
                $cat_name = $Category->getName();
            }
        }

        if (!empty($child_category)) {
            $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                            array('categoryType' => Constants::CHILD_CATEGORY_IDENTIFIER, 'slugName' => $child_category));
            if ($slug) {
                $child_cat_id = $slug->getCategoryId();
            }
              else
            {
                 return $this->render('iFlairLetsBonusFrontBundle:Error:error404.html.twig');
            }
            $ChildCategoryRepository = $sm->getRepository('iFlairLetsBonusAdminBundle:childCategory');
            $ChildCategory = $ChildCategoryRepository->findOneBy(array('parentCategory' => $parent_cat_id, 'Category' => $cat_id, 'id' => $child_cat_id, 'status' => 1));

            if ($ChildCategory) {
                $child_cat_id = $ChildCategory->getId();
                $child_cat_name = $ChildCategory->getName();
            }
        }


        $session = $this->getRequest()->getSession();

        if (!empty($parent_cat_id) && empty($cat_id) && empty($child_cat_id)) 
        {
            $statement = $connection->prepare('SELECT s.*,c.name AS collection_name, s.id AS shop_id
                                           FROM lb_shop AS s 
                                           JOIN lb_shop_collection AS sc on s.id = sc.shop_id 
                                           JOIN lb_collection AS c on c.id = sc.collection_id
                                           JOIN lb_shop_parent_category AS spc on spc.shop_id = s.id
                                           JOIN lb_parent_category AS pc ON spc.parent_category_id = pc.id
                                           WHERE s.shopStatus = :shopStatus AND sc.collection_id = :collection_id AND pc.status = 1 
                                           AND spc.parent_category_id = :parent_category_id');
            $statement->bindValue('parent_category_id', $parent_cat_id);
            $statement->bindValue('collection_id', $collection_id);
            $statement->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        }
        elseif (!empty($parent_cat_id) && !empty($cat_id) && empty($child_cat_id)) 
        {
            $statement = $connection->prepare('SELECT s.*,c.name AS collection_name, s.id AS shop_id
                                           FROM lb_shop AS s 
                                           JOIN lb_shop_collection AS sc on s.id = sc.shop_id 
                                           JOIN lb_collection AS c on c.id = sc.collection_id
                                           JOIN lb_shop_category AS scat on scat.shop_id = s.id
                                           JOIN lb_category AS cat ON scat.category_id = cat.id
                                           WHERE s.shopStatus = :shopStatus AND sc.collection_id = :collection_id 
                                           AND cat.status = 1 
                                           AND cat.parent_category_id = :parent_category_id
                                           AND scat.category_id = :category_id');
            $statement->bindValue('parent_category_id', $parent_cat_id);
            $statement->bindValue('category_id', $cat_id);
            $statement->bindValue('collection_id', $collection_id);
            $statement->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        }
        elseif (!empty($parent_cat_id) && !empty($cat_id) && !empty($child_cat_id)) 
        {
            $statement = $connection->prepare('SELECT s.*,c.name AS collection_name, s.id AS shop_id
                                           FROM lb_shop AS s 
                                           JOIN lb_shop_collection AS sc on s.id = sc.shop_id 
                                           JOIN lb_collection AS c on c.id = sc.collection_id
                                           JOIN lb_shop_child_category AS scc on scc.shop_id = s.id
                                           JOIN lb_child_category AS cc ON scc.child_category_id = cc.id
                                           WHERE s.shopStatus = :shopStatus AND sc.collection_id = :collection_id 
                                           AND cc.status = 1 
                                           AND cc.parent_category_id = :parent_category_id
                                           AND cc.category_id = :category_id
                                           AND scc.child_category_id = :child_category_id');
            $statement->bindValue('parent_category_id', $parent_cat_id);
            $statement->bindValue('category_id', $cat_id);
            $statement->bindValue('child_category_id', $child_cat_id);
            $statement->bindValue('collection_id', $collection_id);
            $statement->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        }
        else
        {
        $statement = $connection->prepare('SELECT s.*,c.name AS collection_name, s.id AS shop_id
                                           FROM lb_shop AS s JOIN lb_shop_collection AS sc on s.id = sc.shop_id JOIN lb_collection AS c on c.id = sc.collection_id WHERE s.shopStatus = :shopStatus AND sc.collection_id = :collection_id');
        $statement->bindValue('collection_id', $collection_id);
        $statement->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        }

        $statement->execute();
        $shop_data = $statement->fetchAll();
        $voucherProgramsEntity = $sm->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms');
        $homepageController = new HomepageController();
        $homepageController->setContainer($this->container);

        if (!empty($shop_data)) {
            $i = 0;
            foreach ($shop_data as $shopdata) {
                $collection_name = $shopdata['collection_name'];
                $shopId = $shopdata['shop_id'];
                $shop_data_record = $this->getShopDetailsByCategoryId($shopId, $connection);
                // if condition for cashback && voucher is not exclusive
                if ((isset($shopId) && $shopdata['offers'] == 'cashback') || (isset($shopId) && $shopdata['offers'] == 'cashback/coupons') || 
                    (isset($shopId) && $shop_data_record[0]['exclusive'] == 0 && $shopdata['offers'] == 'voucher')) {
                    $shop_history = $sm->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findBy(array('shop' => $shopId), array('startDate'=>'DESC'), 1);
                    foreach ($shop_history as $key => $shop_value) {
                        $shopHistoryId = $shop_value->getId();
                        $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $shopHistoryId));
                        $variations = $this->getShopHistoryVariationByShopHistoryId($shopHistoryId, $sm);
                        $cat_arr[$i][$shopHistoryId]['shop_history_variation'] = $variations;
                        $voucher_count = $this->getVoucherCountByShopId($shopId, $connection);
                        $cat_arr[$i][$shopHistoryId]['voucher_code_count'] = 0;
                        if ($voucher_count) {
                            $cat_arr[$i][$shopHistoryId]['voucher_code_count'] = count($voucher_count);
                        }
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
                       // $cat_arr[$i][$shopHistoryId]['shop_history_shop_end_date'] = $shop_value->getEndDate();
                        $cat_arr[$i][$shopHistoryId]['shop_terms'] = $shop_value->getTearms();
                        $cat_arr[$i][$shopHistoryId]['letsBonusPercentage'] = $shop_value->getLetsBonusPercentage();
                        $cat_arr[$i][$shopHistoryId]['shop_image'] = $this->getMediaUrlByShopId($shopId, 'default_shop', $sm);
                        $cat_arr[$i][$shopHistoryId]['top_shop_image'] = $this->getMediaForHighLineTab($shopId, 'default_highline_offer_image', $sm);
                        $cat_arr[$i][$shopHistoryId]['voucher_id'] = $shop_data_record[0]['id'];
                        $cat_arr[$i][$shopHistoryId]['voucher_name'] = $shop_data_record[0]['title'];
                        $cat_arr[$i][$shopHistoryId]['shop_id'] = $shopId;
                        $cat_arr[$i][$shopHistoryId]['discount_amount'] = $shop_data_record[0]['discount_amount'];
                        $cat_arr[$i][$shopHistoryId]['is_percentage'] = $shop_data_record[0]['is_percentage'];
                        $cat_arr[$i][$shopHistoryId]['short_description'] = strip_tags($shop_data_record[0]['short_description']);
                        $cat_arr[$i][$shopHistoryId]['description'] = strip_tags($shop_data_record[0]['description']);
                        if (strtotime($shop_data_record[0]['voucher_expire_date']) > strtotime('-30 days')) {
                            $date = strtotime($shop_data_record[0]['voucher_expire_date']);
                            $dat = date('d/m/y', $date);
                        } else {
                            $dat = '';
                        }


                        $cat_arr[$i][$shopHistoryId]['voucher_expire_date'] = $dat;
                        $cat_arr[$i][$shopHistoryId]['exclusive'] = $shop_data_record[0]['exclusive'];

                        $cat_arr[$i][$shopHistoryId]['brand_logo'] = $shop_data_record[0]['brand_logo'];

                        if (!empty($shop_data_record[0]['brand_image'])) {
                            $cat_arr[$i][$shopHistoryId]['brand_logo'] = $this->getImageUrl($voucherProgramsEntity, $shop_data_record[0]['brand_image'], 'brand_on_shop', 'voucherprogram');
                        }

                        $cat_arr[$i][$shopHistoryId]['brand_name'] = $shop_data_record[0]['brand_name'];
                        $cat_arr[$i][$shopHistoryId]['shop_affiliate_url'] = $shop_data_record[0]['urlAffiliate'];
                        $cat_arr[$i][$shopHistoryId]['cashbackPrice'] = $shop_data_record[0]['cashbackPrice'];
                        $cat_arr[$i][$shopHistoryId]['cashbackPercentage'] = $shop_data_record[0]['cashbackPercentage'];
                        $cat_arr[$i][$shopHistoryId]['rating_percentage'] = $homepageController->ratingAction($shopId, $shopHistoryId);
                    }
                    // end if condition for cashback
                } 
                /*elseif (isset($shopId) && $shop_data_record[0]['exclusive'] == 1) */
                elseif ((isset($shopId) && $shopdata['offers'] == 'offer') || (isset($shopId) && $shop_data_record[0]['exclusive'] == 1 && $shopdata['offers'] == 'voucher'))
                {
                    // checking offer type voucher
                    $shop_history = $sm->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('shop' => $shopId), array('startDate' => 'DESC'), 1);
                    /*$shop_history = $sm->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findBy(array('shop' => $shopId), array('startDate'=>'DESC'), 1);*/
                    $shopHistoryId = $shop_history->getId();
                    $variations = $this->getShopHistoryVariationByShopHistoryId($shopHistoryId, $sm);
                    $cat_arr[$i][$shopHistoryId]['shop_history_variation'] = $variations;
                    $voucher_count = $this->getVoucherCountByShopId($shopId, $connection);
                    $cat_arr[$i][$shopHistoryId]['voucher_code_count'] = 0;
                    if ($voucher_count) {
                        $cat_arr[$i][$shopHistoryId]['voucher_code_count'] = count($voucher_count);
                    }
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
                    $cat_arr[$i][$shopHistoryId]['letsBonusPercentage'] = $shop_history->getLetsBonusPercentage();
                    $cat_arr[$i][$shopHistoryId]['shop_image'] = $this->getMediaUrlByShopId($shopId, 'default_shop', $sm);
                    $cat_arr[$i][$shopHistoryId]['top_shop_image'] = $this->getMediaForHighLineTab($shopId, 'default_highline_offer_image', $sm);
                    $cat_arr[$i][$shopHistoryId]['voucher_id'] = $shop_data_record[0]['id'];
                    $cat_arr[$i][$shopHistoryId]['voucher_name'] = $shop_data_record[0]['title'];
                    $cat_arr[$i][$shopHistoryId]['shop_id'] = $shopId;
                    $cat_arr[$i][$shopHistoryId]['discount_amount'] = $shop_data_record[0]['discount_amount'];
                    $cat_arr[$i][$shopHistoryId]['is_percentage'] = $shop_data_record[0]['is_percentage'];
                    $cat_arr[$i][$shopHistoryId]['short_description'] = strip_tags($shop_data_record[0]['short_description']);
                    $cat_arr[$i][$shopHistoryId]['description'] = strip_tags($shop_data_record[0]['description']);
                    if (strtotime($shop_data_record[0]['voucher_expire_date']) > strtotime('-30 days')) {
                        $date = strtotime($shop_data_record[0]['voucher_expire_date']);
                        $dat = date('d/m/y', $date);
                    } else {
                        $dat = '';
                    }
                    $cat_arr[$i][$shopHistoryId]['voucher_expire_date'] = $dat;
                    $cat_arr[$i][$shopHistoryId]['exclusive'] = $shop_data_record[0]['exclusive'];
                    $cat_arr[$i][$shopHistoryId]['logo_image'] = $shop_data_record[0]['brand_logo'];
                    if (!empty($shop_data_record[0]['brand_image'])) {
                        $cat_arr[$i][$shopHistoryId]['brand_logo'] = $this->getImageUrl($voucherProgramsEntity, $shop_data_record[0]['brand_image'], 'brand_on_shop', 'voucherprogram');
                    }
                    $cat_arr[$i][$shopHistoryId]['brand_name'] = $shop_data_record[0]['brand_name'];
                    $cat_arr[$i][$shopHistoryId]['shop_affiliate_url'] = $shop_data_record[0]['urlAffiliate'];
                    $cat_arr[$i][$shopHistoryId]['cashbackPrice'] = $shop_data_record[0]['cashbackPrice'];
                    $cat_arr[$i][$shopHistoryId]['cashbackPercentage'] = $shop_data_record[0]['cashbackPercentage'];
                    $cat_arr[$i][$shopHistoryId]['rating_percentage'] = $homepageController->ratingAction($shopId, $shopHistoryId);
                }
                // end elseif checking offer type voucher
                ++$i;
            }
            $userAddToFav = [];
            if (!empty($session->get('user_id'))) {
                $userAddToFav = $homepageController->addtofevlistAction();
            }

            $collection_details = $this->getFilter($cat_arr);
        }

        $max_voucher = $this->getMaxVoucherFilter($collection_details);
        $max_cashback = $this->getMaxCashbackFilter($collection_details);
        $max_voucher_cashback = $this->getMaxVoucherCashback($collection_details);
        $cashback = $this->getCahbackOfferFilter($collection_details);
        $product = $this->getProductOfferFilter($collection_details);
        $voucher = $this->getVoucherOfferFilter($collection_details);

        $product_count = count($collection_details);
        $init_count = 12;
        $final_count = count($collection_details);
        $remove_load_more = 0;
    
        $collectionFilterController = new CollectionFilterController();
        $collectionFilterController->setContainer($this->container);

        if($request->get('offer')){
            $offerFilter=$request->get('offer');
            $filterNavigationCounter = $collectionFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$collection_details,$request);
            $offerFilterData = $filterNavigationCounter['offerFilterData'];
            $collection_details = $filterNavigationCounter['data_details'];
            $remove_load_more = $filterNavigationCounter['remove_load_more'];
            $target_count = $filterNavigationCounter['target_count'];
            if($request->get('alphabet')){
                $alphabetFilter=$request->get('alphabet');
                $filterNavigationCounter = $collectionFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$offerFilterData,$request);
                $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                $collection_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('category_id_string')){
                    $catagoryFilter=$request->get('category_id_string');
                    $filterNavigationCounter = $collectionFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$alphabetFilterData,$request);
                    $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                    $collection_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }elseif($request->get('category_id_string')){
                $catagoryFilter=$request->get('category_id_string');
                $filterNavigationCounter = $collectionFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$offerFilterData,$request);
                $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                $collection_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('alphabet')){
                    $alphabetFilter=$request->get('alphabet');
                    $filterNavigationCounter = $collectionFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$catagoryFilterData,$request);
                    $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                    $collection_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }
           return $this->filterCollectionPage($collection_details, $userAddToFav, $collection_name, $cashback, $product, $voucher, $max_voucher[0], $max_cashback[0], $max_voucher_cashback[0],$parent_cat_name,$cat_name,$child_cat_name, $final_count, $init_count, $target_count, $remove_load_more, $request->get('offer'), $request->get('alphabet'), $request->get('category_id_string'), $product_count,$collectionTopBanner,$collectionsBannerTitle,$collectionsBannerDescription);
        }elseif($request->get('alphabet')){
            $alphabetFilter=$request->get('alphabet');
            $filterNavigationCounter = $collectionFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$collection_details,$request);
            $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
            $collection_details = $filterNavigationCounter['data_details'];
            $remove_load_more = $filterNavigationCounter['remove_load_more'];
            $target_count = $filterNavigationCounter['target_count'];
            if($request->get('offer')){
                $offerFilter=$request->get('offer');
                $filterNavigationCounter = $collectionFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$alphabetFilterData,$request);
                $offerFilterData = $filterNavigationCounter['offerFilterData'];
                $collection_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('category_id_string')){
                    $catagoryFilter=$request->get('category_id_string');
                    $filterNavigationCounter = $collectionFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$offerFilterData,$request);
                    $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                    $collection_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }elseif($request->get('category_id_string')){
                $catagoryFilter=$request->get('category_id_string');
                $filterNavigationCounter = $collectionFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$alphabetFilterData,$request);
                $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                $collection_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('offer')){
                    $offerFilter=$request->get('offer');
                    $filterNavigationCounter = $collectionFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$catagoryFilterData,$request);
                    $offerFilterData = $filterNavigationCounter['offerFilterData'];
                    $collection_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }
           return $this->filterCollectionPage($collection_details, $userAddToFav, $collection_name, $cashback, $product, $voucher, $max_voucher[0], $max_cashback[0], $max_voucher_cashback[0],$parent_cat_name,$cat_name,$child_cat_name, $final_count, $init_count, $target_count, $remove_load_more, $request->get('offer'), $request->get('alphabet'), $request->get('category_id_string'), $product_count,$collectionTopBanner,$collectionsBannerTitle,$collectionsBannerDescription);
        }elseif($request->get('category_id_string')){
            $catagoryFilter=$request->get('category_id_string');
            $filterNavigationCounter = $collectionFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$collection_details,$request);
            $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
            $collection_details = $filterNavigationCounter['data_details'];
            $remove_load_more = $filterNavigationCounter['remove_load_more'];
            $target_count = $filterNavigationCounter['target_count'];
            if($request->get('offer')){
                $offerFilter=$request->get('offer');
                $filterNavigationCounter = $collectionFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$catagoryFilterData,$request);
                $offerFilterData = $filterNavigationCounter['offerFilterData'];
                $collection_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('alphabet')){
                    $alphabetFilter=$request->get('alphabet');
                    $filterNavigationCounter = $collectionFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$offerFilterData,$request);
                    $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                    $collection_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }elseif($request->get('alphabet')){
                $alphabetFilter=$request->get('alphabet');
                $filterNavigationCounter = $collectionFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$catagoryFilterData,$request);
                $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                $collection_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('offer')){
                    $offerFilter=$request->get('offer');
                    $filterNavigationCounter = $collectionFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$alphabetFilterData,$request);
                    $offerFilterData = $filterNavigationCounter['offerFilterData'];
                    $collection_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }
            return $this->filterCollectionPage($collection_details, $userAddToFav, $collection_name, $cashback, $product, $voucher, $max_voucher[0], $max_cashback[0], $max_voucher_cashback[0],$parent_cat_name,$cat_name,$child_cat_name, $final_count, $init_count, $target_count, $remove_load_more, $request->get('offer'), $request->get('alphabet'), $request->get('category_id_string'), $product_count,$collectionTopBanner,$collectionsBannerTitle,$collectionsBannerDescription);
        }else {
            if ($request->get('target_count')) {
                $target_count = $request->get('target_count');
                if ($request->get('alphabet') == 'TODAS') {
                    $target_count = 1;
                }
                $filterNavigationCounter = $collectionFilterController->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$collection_details);
                $collection_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                return $this->filterCollectionPage($collection_details, $userAddToFav, $collection_name, $cashback, $product, $voucher, $max_voucher[0], $max_cashback[0], $max_voucher_cashback[0],$parent_cat_name,$cat_name,$child_cat_name, $final_count, $init_count, $target_count, $remove_load_more, $request->get('offer'), $request->get('alphabet'), $request->get('category_id_string'), $product_count,$collectionTopBanner,$collectionsBannerTitle,$collectionsBannerDescription);
            } else {
                $target_count = $execute_count = 1;
                $filterNavigationCounter = $collectionFilterController->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$collection_details);
                 
                $collection_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                return $this->renderCollectionPage($collection_details, $userAddToFav, $collection_name, $cashback, $product, $voucher, $max_voucher[0], $max_cashback[0], $max_voucher_cashback[0],$parent_cat_name,$cat_name,$child_cat_name, $final_count, $init_count, $execute_count, $target_count, $remove_load_more,$collectionTopBanner,$collectionsBannerTitle,$collectionsBannerDescription);
            }
        }
    }

    public function filterCollectionPage($collection_details,$userAddToFav,$collection_name,$cashback,$product,$voucher,$max_voucher,$max_cashback,$max_voucher_cashback,$parent_cat_name,$cat_name,$child_cat_name,$final_count,$init_count,$target_count,$remove_load_more,$OF,$AF,$CF,$product_count,$collectionTopBanner,$collectionsBannerTitle,$collectionsBannerDescription){
       
        $render_data = array(
            'category_detail' => $collection_details,
            'addtofevlist' => $userAddToFav,
            'collection_name' => $collection_name,
            'cashback_shop' => $cashback,
            'product_shop' => $product,
            'voucher_shop' => $voucher,
            'max_voucher_data' => $max_voucher,
            'max_cashback_percentage' => $max_cashback,
            'max_voucher_cashback' => $max_voucher_cashback,
             'parent_slug_name' => $parent_cat_name,
            'middle_slug_name' => $cat_name,
            'child_slug_name' => $child_cat_name,
            'final_count' => $final_count,
            'init_count' => $init_count,
            'target_count' => $target_count,
            'target_count_category_filter' => $target_count,
            'remove_load_more' => $remove_load_more,
            'OF' => json_encode($OF),
            'AF' => $AF,
            'CF' => $CF,
            'collection_top_banner' => $collectionTopBanner,
            'collectionsTitle' => $collectionsBannerTitle,
            'collectionsBannerDescription' => $collectionsBannerDescription,
        );
        $arr = array('product_count'=>$product_count,'html' => $this->render('iFlairLetsBonusFrontBundle:Collection:collection-page-loadmore.html.twig',$render_data)->getContent());
      
        return new Response(json_encode($arr));
    }

   public function renderCollectionPage($collection_details,$userAddToFav,$collection_name,$cashback,$product,$voucher,$max_voucher,$max_cashback,$max_voucher_cashback,$parent_cat_name,$cat_name,$child_cat_name,$final_count,$init_count,$execute_count,$target_count,$remove_load_more,$collectionTopBanner,$collectionsBannerTitle,$collectionsBannerDescription){
        
        $render_data = array(
            'category_detail' => $collection_details,
            'addtofevlist' => $userAddToFav,
            'collection_name' => $collection_name,
            'cashback_shop' => $cashback,
            'product_shop' => $product,
            'voucher_shop' => $voucher,
            'max_voucher_data' => $max_voucher,
            'max_cashback_percentage' => $max_cashback,
            'max_voucher_cashback' => $max_voucher_cashback,
            'parent_slug_name' => $parent_cat_name,
            'middle_slug_name' => $cat_name,
            'child_slug_name' => $child_cat_name,
            'execute_count' => $execute_count,
            'final_count' => $final_count,
            'init_count' => $init_count,
            'target_count' => $target_count,
            'remove_load_more' => $remove_load_more,
            'collection_top_banner' => $collectionTopBanner,
            'collectionsTitle' => $collectionsBannerTitle,
            'collectionsBannerDescription' => $collectionsBannerDescription,
        );
        
        return $this->render('iFlairLetsBonusFrontBundle:Collection:collection-page.html.twig', $render_data);
    }
}
