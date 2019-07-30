<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use iFlair\LetsBonusAdminBundle\Entity\Advertisement;
use iFlair\LetsBonusAdminBundle\Entity\childCategory;
use iFlair\LetsBonusAdminBundle\Entity\parentCategory;
use iFlair\LetsBonusAdminBundle\Entity\Settings;
use iFlair\LetsBonusAdminBundle\Entity\Shop;
use iFlair\LetsBonusAdminBundle\Slug\Constants;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    public function getMenuCategoryAction(Request $request, $cate_page = null, $current_path = null)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();
        $size = 12;
        $default_cat_image_url = '';
        $query = $connection->prepare('SELECT pc.*,COALESCE((SELECT count(distinct(ls.vprogram_id)) FROM lb_shop as ls JOIN lb_shop_parent_category as lsp ON ls.id = lsp.shop_id WHERE lsp.parent_category_id = pc.id AND ls.shopStatus = :shopStatus GROUP BY lsp.parent_category_id),0) AS counts 
                                        FROM lb_parent_category AS pc
                                        LEFT JOIN lb_shop_parent_category AS spc ON pc.id = spc.parent_category_id 
                                        WHERE pc.status = 1 AND pc.highlightedHome = 1
                                        GROUP BY pc.id 
                                        ORDER BY pc.name');
        $query->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        $query->execute();
        $parentCategory = $query->fetchAll();

        $parentcategories = array();
        $entity = 'parentCategory';
        $imageType = 'big';
        $entities = $em->getRepository('iFlairLetsBonusAdminBundle:'.$entity);

        foreach ($parentCategory as $key => $value) {
            $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryType' => Constants::PARENT_CATEGORY_IDENTIFIER, 'categoryId' => $value['id']));

            $parentcategories[$key]['parenturlSlug'] = '';
            $parentcategories[$key]['parenturlImprovedSlug'] = '';
            if ($slug) {
                $parentcategories[$key]['parenturlImprovedSlug'] = preg_replace( '/[^[:print:]\r\n]/', '',$slug->getSlugName());
                $parentcategories[$key]['parenturlSlug'] = $slug->getSlugName();
            }

            foreach ($value as $key1 => $value1) {
                $parentcategories[$key][$key1] = $value1;
            }

            if (!empty($value['nimage_id'])) {
                $imageUrl = $this->getImageUrl($entities, $value['nimage_id'], $imageType);
                $parentcategories[$key]['image_path'] = $imageUrl;
            }

            if ($this->get('app.category_slugger')->checkParentCategoryHasCategory($value['id'])) {
                $parentcategories[$key]['hasCategory'] = '';
            } else {
                $parentcategories[$key]['hasCategory'] = 'no_category';
            }
        }

        $query = $connection->prepare('SELECT c.*,sc.*,COALESCE((SELECT count(distinct(ls.vprogram_id)) FROM lb_shop as ls JOIN lb_shop_category as lsc ON ls.id = lsc.shop_id WHERE lsc.category_id = c.id AND ls.shopStatus = :shopStatus GROUP BY lsc.category_id),0) AS counts 
                                        FROM  lb_category AS c 
                                        LEFT JOIN lb_shop_category AS sc ON c.id = sc.category_id
                                        WHERE c.status = 1 
                                        GROUP BY c.id 
                                        ORDER BY c.name');
        $query->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        $query->execute();
        $category = $query->fetchAll();

        $query = $connection->prepare('SELECT cc.*,scc.*,COALESCE((SELECT count(distinct(ls.vprogram_id)) FROM lb_shop as ls JOIN lb_shop_child_category as lscc ON ls.id = lscc.shop_id WHERE lscc.child_category_id = cc.id AND ls.shopStatus = :shopStatus GROUP BY lscc.child_category_id),0) AS counts
                                        FROM  lb_child_category AS cc 
                                        LEFT JOIN lb_shop_child_category AS scc ON cc.id = scc.child_category_id 
                                        WHERE cc.status = 1 
                                        GROUP BY cc.id 
                                        ORDER BY cc.name');
        $query->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        $query->execute();
        $childCategory = $query->fetchAll();

        $categories = array();
        $entity = 'Category';
        $entities = $em->getRepository('iFlairLetsBonusAdminBundle:'.$entity);
        foreach ($category as $key => $value) {
            $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                                array('categoryType' => Constants::MIDDLE_CATEGORY_IDENTIFIER, 'categoryId' => $value['id']));
            $categories[$value['parent_category_id']][$key]['categoryurlSlug'] = '';
            $categories[$value['parent_category_id']][$key]['categoryurlImprovedSlug'] = '';
            if ($slug) {
                $categories[$value['parent_category_id']][$key]['categoryurlImprovedSlug'] = preg_replace( '/[^[:print:]\r\n]/', '',$slug->getSlugName());
                $categories[$value['parent_category_id']][$key]['categoryurlSlug'] = $slug->getSlugName();
            }
            foreach ($value as $key1 => $value1) {
                $categories[$value['parent_category_id']][$key][$key1] = $value1;
            }
            if (!empty($value['nimage_id'])) {
                $imageUrl = $this->getImageUrl($entities, $value['nimage_id'], $imageType);
                $categories[$value['parent_category_id']][$key]['image_path'] = $imageUrl;
            }
            if ($this->get('app.category_slugger')->checkCategoryHasChildCategory($value['id'])) {
                $categories[$value['parent_category_id']][$key]['hasChildCategory'] = '';
            } else {
                $categories[$value['parent_category_id']][$key]['hasChildCategory'] = 'no_child_category';
            }
        }

        $childCategories = array();
        $entity = 'childCategory';
        $entities = $em->getRepository('iFlairLetsBonusAdminBundle:'.$entity);

        foreach ($childCategory as $key => $value) {
            $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                                array('categoryType' => Constants::CHILD_CATEGORY_IDENTIFIER, 'categoryId' => $value['id']));
            $childCategories[$value['category_id']][$key]['urlSlug'] = '';
            $childCategories[$value['category_id']][$key]['urlImprovedSlug'] = '';
            if ($slug) {
                $childCategories[$value['category_id']][$key]['urlImprovedSlug'] = preg_replace( '/[^[:print:]\r\n]/', '',$slug->getSlugName());;
                $childCategories[$value['category_id']][$key]['urlSlug'] = $slug->getSlugName();
            }

            foreach ($value as $key1 => $value1) {
                $childCategories[$value['category_id']][$key][$key1] = $value1;
            }
            if (!empty($value['nimage_id'])) {
                $imageUrl = $this->getImageUrl($entities, $value['nimage_id'], $imageType);
                $childCategories[$value['category_id']][$key]['image_path'] = $imageUrl;
            }
        }

         /* for category page */
        $category_parent = $parentcategories;
        $category_middle = $categories;
        $category_child = $childCategories;
        /*End for category page */

        $parentcategories = array_slice($parentcategories, 0, $size, true);
        $categories = $this->get('app.category_slugger')->getCategoriesTrimmedToSize($categories, $size);
        $childCategories = $this->get('app.category_slugger')->getCategoriesTrimmedToSize($childCategories, $size);

        $query = $connection->prepare("SELECT * 
                                        FROM lb_advertisement AS a
                                        JOIN lb_advertisement_type AS at ON a.adv_type = at.id 
                                        WHERE at.adv_type_name = 'Category Default Image' 
                                        ORDER BY a.created DESC 
                                        LIMIT 1");

        $query->execute();
        $advertisement = $query->fetchAll();

        $advertisementData = array();

        $entities = $em->getRepository('iFlairLetsBonusAdminBundle:Advertisement');
        foreach ($advertisement as $mainKey => $advData) {
            foreach ($advData as $advDatakey => $advDatavalue) {
                if ($advDatakey == 'image_id') {
                    $media = $entities->findOneBy(array('image' => $advData[$advDatakey]));

                    if (!empty($media) && !empty($advData['image_id'])) {
                        $media = $media->getImage();
                        $mediaManager = $this->get('sonata.media.pool');
                        $provider = $mediaManager->getProvider($media->getProviderName());
                        $format = $provider->getFormatName($media, 'big');
                        $productpublicUrl = $provider->generatePublicUrl($media, $format);
                        $advertisementData[$mainKey]['catdefault_adv_image_path'] = $productpublicUrl;
                    }
                }
                $advertisementData[$mainKey][$advDatakey] = $advDatavalue;
            }
        }
        if (!empty($advertisementData)) {
            if (isset($advertisementData[0]['catdefault_adv_image_path'])) {
                $default_cat_image_url = $advertisementData[0]['catdefault_adv_image_path'];
            }
        }

        $categoryPlaceholder = array();
        $entities = $em->getRepository('iFlairLetsBonusAdminBundle:Settings');
        $categoryPlaceholderData = $entities->findOneBy(array('code' => Settings::CATEGORYPLACEHOLDER, 'status' => Settings::YES));
        if ($categoryPlaceholderData) {
            $categoryPlaceholder['url'] = $categoryPlaceholderData->getUrl();
            $media = $categoryPlaceholderData->getImage();
            $mediaManager = $this->get('sonata.media.pool');
            $provider = $mediaManager->getProvider($media->getProviderName());
            $format = $provider->getFormatName($media, 'big');
            $settingImg = $provider->generatePublicUrl($media, $format);
            $categoryPlaceholder['value'] = $settingImg;
        }

        /***********************************************************************************************************/

        if (!empty($cate_page)) {
             return $this->render('iFlairLetsBonusFrontBundle:Category:category_list_left.html.twig', array(
                'parentcategories' => $category_parent,
                'categories' => $category_middle,
                'childcategories' => $category_child,
                'page_type' => $current_path,
            ));
         }

        return $this->render('iFlairLetsBonusFrontBundle:Category:menuCategory.html.twig', array(
            'parentcategories' => $parentcategories,
            'categoryPlaceholder' => $categoryPlaceholder,
            'categories' => $categories,
            'childcategories' => $childCategories,
            'default_category_image_path' => $default_cat_image_url,
        ));
    }

    public function getResponsiveCategoryAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();
        $size = 12;

        $query = $connection->prepare('SELECT pc.*,count(spc.parent_category_id) AS counts 
                                        FROM lb_parent_category AS pc 
                                        LEFT JOIN lb_shop_parent_category AS spc ON pc.id = spc.parent_category_id
                                        GROUP BY pc.id');
        $query->execute();
        $parentCategory = $query->fetchAll();

        $query = $connection->prepare('SELECT c.*,sc.*,count(sc.category_id) AS counts 
                                        FROM  lb_category AS c 
                                        LEFT JOIN lb_shop_category AS sc ON c.id = sc.category_id
                                        GROUP BY c.id');
        $query->execute();
        $category = $query->fetchAll();

        $query = $connection->prepare('SELECT cc.*,scc.*,count(scc.child_category_id) AS counts
                                        FROM  lb_child_category AS cc 
                                        LEFT JOIN lb_shop_child_category AS scc ON cc.id = scc.child_category_id 
                                        GROUP BY cc.id');

        $query->execute();
        $childCategory = $query->fetchAll();

        $categories = array();
        foreach ($category as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $categories[$value['parent_category_id']][$key][$key1] = $value1;
            }
        }

        $childCategories = array();
        foreach ($childCategory as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $childCategories[$value['category_id']][$key][$key1] = $value1;
            }
        }

        $parentCategory = array_slice($parentCategory, 0, $size, true);
        $categories = $this->get('app.category_slugger')->getCategoriesTrimmedToSize($categories, $size);
        $childCategories = $this->get('app.category_slugger')->getCategoriesTrimmedToSize($childCategories, $size);

        return $this->render('iFlairLetsBonusFrontBundle:Category:categoryResponsive.html.twig', array(
            'parentcategories' => $parentCategory,
            'categories' => $categories,
            'childcategories' => $childCategories,
        ));
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

    public function getFooterCategoryAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();

        $query = $connection->prepare('SELECT pc.*,count(spc.parent_category_id) AS counts
                                        FROM lb_parent_category AS pc 
                                        LEFT JOIN lb_shop_parent_category AS spc ON pc.id = spc.parent_category_id 
                                         WHERE pc.status = 1 AND pc.highlightedHome = 1
                                        GROUP BY pc.id');

        $query->execute();
        $parentCategory = $query->fetchAll();

        $parentcategories = array();
        foreach ($parentCategory as $key => $value) {
            $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryType' => Constants::PARENT_CATEGORY_IDENTIFIER, 'categoryId' => $value['id']));

            if ($slug) {
                $parentcategories[$key]['parenturlSlug'] = $slug->getSlugName();
            } else {
                $parentcategories[$key]['parenturlSlug'] = '';
            }

            foreach ($value as $key1 => $value1) {
                $parentcategories[$key][$key1] = $value1;
            }
        }
        unset($connection);

        return $this->render('iFlairLetsBonusFrontBundle:Category:footerCategory.html.twig', array(
            'parentcategories' => $parentcategories,
        ));
    }

    public function getTotalParentCategoryCountAction(Request $request)
    {
        $parentCategoryCount = $this->get('app.category_slugger')->getTotalParentCategoryCount();

        return $this->render('iFlairLetsBonusFrontBundle:Category:allCategoryCounts.html.twig', array(
            'counts' => $parentCategoryCount,
        ));
    }

    public function getTotalCategoryCountAction(Request $request, $id)
    {
        $categoryCount = $this->get('app.category_slugger')->getTotalCategoryCountByParentCatId($id);

        return $this->render('iFlairLetsBonusFrontBundle:Category:allCategoryCounts.html.twig', array(
            'counts' => $categoryCount,
        ));
    }

    public function getTotalChildCategoryCountAction(Request $request, $id)
    {
        $childCategoryCount = $this->get('app.category_slugger')->getTotalChildCategoryCountByParentCatId($id);

        return $this->render('iFlairLetsBonusFrontBundle:Category:allCategoryCounts.html.twig', array(
            'counts' => $childCategoryCount,
        ));
    }

    /*
     *  NOTE    :: Its an common method to show top banner for all sections.
     *  DEFINED IN  ::  CategoryController
     *  ARGS    :: type_of_media_image, code_of_banner_for_any_section, em
     */
    public function getTopBanner($mediaType, $code, $em)
    {
        $settingsRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Settings');
        $settings = $settingsRepository->findOneBy(array(
            'status' => Settings::YES,
            'code' => $code,
        ));
        if ($settings) {
            $settingsMedia = $settings->getImage();
            if ($settingsMedia) {
                return $this->getMediaURL($settingsMedia, $mediaType);
            }
        }
    }
    public function getBannerTitleDescription($code, $connection)
    {
        $statement = $connection->prepare('SELECT s.bannertitle,s.bannerdescription
                                           FROM lb_settings AS s
                                           WHERE s.status = 1 
                                           AND s.code = :category_code ORDER BY s.id DESC LIMIT 1');
        $statement->bindValue('category_code', $code);
        $statement->execute();
        $cat_data = $statement->fetchAll();

        return $cat_data;
    }

    public function getMediaUrlByCategoryId($parent_cat_id, $cat_id, $child_cat_id, $mediaImageType, &$sm)
    {
        if (!empty($parent_cat_id) && empty($cat_id) && empty($child_cat_id)) {
            $id = $parent_cat_id;
            $entity = 'parentCategory';
        } elseif (!empty($parent_cat_id) && !empty($cat_id) && empty($child_cat_id)) {
            $id = $cat_id;
            $entity = 'Category';
        } elseif (!empty($parent_cat_id) && !empty($cat_id) && !empty($child_cat_id)) {
            $id = $child_cat_id;
            $entity = 'childCategory';
        } else {
            return $this->getTopBanner($mediaImageType, Settings::CATEGORYTOPBANNER, $sm);

            /*
             *  Issue Finder: container not generated issue with below method so not used it for now, and given comment
             */
            /*$settingsRepository = $sm->getRepository('iFlairLetsBonusAdminBundle:Settings');
            $setting = $settingsRepository->getSettingsBanner($mediaImageType, Settings::CATEGORYTOPBANNER, $sm);*/
        }

        if (!empty($id)) {
            $parentCategoryRepository = $sm->getRepository('iFlairLetsBonusAdminBundle:'.$entity);
            $parentCategory = $parentCategoryRepository->findOneBy(array('id' => $id, 'status' => 1));
            if ($parentCategory) {
                if ($entity == 'parentCategory') {
                    $parentCategoryMedia = $parentCategory->getBannerImage();
                } else {
                    $parentCategoryMedia = $parentCategory->getnImage();
                }
                if ($parentCategoryMedia) {
                    return $this->getMediaURL($parentCategoryMedia, $mediaImageType);
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

    public function getTagLabelByShopHistoryId($shopHistoryId, $connection)
    {
        $query = $connection->prepare('SELECT s.offers AS offerType, tgs.name AS tag_name, v.isnew, v.exclusive
                                       FROM lb_shop_history AS sh
                                       LEFT JOIN lb_shop AS s ON s.id = sh.shop
                                       LEFT JOIN lb_shop_voucher AS sv ON sv.shop_id = s.id
                                       LEFT JOIN lb_voucher AS v ON sv.voucher_id = v.id
                                       LEFT JOIN lb_tags AS tgs ON sh.tag = tgs.id
                                       WHERE sh.id = :id AND s.shopStatus = :shopStatus
                                       LIMIT 1');

        $query->bindValue('id', $shopHistoryId);
        $query->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        $query->execute();
        $cashback_type = $query->fetchAll(); 

        foreach ($cashback_type as $key => $cashbackData) {
            if($cashbackData['offerType'] == "cashback") {
                return $cashbackData["tag_name"];
            } elseif($cashbackData['offerType'] == "voucher") {
                if($cashbackData['exclusive'] && $cashbackData['isnew']) {
                    return  " *Novedad exclusiva";
                } elseif($cashbackData['exclusive'] && !$cashbackData['isnew']) {
                    return "*exclusivo";
                } elseif(!$cashbackData['exclusive'] && $cashbackData['isnew']) {
                    return "*neuvo";
                }
            }
        }
        return "";
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
        $cashback = [];
        if (!empty($categories_details)) {
            foreach ($categories_details as $key => $value) {
                if (!empty($value['shop_id'])) {
                    if ($value['shop_offers'] === 'cashback' || $value['shop_offers'] === 'cashback/coupons') {
                        $cashback[] = $value;
                    }
                }
            }
        }
        return $cashback;
    }

    public function getProductOfferFilter($categories_details)
    {
        $product = [];
        if (!empty($categories_details)) {
            foreach ($categories_details as $key => $value) {
                if (!empty($value['shop_id'])) {
                    if ($value['shop_offers'] === 'product') {
                        $product[] = $value;
                    }
                }
            }
        }

        return $product;
    }

    public function getVoucherOfferFilter($categories_details)
    {
        $product = [];
        if (!empty($categories_details)) {
            foreach ($categories_details as $key => $value) {
                if (!empty($value['shop_id'])) {
                    if ($value['shop_offers'] === 'voucher' || $value['shop_offers'] === 'cashback/coupons') {
                        $product[] = $value;
                    }
                }
            }
        }

        return $product;
    }

    public function getMaxVoucherFilter($categories_details)
    {
        $max_voucher = [];
        foreach ($categories_details as $key => $value) {
            if (!empty($value['shop_id']) && ($value['shop_offers'] === 'voucher' || $value['shop_offers'] === 'cashback/coupons')) {
                $max_voucher[] = $value;
            }
        }

        if (!empty($max_voucher)) {
            foreach ($max_voucher as $key => $value) {
                $voucher[$key] = $value['voucher_code_count'];
            }
            array_multisort($voucher, SORT_DESC, $max_voucher);
        } else {
            $max_voucher[0] = [];
        }

        return $max_voucher;
    }

    public function getMaxCashbackFilter($categories_details)
    {
        foreach ($categories_details as $key => $value) {
            if (!empty($value['shop_id']) && ($value['shop_offers'] === 'cashback' || $value['shop_offers'] === 'cashback/coupons')) {
                $max_cashback[] = $value;
            }
        }

        if (!empty($max_cashback)) {
            foreach ($max_cashback as $key => $value) {
                $cashback[$key] = $value['letsBonusPercentage'];
            }

            array_multisort($cashback, SORT_DESC, $max_cashback);
        } else {
            $max_cashback[0] = [];
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
    public function getShopOffersByCategoryId($parent_cat_id, $cat_id, $child_cat_id, $connection, $parent_category, $category, $child_category)
    {
        $statement = '';
        $data = array();
        if (!empty($parent_cat_id) && empty($cat_id) && empty($child_cat_id)) {
            $statement = $connection->prepare('SELECT shop_id,s.offers,s.urlAffiliate,pc.bannertitle,pc.bannerdescription
                                           FROM lb_shop_parent_category AS spc
                                           JOIN lb_parent_category AS pc ON spc.parent_category_id = pc.id
                                           JOIN lb_shop AS s on spc.shop_id = s.id
                                           WHERE s.shopStatus = :shopStatus AND pc.status = 1 
                                           AND spc.parent_category_id = :parent_category_id');
            $statement->bindValue('parent_category_id', $parent_cat_id);
            $statement->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
            $statement->execute();
            $shop_data = $statement->fetchAll();

            $statement = $connection->prepare('SELECT pc.bannertitle,pc.bannerdescription
                                           FROM lb_parent_category AS pc 
                                           WHERE pc.status = 1 
                                           AND pc.id = :parent_category_id');
            $statement->bindValue('parent_category_id', $parent_cat_id);
            $statement->execute();
            $cat_data = $statement->fetchAll();

            $data[0] = $shop_data;
            $data[1] = $cat_data;
        } elseif (!empty($parent_cat_id) && !empty($cat_id) && empty($child_cat_id)) {
            $statement = $connection->prepare('SELECT shop_id,s.offers,s.urlAffiliate,c.bannertitle,c.bannerdescription
                                           FROM lb_shop_category AS sc
                                           JOIN lb_category AS c ON sc.category_id = c.id
                                           JOIN lb_shop AS s on sc.shop_id = s.id
                                           WHERE s.shopStatus = :shopStatus AND c.status = 1
                                           AND c.parent_category_id = :parent_category_id
                                           AND sc.category_id = :category_id');
            $statement->bindValue('category_id', $cat_id);
            $statement->bindValue('parent_category_id', $parent_cat_id);
            $statement->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
            $statement->execute();
            $shop_data = $statement->fetchAll();

            $statement = $connection->prepare('SELECT c.bannertitle,c.bannerdescription
                                           FROM lb_category AS c 
                                           WHERE c.status = 1 
                                           AND c.parent_category_id = :parent_category_id
                                           AND c.id = :category_id');
            $statement->bindValue('category_id', $cat_id);
            $statement->bindValue('parent_category_id', $parent_cat_id);
            $statement->execute();
            $cat_data = $statement->fetchAll();

            $data[0] = $shop_data;
            $data[1] = $cat_data;
        } elseif (!empty($parent_cat_id) && !empty($cat_id) && !empty($child_cat_id)) {
            $statement = $connection->prepare('SELECT shop_id,s.offers,s.urlAffiliate,cc.bannertitle,cc.bannerdescription
                                           FROM lb_shop_child_category AS scc
                                           JOIN lb_child_category AS cc ON scc.child_category_id = cc.id
                                           JOIN lb_shop AS s on scc.shop_id = s.id
                                           WHERE s.shopStatus = :shopStatus AND cc.status = 1
                                           AND cc.parent_category_id = :parent_category_id
                                           AND cc.category_id = :category_id
                                           AND scc.child_category_id = :child_category_id');
            $statement->bindValue('category_id', $cat_id);
            $statement->bindValue('parent_category_id', $parent_cat_id);
            $statement->bindValue('child_category_id', $child_cat_id);
            $statement->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
            $statement->execute();
            $shop_data = $statement->fetchAll();

            $statement = $connection->prepare('SELECT cc.bannertitle,cc.bannerdescription
                                           FROM lb_child_category AS cc
                                           WHERE cc.status = 1 
                                           AND cc.parent_category_id = :parent_category_id
                                           AND cc.category_id = :category_id
                                           AND cc.id = :child_category_id');
            $statement->bindValue('category_id', $cat_id);
            $statement->bindValue('parent_category_id', $parent_cat_id);
            $statement->bindValue('child_category_id', $child_cat_id);
            $statement->execute();
            $cat_data = $statement->fetchAll();

            $data[0] = $shop_data;
            $data[1] = $cat_data;
        } else {
            $statement = $connection->prepare('SELECT s.*, s.id AS shop_id
                                           FROM lb_shop AS s WHERE s.shopStatus = :shopStatus');
            $statement->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
            $statement->execute();
            $shop_data = $statement->fetchAll();

            $cat_data = $this->getBannerTitleDescription(Settings::CATEGORYTOPBANNER, $connection);
            $data[0] = $shop_data;
            $data[1] = $cat_data;
        }

        return $data;
    }

    public function categorypageAction(Request $request, $parent_category = null, $category = null, $child_category = null)
    {
        $id = '';
        $parent_cat_id = '';
        $cat_id = '';
        $child_cat_id = '';
        $parent_cat_name = '';
        $cat_name = '';
        $child_cat_name = '';
        $userAddToFav = array();
        $sm = $this->getDoctrine()->getEntityManager();
        $connection = $sm->getConnection();
        $parent = '';
        $banner_title = '';
        $banner_description = '';
        $slug_type = $this->slugType($parent_category, $sm);

       /* if (!empty($slug_type)) {
            if ($slug_type['type'] == Constants::COLLECTION_IDENTIFIER) {
        
                $slug_type = $this->collectionType($slug_type['slug_type_id'], $sm, $connection,$request);

                return $slug_type;
            }
        }*/
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
        $cat_arr = array();

        $category_image['cat_image'] = $this->getMediaUrlByCategoryId($parent_cat_id, $cat_id, $child_cat_id, 'default_list_page_type', $sm);

        $data = $this->getShopOffersByCategoryId($parent_cat_id, $cat_id, $child_cat_id, $connection, $parent_category, $category, $child_category);

        $shop_data = $data[0];
        $cate_data = $data[1];

        if (isset($cate_data[0]['bannertitle']) && isset($cate_data[0]['bannerdescription'])) {
            $banner_title = $cate_data[0]['bannertitle'];
            $banner_description = $cate_data[0]['bannerdescription'];
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
            $shopRepository = $sm->getRepository('iFlairLetsBonusAdminBundle:Shop');
            foreach ($shop_data as $key => $shopdata) {
                $shopId = $shopdata['shop_id'];
                $shop_data_record = $this->getShopDetailsByCategoryId($shopId, $connection);
                /*$slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryType' => Constants::MARCAS_IDENTIFIER, 'categoryId' => $shop_data_record[0]['brand_id']));*/
                /*
                 * FOR CASHBACK :: CLICKS :: AFFILIATION MANAGEMENT
                 */
                $shop = $shopRepository->findOneBy(array(
                    'id' => $shopId,
                    'shopStatus' => Shop::SHOP_ACTIVATED,
                ));

                // if condition for cashback && voucher is not exclusive
                if ((isset($shopId) && $shopdata['offers'] == 'cashback') || (isset($shopId) && $shopdata['offers'] == 'cashback/coupons') ||
                    (isset($shopId) && $shop_data_record[0]['exclusive'] == 0 && $shopdata['offers'] == 'voucher')) 
                {
                    $shopHistoryRepo = $sm->getRepository('iFlairLetsBonusAdminBundle:shopHistory');
                    $query = $shopHistoryRepo->createQueryBuilder('sh')
                        ->join('iFlairLetsBonusAdminBundle:Slug', 'sl', \Doctrine\ORM\Query\Expr\Join::WITH, 'sl.categoryId = sh.id')
                        ->where('sh.shop = :shopId')
                        ->setParameter('shopId', $shopId)
                        ->andWhere('sl.categoryType = :shopType')
                        ->setParameter('shopType', Constants::SHOP_IDENTIFIER)                        
                        ->getQuery();
                    $shop_history = $query->getResult();
                    $voucher_count = $this->getVoucherCountByShopId($shopId, $connection);
                    foreach ($shop_history as $shop_value) {
                        $shopHistory = $shop_value;
                        $shopHistoryId = $shop_value->getId();
                        $affiliationUrlArgs = $affiliationArgs->getAffiliation($shop, $shopHistory, $sm);
                        $cat_arr[$i][$shopHistoryId]['shop_affiliate_url'] = '';
                        $cat_arr[$i][$shopHistoryId]['shop_affiliate_url_origin'] = '';
                        if (!empty($shop_value->getUrlAffiliate())) {
                            $cat_arr[$i][$shopHistoryId]['shop_affiliate_url_origin'] = $shop_value->getUrlAffiliate();
                            $redirect_url = $shop_value->getUrlAffiliate().$affiliationUrlArgs;
                            $cat_arr[$i][$shopHistoryId]['shop_affiliate_url'] = $redirect_url;
                        }
                        $cat_arr[$i][$shopHistoryId]['program_id'] = $shop->getProgramId();
                        $variations = $this->getShopHistoryVariationByShopHistoryId($shopHistoryId, $sm);
                        $cat_arr[$i][$shopHistoryId]['shop_history_variation'] = $variations;
                        $cat_arr[$i][$shopHistoryId]['voucher_code_count'] = 0;
                        if ($voucher_count) {
                            $cat_arr[$i][$shopHistoryId]['voucher_code_count'] = count($voucher_count);
                        }

                        $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $shopHistoryId));
                        if ($slug) {
                            $cat_arr[$i][$shopHistoryId]['slug_name'] = $slug->getSlugName();
                        } else {
                            $cat_arr[$i][$shopHistoryId]['slug_name'] = '';
                        }

                        if(!empty($shop_value->getTag())) {
                            $cat_arr[$i][$shopHistoryId]['cashback_type_value'] = $shop_value->getTag()->getName();
                        } else {
                            $cat_arr[$i][$shopHistoryId]['cashback_type_value'] = '';
                        }

                        $cat_arr[$i][$shopHistoryId]['shop_offers'] = $shopdata['offers'];
                        if($shopdata['offers'] == 'cashback') {
                          $cat_arr[$i][$shopHistoryId]['shop_type'] = 'cashback';
                        }
                        if($shopdata['offers'] == 'cashback/coupons') {
                          $cat_arr[$i][$shopHistoryId]['shop_type'] = 'cashback';
                        }
                        $cat_arr[$i][$shopHistoryId]['shop_history_id'] = $shopHistoryId;
                        $cat_arr[$i][$shopHistoryId]['shop_history_shop_title'] = $shop_value->getTitle();
                        $cat_arr[$i][$shopHistoryId]['shop_history_shop_description'] = strip_tags($shop_value->getIntroduction());
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
                        $cat_arr[$i][$shopHistoryId]['voucher_expire_date'] = $shop_data_record[0]['publish_end_date'];
                        $cat_arr[$i][$shopHistoryId]['exclusive'] = $shop_data_record[0]['exclusive'];
                        $cat_arr[$i][$shopHistoryId]['brand_logo'] = $shop_data_record[0]['brand_logo'];
                        if (!empty($shop_data_record[0]['brand_image'])) {
                            $cat_arr[$i][$shopHistoryId]['brand_logo'] = $this->getImageUrl($voucherProgramsEntity, $shop_data_record[0]['brand_image'], 'brand_on_shop', 'voucherprogram');
                        }
                        if (!empty($shop_data_record[0]['brand_popup_image'])) {
                            $cat_arr[$i][$shopHistoryId]['brand_logo_popup'] = $this->getImageUrl($voucherProgramsEntity, $shop_data_record[0]['brand_popup_image'], 'cashback_voucher_popup', 'voucherprogram');
                        } else {
                            $cat_arr[$i][$shopHistoryId]['brand_logo_popup'] = $shop_data_record[0]['brand_logo'];
                        }
                        $cat_arr[$i][$shopHistoryId]['brand_name'] = $shop_data_record[0]['brand_name'];

                        $cat_arr[$i][$shopHistoryId]['brand_id'] = $shop_data_record[0]['brand_id'];

                        $cat_arr[$i][$shopHistoryId]['shop_affiliate_url'] = $shop_data_record[0]['urlAffiliate'];

                        $cat_arr[$i][$shopHistoryId]['cashbackPrice'] = $shop_data_record[0]['cashbackPrice'];
                        $cat_arr[$i][$shopHistoryId]['cashbackPercentage'] = $shop_data_record[0]['cashbackPercentage'];

                        $cat_arr[$i][$shopHistoryId]['rating'] = $homepageController->ratingAction($shopId, $shopHistoryId);
                        $voucherFinal = $brandController->getVoucherByShopId($shop->getId(), $shop->getVprogram(), $connection);
                        if (count($voucherFinal) > 0) {
                            $cat_arr[$i][$shopHistoryId]['voucher_id'] = $voucherFinal[0]['voucher_id'];
                            $cat_arr[$i][$shopHistoryId]['voucher_code'] = $voucherFinal[0]['voucher_code'];
                            
                            if($shopdata['offers'] == 'voucher') {
                                if(isset($voucherFinal[0]['voucher_code']) && !empty($voucherFinal[0]['voucher_code'])) {
                                    $cat_arr[$i][$shopHistoryId]['shop_type'] = 'coupon';
                                } else {
                                    $cat_arr[$i][$shopHistoryId]['shop_type'] = 'oferta';
                                }
                            }
                            $cat_arr[$i][$shopHistoryId]['voucher_name'] = $voucherFinal[0]['voucher_name'];
                            if (strtotime($voucherFinal[0]['voucher_expire_date']) > strtotime('-30 days')) {
                                $date = strtotime($voucherFinal[0]['voucher_expire_date']);
                                $dat = date('d/m/y', $date);
                            } else {
                                $dat = '';
                            }
                            $cat_arr[$i][$shopHistoryId]['voucher_expire_date'] = $dat;
                            $cat_arr[$i][$shopHistoryId]['discount_amount'] = $voucherFinal[0]['discount_amount'];
                            $cat_arr[$i][$shopHistoryId]['is_percentage'] = $voucherFinal[0]['is_percentage'];
                            $cat_arr[$i][$shopHistoryId]['exclusive'] = $voucherFinal[0]['exclusive'];
                            $cat_arr[$i][$shopHistoryId]['short_description'] = $voucherFinal[0]['short_description'];
                            $cat_arr[$i][$shopHistoryId]['description'] = strip_tags($shop_data_record[0]['description']);
                            $cat_arr[$i][$shopHistoryId]['default_track_uri'] = $voucherFinal[0]['default_track_uri'];
                            $cat_arr[$i][$shopHistoryId]['description'] = $voucherFinal[0]['description'];
                            $cat_arr[$i][$shopHistoryId]['voucher_program_name'] = $shop->getVprogram()->getProgramName();
                        }
                    }
                    // end if condition for cashback
                } elseif ((isset($shopId) && $shopdata['offers'] == 'offer') || (isset($shopId) && $shopdata['offers'] == 'voucher' && ($shop_data_record[0]['exclusive'] == 1 || $shop_data_record[0]['isnew'] == 1))) {
                    
                    // checking offer type voucher
                    $shop_history = $sm->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('shop' => $shopId), array('startDate'=>'DESC'), 1);
                    $shopHistoryId = $shop_history->getId();
                    $variations = $this->getShopHistoryVariationByShopHistoryId($shopHistoryId, $sm);
                    $cat_arr[$i][$shopHistoryId]['shop_history_variation'] = $variations;
                    $cat_arr[$i][$shopHistoryId]['cashback_type_value'] = $this->getTagLabelByShopHistoryId($shopHistoryId, $connection);
                    
                    $voucher_count = $this->getVoucherCountByShopId($shopId, $connection);
                    $cat_arr[$i][$shopHistoryId]['voucher_code_count'] = 0;
                    if ($voucher_count) {
                        $cat_arr[$i][$shopHistoryId]['voucher_code_count'] = count($voucher_count);
                    }

                    $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $shopHistoryId));
                    if ($slug) {
                        $cat_arr[$i][$shopHistoryId]['slug_name'] = $slug->getSlugName();
                    } else {
                        $cat_arr[$i][$shopHistoryId]['slug_name'] = '';
                    }
                    $cat_arr[$i][$shopHistoryId]['shop_offers'] = $shopdata['offers'];
                    if($shopdata['offers'] == 'cashback' || $shopdata['offers'] == 'cashback/coupons') {
                        $cat_arr[$i][$shopHistoryId]['shop_type'] = 'cashback';
                    }
                     if($shopdata['offers'] == 'offer') {
                        $cat_arr[$i][$shopHistoryId]['shop_type'] = 'oferta';
                    }
                    $cat_arr[$i][$shopHistoryId]['shop_affiliate_url'] = $shopdata['urlAffiliate'];
                    $cat_arr[$i][$shopHistoryId]['shop_history_id'] = $shopHistoryId;
                    $cat_arr[$i][$shopHistoryId]['shop_history_shop_title'] = $shop_history->getTitle();
                    $cat_arr[$i][$shopHistoryId]['shop_history_shop_description'] = strip_tags($shop_history->getIntroduction());
                   // $cat_arr[$i][$shopHistoryId]['shop_history_shop_end_date'] = $shop_history->getEndDate();
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
                    $cat_arr[$i][$shopHistoryId]['voucher_expire_date'] = $shop_data_record[0]['publish_end_date'];
                    $cat_arr[$i][$shopHistoryId]['exclusive'] = $shop_data_record[0]['exclusive'];
                    $cat_arr[$i][$shopHistoryId]['brand_logo'] = $shop_data_record[0]['brand_logo'];
                    if (!empty($shop_data_record[0]['brand_image'])) {
                        $cat_arr[$i][$shopHistoryId]['brand_logo'] = $this->getImageUrl($voucherProgramsEntity, $shop_data_record[0]['brand_image'], 'brand_on_shop', 'voucherprogram');
                    }
                    if (!empty($shop_data_record[0]['brand_popup_image'])) {
                        $cat_arr[$i][$shopHistoryId]['brand_logo_popup'] = $this->getImageUrl($voucherProgramsEntity, $shop_data_record[0]['brand_popup_image'], 'cashback_voucher_popup', 'voucherprogram');
                    } else {
                        $cat_arr[$i][$shopHistoryId]['brand_logo_popup'] = $shop_data_record[0]['brand_logo'];
                    }
                    $cat_arr[$i][$shopHistoryId]['brand_name'] = $shop_data_record[0]['brand_name'];
                    $cat_arr[$i][$shopHistoryId]['brand_id'] = $shop_data_record[0]['brand_id'];
                    $cat_arr[$i][$shopHistoryId]['shop_affiliate_url'] = $shop_data_record[0]['urlAffiliate'];
                    $cat_arr[$i][$shopHistoryId]['cashbackPrice'] = $shop_data_record[0]['cashbackPrice'];
                    $cat_arr[$i][$shopHistoryId]['cashbackPercentage'] = $shop_data_record[0]['cashbackPercentage'];
                    $cat_arr[$i][$shopHistoryId]['rating'] = $homepageController->ratingAction($shopId, $shopHistoryId);
                    $voucherFinal = $brandController->getVoucherByShopId($shop->getId(), $shop->getVprogram(), $connection);
                    if (count($voucherFinal) != 0) {
                        $cat_arr[$i][$shopHistoryId]['voucher_id'] = $voucherFinal[0]['voucher_id'];
                        $cat_arr[$i][$shopHistoryId]['voucher_code'] = $voucherFinal[0]['voucher_code'];

                        if($shopdata['offers'] == 'voucher') {
                            if(isset($voucherFinal[0]['voucher_code']) && !empty($voucherFinal[0]['voucher_code'])) {
                                $cat_arr[$i][$shopHistoryId]['shop_type'] = 'coupon';
                            } else {
                                $cat_arr[$i][$shopHistoryId]['shop_type'] = 'oferta';
                            }
                        }

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
                        $cat_arr[$i][$shopHistoryId]['description'] = strip_tags($shop_data_record[0]['description']);
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
        $categories_details = $this->getFilter($cat_arr);

        $max_voucher = $this->getMaxVoucherFilter($categories_details);
        $max_cashback = $this->getMaxCashbackFilter($categories_details);
        $max_voucher_cashback = $this->getMaxVoucherCashback($categories_details);
        $cashback = $this->getCahbackOfferFilter($categories_details);
        $product = $this->getProductOfferFilter($categories_details);
        $voucher = $this->getVoucherOfferFilter($categories_details);

        $product_count = count($categories_details);
        $init_count = 12;
        $final_count = count($categories_details);
        $remove_load_more = 0;

        $categoryFilterController = new CategoryFilterController();
        $categoryFilterController->setContainer($this->container);

        if($request->get('offer')){
          
            $offerFilter=$request->get('offer');
            $filterNavigationCounter = $categoryFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$categories_details,$request);
            $offerFilterData = $filterNavigationCounter['offerFilterData'];
            $categories_details = $filterNavigationCounter['data_details'];
            $remove_load_more = $filterNavigationCounter['remove_load_more'];
            $target_count = $filterNavigationCounter['target_count'];
            if($request->get('alphabet')){
                $alphabetFilter=$request->get('alphabet');
                $filterNavigationCounter = $categoryFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$offerFilterData,$request);
                $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                $categories_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('category_id_string')){
                    $catagoryFilter=$request->get('category_id_string');
                    $filterNavigationCounter = $categoryFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$alphabetFilterData,$request);
                    $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                    $categories_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }elseif($request->get('category_id_string')){
                $catagoryFilter=$request->get('category_id_string');
                $filterNavigationCounter = $categoryFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$offerFilterData,$request);
                $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                $categories_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('alphabet')){
                    $alphabetFilter=$request->get('alphabet');
                    $filterNavigationCounter = $categoryFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$catagoryFilterData,$request);
                    $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                    $categories_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }
            return $this->filterCategoryPage($categories_details,$userAddToFav,$category_image['cat_image'],$cashback,$product,$voucher,$max_voucher[0],$max_cashback[0],$max_voucher_cashback[0],$parent_cat_name,$cat_name,$child_cat_name,$banner_title,$banner_description,$final_count,$init_count,$target_count,$remove_load_more,$request->get('offer'),$request->get('alphabet'),$request->get('category_id_string'), $product_count);
        }elseif($request->get('alphabet')){

            $alphabetFilter=$request->get('alphabet');
            $filterNavigationCounter = $categoryFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$categories_details,$request);
            $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
            $categories_details = $filterNavigationCounter['data_details'];
            $remove_load_more = $filterNavigationCounter['remove_load_more'];
            $target_count = $filterNavigationCounter['target_count'];
            if($request->get('offer')){
                $offerFilter=$request->get('offer');
                $filterNavigationCounter = $categoryFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$alphabetFilterData,$request);
                $offerFilterData = $filterNavigationCounter['offerFilterData'];
                $categories_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('category_id_string')){
                    $catagoryFilter=$request->get('category_id_string');
                    $filterNavigationCounter = $categoryFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$offerFilterData,$request);
                    $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                    $categories_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }elseif($request->get('category_id_string')){
                $catagoryFilter=$request->get('category_id_string');
                $filterNavigationCounter = $categoryFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$alphabetFilterData,$request);
                $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
                $categories_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('offer')){
                    $offerFilter=$request->get('offer');
                    $filterNavigationCounter = $categoryFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$catagoryFilterData,$request);
                    $offerFilterData = $filterNavigationCounter['offerFilterData'];
                    $categories_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }
            return $this->filterCategoryPage($categories_details,$userAddToFav,$category_image['cat_image'],$cashback,$product,$voucher,$max_voucher[0],$max_cashback[0],$max_voucher_cashback[0],$parent_cat_name,$cat_name,$child_cat_name,$banner_title,$banner_description,$final_count,$init_count,$target_count,$remove_load_more,$request->get('offer'),$request->get('alphabet'),$request->get('category_id_string'), $product_count);
        }elseif($request->get('category_id_string')){
            $catagoryFilter=$request->get('category_id_string');
            $filterNavigationCounter = $categoryFilterController->checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$categories_details,$request);
            $catagoryFilterData = $filterNavigationCounter['catagoryFilterData'];
            $categories_details = $filterNavigationCounter['data_details'];
            $remove_load_more = $filterNavigationCounter['remove_load_more'];
            $target_count = $filterNavigationCounter['target_count'];
            if($request->get('offer')){
                $offerFilter=$request->get('offer');
                $filterNavigationCounter = $categoryFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$catagoryFilterData,$request);
                $offerFilterData = $filterNavigationCounter['offerFilterData'];
                $categories_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('alphabet')){
                    $alphabetFilter=$request->get('alphabet');
                    $filterNavigationCounter = $categoryFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$offerFilterData,$request);
                    $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                    $categories_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }elseif($request->get('alphabet')){
                $alphabetFilter=$request->get('alphabet');
                $filterNavigationCounter = $categoryFilterController->checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$catagoryFilterData,$request);
                $alphabetFilterData = $filterNavigationCounter['alphabetFilterData'];
                $categories_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                if($request->get('offer')){
                    $offerFilter=$request->get('offer');
                    $filterNavigationCounter = $categoryFilterController->checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$alphabetFilterData,$request);
                    $offerFilterData = $filterNavigationCounter['offerFilterData'];
                    $categories_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                }
            }
            return $this->filterCategoryPage($categories_details,$userAddToFav,$category_image['cat_image'],$cashback,$product,$voucher,$max_voucher[0],$max_cashback[0],$max_voucher_cashback[0],$parent_cat_name,$cat_name,$child_cat_name,$banner_title,$banner_description,$final_count,$init_count,$target_count,$remove_load_more,$request->get('offer'),$request->get('alphabet'),$request->get('category_id_string'), $product_count);
        }else {
            if ($request->get('target_count')) {
                $target_count = $request->get('target_count');
                if ($request->get('alphabet') == 'TODAS') {
                    $target_count = 1;
                }
                $filterNavigationCounter = $categoryFilterController->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$categories_details);
                $categories_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                return $this->filterCategoryPage($categories_details, $userAddToFav, $category_image['cat_image'], $cashback, $product, $voucher, $max_voucher[0], $max_cashback[0], $max_voucher_cashback[0], $parent_cat_name, $cat_name, $child_cat_name, $banner_title, $banner_description, $final_count, $init_count, $target_count, $remove_load_more, $request->get('offer'), $request->get('alphabet'), $request->get('category_id_string'), $product_count);
            } else {
                $target_count = $execute_count = 1;
                $filterNavigationCounter = $categoryFilterController->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$categories_details);
                 
                $categories_details = $filterNavigationCounter['data_details'];
                $remove_load_more = $filterNavigationCounter['remove_load_more'];
                $target_count = $filterNavigationCounter['target_count'];
                return $this->renderCategoryPage($categories_details, $userAddToFav, $category_image['cat_image'], $cashback, $product, $voucher, $max_voucher[0], $max_cashback[0], $max_voucher_cashback[0], $parent_cat_name, $cat_name, $child_cat_name, $banner_title, $banner_description, $final_count, $init_count, $execute_count, $target_count, $remove_load_more);
            }
        }
    }

    public function filterCategoryPage($categories_details,$userAddToFav,$category_image,$cashback,$product,$voucher,$max_voucher,$max_cashback,$max_voucher_cashback,$parent_cat_name,$cat_name,$child_cat_name,$banner_title,$banner_description,$final_count,$init_count,$target_count,$remove_load_more,$OF,$AF,$CF,$product_count){
        $render_data = array(
            'category_detail' => $categories_details,
            'addtofevlist' => $userAddToFav,
            'cat_image' => $category_image,
            'cashback_shop' => $cashback,
            'product_shop' => $product,
            'voucher_shop' => $voucher,
            'max_voucher_data' => $max_voucher,
            'max_cashback_percentage' => $max_cashback,
            'max_voucher_cashback' => $max_voucher_cashback,
            'parent_slug_name' => $parent_cat_name,
            'middle_slug_name' => $cat_name,
            'child_slug_name' => $child_cat_name,
            'banner_title' => $banner_title,
            'banner_description' => $banner_description,
            'final_count' => $final_count,
            'init_count' => $init_count,
            'target_count' => $target_count,
            'target_count_category_filter' => $target_count,
            'remove_load_more' => $remove_load_more,
            'OF' => json_encode($OF),
            'AF' => $AF,
            'CF' => $CF
        );
        $arr = array('product_count'=>$product_count,'html' => $this->render('iFlairLetsBonusFrontBundle:Category:category-page-loadmore.html.twig',$render_data)->getContent());
        return new Response(json_encode($arr));
    }

    public function renderCategoryPage($categories_details,$userAddToFav,$category_image,$cashback,$product,$voucher,$max_voucher,$max_cashback,$max_voucher_cashback,$parent_cat_name,$cat_name,$child_cat_name,$banner_title,$banner_description,$final_count,$init_count,$execute_count,$target_count,$remove_load_more){
        $render_data = [
            'category_detail' => $categories_details,
            'addtofevlist' => $userAddToFav,
            'cat_image' => $category_image,
            'cashback_shop' => $cashback,
            'product_shop' => $product,
            'voucher_shop' => $voucher,
            'max_voucher_data' => $max_voucher,
            'max_cashback_percentage' => $max_cashback,
            'max_voucher_cashback' => $max_voucher_cashback,
            'parent_slug_name' => $parent_cat_name,
            'middle_slug_name' => $cat_name,
            'child_slug_name' => $child_cat_name,
            'banner_title' => $banner_title,
            'banner_description' => $banner_description,
            'execute_count' => $execute_count,
            'final_count' => $final_count,
            'init_count' => $init_count,
            'target_count' => $target_count,
            'remove_load_more' => $remove_load_more,
        ];

        return $this->render('iFlairLetsBonusFrontBundle:Category:category-page.html.twig', $render_data);
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

  
}
