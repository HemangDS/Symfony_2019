<?php

namespace iFlair\LetsBonusAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use iFlair\LetsBonusAdminBundle\Entity\FrontUser;
use iFlair\LetsBonusAdminBundle\Entity\Clicks;
use Symfony\Component\HttpFoundation\Response;
use iFlair\LetsBonusAdminBundle\Slug\Constants;
use iFlair\LetsBonusAdminBundle\Entity\Slug;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SacController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $admin_pool = $this->get('sonata.admin.pool');
        $user = new FrontUser();
        $search_field = $this->getRequest()->get('user_search');

        if (!empty($search_field)) {
            $em = $this->getDoctrine()->getEntityManager();
            $search_data = $em->createQueryBuilder()
                    ->select('b')
                    ->from('iFlairLetsBonusAdminBundle:FrontUser',  'b')
                    ->where("b.name = '".(string) $search_field."' OR b.email LIKE '%".(string) $search_field."%'")
                    ->getQuery()
                       ->getResult();

            return $this->render('iFlairLetsBonusAdminBundle:SAC:index.html.twig', array('admin_pool' => $admin_pool, 'search_data' => $search_data));
        }

        return $this->render('iFlairLetsBonusAdminBundle:SAC:index.html.twig', array('admin_pool' => $admin_pool, 'search_data' => ''));
    }
    public function filterAction($id, Request $request)
    {
        $data = $request->request;

        if (isset($data->all()['page'])) {
            $page = $data->all()['page'] - 1;
        } else {
            $page = 0;
        }
        $filter_data_num = $data->all()['filter_num'];
        $filter_data = $data->all()['filter_data'];
        $start_date = $data->all()['start_date'];
        $end_date = $data->all()['end_date'];

        $page_pointer = $filter_data_num * $page;

        $start_date_time = date('Y-m-d H:m:s', strtotime($start_date));
        $end_date_time = date('Y-m-d H:m:s', strtotime($end_date));

        if (!empty($filter_data)) {
            if (!empty($start_date) && !empty($end_date)) {
                if ($start_date_time < $end_date_time) {
                    $condition = ' b.userId = '.$id." AND b.created >= '".$start_date_time."'  AND b.created <= '".$end_date_time."' AND b.id LIKE '%".$filter_data."%' OR b.shopId LIKE '%".$filter_data."%' OR b.type LIKE '%".$filter_data."%'";
                }
            } else {
                $condition = 'b.userId = '.$id." AND b.id LIKE '%".$filter_data."%' OR b.shopId LIKE '%".$filter_data."%' OR b.type LIKE '%".$filter_data."%'";
            }
        } else {
            if (!empty($start_date) && !empty($end_date)) {
                if ($start_date_time < $end_date_time) {
                    $condition = ' b.userId = '.$id." AND b.created >= '".$start_date_time."'  AND b.created <= '".$end_date_time."'";
                }
            } else {
                $condition = 'b.userId = '.$id;
            }
        }

        $em = $this->getDoctrine()->getEntityManager();
        $search_data_transaction = $em->createQueryBuilder()
               ->select('b')
               ->from('iFlairLetsBonusAdminBundle:Clicks',  'b')
               ->where($condition);

        $search_data_transaction_count = $search_data_transaction->getQuery()->getResult();
        $search_count = count($search_data_transaction_count);

        if (!empty($filter_data_num)) {
            $search_data_transaction = $search_data_transaction->setFirstResult($page_pointer)
                                        ->setMaxResults($filter_data_num)
                                        ->getQuery()
                                        ->getResult();
        } else {
            $search_data_transaction = $search_data_transaction
                            ->getQuery()
                            ->getResult();
        }

        $record = '';
        $response['count'] = $search_count;

        foreach ($search_data_transaction as $item) {
            $shop = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => $item->getShopId()));
            if($shop) {
                $shopshistory = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => $item->getShopsHistoryId()));
                if($shopshistory){
                    $voucherProgramId = $shop->getVprogram()->getId();
                    $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryId' => $voucherProgramId, 'categoryType'=>Constants::MARCAS_IDENTIFIER));
                    if($slug){
                        $brandUrl = $this->generateUrl('marcas_view', array('slug_name' => $slug->getSlugName()), UrlGeneratorInterface::ABSOLUTE_URL);
                    }else{
                        $brandUrl = '';
                    }
                    $record .='<tr class="gradeA odd" >';
                    $record .='<td align="left" class="sorting_1 center  ">'.$shopshistory->getTitle().'</td>';
                    $record .='<td align="left" class="center  "> '.$shop->getId().'</td>';
                    $record .='<td align="left" class="center  "> '.$shop->getNetwork()->getName().'</td>';
                    $record .='<td align="left" class="center  "> '.$item->getType().' </td>';
                    $record .='<td align="left" class="center  "> '.$item->getModified()->format('Y-m-d H:i:s').' </td>';
                    $record .='<td class=" ">'.$shop->getCompanies()->getIsoCode().'</td>';
                    $record .='<td class=" ">';
                    $record .='<a href="'.$brandUrl.'" target="_blank">Previsualizar</a><br>';
                    $record .='</td>';
                    $record .='</tr>';
                }
            }
        }
        if (empty($record)) {
            $record .= '<tr class="gradeA odd">';
            $record .= '<td colspan="6">';
            $record .= 'No records found.';
            $record .= '</td></tr>';
        }
        $response['html'] = $record;

        return new Response(json_encode($response));
    }

    public function userfilterAction(Request $request)
    {
        $data = $request->request;

        if (isset($data->all()['page'])) {
            $page = $data->all()['page'] - 1;
        } else {
            $page = 0;
        }
        $filter_data_num = $data->all()['filter_num'];
        $filter_data = $data->all()['filter_data'];

        $page_pointer = $filter_data_num * $page;

        if (!empty($filter_data)) {
            $em = $this->getDoctrine()->getEntityManager();
            $search_data_transaction = $em->createQueryBuilder()
               ->select('b')
               ->from('iFlairLetsBonusAdminBundle:FrontUser',  'b')
               ->where("b.name = '".(string) $filter_data."' OR b.email LIKE '%".(string) $filter_data."%'  OR b.surname LIKE '%".(string) $filter_data."%'");
        } else {
            $em = $this->getDoctrine()->getEntityManager();
            $search_data_transaction = $em->createQueryBuilder()
                   ->select('b')
                   ->from('iFlairLetsBonusAdminBundle:FrontUser',  'b');
        }

        $search_data_transaction_count = $search_data_transaction->getQuery()->getResult();
        $search_count = count($search_data_transaction_count);

        if (!empty($filter_data_num)) {
            $search_data_transaction = $search_data_transaction->setFirstResult($page_pointer)->setMaxResults($filter_data_num)->getQuery()->getResult();
        } else {
            $search_data_transaction = $search_data_transaction->getQuery()->getResult();
        }

        $record = '';
        $response['count'] = $search_count;

        foreach ($search_data_transaction as $item) {
            $record .=' <tr class="gradeA odd">';
            $record .=' <td class="sorting_1"> '.$item->getId().' </td>';
            $record .=' <td align="left" class="center  "> '.$item->getName().' </td>';
            $record .=' <td align="left" class="center  "> '.$item->getSurname().'</td>';
            $record .=' <td align="left" class="center  "> '.$item->getEmail().'</td>';
            $record .=' <td class=" ">'.$item->getCreated()->format('Y-m-d H:i:s').' </td>';
            $record .=' <td class=" ">'.$item->getModified()->format('Y-m-d H:i:s').'</td>';
            $record .=' <td class="actions ">';
            $record .=' <a href="sac/users/view/'.$item->getId().' ">View</a> </td></tr>';
        }

        if (empty($record)) {
            $record .='<tr class="gradeA odd">';
            $record .='<td colspan="6">';
            $record .='No records found.';
            $record .='</td></tr>';
        }
        $response['html'] = $record;

        return new Response(json_encode($response));
    }

    public function viewAction($id, Request $request)
    {
        $arr = $request->request->get('data');
        $start_date = $arr['User']['start_date'];
        $end_date = $arr['User']['end_date'];

        $start_date_time = date('Y-m-d H:m:s', strtotime($start_date));
        $end_date_time = date('Y-m-d H:m:s', strtotime($end_date));

        $admin_pool = $this->get('sonata.admin.pool');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser');
        $user_info = $repository->findOneBy(array('id' => $id));
        $search_data_transaction = array();
        if (!empty($start_date) && !empty($end_date)) {
            if ($start_date_time < $end_date_time) {
                $em = $this->getDoctrine()->getEntityManager();
                $search_data_transaction = $em->createQueryBuilder()
                       ->select('b')
                       ->from('iFlairLetsBonusAdminBundle:Clicks',  'b')
                       ->where('b.userId = '.$id." AND b.created >= '".$start_date_time."'  AND b.created <= '".$end_date_time."'")
                       ->getQuery()
                       ->getResult();
            } else {
                $search_data_transaction = array();
                $message = 'Please select proper dates';
            }
        } else {
            $em = $this->getDoctrine()->getEntityManager();
            $clicks = $em->getRepository('iFlairLetsBonusAdminBundle:Clicks')->findBy(array('userId' => $id));
            $i=0;
            foreach($clicks as $click){
                $shop = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => $click->getShopId()));
                if($shop) {
                    $shopshistory = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => $click->getShopsHistoryId()));
                    if($shopshistory) {
                        $search_data_transaction[$i]['shopId'] = $shop->getId();
                        $search_data_transaction[$i]['shopHistoryId'] = $shopshistory->getId();
                        $search_data_transaction[$i]['shopTitle'] = $shopshistory->getTitle();
                        $search_data_transaction[$i]['shopNetworkName'] = $shop->getNetwork()->getName();
                        $search_data_transaction[$i]['shopType'] = $click->getType();
                        $voucherProgramId = 0;
                        if($shop->getVprogram()) {
                            $voucherProgramId = $shop->getVprogram()->getId();
                        }                        
                        $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryId' => $voucherProgramId, 'categoryType' => Constants::MARCAS_IDENTIFIER));
                        if($slug) {
                            $url = $this->generateUrl('marcas_view', array('slug_name' => $slug->getSlugName()), UrlGeneratorInterface::ABSOLUTE_URL);
                            $search_data_transaction[$i]['brandUrl'] = $url;
                        }else{
                            $search_data_transaction[$i]['brandUrl'] = '';
                        }
                        $search_data_transaction[$i]['modifiedDate'] = $click->getModified()->format('Y-m-d H:i:s');
                        $search_data_transaction[$i]['shopCompany'] = $shop->getCompanies()->getIsoCode();
                    }
                }
                $i++;
            }
            /*$search_data_transaction = $em->createQueryBuilder()
                   ->select('b')
                   ->from('iFlairLetsBonusAdminBundle:Clicks',  'b')
                   ->innerJoin('iFlairLetsBonusAdminBundle:Shop', 's')
                   ->where('b.userId = '.$id)
                   ->andWhere('b.shopId = s.id')
                   ->getQuery()
                   ->getResult();*/
            /*$search_data_transaction = $em->createQueryBuilder()
                ->add('select', 's, c')
                ->add('from', 'iFlairLetsBonusAdminBundle:Clicks c')
                ->leftJoin('iFlairLetsBonusAdminBundle:Shop', 's')
                ->where('c.userId = '.$id)
                ->andWhere('c.shopId = s.id')
                ->getQuery()
                ->getResult();*/
        }

        if (empty($search_data_transaction)) {
            $message = 'No records found';
        } else {
            $message = '';
        }

        $cashbackTransactionsRepository = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions');
        $cashbackTransactions = $cashbackTransactionsRepository->findBy(array('userId' => $id));

        $messageCashbackTransaction = count($cashbackTransactions);

        $amount = 0;
        if($messageCashbackTransaction > 0){
            foreach($cashbackTransactions as $cashbackTransaction){
                $amount = $amount + $cashbackTransaction->getAmount();
            }
        }

        return $this->render('iFlairLetsBonusAdminBundle:SAC:edit.html.twig', array('admin_pool' => $admin_pool, 'label' => $user_info, 'search_data_transaction' => $search_data_transaction, 'message' => $message, 'cashbackTransactions' => $cashbackTransactions, 'messageCashbackTransaction' => $messageCashbackTransaction, 'amount' => $amount));
    }

    public function reportAction()
    {
        $admin_pool = $this->get('sonata.admin.pool');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions');
        $transaction_info = $repository->findAll();

        return $this->render('iFlairLetsBonusAdminBundle:SAC:report.html.twig', array('admin_pool' => $admin_pool, 'transaction_info' => $transaction_info));
    }
}
