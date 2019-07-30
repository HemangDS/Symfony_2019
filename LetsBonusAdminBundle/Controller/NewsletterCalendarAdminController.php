<?php

namespace iFlair\LetsBonusAdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;

class NewsletterCalendarAdminController extends CRUDController
{
    public function ListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('iFlairLetsBonusAdminBundle:Newsletter');
        $newsletters = $entities->findAll();

        $newsletters_array = array();
        $count = 0;
        foreach ($newsletters as $newsletter) {
            $newsletters_array[$count]['title'] = $newsletter->getStatus();

            $newsletter_date = $newsletter->getNdate()->format('Y-m-d h:i:s');
            $date = explode(' ', $newsletter_date);
            $actual_date = $date[0].'T'.$date[1];
            $newsletters_array[$count]['start'] = $actual_date;

            if ($newsletter->getStatus() == 'ready') {
                $newsletters_array[$count]['color'] = '#A0F2AA';
            } elseif ($newsletter->getStatus() == 'notpublished') {
                $newsletters_array[$count]['color'] = '';
            } else {
                $newsletters_array[$count]['color'] = '#F06F6F';
            }
            ++$count;
        }

        $newsletterdata = $newsletters_array;

        return $this->render('iFlairLetsBonusAdminBundle:NewsletterCalender:newslettercalender.html.twig', array(
            'newsletterdata' => $newsletterdata,
        ));
    }
}
