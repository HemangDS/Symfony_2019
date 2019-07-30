<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\TransactionalQueueMail;

class TransactionalqueuemailsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('network:transactionqueueemail')->setDescription('Transaction Registration Email Alert');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $pendingtosendMails = $em->getRepository('iFlairLetsBonusAdminBundle:TransactionalQueueMail')->findByStatus('PENDING');

        /* Here :: We need to change Date like they have used */
        /* FROM :: strtotime($element->getPurchaseDate()->format('Y-m-d')) */
        /* TO :: date('Y-m-d',strtotime($element['Transactionalqueuemail']['purchaseDate'])) :: TIMESTAMP  */
        /* And Also need to change For Email sending */

        $provider = $this->getContainer()->get('sonata.media.provider.image');
        $user_name = '';
        $shop_name = '';
        $total_amount = '';
        $shop_image = '';
        $currency_symbol = '';
        $user_email = '';
        foreach ($pendingtosendMails as $element) {
            if ($element->getCurrency() == 'EUR') {
                $currency_symbol = '€';
            } else {
                $currency_symbol = '€';
            }
            $UserEntity = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('id' => $element->getIdClient()));
            $user_name = $UserEntity->getName(); // User Name

            $user_email = $UserEntity->getEmail(); // User Email

            $ShopEntity = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => $element->getShop()));
            $shop_name = $ShopEntity->getTitle(); // Shop Name

            $cashbackEntity = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('id' => $element->getCashbacktransactionId()));
            $cashbackEntity->getId();
            $total_amount = $cashbackEntity->getAmount(); // Total Amount

            /* Shop Logo Code */
            $shop_image = '';
            if (!empty($ShopEntity->getImage())):
                $media = $ShopEntity->getImage();
            $format = $provider->getFormatName($media, 'hoy_te_recomendamos');
            $shop_image = $provider->generatePublicUrl($media, $format); // Shop Image
            else:
                $shop_image = ''; // Shop Image
            endif;

            $message = \Swift_Message::newInstance()
                ->setSubject('Tu compra ha sido registrada correctamente')
                ->setFrom($this->getContainer()->getParameter('from_send_email_id'))
                ->setTo(trim($user_email))
                ->setBody($this->getContainer()->get('templating')->render(
                    'iFlairLetsBonusFrontBundle:Email:Transactional_Email_Compra_Realizada.html.twig',
                    array(
                            'name' => $user_name,
                            'email' => $user_email,
                            'shopname' => $shop_name,
                            'amount' => $total_amount,
                            'shopimage' => $shop_image,
                            'currency' => $currency_symbol,
                        )
                    ), 'text/html');
            $containerEmailObject = $this->getContainer()->get('mailer');
            $res = $containerEmailObject->send($message);
            if ($res == 1):
                $em = $this->getContainer()->get('doctrine')->getManager();
            $element->setStatus('SENDED');
            $element->setSendedDate(new \DateTime());
            $em->persist($element);
            $em->flush(); else:
                echo 'Email Alert Failed For :: Transaction Registration Email Alert';
            endif;
        }
    }
    /*public function transactionQueueEmailHtml($parameters)
    {
        $templateContent = '';
        $templateContent .= '<div>';
        $templateContent .= '<h3>Transaction Queue Email Details</h3>';
        $templateContent .= '<p> idClient ::   '.$parameters['mailParams']['idClient'].'</p>';
        $templateContent .= '<p> isoCode ::   '.$parameters['mailParams']['isoCode'].'</p>';
        $templateContent .= '<p> mailType ::   '.$parameters['mailParams']['mailType'].'</p>';
        $templateContent .= '<p> shopName ::   '.$parameters['mailParams']['shopName'].'</p>';
        $templateContent .= '<p> amount ::   '.$parameters['mailParams']['amount'].'</p>';
        $templateContent .= '<p> total ::   '.$parameters['mailParams']['total'].'</p>';
        $templateContent .= '<p> currency ::   '.$parameters['mailParams']['currency'].'</p>';
        $templateContent .= '<p> purchaseDate ::   '.$parameters['mailParams']['purchaseDate'].'</p>';
        $templateContent .= '</div>';
        return $templateContent;
    }*/
}
