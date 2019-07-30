<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use iFlair\LetsBonusAdminBundle\Entity\Shop;
use iFlair\LetsBonusAdminBundle\Entity\Searchlogs;
use iFlair\LetsBonusAdminBundle\Slug\Constants;

class SearchlogsController extends Controller
{
    public function indexAction(Request $request)
    {
        $searchKeyWord = '';
        $latitude = '';
        $longitude = '';
        $city = '';

        if (!empty($request->request->all())) {
            $searchKeyWord = isset($request->request->all()['q'])?$request->request->all()['q']:"";
            $latitude = isset($request->request->all()['lat'])?$request->request->all()['lat']:"";
            $longitude = isset($request->request->all()['lng'])?$request->request->all()['lng']:"";
            $city = isset($request->request->all()['city_name'])?$request->request->all()['city_name']:"";
        }
        $sphinxSearch = $this->get('iakumai.sphinxsearch.search');
        if (!empty($searchKeyWord)) {
            $result = $sphinxSearch->search($searchKeyWord, array('sphinxlbsearcher'));
            $ids = array();
            if (!empty($result) && isset($result['matches'])) {
                foreach ($result['matches'] as $key => $value) {
                    $ids[] = $key;
                }
            }

            $em = $this->getDoctrine()->getEntityManager();
            $connection = $em->getConnection();
            
            $searchResult = $em->createQueryBuilder()
                ->select('s')
                ->from('iFlairLetsBonusAdminBundle:Shop',  's')
                ->where('s.id IN (:miarray)')
                ->setParameter('miarray', $ids)
                ->andWhere('s.shopStatus = :shopStatus')
                ->setParameter('shopStatus', Shop::SHOP_ACTIVATED)
                ->getQuery()
                ->getResult();

            //  Initiate curl
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, 'http://freegeoip.net/json/');

            $result = curl_exec($ch);
            $resultData = json_decode($result, true);

            $ip = $resultData['ip'];
            $countryCode = $resultData['country_code'];
            $countryName = $resultData['country_name'];
            $timeZone = $resultData['time_zone'];

            if (empty($latitude)) {
                $latitude = $resultData['latitude'];
            }

            if (empty($longitude)) {
                $longitude = $resultData['longitude'];
            }

            if (empty($city)) {
                $city = $resultData['city'];
            }
            $numResult = count($searchResult);
            $searchLogsEntity = new Searchlogs();

            $searchLogsEntity->setIdClient(0);
            $searchLogsEntity->setIdCity('');
            $searchLogsEntity->setCleanedTerm('');
            $searchLogsEntity->setBreadcrumb('');
            $searchLogsEntity->setVertical('');
            $searchLogsEntity->setSearchFrom(0);
            $searchLogsEntity->setInternalSearch(0);
            $searchLogsEntity->setLatitude($latitude);
            $searchLogsEntity->setLongitude($longitude);
            $searchLogsEntity->setTerm($searchKeyWord);
            $searchLogsEntity->setIpAddress(ip2long($ip));
            $searchLogsEntity->setSearchApp('desktop');

            $searchTermResult = $em->createQueryBuilder()
                ->select('s.numSearch')
                ->from('iFlairLetsBonusAdminBundle:Searchlogs',  's')
                ->where('s.term=:slug')
                ->orderBy('s.id', 'DESC')
                ->setParameter('slug', $searchKeyWord)
                ->setFirstResult(0)
                ->setMaxResults(1)
                ->getQuery()
                ->getResult();

            $lastCount = 0;
            if (count($searchTermResult) > 0) {
                $lastCount = $searchTermResult[0]['numSearch'];
                $searchLogsEntity->setNumSearch($lastCount + 1);
            } else {
                $searchLogsEntity->setNumSearch(1);
            }

            $searchLogsEntity->setNumResults($numResult);

            $em1 = $this->getDoctrine()->getManager();

            $em1->persist($searchLogsEntity);
            $em1->flush();
            //echo count($searchResult);

            /*********************************************************************************************************************/
            $categoryController = new CategoryController();
            $categoryController->setContainer($this->container);

            $brandController = new BrandController();
            $brandController->setContainer($this->container);

            $homepageController = new HomepageController();
            $homepageController->setContainer($this->container);

            $affiliationArgs = new DefaultController();
            $affiliationArgs->setContainer($this->container);
            $cat_arr = array();
            $session = $this->getRequest()->getSession();
            if (!empty($searchResult)) {
                $i = 0;
                

                $voucherProgramsEntity = $em->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms');
                
                foreach ($searchResult as $key => $shopdata) {
                    $shopId = $shopdata->getId();

                    $shop_data_record = $categoryController->getShopDetailsByCategoryId($shopId, $connection);
                    
                    $shopRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Shop');
                    $shop = $shopRepository->findOneBy(array(
                        'id' => $shopId,
                        'shopStatus' => Shop::SHOP_ACTIVATED,
                    ));

                    // if condition for cashback && voucher is not exclusive
                    if ((isset($shopId) && $shopdata->getOffers() == 'cashback') ||
                        (isset($shopId) && $shop_data_record[0]['exclusive'] == 0 && $shopdata->getOffers() == 'voucher')) 
                    {
                        $shopHistoryRepo = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory');
                        $query = $shopHistoryRepo->createQueryBuilder('sh')
                        ->join('iFlairLetsBonusAdminBundle:Slug', 'sl', \Doctrine\ORM\Query\Expr\Join::WITH, 'sl.categoryId = sh.id')
                        ->where('sh.shop = :shopId')
                        ->setParameter('shopId', $shopId)
                        ->andWhere('sl.categoryType = :shopType')
                        ->setParameter('shopType', Constants::SHOP_IDENTIFIER)                        
                        ->getQuery();

                        $shop_history = $query->getResult();                        
                        foreach ($shop_history as $key => $shop_value) {
                            $shopHistory = $shop_value;
                            $shopHistoryId = $shop_value->getId();
                            $affiliationUrlArgs = $affiliationArgs->getAffiliation($shop, $shopHistory, $em);

                            if (!empty($shop_value->getUrlAffiliate())) {
                                $cat_arr[$i][$shopHistoryId]['shop_affiliate_url_origin'] = $shop_value->getUrlAffiliate();
                                $redirect_url = $shop_value->getUrlAffiliate().$affiliationUrlArgs;
                                $cat_arr[$i][$shopHistoryId]['shop_affiliate_url'] = $redirect_url;
                            }

                            $cat_arr[$i][$shopHistoryId]['program_id'] = $shop->getProgramId();
                            $variations = $categoryController->getShopHistoryVariationByShopHistoryId($shopHistoryId, $em);

                            $cat_arr[$i][$shopHistoryId]['shop_history_variation'] = $variations;
                            $voucher_count = $categoryController->getVoucherCountByShopId($shopId, $connection);
                            $cat_arr[$i][$shopHistoryId]['voucher_code_count'] = 0;
                            if ($voucher_count) {
                                $cat_arr[$i][$shopHistoryId]['voucher_code_count'] = count($voucher_count);
                            }

                            $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $shopHistoryId));
                            if ($slug) {
                                $cat_arr[$i][$shopHistoryId]['slug_name'] = $slug->getSlugName();
                            } else {
                                $cat_arr[$i][$shopHistoryId]['slug_name'] = '';
                            }

                            if(!empty($shop_value->getTag())) {
                                $cat_arr[$i][$shopHistoryId]['cashback_type_value'] = $shop_value->getTag()->getName();
                            } else {
                                $cat_arr[$i][$shopHistoryId]['cashback_type_value'] ="";
                            }

                            $cat_arr[$i][$shopHistoryId]['shop_offers'] = $shopdata->getOffers();
                            if($shopdata->getOffers() == 'cashback') {
                              $cat_arr[$i][$shopHistoryId]['shop_type'] = 'cashback';
                            }

                            $cat_arr[$i][$shopHistoryId]['shop_history_id'] = $shopHistoryId;
                            $cat_arr[$i][$shopHistoryId]['shop_history_shop_title'] = $shop_value->getTitle();
                            $cat_arr[$i][$shopHistoryId]['shop_history_shop_description'] = strip_tags($shop_value->getIntroduction());
                            //$cat_arr[$i][$shopHistoryId]['shop_history_shop_end_date'] = $shop_value->getEndDate();
                            $cat_arr[$i][$shopHistoryId]['shop_terms'] = $shop_value->getTearms();
                            $cat_arr[$i][$shopHistoryId]['letsBonusPercentage'] = $shop_value->getLetsBonusPercentage();

                            $cat_arr[$i][$shopHistoryId]['shop_image'] = $categoryController->getMediaUrlByShopId($shopId, 'default_shop', $em);
                            $cat_arr[$i][$shopHistoryId]['top_shop_image'] = $categoryController->getMediaForHighLineTab($shopId, 'default_highline_offer_image', $em);
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
                                $cat_arr[$i][$shopHistoryId]['brand_logo'] = $categoryController->getImageUrl($voucherProgramsEntity, $shop_data_record[0]['brand_image'], 'brand_on_shop', 'voucherprogram');
                            }
                            if (!empty($shop_data_record[0]['brand_popup_image'])) {
                                $cat_arr[$i][$shopHistoryId]['brand_logo_popup'] = $categoryController->getImageUrl($voucherProgramsEntity, $shop_data_record[0]['brand_popup_image'], 'cashback_voucher_popup', 'voucherprogram');
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
                                if(isset($voucherFinal[0]['voucher_code']) && !empty($voucherFinal[0]['voucher_code'])) {
                                    $cat_arr[$i][$shopHistoryId]['shop_type'] = 'coupon';
                                } else {
                                    $cat_arr[$i][$shopHistoryId]['shop_type'] = 'oferta';
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
                        // end if condition for cashback
                    } elseif ((isset($shopId) && $shopdata->getOffers() == 'offer') || (isset($shopId) && $shopdata->getOffers() == 'voucher' && ($shop_data_record[0]['exclusive'] == 1 || $shop_data_record[0]['isnew'] == 1))) {
                        
                        // checking offer type voucher
                        $shop_history = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('shop' => $shopId), array('startDate'=>'DESC'), 1);
                        $shopHistoryId = $shop_history->getId();
                        $variations = $categoryController->getShopHistoryVariationByShopHistoryId($shopHistoryId, $em);
                        $cat_arr[$i][$shopHistoryId]['shop_history_variation'] = $variations;
                        $cat_arr[$i][$shopHistoryId]['cashback_type_value'] = $categoryController->getTagLabelByShopHistoryId($shopHistoryId, $connection);
                        
                        $voucher_count = $categoryController->getVoucherCountByShopId($shopId, $connection);
                        $cat_arr[$i][$shopHistoryId]['voucher_code_count'] = 0;
                        if ($voucher_count) {
                            $cat_arr[$i][$shopHistoryId]['voucher_code_count'] = count($voucher_count);
                        }

                        $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $shopHistoryId));
                        if ($slug) {
                            $cat_arr[$i][$shopHistoryId]['slug_name'] = $slug->getSlugName();
                        } else {
                            $cat_arr[$i][$shopHistoryId]['slug_name'] = '';
                        }
                        $cat_arr[$i][$shopHistoryId]['shop_offers'] = $shopdata->getOffers();
                        if($shopdata->getOffers() == 'cashback') {
                            $cat_arr[$i][$shopHistoryId]['shop_type'] = 'cashback';
                        }
                         if($shopdata->getOffers() == 'offer') {
                            $cat_arr[$i][$shopHistoryId]['shop_type'] = 'oferta';
                        }
                        $cat_arr[$i][$shopHistoryId]['shop_affiliate_url'] = $shopdata->getUrlAffiliate();
                        $cat_arr[$i][$shopHistoryId]['shop_history_id'] = $shopHistoryId;
                        $cat_arr[$i][$shopHistoryId]['shop_history_shop_title'] = $shop_history->getTitle();
                        $cat_arr[$i][$shopHistoryId]['shop_history_shop_description'] = strip_tags($shop_history->getIntroduction());
                       // $cat_arr[$i][$shopHistoryId]['shop_history_shop_end_date'] = $shop_history->getEndDate();
                        $cat_arr[$i][$shopHistoryId]['shop_terms'] = $shop_history->getTearms();
                        $cat_arr[$i][$shopHistoryId]['letsBonusPercentage'] = $shop_history->getLetsBonusPercentage();
                        $cat_arr[$i][$shopHistoryId]['shop_image'] = $categoryController->getMediaUrlByShopId($shopId, 'default_shop', $em);
                        $cat_arr[$i][$shopHistoryId]['top_shop_image'] = $categoryController->getMediaForHighLineTab($shopId, 'default_highline_offer_image', $em);
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
                            $cat_arr[$i][$shopHistoryId]['brand_logo'] = $categoryController->getImageUrl($voucherProgramsEntity, $shop_data_record[0]['brand_image'], 'brand_on_shop', 'voucherprogram');
                        }
                        if (!empty($shop_data_record[0]['brand_popup_image'])) {
                            $cat_arr[$i][$shopHistoryId]['brand_logo_popup'] = $categoryController->getImageUrl($voucherProgramsEntity, $shop_data_record[0]['brand_popup_image'], 'cashback_voucher_popup', 'voucherprogram');
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

                            if($shopdata->getOffers() == 'voucher') {
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

            $categories_details = $categoryController->getFilter($cat_arr);
            
            $max_voucher = $categoryController->getMaxVoucherFilter($categories_details);
            $max_cashback = $categoryController->getMaxCashbackFilter($categories_details);
            $max_voucher_cashback = $categoryController->getMaxVoucherCashback($categories_details);
            $cashback = $categoryController->getCahbackOfferFilter($categories_details);
            $product = $categoryController->getProductOfferFilter($categories_details);
            $voucher = $categoryController->getVoucherOfferFilter($categories_details);

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
                return $this->filterCategoryPage($categories_details,$userAddToFav,$cashback,$product,$voucher,$max_voucher[0],$max_cashback[0],$max_voucher_cashback[0],$final_count,$init_count,$target_count,$remove_load_more,$request->get('offer'),$request->get('alphabet'),$request->get('category_id_string'), $product_count, $searchKeyWord);
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
                return $this->filterCategoryPage($categories_details,$userAddToFav, $cashback,$product,$voucher,$max_voucher[0],$max_cashback[0],$max_voucher_cashback[0],$final_count,$init_count,$target_count,$remove_load_more,$request->get('offer'),$request->get('alphabet'),$request->get('category_id_string'), $product_count, $searchKeyWord);
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
                return $this->filterCategoryPage($categories_details,$userAddToFav,$cashback,$product,$voucher,$max_voucher[0],$max_cashback[0],$max_voucher_cashback[0],$final_count,$init_count,$target_count,$remove_load_more,$request->get('offer'),$request->get('alphabet'),$request->get('category_id_string'), $product_count, $searchKeyWord);
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
                    return $this->filterCategoryPage($categories_details, $userAddToFav, $cashback, $product, $voucher, $max_voucher[0], $max_cashback[0], $max_voucher_cashback[0], $final_count, $init_count, $target_count, $remove_load_more, $request->get('offer'), $request->get('alphabet'), $request->get('category_id_string'), $product_count, $searchKeyWord);
                } else {
                    $target_count = $execute_count = 1;
                    $filterNavigationCounter = $categoryFilterController->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$categories_details);
                     
                    $categories_details = $filterNavigationCounter['data_details'];
                    $remove_load_more = $filterNavigationCounter['remove_load_more'];
                    $target_count = $filterNavigationCounter['target_count'];
                    return $this->renderCategoryPage($categories_details, $userAddToFav, $cashback, $product, $voucher, $max_voucher[0], $max_cashback[0], $max_voucher_cashback[0], $final_count, $init_count, $execute_count, $target_count, $remove_load_more, $searchKeyWord);
                }
            }
            /*********************************************************************************************************************/
        }

        return $this->render('iFlairLetsBonusFrontBundle:Default:index.html.twig');
    }
    
    public function renderCategoryPage($categories_details,$userAddToFav,$cashback,$product,$voucher,$max_voucher,$max_cashback,$max_voucher_cashback,$final_count,$init_count,$execute_count,$target_count,$remove_load_more, $searchKeyWord){

        $render_data = array(
            'category_detail' => $categories_details,
            'addtofevlist' => $userAddToFav,
            'cashback_shop' => $cashback,
            'product_shop' => $product,
            'voucher_shop' => $voucher,
            'max_voucher_data' => $max_voucher,
            'max_cashback_percentage' => $max_cashback,
            'max_voucher_cashback' => $max_voucher_cashback,
            'execute_count' => $execute_count,
            'final_count' => $final_count,
            'init_count' => $init_count,
            'target_count' => $target_count,
            'remove_load_more' => $remove_load_more,
            'search_keyword' => $searchKeyWord
        );

        return $this->render('iFlairLetsBonusFrontBundle:Searchlogs:index.html.twig', $render_data);
    }

    public function filterCategoryPage($categories_details,$userAddToFav,$cashback,$product,$voucher,$max_voucher,$max_cashback,$max_voucher_cashback,$final_count,$init_count,$target_count,$remove_load_more,$OF,$AF,$CF,$product_count, $searchKeyWord){
        $render_data = array(
            'category_detail' => $categories_details,
            'addtofevlist' => $userAddToFav,
            'cashback_shop' => $cashback,
            'product_shop' => $product,
            'voucher_shop' => $voucher,
            'max_voucher_data' => $max_voucher,
            'max_cashback_percentage' => $max_cashback,
            'max_voucher_cashback' => $max_voucher_cashback,
            'final_count' => $final_count,
            'init_count' => $init_count,
            'target_count' => $target_count,
            'target_count_category_filter' => $target_count,
            'remove_load_more' => $remove_load_more,
            'OF' => json_encode($OF),
            'AF' => $AF,
            'CF' => $CF,
            'search_keyword' => $searchKeyWord
        );
        $arr = array('product_count'=>$product_count,'html' => $this->render('iFlairLetsBonusFrontBundle:Searchlogs:index-loadmore.html.twig',$render_data)->getContent());
        return new Response(json_encode($arr));
    }
}

