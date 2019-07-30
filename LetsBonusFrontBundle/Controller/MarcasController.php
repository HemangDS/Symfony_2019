<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use iFlair\LetsBonusAdminBundle\Entity\Shop;
use iFlair\LetsBonusAdminBundle\Entity\Slug;
use iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms;
use iFlair\LetsBonusAdminBundle\Slug\Constants;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MarcasController extends Controller
{
    public function getMenuMarcasAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();

        /* $query = $connection->prepare('SELECT vp.*,s.slugName AS marcas_slugname FROM lb_voucher_programs AS vp JOIN lb_slug AS s ON vp.id = s.categoryId WHERE s.categoryType ='.Constants::MARCAS_IDENTIFIER.' ORDER BY vp.program_name');*/
        /*$query = $connection->prepare('SELECT sh.*,s.slugName AS shop_history_slugname FROM 
                                lb_shop_history AS sh JOIN lb_slug AS s ON sh.id = s.categoryId 
                                JOIN lb_shop AS sp ON sh.shop = sp.id 
                                WHERE sp.shopStatus = 1 AND s.categoryType ='.Constants::SHOP_IDENTIFIER.' ORDER BY sh.title');*/

        $query = $connection->prepare('SELECT vp.id, vp.program_name,sh.id AS shop_history_id,sl.slugName AS shop_history_slugname,sh.created,sh.title FROM lb_voucher_programs AS vp LEFT JOIN lb_shop AS s ON s.vprogram_id = vp.id LEFT JOIN lb_shop_history AS sh ON sh.shop = s.id LEFT JOIN lb_slug AS sl ON sh.id = sl.categoryId WHERE s.shopStatus = :shopStatus AND sl.categoryType = :categoryType GROUP BY vp.id ORDER BY sh.created DESC, sh.id DESC LIMIT 12');
        $query->bindValue('categoryType', Constants::SHOP_IDENTIFIER);
        $query->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        $query->execute();
        $marcas = $query->fetchAll();

        $totalbrands = count($marcas);        
        if($marcas) {
            foreach($marcas as $key1 => $value) {                
                $marcasRelatedBrands = $this->getRelatedBrandsData($value['shop_history_id']);
                $marcasRelatedShop = $this->getBrandAssociatedShop($value['shop_history_id']);
             
                if (count($marcasRelatedShop) > 0) {
                    $marcasRelatedShop = array_slice($marcasRelatedShop, 0, 1);
                }
                $marcas[$key1]['related_brands'] = $marcasRelatedBrands;
                $marcas[$key1]['related_shop'] = $marcasRelatedShop;
                $marcas[$key1]['slug_name'] = $value['shop_history_slugname'];
            }            
        }
       
        $mainDefaultMarcasMenuData = array();
        $marcasHighestRelatedShop = $this->getHighestBrandAssociatedShop();
        if (count($marcasHighestRelatedShop) > 0) {
            $marcasHighestRelatedShop = array_slice($marcasHighestRelatedShop, 0, 1);
        }
        $mainDefaultMarcasMenuData[0]['related_brands'] = $this->getHighestRelatedBrandsData();
        $mainDefaultMarcasMenuData[0]['related_shop'] = $marcasHighestRelatedShop;

        return $this->render('iFlairLetsBonusFrontBundle:Marcas:menumarcas.html.twig', array(
            'marcas' => $marcas,
            'totalbrands' => $totalbrands,
            'mainDefaultMarcasMenuDatas' => $mainDefaultMarcasMenuData,
        ));
    }

    public function getHighestBrandAssociatedShop()
    {
        $em = $this->getDoctrine()->getEntityManager();

        /* Query to get brand relate sidebar voucher's data */
        unset($connection);
        $connection = $em->getConnection();
        $query = $connection->prepare('SELECT s.id,s.image_id,s.vprogram_id,vp.logo_path,sh.cashbackPercentage,sh.cashbackPrice, sh.cashbackPercentage, cs.type
                                        FROM lb_voucher_programs AS vp
										JOIN lb_voucher AS v ON vp.id = v.program_id
										JOIN lb_shop_voucher AS sv ON v.id = sv.voucher_id
										JOIN lb_shop AS s ON s.id = sv.shop_id
										JOIN lb_cachback_settings_shop AS css ON css.shop_id = s.id
										JOIN lb_cashbackSettings AS cs ON cs.id = css.cashback_settings_id
										JOIN lb_shop_history AS sh ON s.id = sh.shop
                                        JOIN lb_slug AS sl ON sl.categoryId = sh.id AND sl.categoryType = :categoryType
										WHERE s.shopStatus = :shopStatus
										GROUP BY s.id
										ORDER BY sh.cashbackPercentage DESC');
        $query->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        $query->bindValue('categoryType', Constants::SHOP_IDENTIFIER);
        $query->execute();
        $marcasSideBarData = $query->fetchAll();

        $entities = $em->getRepository('iFlairLetsBonusAdminBundle:Shop');
        $vp_entities = $em->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms');
        if($marcasSideBarData) {
            foreach ($marcasSideBarData as $key => $shopData) {
                $media = $entities->findOneBy(array('image' => $shopData['image_id']));
                if (!empty($media) && !empty($shopData['image_id'])) {
                    $media = $media->getImage();
                    if (isset($media)) {
                        $mediaManager = $this->get('sonata.media.pool');
                        $provider = $mediaManager->getProvider($media->getProviderName());
                        $productpublicUrl = $provider->generatePublicUrl($media, 'default_offertas_del_dia_type');
                        $marcasSideBarData[$key]['shop_image_path'] = $productpublicUrl;
                    }
                }

                $media_logo = $vp_entities->findOneBy(array('id' => $shopData['vprogram_id']));
                if (!empty($media_logo) && !empty($shopData['image_id'])) {
                    $media_logo = $media_logo->getImage();
                    if (isset($media_logo)) {
                        $format1 = $provider->getFormatName($media_logo, 'offertas_del_dia_type');
                        $logo_url = $provider->generatePublicUrl($media_logo, $format1);
                        $marcasSideBarData[$key]['shop_logo_path'] = $logo_url;
                    }
                } else {
                    $marcasSideBarData[$key]['shop_logo_path'] = $shopData['logo_path'];
                }
            }
        }
        
        return $marcasSideBarData;
    }

    public function getHighestRelatedBrandsData()
    {
        $marcasRelatedBrands = array();
        $em = $this->getDoctrine()->getEntityManager();

        /* Query to get brand relate sidebar voucher's data */
            unset($connection);
        $connection = $em->getConnection();

        $query = $connection->prepare('SELECT vp.id AS vpid,vp.logo_path,vp.image_id,sl.slugName, sh.cashbackPrice,sh.cashbackPercentage, count(vp.id) AS offerCount FROM lb_shop_history AS sh LEFT JOIN lb_shop AS s ON s.id = sh.shop LEFT JOIN lb_voucher_programs AS vp ON vp.id = s.vprogram_id JOIN lb_slug AS sl ON sl.categoryId = sh.id WHERE sl.categoryType = :slugCategoryType AND s.vprogram_id IS NOT NULL AND vp.image_id IS NOT NULL AND s.shopStatus = :shopStatus GROUP BY sh.shop ORDER BY sh.cashbackPercentage DESC LIMIT 4');        
        $query->bindValue('slugCategoryType',Constants::SHOP_IDENTIFIER);
        $query->bindValue('shopStatus',Shop::SHOP_ACTIVATED);
        $query->execute();
        $marcasRelatedBrandsResult = $query->fetchAll();

        if($marcasRelatedBrandsResult) {
            $vp_entities = $em->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms');
            $provider = $this->container->get('sonata.media.provider.image');
            foreach($marcasRelatedBrandsResult as $marcasRelatedBrandKey => $marcasRelatedBrandValue) {
                $marcasRelatedBrands[$marcasRelatedBrandKey]['offer_count'] = $marcasRelatedBrandValue['offerCount'];
                $marcasRelatedBrands[$marcasRelatedBrandKey]['cashback_percent'] = ($marcasRelatedBrandValue['cashbackPercentage'])?$marcasRelatedBrandValue['cashbackPercentage']:0;
                $marcasRelatedBrands[$marcasRelatedBrandKey]['cashback_price'] = $marcasRelatedBrandValue['cashbackPrice'];
                $marcasRelatedBrands[$marcasRelatedBrandKey]['id'] = $marcasRelatedBrandValue['vpid'];
                $marcasRelatedBrands[$marcasRelatedBrandKey]['slug_name'] = $marcasRelatedBrandValue['slugName'];
                $marcasRelatedBrands[$marcasRelatedBrandKey]['logo_path'] = $marcasRelatedBrandValue['logo_path'];                
                if(!empty($marcasRelatedBrandValue['image_id'])) {
                    $media_logo = $vp_entities->findOneBy(array('id' => $marcasRelatedBrandValue['vpid']));
                    if (!empty($media_logo) && !empty($marcasRelatedBrandValue['image_id'])) {
                        $media_logo = $media_logo->getImage();
                        if (isset($media_logo)) {
                            $format1 = $provider->getFormatName($media_logo, 'brand_on_shop');
                            $logo_url = $provider->generatePublicUrl($media_logo, $format1);
                            $marcasRelatedBrands[$marcasRelatedBrandKey]['logo_path'] = $logo_url;
                        }
                    }
                }
            }
        }

        /*$marcasRelatedBrandsQB = $em->createQueryBuilder();
        $marcasRelatedBrandsResult = $marcasRelatedBrandsQB->select('partial vp.{id,image,logoPath}, partial sl.{id,slugName}, partial sh.{id,cashbackPercentage}',$marcasRelatedBrandsQB->expr()->count('vp.id').' AS offerCount')
                            ->from('iFlairLetsBonusAdminBundle:shopHistory',  'sh')
                            ->join('iFlairLetsBonusAdminBundle:Shop', 's', \Doctrine\ORM\Query\Expr\Join::WITH, 's.id = sh.shop')
                            ->join('iFlairLetsBonusAdminBundle:VoucherPrograms', 'vp', \Doctrine\ORM\Query
                                \Expr\Join::WITH, 'vp.id = s.vprogram')
                            ->join('iFlairLetsBonusAdminBundle:Slug', 'sl', \Doctrine\ORM\Query\Expr\Join::WITH, 'sl.categoryId = sh.id')
                            ->where('sl.categoryType = :slugCategoryType')
                            ->andWhere('s.vprogram IS NOT NULL')
                            ->setParameter('slugCategoryType', Constants::SHOP_IDENTIFIER)
                            ->groupBy('sh.shop')
                            ->orderBy('sh.cashbackPercentage','DESC')
                            ->setFirstResult(0)
                            ->setMaxResults(4)
                            ->getQuery()
                            //->getSql()
                            ->getResult()
                        ;        
        if($marcasRelatedBrandsResult) {
            foreach($marcasRelatedBrandsResult as $marcasRelatedBrandKey => $marcasRelatedBrandObject) {
                $marcasRelatedBrands[$marcasRelatedBrandKey]['offer_count'] = $marcasRelatedBrandObject->getOfferCount();
                $marcasRelatedBrands[$marcasRelatedBrandKey]['cashback_percent'] = $marcasRelatedBrandObject->getCashbackPercentage();
                $marcasRelatedBrands[$marcasRelatedBrandKey]['offer_count'] = $marcasRelatedBrandObject->getId();
            }
        }*/        

        return $marcasRelatedBrands;
    }

    public function getBrandAssociatedShop($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        /* Query to get brand relate sidebar voucher's data */
        unset($connection);
        $connection = $em->getConnection();
        $query = $connection->prepare('SELECT 
                            s.id,s.image_id,s.vprogram_id,s.offers, 
                            vp.id AS voucher_program_id ,vp.logo_path, vp.program_name, vp.pop_up_image_id, 
                            vp.image_id as voucher_image_id, 
                            sh.cashbackPercentage,sh.cashbackPrice, sh.title, sh.cashbackPercentage, 
                            sh.id as shop_history_id, sh.urlAffiliate, sl.slugName, 
                            tgs.name as tagname 
                            FROM lb_voucher_programs AS vp 
                            LEFT JOIN lb_shop AS s ON s.vprogram_id = vp.id 
                            LEFT JOIN lb_shop_history AS sh ON s.id = sh.shop 
                            LEFT JOIN lb_tags as tgs ON sh.tag=tgs.id 
                            LEFT JOIN lb_slug AS sl ON sl.categoryId = sh.id AND sl.categoryType = :categoryType WHERE sh.id = :shid AND s.shopStatus = :shopStatus GROUP BY s.id  ORDER BY sh.cashbackPercentage DESC');
        $query->bindValue('shid', $id);
        $query->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        $query->bindValue('categoryType', Constants::SHOP_IDENTIFIER);
        $query->execute();
        $marcasSideBarData = $query->fetchAll();
        $entities = $em->getRepository('iFlairLetsBonusAdminBundle:Shop');
        $vp_entities = $em->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms');
        $provider = $this->container->get('sonata.media.provider.image');
        if($marcasSideBarData) {
            foreach ($marcasSideBarData as $key => $shopData) {
                $media = $entities->findOneBy(array('image' => $shopData['image_id']));            
                if (!empty($media) && !empty($shopData['image_id'])) {
                    $media = $media->getImage();
                    if (isset($media)) {
                        $format = $provider->getFormatName($media, 'offertas_del_dia_type');
                        $image_url = $provider->generatePublicUrl($media, $format);
                        $marcasSideBarData[$key]['shop_image_path'] = $image_url;
                    }
                }

                $media_logo = $vp_entities->findOneBy(array('id' => $shopData['vprogram_id']));
                if (!empty($media_logo) && !empty($shopData['voucher_image_id'])) {
                    $media_logo = $media_logo->getImage();
                    if (isset($media_logo)) {
                        $format1 = $provider->getFormatName($media_logo, 'brand_on_shop');
                        $logo_url = $provider->generatePublicUrl($media_logo, $format1);
                        $marcasSideBarData[$key]['shop_logo_path'] = $logo_url;
                    }
                } else {
                    $marcasSideBarData[$key]['shop_logo_path'] = $shopData['logo_path'];
                }

                $media_logo = $vp_entities->findOneBy(array('id' => $shopData['vprogram_id']));
                if (!empty($media_logo) && !empty($shopData['pop_up_image_id'])) {
                    $media_logo = $media_logo->getPopUpImage();
                    if (isset($media_logo)) {
                        $format1 = $provider->getFormatName($media_logo, 'cashback_voucher_popup');
                        $logo_url = $provider->generatePublicUrl($media_logo, $format1);
                        $marcasSideBarData[$key]['shop_logo_popup'] = $logo_url;
                    }
                } else {
                    $marcasSideBarData[$key]['shop_logo_popup'] = $marcasSideBarData[$key]['shop_logo_path'];
                }
                $marcasSideBarData[$key]['slug_name'] = $shopData['slugName'];
                $marcasSideBarData[$key]['cashback_price'] = $shopData['cashbackPrice'].'€';        
                if($shopData['cashbackPercentage'] > 0) {
                    $marcasSideBarData[$key]['cashback_price'] = $shopData['cashbackPercentage'].'%';
                }
            }
        }
        if (count($marcasSideBarData) > 0) {
            foreach ($marcasSideBarData as $key => $value) {

                if($value["offers"] == "voucher")
                {

                    $brandController = new BrandController();
                    $brandController->setContainer($this->container);
                    $voucherData = $brandController->getVoucherByShopId($value["id"],$value["voucher_program_id"],$connection);
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
                        $value['tagname'] =  $string." ".$new;
                    }
                    else
                    {
                        $value['tagname'] = 'Voucher';
                    }
                  
                    $marcasSideBarData[0] = $value;
                }
                elseif ($value["offers"] == "offer") 
                {
                    $value['tagname'] = 'Offer';
                    $marcasSideBarData[0] = $value;
                }
                elseif ($value["offers"] == "cashback") 
                {
                    $marcasSideBarData[0] = $value;
                }
            }

            return $marcasSideBarData;
        }

        return new Response();
    }

    public function getRelatedBrandsDataByCategoryLevel($historyId, $categoryFieldName, $categoryTableAlias, $categoryTable, $limit) 
    {
        $marcasRelatedBrands = array();
        $em = $this->getDoctrine()->getEntityManager();

        /* Query to get brand relate sidebar voucher's data */
        $connection = $em->getConnection();
        $query = $connection->prepare('SELECT '.$categoryTableAlias.'.'.$categoryFieldName.' FROM '.$categoryTable.' AS '.$categoryTableAlias.' LEFT JOIN lb_shop as s ON s.id = '.$categoryTableAlias.'.shop_id
                                        LEFT JOIN lb_shop_history as sh ON s.id = sh.shop
                                        JOIN lb_slug AS sl ON sl.categoryId = sh.id AND sl.categoryType = :categoryType
                                        LEFT JOIN lb_voucher_programs as vp ON s.vprogram_id = vp.id                                        
                                        WHERE sh.id = :shid AND s.shopStatus = :shopStatus');
        $query->bindValue('shid', $historyId);
        $query->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        $query->bindValue('categoryType', Constants::SHOP_IDENTIFIER);
        $query->execute();
        $marcasRelatedBrandsCategories = $query->fetchAll();
        $categories = array();
        if (count($marcasRelatedBrandsCategories) > 0) {
            foreach ($marcasRelatedBrandsCategories as $value) {
                if (!empty($value[$categoryFieldName])) {
                    $categories[] = $value[$categoryFieldName];
                }
            }
            $query = $connection->prepare('SELECT vp.id,vp.*,sh.id AS shop_history_id FROM lb_voucher_programs as vp
                                            LEFT JOIN lb_shop as s ON s.vprogram_id = vp.id
                                            JOIN lb_shop_history AS sh ON s.id = sh.shop
                                            LEFT JOIN '.$categoryTable.' as '.$categoryTableAlias.' ON '.$categoryTableAlias.'.shop_id = s.id  
                                            JOIN lb_slug AS sl ON sl.categoryId = sh.id AND sl.categoryType = :categoryType                                          
                                            WHERE '.$categoryTableAlias.'.'.$categoryFieldName.' IN (:'.$categoryFieldName.') AND sh.id != :shid AND s.shopStatus = :shopStatus
                                            GROUP BY vp.id
                                            ORDER BY sh.created DESC LIMIT '.$limit);
            $query->bindValue($categoryFieldName, implode(',', array_map('intval', $categories)));
            $query->bindValue('shid', $historyId);
            $query->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
            $query->bindValue('categoryType', Constants::SHOP_IDENTIFIER);
            $query->execute();
            $marcasRelatedBrands = $query->fetchAll();

           
            $voucherProgramsEntity = $em->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms');
            foreach ($marcasRelatedBrands as $key => $data) {
                $query = $connection->prepare('SELECT sh.cashbackPrice,sh.cashbackPercentage, sg.slugName as slug_name
                                                FROM lb_slug AS sg, lb_voucher_programs AS vp
                                                LEFT JOIN lb_shop AS s ON s.vprogram_id = vp.id
                                                LEFT JOIN lb_shop_history AS sh ON sh.shop = s.id
                                                WHERE sh.id = :shid AND s.shopStatus = :shopStatus AND sg.categoryId = :shid AND sg.categoryType = :slugCategoryType
                                                ORDER BY sh.cashbackPercentage, sh.created DESC LIMIT 1');
                $query->bindValue('shid', $data['shop_history_id']);
                $query->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
                $query->bindValue('slugCategoryType', Constants::SHOP_IDENTIFIER);
                $query->execute();
                $marcasSideBarData = $query->fetchAll();
                $offerCount = count($this->getOfferData($data['shop_history_id']));
                //$marcasSideBarFinalData  = array();
                if (count($marcasSideBarData) > 0) {
                    $marcasSideBarData = array_slice($marcasSideBarData, 0, 1);
                    //$marcasSideBarFinalData[0]  = $marcasSideBarData[array_rand($marcasSideBarData)];
                    $marcasRelatedBrands[$key]['slug_name'] = $marcasSideBarData[0]['slug_name'];
                    $marcasRelatedBrands[$key]['offer_count'] = $offerCount;
                    $marcasRelatedBrands[$key]['cashback_percent'] = $marcasSideBarData[0]['cashbackPercentage'];
                    $marcasRelatedBrands[$key]['cashback_price'] = $marcasSideBarData[0]['cashbackPrice'];
                    $logo_path = $marcasRelatedBrands[$key]['logo_path'];
                    if (!empty($marcasRelatedBrands[$key]['image_id'])) {
                        $logo_path = $this->getImageUrl($voucherProgramsEntity, $marcasRelatedBrands[$key]['image_id']);
                    }
                    $marcasRelatedBrands[$key]['logo_path'] = $logo_path;
                }
            }

            //Unset value another loop - TODO :: Reduce loop
            foreach($marcasRelatedBrands as $key => $marcasRelatedBrand) {                
                if($marcasRelatedBrand['slug_name'] == null) {
                    unset($marcasRelatedBrands[$key]);
                }
            }

        }
        //echo "<pre>";
        //print_r($marcasRelatedBrands);
        return $marcasRelatedBrands;
    }


    public function getRelatedBrandsData($historyId)
    {
        $categoryFieldName  = "parent_category_id";
        $categoryTableAlias = "spc";
        $categoryTable      = "lb_shop_parent_category";
        $limit              = 4;

        $marcasRelatedBrandsParentCat = $this->getRelatedBrandsDataByCategoryLevel($historyId, $categoryFieldName, $categoryTableAlias, $categoryTable, $limit);
        
        if(count($marcasRelatedBrandsParentCat) < 4) { 
            $limit              = $limit - count($marcasRelatedBrandsParentCat);
            $categoryFieldName  = "category_id";
            $categoryTableAlias = "sc";
            $categoryTable      = "lb_shop_category";

            $marcasRelatedBrandsCat = $this->getRelatedBrandsDataByCategoryLevel($historyId, $categoryFieldName, $categoryTableAlias, $categoryTable, $limit);
            foreach ($marcasRelatedBrandsCat as $value) {
                $marcasRelatedBrandsParentCat[] = $value;
            }
        }


        if(count($marcasRelatedBrandsParentCat) < 4) {
            $limit              = $limit - count($marcasRelatedBrandsParentCat); 
            $categoryFieldName  = "child_category_id";
            $categoryTableAlias = "scc";
            $categoryTable      = "lb_shop_child_category";

            $marcasRelatedBrandsChildCat = $this->getRelatedBrandsDataByCategoryLevel($historyId, $categoryFieldName, $categoryTableAlias, $categoryTable, $limit);
            foreach ($marcasRelatedBrandsChildCat as $value) {
                $marcasRelatedBrandsParentCat[] = $value;
            }
        }

        return $marcasRelatedBrandsParentCat;
    }

    public function getImageUrl($entity, $imageId, $imageType = 'preview')
    {
        $media = $entity->findOneBy(array('image' => $imageId));
        $imageUrl = '';
        if (!empty($media) && !empty($imageId)) {
            $media = $media->getImage();
            $mediaManager = $this->get('sonata.media.pool');
            $provider = $mediaManager->getProvider($media->getProviderName());
            $format = $provider->getFormatName($media, $imageType);
            $imageUrl = $provider->generatePublicUrl($media, $format);
        }

        return $imageUrl;
    }

    public function getResponsiveMarcasAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();
        $query = $connection->prepare(
            'SELECT vp.id, vp.program_name,sh.id AS shop_history_id,
             sl.slugName AS shop_history_slugname,sh.created,sh.title 
             FROM lb_voucher_programs AS vp 
             LEFT JOIN lb_shop AS s ON s.vprogram_id = vp.id 
             LEFT JOIN lb_shop_history AS sh ON sh.shop = s.id 
             LEFT JOIN lb_slug AS sl ON sh.id = sl.categoryId 
             WHERE s.shopStatus = :shopStatus AND sl.categoryType = :categoryType 
             GROUP BY vp.id ORDER BY sh.created DESC, sh.title ASC LIMIT 12');
        $query->bindValue('categoryType', Constants::SHOP_IDENTIFIER);
        $query->bindValue('shopStatus', Shop::SHOP_ACTIVATED);
        $query->execute();
        $marcas = $query->fetchAll();
        unset($connection);
        if($marcas) {
            foreach($marcas as $key1 => $value) {
                $marcas[$key1]['slug_name'] = $value['shop_history_slugname'];
            }
        }
        if (count($marcas) > 0) {
            return $this->render('iFlairLetsBonusFrontBundle:Marcas:menumarcasResponsive.html.twig', array(
                'marcas' => $marcas,
            ));
        }

        return new Response();
    }

    public function getOfferData($id)
    {
        /* Query to get brand relate voucher's data */
        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();
        /*$query = $connection->prepare('SELECT vp.*,v.*,s.id AS shop_id,s.urlAffiliate AS shop_affiliate_url ,sh.id AS shop_history_id, sh.title AS shop_history_shop_title, vp.pop_up_image_id AS brand_popup_image, v.publish_end_date AS voucher_expire_date, vp.id AS brand_id
										FROM lb_voucher_programs AS vp
										LEFT JOIN lb_shop AS s ON s.vprogram_id = vp.id
                                        LEFT JOIN lb_shop_history AS sh ON s.id = sh.shop
										LEFT JOIN lb_shop_voucher AS sv ON sv.shop_id = s.id
										LEFT JOIN lb_voucher AS v ON v.id = sv.voucher_id
										WHERE sh.id = :shid AND v.id IS NOT NULL ORDER BY sh.startDate DESC');*/
        $query = $connection->prepare('SELECT vp.*,v.*,s.id AS shop_id,s.offers AS shop_offers,s.urlAffiliate AS shop_affiliate_url,
                                        sh.id AS shop_history_id,
                                        vp.pop_up_image_id AS brand_popup_image,vp.logo_path AS brand_logo,
                                        sh.title AS shop_history_shop_title
										FROM lb_voucher_programs AS vp
										LEFT JOIN lb_shop AS s ON s.vprogram_id = vp.id
                                        LEFT JOIN lb_shop_history AS sh ON s.id = sh.shop
										LEFT JOIN lb_shop_voucher AS sv ON sv.shop_id = s.id
										LEFT JOIN lb_voucher AS v ON v.id = sv.voucher_id
										WHERE sh.id = :shid AND v.id IS NOT NULL ORDER BY sh.startDate DESC');
        $query->bindValue('shid', $id);        
        $query->execute();
        $voucherData = $query->fetchAll();

        $finalVoucherData=array();

        $voucherProgramsEntity = $em->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms');
        foreach ($voucherData as $key => $value) {
            $finalVoucherData[$key]['id'] = $value['id'];
            $finalVoucherData[$key]['network_id'] = $value['network_id'];
            $finalVoucherData[$key]['image_id'] = $value['image_id'];
            $finalVoucherData[$key]['banner_id'] = $value['banner_id'];
            $finalVoucherData[$key]['nprogram_id'] = $value['nprogram_id'];
            $finalVoucherData[$key]['program_name'] = $value['program_name'];
            $finalVoucherData[$key]['logo_path'] = $value['logo_path'];
            $finalVoucherData[$key]['created'] = $value['created'];
            $finalVoucherData[$key]['modified'] = $value['modified'];
            $finalVoucherData[$key]['right_block_image_id'] = $value['right_block_image_id'];
            $finalVoucherData[$key]['pop_up_image_id'] = $value['pop_up_image_id'];
            $finalVoucherData[$key]['program_id'] = $value['program_id'];
            $finalVoucherData[$key]['language_id'] = $value['language_id'];
            $finalVoucherData[$key]['currency_id'] = $value['currency_id'];
            $finalVoucherData[$key]['ref_voucher_id'] = $value['ref_voucher_id'];
            $finalVoucherData[$key]['code'] = $value['code'];
            $finalVoucherData[$key]['publish_start_date'] = $value['publish_start_date'];
            $finalVoucherData[$key]['voucher_expire_date'] = $value['publish_end_date'];
            $finalVoucherData[$key]['voucher_name'] = $value['title'];
            $finalVoucherData[$key]['short_description'] = $value['short_description'];
            $finalVoucherData[$key]['description'] = $value['description'];
            $finalVoucherData[$key]['voucher_type_id'] = $value['voucher_type_id'];
            $finalVoucherData[$key]['default_track_uri'] = $value['default_track_uri'];
            $finalVoucherData[$key]['site_specific'] = $value['site_specific'];
            $finalVoucherData[$key]['landing_url'] = $value['landing_url'];
            $finalVoucherData[$key]['discount_amount'] = $value['discount_amount'];
            $finalVoucherData[$key]['is_percentage'] = $value['is_percentage'];
            $finalVoucherData[$key]['publisher_info'] = $value['publisher_info'];
            $finalVoucherData[$key]['exclusive'] = $value['exclusive'];
            $finalVoucherData[$key]['status'] = $value['status'];
            $finalVoucherData[$key]['isdisplayonfront'] = $value['isdisplayonfront'];
            $finalVoucherData[$key]['isnew'] = $value['isnew'];
            $finalVoucherData[$key]['shop_id'] = $value['shop_id'];
            $finalVoucherData[$key]['shop_offers'] = $value['shop_offers'];
            $finalVoucherData[$key]['shop_affiliate_url'] = $value['shop_affiliate_url'];
            $finalVoucherData[$key]['shop_history_id'] = $value['shop_history_id'];
            $finalVoucherData[$key]['title'] = $value['title'];
            $finalVoucherData[$key]['brand_id'] = $value['id'];
            $finalVoucherData[$key]['publish_end_date'] = $value['publish_end_date'];
            $finalVoucherData[$key]['shop_history_shop_title'] = $value['shop_history_shop_title'];
            if (!empty($value['brand_popup_image'])) {
                $finalVoucherData[$key]['brand_logo_popup'] = $this->getPopUpImageUrl($voucherProgramsEntity, $value['brand_popup_image'], 'cashback_voucher_popup', 'voucherprogram');
            } else {
                $finalVoucherData[$key]['brand_logo_popup'] = $value['brand_logo'];
            }
        }

        return $finalVoucherData;
    }

    public function getPopUpImageUrl($entities, $imageId, $imageType = 'big', $definedType = 'category')
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

    public function viewAction(Request $request, $slug_name)
    {
        $id = '';
        $em = $this->getDoctrine()->getEntityManager();

       $userAddToFav = array();
        unset($connection);
        $connection = $em->getConnection();
        $session = $this->getRequest()->getSession();
        $homepageController = new HomepageController();
        $homepageController->setContainer($this->container);
        $affiliationArgs = new DefaultController();
        $affiliationArgs->setContainer($this->container);
        $brandController = new BrandController();
        $brandController->setContainer($this->container);
        if ($slug_name === '0TO9' || $slug_name === 'TODAS' || (ctype_upper($slug_name) && strlen($slug_name) === 1 && preg_match('/[A-Z ]+/', $slug_name))) {
            $tiendasController = new TiendasController();
            $tiendasController->setContainer($this->container);
            $tiendas_data = $tiendasController->tiendasLetterAction($slug_name, $em, $connection,$request);

            return $tiendas_data;
        }
        
        /*$slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                            array('categoryType' => Constants::MARCAS_IDENTIFIER, 'slugName' => $slug_name));*/
        $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                            array('categoryType' => Constants::SHOP_IDENTIFIER, 'slugName' => $slug_name));
        if ($slug) {
            $id = $slug->getCategoryId();
        } else {
            return $this->render('iFlairLetsBonusFrontBundle:Error:error404.html.twig');
        }
        $provider = $this->container->get('sonata.media.provider.image');
        /* Query to get main brand data */
        $query = $connection->prepare('SELECT s.id AS shop_id,
        									  s.keywords,
                                              vp.id,
                                              vp.program_name,
                                              vp.banner_id AS img_id,
                                              vp.image_id as logo_id,
                                              vp.logo_path as logo_path,
                                              vp.pop_up_image_id AS brand_popup_image,
                                              sh.id AS shop_history_id,
                                              sh.description,
                                              sh.introduction,
                                              sh.title, s.offers,
                                              sh.tearms,
                                              sh.cashbackPrice,
                                              sh.cashbackPercentage,
                                              sh.urlAffiliate,
                                              sh.startDate,
                                              sh.created,
                                              sh.modified,
                                              sh.letsBonusPercentage,
                                              sv.voucher_id
                                        FROM lb_voucher_programs AS vp
										LEFT JOIN lb_shop AS s ON s.vprogram_id = vp.id
										LEFT JOIN lb_shop_history AS sh ON s.id = sh.shop
										LEFT JOIN lb_cachback_settings_shop AS css ON css.shop_id = s.id
										LEFT JOIN lb_cashbackSettings AS cs ON cs.id = css.cashback_settings_id
										LEFT JOIN lb_shop_voucher AS sv ON sv.shop_id = s.id
										LEFT JOIN lb_voucher as v ON v.id = sv.voucher_id
										WHERE sh.id = :shid
										GROUP BY s.id
										ORDER BY sh.created DESC');
        $query->bindValue('shid', $id);
        $query->execute();

        $shopData = $query->fetch();        
        $entities = $em->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms');
        $mediaManager = $this->get('sonata.media.pool');
        $vpImages = $entities->findOneBy(array(
            'banner' => $shopData['img_id'],
            'image' => $shopData['logo_id'],
            'popUpImage' => $shopData['brand_popup_image'],
        ));
        if (!empty($vpImages) && !empty($shopData['img_id'])) {
            $media = $vpImages->getBanner();
            $provider = $mediaManager->getProvider($media->getProviderName());
            $format = $provider->getFormatName($media, 'brand_banner');
            $productpublicUrl = $provider->generatePublicUrl($media, $format);
            $shopData['brand_image_path'] = $productpublicUrl;
        }        
        if (!empty($vpImages) && !empty($shopData['logo_id'])) {
            $media = $vpImages->getImage();
            $provider = $mediaManager->getProvider($media->getProviderName());
            $format = $provider->getFormatName($media, 'brand_on_shop');
            $productpublicUrl = $provider->generatePublicUrl($media, $format);
            $shopData['brand_logo_path'] = $productpublicUrl;
        } elseif (!empty($vpImages) && !empty($shopData['logo_path'])) {
            $shopData['brand_logo_path'] = $vpImages->getLogoPath();
        }
        if (!empty($vpImages) && !empty($shopData['brand_popup_image'])) {
            $media = $vpImages->getPopUpImage();
            $provider = $mediaManager->getProvider($media->getProviderName());
            $format = $provider->getFormatName($media, 'cashback_voucher_popup');
            $productpublicUrl = $provider->generatePublicUrl($media, $format);
            $shopData['brand_logo_popup'] = $productpublicUrl;
        } else {
            $shopData['brand_logo_popup'] = $shopData['brand_logo_path'];
        }

        $shopData['cashback_price'] = $shopData['cashbackPrice'].'€';        
        if($shopData['cashbackPercentage'] > 0) {
            $shopData['cashback_price'] = $shopData['cashbackPercentage'].'%';
        }

        if (!empty($session->get('user_id'))) {
            $userAddToFav = $homepageController->addtofevlistAction();
        } else {
            $userAddToFav = array();
        }

        $voucherData = $this->getOfferData($id);
   
        return $this->render('iFlairLetsBonusFrontBundle:Marcas:marcasview.html.twig', array(
            'voucherDatas' => $voucherData,
            'offerCount' => count($voucherData),
            'marcasDatas' => $shopData,
            'shopDatas' => $shopData, //TO-DO :: Update with proper product data once product is implemented.
            'addtofevlist' => $userAddToFav,
        ));
    }

    public function getMarcasSidebarAction(Request $request, $slugname, $voucherCount = 0)
    {
        $marcasSideBarData = array();

        if ($slugname) {
            $em = $this->get('doctrine')->getManager();
            $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('slugName' => $slugname));
            if (!empty($slug)) {
                if ($slug->getCategoryType() == Constants::SHOP_IDENTIFIER) {
                    $marcasSideBarData = $this->getBrandAssociatedShop($slug->getCategoryId());
                    if (count($marcasSideBarData) > 0) {
                        $marcasSideBarData = array_slice($marcasSideBarData, 0, 1);
                    }
                }

                return $this->render('iFlairLetsBonusFrontBundle:Marcas:marcassidebar.html.twig', array(
                    'marcasSideBarDatas' => $marcasSideBarData,
                    'offersCount' => $voucherCount,
                ));
            }
        }

        return new Response();
    }

    public function getMarcasVariationsAction(Request $request, $slugname)
    {
        $marcasVariationData = array();

        if ($slugname) {
            $em = $this->get('doctrine')->getManager();
            $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('slugName' => $slugname));
            if (!empty($slug)) {
                if ($slug->getCategoryType() == Constants::SHOP_IDENTIFIER) {
                    $id = $slug->getCategoryId();
                    /* Query to get brand relate sidebar voucher's data */
                    unset($connection);
                    $connection = $em->getConnection();
                    $query = $connection->prepare('SELECT v.number, v.title, v.date FROM lb_variation AS v LEFT JOIN lb_shop_history AS sh ON sh.id = v.shop_history_id WHERE v.shop_history_id = :shop_history_id ORDER BY v.number DESC');
                    $query->bindValue('shop_history_id', $id);                    
                    $query->execute();
                    $marcasVariationData = $query->fetchAll();
                }
            }
        }

        return $this->render('iFlairLetsBonusFrontBundle:Marcas:marcasvariations.html.twig', array(
            'marcasVariationDatas' => $marcasVariationData,
        ));
    }
    public function getMarcasRelatedBrandsAction(Request $request, $slugname, $programName)
    {
        if ($slugname) {
            $em = $this->get('doctrine')->getManager();
            $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('slugName' => $slugname));
            if (!empty($slug)) {
                if ($slug->getCategoryType() == Constants::SHOP_IDENTIFIER) {
                    $id = $slug->getCategoryId();
                    if ($id) {
                        $marcasRelatedBrands = $this->getRelatedBrandsData($id);
                        if (count($marcasRelatedBrands) > 0) {
                            return $this->render('iFlairLetsBonusFrontBundle:Marcas:marcasrelatedbrand.html.twig', array(
                                'programName' => $programName,
                                'marcasRelatedBrandsDatas' => $marcasRelatedBrands,
                            ));
                        }
                    }
                }
            }
        }

        return new Response();
    }

    public function getMarcasReviewAction(Request $request, $slugname, $programName)
    {
        if ($slugname) {
            $em = $this->get('doctrine')->getManager();
            $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('slugName' => $slugname));
            if (!empty($slug)) {
                if ($slug->getCategoryType() == Constants::SHOP_IDENTIFIER) {
                    $id = $slug->getCategoryId();
                    if ($id) {
                        $em = $this->getDoctrine()->getEntityManager();
                        /* Query to get brand relate sidebar voucher's data */
                        $connection = $em->getConnection();
                        $query = $connection->prepare('SELECT r.*,u.*
                                                        FROM lb_review AS r
                                                        JOIN lb_front_user AS u
                                                        ON r.user_id = u.id
                                                        WHERE r.brand_id = :id 
                                                        ORDER BY r.created DESC');
                        $query->bindValue('id', $id);
                        $query->execute();
                        $marcasReviewData = $query->fetchAll();
                        $totalReviewCount = '';
                        if (!empty($marcasReviewData)) {
                            $totalReviewCount = count($marcasReviewData);
                        }

                        unset($query);

                        $query = $connection->prepare('SELECT r.*
                                                        FROM lb_review AS r
                                                        JOIN lb_front_user AS u
                                                        ON r.user_id = u.id
                                                        WHERE r.brand_id=:id
                                                        GROUP BY r.user_id');
                        $query->bindValue('id', $id);
                        $query->execute();
                        $reviewData = $query->fetchAll();
                        $totalOfRatings = 0;
                        foreach ($reviewData as $key => $value) {
                            $totalOfRatings += $value['rating'];
                        }

                        $ratingPercentage = 0;
                        if (!empty($reviewData)) {
                            $totalUserCount = count($reviewData) > 0 ? count($reviewData) : 1;
                            $avgRating = $totalOfRatings / $totalUserCount;
                            $ratingPercentage = ($avgRating * 100) / 5;
                        }

                        if (count($marcasReviewData) > 0) {
                            return $this->render('iFlairLetsBonusFrontBundle:Marcas:marcasReview.html.twig', array(
                                'programName' => $programName,
                                'review' => $marcasReviewData,
                                'reviewCounts' => $totalReviewCount,
                                'reviewPercentage' => $ratingPercentage,
                            ));
                        }
                    }
                }
            }
        }

        return new Response();
    }
}
