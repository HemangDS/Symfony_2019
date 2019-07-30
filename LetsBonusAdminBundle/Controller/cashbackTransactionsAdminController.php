<?php

namespace iFlair\LetsBonusAdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use iFlair\LetsBonusAdminBundle\Entity\parentCategory;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;

class cashbackTransactionsAdminController extends CRUDController
{
    public function doubleCashBackAction($id = null)
    {
        $em = $this->getDoctrine()->getManager();
        $doubleCachBack = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->setDoubleCachBack($em, $id);
        $this->get('session')->getFlashBag()->add('success', 'Cash back amount has been doubled!');

        return $this->redirect($this->generateUrl('admin_iflair_letsbonusadmin_cashbacktransactions_list'));
    }

    /**
     * (non-PHPdoc).
     *
     * @see Sonata\AdminBundle\Controller.CRUDController::listAction()
     */
    public function listAction(Request $request = NULL)
    {
    	$data       = $request->request->all();
    	$categoryId = "";
    	$parentCategory = array();
    	$startDate = "";
    	$endDate = "";
    	$cashbackTransactionData = array();

    	if(!empty($data)) {
			$startDate = !empty($data['form']['start-date'])?$data['form']['start-date']:"";
    		$endDate = !empty($data['form']['end-date'])?$data['form']['end-date']:"";    		
			$categoryId = !empty($data['form']['name'])?$data['form']['name']:"";
			
			if(!empty($categoryId)) {
		    	$em = $this->getDoctrine()->getManager();
		    	$connection = $em->getConnection();
		    	$statement = $connection->prepare('SELECT ct.id,ct.shop_id AS tienda_id,vp.program_name AS tienda_title,ct.user_id,n.name AS network,ct.amount,c.code AS currency,ct.status,cpn.isoCode,ct.date,ct.created,spc.parent_category_id
		    										FROM lb_cashback_transactions AS ct 
		    										LEFT JOIN lb_shop_parent_category AS spc ON ct.shop_id = spc.shop_id
		    										LEFT JOIN lb_shop AS s ON ct.shop_id = s.id
		    										LEFT JOIN lb_voucher_programs AS vp ON s.vprogram_id = vp.id
		    										LEFT JOIN lb_network AS n ON ct.network_id = n.id
		    										LEFT JOIN lb_currency AS c ON ct.currency = c.id
		    										LEFT JOIN lb_companies AS cpn ON ct.company_id = cpn.id
		    										WHERE spc.parent_category_id = :categoryId
		    										');
		    	$statement->bindValue('categoryId', $categoryId);
		    	$statement->execute();
	            $cashbackTransactionData = $statement->fetchAll();
			}
    	}
    	
    	$parentCategory = new parentCategory();
    	
    	$parentCategoryform = $this->createFormBuilder($parentCategory)
    		->add('name', 'entity', array(
                            'class' => 'iFlair\LetsBonusAdminBundle\Entity\parentCategory',
                            'property' => 'name',
                            'attr' => array('style' => 'width: auto'),
                            'placeholder' => 'Select category',
                            'required' => true,
                            'preferred_choices' => array($categoryId),
                        ))
            ->getForm()->createView();

    	if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render("iFlairLetsBonusAdminBundle:CashbackTransactions:list.html.twig", array(
            'action'     => 'list',
            'form'       => $formView,
            'datagrid'   => $datagrid,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
            'parentCategoryform' => $parentCategoryform,
            'cashbackTransactionData' => $cashbackTransactionData,
            'startdate' => $startDate,
            'enddate' => $endDate,
        ), null);
    }
}
