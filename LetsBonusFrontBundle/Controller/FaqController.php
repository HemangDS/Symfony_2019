<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FaqController extends Controller
{
    public function getFaqAction()
    {
        $connection = $this->get('doctrine.orm.default_entity_manager')->getConnection();

        $query = $connection->query(
            'SELECT fq.*,fpc.* FROM lb_faq_parent_category AS fpc
             JOIN lb_faq_question AS fq ON fq.faq_parent_category_id = fpc.id
             WHERE fq.status = 1 AND fpc.status = 1'
        );
        $query->execute();
        $faqs = $query->fetchAll();
        $categoryWiseFaq = [];
        foreach ($faqs as $value) {
            $categoryWiseFaq[$value['name']][] = $value;
        }

        return $this->render('iFlairLetsBonusFrontBundle:Faq:faq.html.twig', ['faqs' => $categoryWiseFaq]);
    }
}
