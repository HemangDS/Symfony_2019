<?php

namespace iFlair\LetsBonusAdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\DataTransformer\ArrayToModelTransformer;

class networkCredentialsAdminController extends CRUDController
{
    /*public function listAction()
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        if ($listMode = $this->getRequest()->get('_list_mode')) {
            $this->admin->setListMode($listMode);
        }

        $this->admin->setSubject($this->admin->getNewInstance());

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render($this->admin->getTemplate('list'), array(
            'action'     => 'list',
            'form'       => $formView,
            'datagrid'   => $datagrid,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
        ));
    }*/
    /*public function loadcredentialsfieldsAction(Request $request)
    {
        $templateKey = 'edit';

        $network = $request->get('value');
        $request->getSession()->set('network_name', $network);
        exit;
        $object = $this->admin->removeFormFieldDescription('amazonCredentials');
        $form = $this->admin->getForm();

        $fieldDescription = $this->admin->getModelManager()
            ->getNewFieldDescriptionInstance($this->admin->getClass(), 'amazonCredentials');
        $fieldDescription->setAssociationAdmin($this->container->get('i_flair_lets_bonus_admin.admin.amazon_credentials'));
        $fieldDescription->setAdmin($this->admin);
        $fieldDescription->setAssociationMapping(array(
            'fieldName' => 'amazonCredentials',
            'type' => ClassMetadataInfo::ONE_TO_MANY,
        ));

        $contractor = $this->container->get('sonata.admin.builder.orm_form');
        $mapper = new FormMapper($contractor, $this->admin->getFormBuilder(), $this->admin);*/

        /*if($network=='Zanox') {
            $formMapper = $mapper->add('amazonCredentials', 'sonata_type_collection', array(
                'cascade_validation' => true,
                'by_reference' => false,
                'label' => 'Variation\'s',
            ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'position',
                    'link_parameters' => array('context' => 'default'),
                    'admin_code' => 'i_flair_lets_bonus_admin.admin.amazon_credentials',
                )
            );
        }*/

        //@ToDo build $form from $form_mapper

        /*return $this->render($this->admin->getTemplate($templateKey), array(
            'form' => $form->createView(),
        ));

        $view = $formMapper->getFormBuilder()->getForm()->createView();

        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'create',
            'form' => $view,
            'object' => $object,
        ));
    }
    public function loadcredentialsfields1Action(Request $request)
    {
        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }
        $templateKey = 'edit';

        $object = $this->admin->getNewInstance();
        $this->admin->setSubject($object);

        $form = $this->admin->getForm();
        $form->setData($object);*/

        /*if ($form->has('programsextra')) {
            $form->remove('programsextra');
        }*/

        /*$fieldDescription = $this->admin->getModelManager()
            ->getNewFieldDescriptionInstance($this->admin->getClass(), 'amazonCredentials');
        $fieldDescription->setAssociationAdmin($this->container->get('i_flair_lets_bonus_admin.admin.amazon_credentials'));
        $fieldDescription->setAdmin($this->admin);
        $fieldDescription->setAssociationMapping(array(
            'fieldName' => 'amazonCredentials',
            'type' => ClassMetadataInfo::ONE_TO_MANY,
        ));*/

        // Getting form mapper in controller:
        /*$contractor = $this->container->get('sonata.admin.builder.orm_form');
        $mapper = new FormMapper($contractor, $this->admin->getFormBuilder(), $this->admin);*/

        /*$formMapper = $mapper->add('amazonCredentials', 'sonata_type_admin', array(
            'sonata_field_description' => $fieldDescription,
            //'class' => $this->container->get('i_max_twisted_admin.admin.programs_extra')->getClass(),
            //'model_manager' => $this->container->get('i_max_twisted_admin.admin.programs_extra')->getModelManager(),
            'label' => 'amazonCredentials',
            'required' => false,
            "mapped"=>false,
        ));*/
       /* if ($request->get('value') == 'Zanox') {
            $formMapper = $mapper->add('amazonCredentials', 'sonata_type_collection', array(
                'cascade_validation' => true,
                'by_reference' => false,
                'label' => 'Variation\'s',
            ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'position',
                    'link_parameters' => array('context' => 'default'),
                    'admin_code' => 'i_flair_lets_bonus_admin.admin.amazon_credentials',
                )
            );
        }*/

        //$view = $form->createView();

        //$fieldDescription->getAdmin()->setSubject($formMapper->getFormBuilder()->getData());

        /*$this->admin->defineFormBuilder($formMapper->getFormBuilder());

        $formMapper->getFormBuilder()->addModelTransformer(new ArrayToModelTransformer($this->admin->getModelManager(), $this->admin->getClass()));

        //$formMapper->getFormBuilder()->getForm()->setData($formMapper->getFormBuilder()->getData());

        $view = $formMapper->getFormBuilder()->getForm()->createView();

        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'create',
            'form' => $view,
            'object' => $object,
        ));
    }*/
}
