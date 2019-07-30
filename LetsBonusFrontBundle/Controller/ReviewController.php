<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use iFlair\LetsBonusFrontBundle\Form\ReviewType;
use iFlair\LetsBonusFrontBundle\Entity\Review;
use iFlair\LetsBonusAdminBundle\Entity\FrontUser;
use iFlair\LetsBonusAdminBundle\Entity\Shop;
use iFlair\LetsBonusAdminBundle\Entity\shopHistory;

class ReviewController extends Controller
{
    public function indexAction(Request $request, $name)
    {
        $review = new Review();
        $form = $this->createForm(new ReviewType(), $review);

        if (!empty($request->request->all()['email']) && !empty($request->request->all()['review']) || !empty($request->request->all()['rating'])) {
            $review->setUsername($name);
            $review->setEmail($request->request->all()['email']);
            $review->setReview($request->request->all()['review']);
            $review->setRating($request->request->all()['rating']);
            $review->setCreated(new \DateTime(date('Y-m-d H:i:s')));
            $em = $this->getDoctrine()->getManager();
            $em->persist($review);
            $em->flush();

            return $this->redirect($this->generateUrl('i_flair_lets_bonus_front_homepage'));
        }

        return $this->render('iFlairLetsBonusFrontBundle:Review:review.html.twig', array('form' => $form->createView()));
    }
    public function shopreviewAction(Request $request, $id, $shopId, $shopHistoryId)
    {
        // demo url: http://localhost:8000/secure/connect/review/user/1/shop/2/shophistory/3
        $review = new Review();
        $form = $this->createForm(new ReviewType(), $review);

        if (!empty($request->request->all()['email']) && !empty($request->request->all()['review']) || !empty($request->request->all()['rating'])) {
            $sm = $this->getDoctrine()->getEntityManager();
            $connection = $sm->getConnection();
            $statement = $connection->prepare('SELECT * FROM lb_front_user where id="'.$id.'" OR facebook_id="'.$id.'" OR google_id ="'.$id.'" ');
            $statement->execute();
            $user = $statement->fetchAll();

            foreach ($user as $key => $value) {
                $user_id = $value['id'];
                $user_name = $value['name'];
            }

            $em = $this->getDoctrine()->getManager();
            $frontUserRepository = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('id' => $user_id));
            $shopRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array(
                'id' => $shopId,
                'shopStatus' => Shop::SHOP_ACTIVATED,
            ));
            $shopHistoryRepository = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => $shopHistoryId));

            $review->setUsername($user_name);
            $review->setUserId($frontUserRepository);
            $review->setShopId($shopRepository);
            $review->setShopHistoryId($shopHistoryRepository);
            $review->setEmail($request->request->all()['email']);
            $review->setReview($request->request->all()['review']);
            $review->setRating($request->request->all()['rating']);
            $review->setCreated(new \DateTime(date('Y-m-d H:i:s')));

            $em = $this->getDoctrine()->getManager();
            $em->persist($review);
            $em->flush();

            return $this->redirect($this->generateUrl('i_flair_lets_bonus_front_homepage'));
        }

        return $this->render('iFlairLetsBonusFrontBundle:Review:review.html.twig', array('form' => $form->createView()));
    }
}
