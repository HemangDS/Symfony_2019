<?php

namespace iFlair\LetsBonusAdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use iFlair\LetsBonusAdminBundle\Entity\CmsAllhelp;

class CmsAllhelpAdminController extends CRUDController
{
    public function createAction()
    {
        if ($this->get('request')->getMethod() == 'POST') {
            $data = $this->get('request');
            $allhelp_data = $this->getRequest()->request->all();
            foreach ($allhelp_data as $allhelp) {
                foreach ($allhelp as $key => $value) {
                    if ($key == 'status') {
                        if ($value == 1) {
                            $aboutus_content = $this->getDoctrine()
                            ->getRepository('iFlairLetsBonusAdminBundle:CmsAllhelp')
                            ->findBy(array('status' => 1));

                            if (empty($aboutus_content)) {
                                return parent::createAction();
                            } else {
                                $this->get('request')->getSession()->getFlashBag()->add('error', 'Please Keep One All Help page Enable Only..');
                                $url = $this->get('request')->headers->get('referer');

                                return new RedirectResponse($url);
                            }
                        } else {
                            return parent::createAction();
                        }
                    }
                }
            }
        } else {
            return parent::createAction();
        }
    }
}
