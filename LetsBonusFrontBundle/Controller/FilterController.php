<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use iFlair\LetsBonusAdminBundle\Slug\Constants;

class FilterController extends Controller
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
       /* if (!empty($request->request->get('category_id_string')) && empty($request->request->get('offer')) && empty($request->request->get('alphabet'))) {
            $cat_arr_filter = $this->categoryFilter($request->request->get('data_array'), $request->request->get('category_id_string'));
        }*/
         if (!empty($request->request->get('category_id_string')) && empty($request->request->get('alphabet'))) {
             $cat_arr_filter = $this->categoryFilter($request->request->get('data_array'), $request->request->get('category_id_string'));
         }

        /*if (empty($request->request->get('category_id_string')) && !empty($request->request->get('alphabet')) && empty($request->request->get('offer'))) {
            $cat_arr_filter = $this->alphabetFilter($request->request->get('data_array'), $request->request->get('alphabet'));
        }*/
       /* if (empty($request->request->get('category_id_string')) && !empty($request->request->get('alphabet'))) {
            $cat_arr_filter = $this->alphabetFilter($request->request->get('data_array'), $request->request->get('alphabet'));
        }*/

       /* if (!empty($request->request->get('shorting')) && empty($request->request->get('offer')) && empty($request->request->get('alphabet')) && empty($request->request->get('offer'))) {
            $cat_arr_filter = $this->shortingFilter($request->request->get('data_array'), $request->request->get('shorting'));
        }*/
         if (!empty($request->request->get('shorting')) && empty($request->request->get('alphabet'))) {
             $cat_arr_filter = $this->shortingFilter($request->request->get('data_array'), $request->request->get('shorting'));
         }
        //End Left Menu cateogry filtering
      /*echo "<pre>";
      print_r($cat_arr_filter);
      exit();*/
        $arr = array('html' => $this->render('iFlairLetsBonusFrontBundle:Brand:brand-page-ajax.html.twig', array('brand_detail' => $cat_arr_filter, 'view' => $view, 'alphabet' => $request->request->get('alphabet'), 'offer' => $offer, 'addtofevlist' => $request->request->get('addtofevlist'), 'responsive_offer' => $request->request->get('offer')))->getContent());

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
            $statement = $connection->prepare('SELECT s.shop_id FROM lb_shop_parent_category s where s.parent_category_id = :parent');
            $statement->bindValue('parent', $parent);
        }
        if (0 === strpos($category_id_string, 'category_id_')) {
            $category = explode('category_id_', $category_id_string);
            $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                            array('categoryType' => Constants::MIDDLE_CATEGORY_IDENTIFIER, 'slugName' => $category));
            if ($slug) {
                $cat = $slug->getCategoryId();
            }
            $statement = $connection->prepare('SELECT s.shop_id FROM lb_shop_category s where s.category_id = :cat');
            $statement->bindValue('cat', $cat);
        }
        if (0 === strpos($category_id_string, 'child_category_id_')) {
            $category = explode('child_category_id_', $category_id_string);
            $slug = $sm->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
                            array('categoryType' => Constants::CHILD_CATEGORY_IDENTIFIER, 'slugName' => $category));
            if ($slug) {
                $child = $slug->getCategoryId();
            }
            $statement = $connection->prepare('SELECT s.shop_id FROM lb_shop_child_category s where s.child_category_id = :child');
            $statement->bindValue('child', $child);
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
                    if ($offer_value['shop_type'] == 'cashback') {
                        $offer_type = 'Cashback';
                    } elseif ($offer_value['shop_type'] == 'oferta') {
                        $offer_type = 'Ofertas';
                    } elseif ($offer_value['shop_type'] == 'product') {
                        $offer_type = 'Product';
                    } elseif ($offer_value['shop_type'] == 'coupon') {
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
                if ($value['shop_type'] == 'coupon') {
                    if ($alphabet == '0-9') {
                        foreach (range(0, 9) as $key => $value_digit) {
                            if (0 === strpos($value['voucher_program_name'], (string) $value_digit)) {
                                $cat_arr_filter[] = $value;
                            }
                        }
                    } elseif ($alphabet == 'TODAS') {
                        $cat_arr_filter[] = $value;
                    } else {
                        if (0 === strpos($value['voucher_program_name'], $alphabet) || 0 === strpos($value['voucher_program_name'], strtolower($alphabet))) {
                            $cat_arr_filter[] = $value;
                        }
                    }
                }

                if ($value['shop_type'] == 'cashback') {
                    if ($alphabet == '0-9') {
                        foreach (range(0, 9) as $key => $value_digit) {
                            if (0 === strpos($value['voucher_program_name'], (string) $value_digit)) {
                                $cat_arr_filter[] = $value;
                            }
                        }
                    } elseif ($alphabet == 'TODAS') {
                        $cat_arr_filter[] = $value;
                    } else {
                        if (0 === strpos($value['voucher_program_name'], $alphabet) || 0 === strpos($value['voucher_program_name'], strtolower($alphabet))) {
                            $cat_arr_filter[] = $value;
                        }
                    }
                }

                if ($value['shop_type'] == 'oferta') {
                    if ($alphabet == '0-9') {
                        foreach (range(0, 9) as $key => $value_digit) {
                            if (0 === strpos($value['voucher_program_name'], (string) $value_digit)) {
                                $cat_arr_filter[] = $value;
                            }
                        }
                    } elseif ($alphabet == 'TODAS') {
                        $cat_arr_filter[] = $value;
                    } else {
                        if (0 === strpos($value['voucher_program_name'], $alphabet) || 0 === strpos($value['voucher_program_name'], strtolower($alphabet))) {
                            $cat_arr_filter[] = $value;
                        }
                    }
                }
            }
        }

        return $cat_arr_filter;
    }
    public function shortingFilter($data_array, $shorting)
    {
        $cashback = array();
        $product = array();
        $voucher = array();
        $offers = array();
        $brand_arr_filter = array();
        if (!empty($data_array)) {
            foreach ($data_array as $key => $offer_value) {
                if (!empty($offer_value['shop_id'])) {
                    if ($offer_value['shop_type'] == 'cashback') {
                        $cashback[] = $offer_value;
                    } elseif ($offer_value['shop_type'] == 'oferta') {
                        $offers[] = $offer_value;
                    } elseif ($offer_value['shop_type'] == 'offer') {
                        $product[] = $offer_value;
                    } elseif ($offer_value['shop_type'] == 'coupon') {
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
            if (!empty($offers)) {
                foreach ($offers as $key => $value) {
                    $short_offer[$key] = $value['voucher_code_count'];
                }

                array_multisort($short_offer, SORT_DESC, $offers);
            }
            $brand_arr_filter = $voucher;
        } elseif ($shorting == 'Products') {
            $brand_arr_filter = $product;
        }

        return $brand_arr_filter;
    }
}
