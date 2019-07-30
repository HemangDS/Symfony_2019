<?php

namespace iFlair\LetsBonusAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use iFlair\LetsBonusAdminBundle\Entity\Searchlogs;
use Symfony\Component\HttpFoundation\Response;

class SearchlogsController extends Controller
{
    public function indexAction(Request $request)
    {
        $admin_pool = $this->get('sonata.admin.pool');
        $searchlogs = new Searchlogs();

        $em = $this->getDoctrine()->getEntityManager();
        $search_data_transaction_limit = $em->createQueryBuilder()
                           ->select('b.id')
                            ->from('iFlairLetsBonusAdminBundle:Searchlogs',  'b')
                            ->setFirstResult(0)
                            ->setMaxResults(1000)
                            ->getQuery()
                            ->getResult();

        $arr = array();

        foreach ($search_data_transaction_limit as $key => $value) {
            $arr[] = $value['id'];
        }

        $em = $this->getDoctrine()->getEntityManager();
        $search_data = $em->createQueryBuilder()
                   ->select('b')
                   ->from('iFlairLetsBonusAdminBundle:Searchlogs',  'b')
                    ->where('b.id IN (:miarray)')
                    ->setParameter('miarray', $arr)
                    ->orderBy('b.numSearch')
                    ->getQuery()
                    ->getResult();

        return $this->render('iFlairLetsBonusAdminBundle:Searchlogs:index.html.twig',
                array('admin_pool' => $admin_pool, 'search_data' => $search_data));
    }
    public function userfilterAction(Request $request)
    {
        $data = $request->request;

        if (isset($data->all()['sort'])) {
            $sort = $data->all()['sort'];
        } else {
            $sort = 'ASC';
        }

        if (isset($data->all()['page'])) {
            $page = $data->all()['page'] - 1;
        } else {
            $page = 0;
        }
        $filter_data_num = $data->all()['filter_num'];
        $filter_data = $data->all()['filter_data'];

        $page_pointer = $filter_data_num * $page;

        $em = $this->getDoctrine()->getEntityManager();
        $search_data_transaction_limit = $em->createQueryBuilder()
                           ->select('b.id')
                            ->from('iFlairLetsBonusAdminBundle:Searchlogs',  'b')
                            ->setFirstResult(0)
                           ->setMaxResults(1000)
                            ->getQuery()
                            ->getResult();

        $arr = array();

        foreach ($search_data_transaction_limit as $key => $value) {
            $arr[] = $value['id'];
        }

        if (!empty($filter_data)) {
            $em = $this->getDoctrine()->getEntityManager();
            $search_data_transaction = $em->createQueryBuilder()
                           ->select('b')
                            ->from('iFlairLetsBonusAdminBundle:Searchlogs',  'b')
                            ->where("(b.term LIKE '%".(string) $filter_data."%'  OR b.numSearch LIKE '%".(string) $filter_data."%'  OR b.numResults LIKE '%".(string) $filter_data."%') AND b.id IN (:miarray)")
                            ->setParameter('miarray', $arr)
                              ->orderBy('b.numSearch', $sort);
        } else {
            $em = $this->getDoctrine()->getEntityManager();
            $search_data_transaction = $em->createQueryBuilder()
                           ->select('b')
                            ->from('iFlairLetsBonusAdminBundle:Searchlogs',  'b')
                            ->where('b.id IN (:miarray)')
                            ->setParameter('miarray', $arr)
                              ->orderBy('b.numSearch', $sort);
        }

        $search_data_transaction_count = $search_data_transaction
                            ->getQuery()
                            ->getResult();
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

        /*******************************************************************************************************/

        $record = '';
        $response['count'] = $search_count;

        foreach ($search_data_transaction as $item) {
            $record = $record.'  <tr class="gradeA odd">
                                    <td class=" ">'.$item->getTerm().' </td>
                                    <td class="sorting_1">'.$item->getNumSearch().' </td>
                                    <td class=" ">'.$item->getNumResults().'  </td>
                                </tr>';
        }

        if (empty($record)) {
            $record = '<tr class="gradeA odd"> 
                            <td colspan="6">
                                No records found.
                            </td></tr>';
        }
        $response['html'] = $record;

        /*******************************************************************************************************/

       return new Response(json_encode($response));
    }
}
