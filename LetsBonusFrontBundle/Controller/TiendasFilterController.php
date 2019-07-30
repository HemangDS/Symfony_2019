<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use iFlair\LetsBonusAdminBundle\Slug\Constants;

class TiendasFilterController extends Controller
{
    public function filterAction(Request $request)
    {
        $cat_arr_filter = array();
        $shop_ids = array();
        $offer = '';
        if (!empty($request->request->get('shorting'))) {
            $offer = $request->request->get('shorting');
        }

        if (!empty($request->request->get('view'))) {
            $view = $request->request->get('view');
        } else {
            $view = 'list';
        }

         // Alphabet filtering
        if (!empty($request->request->get('alphabet'))) {
            $cat_arr_filter = $this->alphabetFilter($request->request->get('data_array'), $request->request->get('alphabet'));

            // Alphabet with offer filtering
            if (!empty($request->request->get('offer'))) {
                $cat_arr_filter = $this->shopOfferFilter($cat_arr_filter, $request->request->get('offer'));
            }
            //End Alphabet with category filtering
            if (!empty($request->request->get('category_id_string'))) {
                $cat_arr_filter = $this->categoryFilter($cat_arr_filter, $request->request->get('category_id_string'));
            }
            if (!empty($request->request->get('shorting'))) {
                $cat_arr_filter = $cat_arr_filter = $this->shortingFilter($cat_arr_filter, $request->request->get('shorting'));
            }
        } else {
            //$cat_arr_filter = $request->request->get('data_array');
            $cat_arr_filter = array();
        }
          //End Alphabet filtering 

        // shop offer type filtering
        if (!empty($request->request->get('offer')) && empty($request->request->get('alphabet')) && empty($request->request->get('category_id_string'))) {
            $cat_arr_filter = $this->shopOfferFilter($request->request->get('data_array'), $request->request->get('offer'));
        }
        //End shop offer type filtering

        // Left Menu cateogry filtering
        if (!empty($request->request->get('category_id_string')) && empty($request->request->get('offer')) && empty($request->request->get('alphabet'))) {
            $cat_arr_filter = $this->categoryFilter($request->request->get('data_array'), $request->request->get('category_id_string'));
        }

        if (empty($request->request->get('category_id_string')) && !empty($request->request->get('alphabet')) && empty($request->request->get('offer'))) {
            $cat_arr_filter = $this->alphabetFilter($request->request->get('data_array'), $request->request->get('alphabet'));
        }

        if (!empty($request->request->get('shorting')) && empty($request->request->get('offer')) && empty($request->request->get('alphabet')) && empty($request->request->get('offer'))) {
            $cat_arr_filter = $this->shortingFilter($request->request->get('data_array'), $request->request->get('shorting'));
        }

        //End Left Menu cateogry filtering

        $arr = array('html' => $this->render('iFlairLetsBonusFrontBundle:Tiendas:tiendas-page-ajax.html.twig', array('category_detail' => $cat_arr_filter, 'view' => $view, 'alphabet' => $request->request->get('alphabet'), 'offer' => $offer, 'addtofevlist' => $request->request->get('addtofevlist'), 'responsive_offer' => $request->request->get('offer')))->getContent());

        return new Response(json_encode($arr));
    }

    public function categoryFilter($data_array, $category_id_string)
    {
        $shop_ids = array();
        $category = array();
        $cat_filter = array();
        $sm = $this->getDoctrine()->getEntityManager();
        $connection = $sm->getConnection();

        if (0 === strpos($category_id_string, 'parent_category_id_')) {
            $category = explode('parent_category_id_', $category_id_string);
            $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                                array('categoryType' => Constants::PARENT_CATEGORY_IDENTIFIER, 'slugName' => $category));
            if ($slug) {
                $parent = $slug->getCategoryId();
            }
            $statement = $connection->prepare('SELECT s.shop_id FROM lb_shop_parent_category s where s.parent_category_id ='.$parent);
        }
        if (0 === strpos($category_id_string, 'category_id_')) {
            $category = explode('category_id_', $category_id_string);
            $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                                array('categoryType' => Constants::MIDDLE_CATEGORY_IDENTIFIER, 'slugName' => $category));
            if ($slug) {
                $cat = $slug->getCategoryId();
            }
            $statement = $connection->prepare('SELECT s.shop_id FROM lb_shop_category s where s.category_id ='.$cat);
        }
        if (0 === strpos($category_id_string, 'child_category_id_')) {
            $category = explode('child_category_id_', $category_id_string);
            $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                                array('categoryType' => Constants::CHILD_CATEGORY_IDENTIFIER, 'slugName' => $category));
            if ($slug) {
                $child = $slug->getCategoryId();
            }
            $statement = $connection->prepare('SELECT s.shop_id FROM lb_shop_child_category s where s.child_category_id ='.$child);
        }

        $statement->execute();
        $category_shop = $statement->fetchAll();
        foreach ($category_shop as $key => $value) {
            $shop_ids[] = $value['shop_id'];
        }

        foreach ($data_array as $key => $value) {
            $key = array_search($value['shop_id'], $shop_ids);

            if ($key > -1) {
                $cat_filter[] = $value;
            }
        }

        return $cat_filter;
    }

    public function shopOfferFilter($data_array, $offer)
    {
        $cashback = array();
        $product = array();
        $voucher = array();
        $cat_arr_filter = array();
        $offer_type = '';

        if (!empty($data_array)) {
            foreach ($data_array as $key => $offer_value) {
                if (!empty($offer_value['shop_id'])) {
                    if ($offer_value['shop_offers'] == 'cashback') {
                        $offer_type = 'Cashback';
                    } elseif ($offer_value['shop_offers'] == 'offer') {
                        $offer_type = 'Ofertas';
                    } elseif ($offer_value['shop_offers'] == 'voucher') {
                        $offer_type = 'Cupones';
                    }

                    if (!empty($offer)) {
                        if (in_array($offer_type, array_map("trim", $offer))) {
                            $cat_arr_filter[] = $offer_value;
                        }
                    }
                }
            }
        }

        return $cat_arr_filter;
    }
    public function alphabetFilter($data_array, $alphabet)
    {
        $cat_arr_filter = array();

        if (!empty($data_array)) {
            foreach ($data_array as $key => $value) {
                /* if ($value['shop_offers'] == 'voucher') 
                {
                    if ($alphabet == '0-9') {
                        foreach (range(0, 9) as $key => $value_digit) {
                            if (0 === strpos($value['voucher_name'], (string) $value_digit)) {
                                $cat_arr_filter[] = $value;
                            }
                        }
                    } elseif ($alphabet == 'TODAS') {
                        $cat_arr_filter[] = $value;
                    } else {
                        if (0 === strpos($value['voucher_name'], $alphabet) || 0 === strpos($value['voucher_name'], strtolower($alphabet))) {
                            $cat_arr_filter[] = $value;
                        }
                    }
                }*/

              /*  if ($value['shop_offers'] == 'cashback') 
                {*/
                    if ($alphabet == '0-9') {
                        foreach (range(0, 9) as $key => $value_digit) {
                            if (0 === strpos($value['brand_name'], (string) $value_digit)) {
                                $cat_arr_filter[] = $value;
                            }
                        }
                    } elseif ($alphabet == 'TODAS') {
                        $cat_arr_filter[] = $value;
                    } else {
                        if (0 === strpos($value['brand_name'], $alphabet) || 0 === strpos($value['brand_name'], strtolower($alphabet))) {
                            $cat_arr_filter[] = $value;
                        }
                    }
                //}
            }
        }

        return $cat_arr_filter;
    }
    public function shortingFilter($data_array, $shorting)
    {
        $cashback = array();
        $product = array();
        $voucher = array();
        $brand_arr_filter = array();
        if (!empty($data_array)) {
            foreach ($data_array as $key => $offer_value) {
                if (!empty($offer_value['shop_id'])) {
                    if ($offer_value['shop_offers'] == 'cashback') {
                        $cashback[] = $offer_value;
                    } elseif ($offer_value['shop_offers'] == 'product') {
                        $product[] = $offer_value;
                    } elseif ($offer_value['shop_offers'] == 'voucher') {
                        $voucher[] = $offer_value;
                    }
                }
            }
        }

        if ($shorting == 'Cashback') {
            if (!empty($cashback)) {
                foreach ($cashback as $key => $value) {
                    $short_cashback[$key] = $value['discount_amount'];
                }

                array_multisort($short_cashback, SORT_DESC, $cashback);
            }
            $brand_arr_filter = $cashback;
        } elseif ($shorting == 'Cupones') {
            if (!empty($voucher)) {
                foreach ($voucher as $key => $value) {
                    $short_voucher[$key] = $value['voucher_code_count'];
                }

                array_multisort($short_voucher, SORT_DESC, $voucher);
            }
            $brand_arr_filter = $voucher;
        } elseif ($shorting == 'Ofertas') {
            $brand_arr_filter = $product;
        }

        return $brand_arr_filter;
    }
    public function executeOfferNavigationFilter($dataArray,$offerFilters){
        $offerFilterData=array();
        foreach($offerFilters as $offerFilter){
            if($offerFilter=='Cashback'){
                foreach($dataArray as $key => $value){
                    if($value['shop_offers'] == 'cashback' || $value['shop_offers'] == 'cashback/coupons'){
                        $offerFilterData[$key] = $value;
                    }
                }
            }
            if($offerFilter=='Cupones'){
                foreach($dataArray as $key => $value){
                    if($value['shop_offers'] == 'voucher' || $value['shop_offers'] == 'cashback/coupons'){
                        $offerFilterData[$key] = $value;
                    }
                }
            }
            if($offerFilter=='Ofertas'){
                foreach($dataArray as $key => $value){
                    if($value['shop_offers'] == 'offer'){
                        $offerFilterData[$key] = $value;
                    }
                }
            }
        }
       
        return $offerFilterData;
    }

    public function executeAlphabetNavigationFilter($dataArray,$alphabetFilters){
        $alphabetFilterData = array();
        if($alphabetFilters == '0-9'){
            foreach($dataArray as $k => $v) {
                foreach(range(0, 9) as $key => $value_digit){
                    if(0 === strpos($v['brand_name'], (string)$value_digit)){
                        $alphabetFilterData[$k] = $v;
                    }
                }
            }
        }elseif($alphabetFilters == 'TODAS'){
            foreach($dataArray as $k => $v) {
                $alphabetFilterData[$k] = $v;
            }
        }else{
            foreach($dataArray as $k => $v){
                if (0 === strpos($v['brand_name'], $alphabetFilters) || 0 === strpos($v['brand_name'], strtolower($alphabetFilters))){
                    $alphabetFilterData[$k] = $v;
                }
            }
        }
        return $alphabetFilterData;
    }

    public function executeCategoryNavigationFilter($dataArray,$catagoryFilter){
        return $this->categoryFilter($dataArray,$catagoryFilter);
    }

    public function executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$offerFilterData){
        $final = array();
        $categories_details = array_slice($offerFilterData, 0, $init_count*$target_count);
        $final['data_details'] = $categories_details;

        if(floor($final_count/$init_count) < $target_count){
            $remove_load_more = 1;
            $final['remove_load_more'] = $remove_load_more;
            $final['target_count'] = $target_count;
        }elseif(floor($final_count/$init_count) >= $target_count){
            $target_count++;
            $final['remove_load_more'] = $remove_load_more;
            $final['target_count'] = $target_count;
        }else{
            $remove_load_more = 1;
            $final['remove_load_more'] = $remove_load_more;
            $target_count = 1;
            $final['target_count'] = $target_count;
        }

        return $final;
    }

    public function checkCatagoryNavigationFilter($init_count,$remove_load_more,$catagoryFilter,$alphabetFilterData,$request){
        $filterNavigationCounter = array();
        $catagoryFilterData=$this->executeCategoryNavigationFilter($alphabetFilterData,$catagoryFilter);
        $filterNavigationCounter['catagoryFilterData'] = $catagoryFilterData;
        $final_count = count($catagoryFilterData);
        if($request->get('execute_count')){
            $target_count = $request->get('execute_count');
            $filterNavigationCounter = array_merge($filterNavigationCounter,$this->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$catagoryFilterData));
        }elseif($request->get('target_count')){
            $target_count = $request->get('target_count');
            $filterNavigationCounter = array_merge($filterNavigationCounter,$this->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$catagoryFilterData));
        }else{
            $target_count = $execute_count = 1;
            $filterNavigationCounter = array_merge($filterNavigationCounter,$this->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$catagoryFilterData));
        }
        return $filterNavigationCounter;
    }

    public function checkAlphabetNavigationFilter($init_count,$remove_load_more,$alphabetFilter,$offerFilterData,$request){
        $filterNavigationCounter = array();
        $alphabetFilterData=$this->executeAlphabetNavigationFilter($offerFilterData,$alphabetFilter);
        $filterNavigationCounter['alphabetFilterData'] = $alphabetFilterData;
        $final_count = count($alphabetFilterData);
        if($request->get('execute_count')){
            $target_count = $request->get('execute_count');
            $filterNavigationCounter = array_merge($filterNavigationCounter,$this->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$alphabetFilterData));
        }elseif($request->get('target_count')){
            $target_count = $request->get('target_count');
            $filterNavigationCounter = array_merge($filterNavigationCounter,$this->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$alphabetFilterData));
        }else{
            $target_count = $execute_count = 1;
            $filterNavigationCounter = array_merge($filterNavigationCounter,$this->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$alphabetFilterData));
        }
        return $filterNavigationCounter;
    }

    public function checkOfferNavigationFilter($init_count,$remove_load_more,$offerFilter,$categories_details,$request){
        $filterNavigationCounter = array();
        $offerFilterData=$this->executeOfferNavigationFilter($categories_details,$offerFilter);
        $filterNavigationCounter['offerFilterData'] = $offerFilterData;
        $final_count = count($offerFilterData);
        if($request->get('execute_count')){
            $target_count = $request->get('execute_count');
            $filterNavigationCounter = array_merge($filterNavigationCounter,$this->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$offerFilterData));
        }elseif($request->get('target_count')){
            $target_count = $request->get('target_count');
            $filterNavigationCounter = array_merge($filterNavigationCounter,$this->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$offerFilterData));
        }else{
            $target_count = $execute_count = 1;
            $filterNavigationCounter = array_merge($filterNavigationCounter,$this->executeFilterNavigationCounter($init_count,$final_count,$target_count,$remove_load_more,$offerFilterData));
        }
        return $filterNavigationCounter;
    }
}
