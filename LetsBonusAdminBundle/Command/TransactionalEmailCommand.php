<?php
/***
    Transactions email of purchase order
*/
/*
        /review/{name} for normal reviews 
        &
        /review/user/{id}/shop/{shopId}/shophistory/{shopHistoryId} for shop reviews
*/

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
// use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions;
use iFlair\LetsBonusAdminBundle\Entity\FrontUser;

class TransactionalEmailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('transaction:email')->setDescription('Transaction Email After 15 Days Of Transaction.');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $emailDate = date('Y-m-d', strtotime('-15 days'));

        $em = $this->getContainer()->get('doctrine')->getManager();

        $fromDatee = new \DateTime($emailDate);
        $fromDatee->setTime(0, 0, 0);
        $fromDate = $fromDatee->format('Y-m-d H:i:s');

        $toDatee = clone $fromDatee;
        $toDatee->modify('+1 day');
        $toDate = $toDatee->format('Y-m-d H:i:s');

        $result = $em->getRepository('iFlairLetsBonusAdminBundle:LetsBonusTransactions')->createQueryBuilder('o')
                    ->where('o.created >= :fcreated')
                    ->andWhere('o.created < :lcreated')
                    ->setParameter('fcreated', $fromDate)
                    ->setParameter('lcreated', $toDate)
                    ->getQuery()
                    ->getResult();

        /*$request = $this->container->get('request');
        $routeName = $request->get('i_flair_lets_bonus_front_review');*/
        // $request = $this->container->get('request');

        /*$request = $this->getContainer()->get('request');
        
        echo "routename is :: ".$routenm = $request->attributes->get('i_flair_lets_bonus_front_review');
        die();
        */
        // $context = sfContext::createInstance($this->configuration);

        // echo link_to('my article', 'article/read?title=Finance_in_France')

        // http://192.168.1.122/letsbonus_devlatest/web/secure/connect/review/Yogesh

        // $routing = $this->getContainer()->get('router');
        // echo $router = $routing->get("i_flair_lets_bonus_front_review")->getAliases();

        // $router = $this->get('router');
        //$route = $router->match('/foo')['_route'];
        // $router = $this->getContainer()->get('router');
        // $router = $this->container->get('router');
        // $routing = $router->getRouting();
        //$routing = $this->getRouting();
        // echo "URL".$url = $routing->generate('i_flair_lets_bonus_front_review', array('name' => 'Yogesh'));

        // $url = $this->generateUrl('i_flair_lets_bonus_front_review', array('name' => 'Yogesh'));

        // echo "home : ".$this->getContainer()->get('router')->getContext()->getBaseUrl();

        // echo $host = $this->getContainer()->getParameter('host');
        // echo "".$this->getContainer()->get('router')->getContext()->setHost($host);
        // echo "tets ".$this->getContainer()->get('router')->getContext()->setScheme('https');
        // echo "tets ".$this->getContainer()->get('router')->getContext()->getBaseUrl(); 

        /*echo $this->getContainer()->get('router')->generateUrl("homepage");
        echo "scheme : ".$scheme = $this->getContainer()->get('router')->getContext()->getScheme();
        echo "baseURL : ".$baseURL = $this->getContainer()->getParameter('base_url');*/

        // echo "gert : ".$this->getContainer->get('router')->getContext()->getBaseUrl();
        // echo $this->get('request')->getSchemeAndHttpHost();
        /*echo "ggiane : ">$this->getContainer()->get('router')->getContext()->getSchemeAndHttpHost();
        echo "home : ".$this->getContainer()->get('router')->getContext()->getBaseUrl();
        echo "url :: ".$host =  $this->getContainer()->get('router')->getContext()->getHost();
        echo "url :: ".$scheme =  $this->getContainer()->get('router')->getContext()->getScheme();*/
        // echo "url :: ".$baseURL = $this->getContainer()->getParameter('base_url');             

        // echo "home : ".$this->getContainer()->get('router')->getContext()->getSchemeAndHttpHost();

        // $url = $this->container->get('router')->generate('blog_show',array('slug' => 'my-blog-post'));
        // echo $route = $router->match($this->getRequest()->getPathInfo());

/* WORKING URL */
        // echo "hoempage : ".$this->getContainer()->get('router')->generate('i_flair_lets_bonus_front_homepage');
        // echo "real : ".$this->getContainer()->get('router')->generate('i_flair_lets_bonus_front_review', array('name' => 'Yogesh'));
        // die();
/* WORKING URL */

        foreach ($result as $res) {
            $userId = $res->getParam0();
            $userdata = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findById(trim($userId));

            $userEmailName = '';
            foreach ($userdata as $udata) {
                $userEmailName = $udata->getName();
            }

            $userEmailName; // User Name 
            
            $message = \Swift_Message::newInstance()
                ->setSubject('Transactional EMail After 15 Days ')
                ->setFrom($this->getContainer()->getParameter('from_send_email_id'))
                ->setTo($userEmailName)
                ->setCharset('UTF-8')
                ->setContentType('text/html')
                ->setBody('Transaction Email After 15 Days Has Been Sent Of Date and Time is '.$res->getCreated()->format('Y-m-d H:i:s').' ..!! <br/><br/> Review Url :: '.$userEmailName.'');

            $res = $this->getContainer()->get('mailer')->send($message);
            if ($res) {
                echo 'Mail Has been Sent..!!';
            }
        }

        // Connection could not be established with host 127.0.0.1 [Connection refused #111]  
        /*print_r($fromDate);
        print_r($toDate);*/
        /*$starrtt = new \DateTime($emailDate . '00:00:00');
        $starrt = $starrtt->format('Y-m-d H:i:s');
        $ensdsdd = new \DateTime($emailDate . '23:59:59');
        $ensdsd = $ensdsdd->format('Y-m-d H:i:s');*/
        /*$res = $em->getEntityManager()
                ->createQuery('SELECT p FROM iFlairLetsBonusAdminBundle:LetsBonusTransactions p WHERE p.created >= '.$fromDate.' && p.created < '.$toDate)
                ->getResult();
        print_r($res);
        die();*/
/*        $query = $em->createQuery(
            'SELECT p
            FROM iFlairLetsBonusAdminBundle:LetsBonusTransactions p
            WHERE p.created > :price
            ORDER BY p.price ASC'
        )->setParameter('price', '19.99');
        $products = $query->getResult();
*/
        /*$repo = $entityManager->getRepository('iFlairLetsBonusAdminBundle:LetsBonusTransactions');*/
        /*$qb = $repo
            ->createQueryBuilder('c')
            ->where('c.tags LIKE :tag')
            ->andWhere('c.reviewed = 1')
            ->setParameter('tag', "%{$tag}%");
        return $qb->getQuery()->getResult();*/
        /*$query = $repo->createQueryBuilder('a')
                    ->where('a.created >= :fcreated')
                    ->andWhere('a.created < :lcreated')
                    ->setParameter('fcreated', $fromDate)
                    ->setParameter('lcreated', $toDate);
                   // ->where('a.created = :created')
                   // ->setParameter('created', $emailDate)
                   // ->getQuery();
        $rslt = $query->getQuery()->getResult();
        print_r($rslt); die();*/
        /*return $qb->getQuery()->getResult();
        foreach($query as $result)
        {
            print_r($result);
            die();
        }
        die();
        $query = $em->createQuery(
            'SELECT p
            FROM AppBundle:Product p
            WHERE p.price > :price
            ORDER BY p.price ASC'
        )->setParameter('price', '19.99');
        $products = $query->getResult();*/
    }
}
