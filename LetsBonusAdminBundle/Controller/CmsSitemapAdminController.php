<?php

namespace iFlair\LetsBonusAdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use iFlair\LetsBonusAdminBundle\Entity\CmsSitemap;

class CmsSitemapAdminController extends CRUDController
{
    public function createAction()
    {
        if ($this->get('request')->getMethod() == 'POST') {
            $data = $this->get('request');
            $sitemap_data = $this->getRequest()->request->all();
            foreach ($sitemap_data as $sitemap) {
                foreach ($sitemap as $key => $value) {
                    if ($key == 'status') {
                        if ($value == 1) {
                            $aboutus_content = $this->getDoctrine()
                            ->getRepository('iFlairLetsBonusAdminBundle:CmsSitemap')
                            ->findBy(array('status' => 1));

                            if (empty($aboutus_content)) {
                                return parent::createAction();
                            } else {
                                $this->get('request')->getSession()->getFlashBag()->add('error', 'Please Keep One Sitemap page Enable Only..');
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
