<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use iFlair\LetsBonusAdminBundle\Entity\AddtoFev;
use iFlair\LetsBonusAdminBundle\Entity\Shop;

class AddtoFevController extends Controller
{
    public function indexAction(Request $request, $id)
    {
        $session = new Session();
        $previous_url = $this->getRequest()->headers->get('referer');

        $sm = $this->getDoctrine()->getEntityManager();
        $connection = $sm->getConnection();
        $statement = $connection->prepare('SELECT * FROM lb_front_user where id="'.$session->get('user_id').'" OR facebook_id="'.$session->get('user_id').'" OR google_id ="'.$session->get('user_id').'" ');
        $statement->execute();
        $user = $statement->fetchAll();

        foreach ($user as $key => $value) {
            $user_id = $value['id'];
        }

        $em = $this->getDoctrine()->getManager();
        $frontUserRepository = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('id' => $user_id));
        $shopRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array(
            'id' => $id,
            'shopStatus' => Shop::SHOP_ACTIVATED,
        ));
        $shopHistoryRepository = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => $this->getRequest()->request->get('shop_history_id')));
        //echo $session->get('user_id')." : ".$id." : ".$_POST['shop_history_id'];
        //////echo $frontUserRepository->getId()." : ".$shopRepository->getId()." : ".$shopHistoryRepository->getId();
        //exit('Done xxx');
      /*  echo $frontUserRepository;
        echo $shopRepository;
        echo $shopHistoryRepository;
        exit();*/
        $AddtoFev = new AddtoFev();
        $AddtoFev->setUserId($frontUserRepository);
        $AddtoFev->setShopId($shopRepository);
        $AddtoFev->setShopHistoryId($shopHistoryRepository);
        $AddtoFev->setCreated(new \DateTime(date('Y-m-d H:i:s')));
        $AddtoFev->setModified(new \DateTime(date('Y-m-d H:i:s')));
        $em->persist($AddtoFev);
        $em->flush();

        return $this->redirect($previous_url);
    }
    public function removefevAction($id)
    {
        $session = new Session();
        $sm = $this->getDoctrine()->getEntityManager();
        $connection = $sm->getConnection();
        $statement = $connection->prepare('SELECT * FROM lb_front_user where id="'.$session->get('user_id').'" OR facebook_id="'.$session->get('user_id').'" OR google_id ="'.$session->get('user_id').'" ');
        $statement->execute();
        $user = $statement->fetchAll();

        foreach ($user as $key => $value) {
            $user_id = $value['id'];
        }

        $previous_url = $this->getRequest()->headers->get('referer');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('iFlairLetsBonusAdminBundle:AddtoFev')
                    ->findBy(array('shopId' => $id, 'userId' => $user_id, 'shopHistoryId' => $_POST['shop_history_id']));

        foreach ($entity as $key => $value) {
            $removefev = $value;
        }
        $em->remove($removefev);

        $em->flush();

        return $this->redirect($previous_url);
    }
}
