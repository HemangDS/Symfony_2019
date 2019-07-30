<?php

namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Reportcontent;

class ReportService
{
	protected $doctrine;
    public function __construct(RequestStack $request,Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    /**
     * Report Content soap service, store user report data into database and send email to user and admin
     * @param string $name 
     * @return mixed
     */
    public function sendreportcontent($user_id,$subject,$content,$report_send_email, $status)
    {
        $em = $this->doctrine->getManager();
		$user = $this->doctrine->getRepository('AppBundle:User')->findOneBy(array('id' => $user_id));
		$report_content = new Reportcontent();
        $report_content->setContent($content);
        $report_content->setSubject($subject);
        $report_content->setUserId($user);
        $report_content->setStatus($status);
        $em->persist($report_content);
        $em->flush();
        $last_inserted_id = $report_content->getId();
        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'id' => $last_inserted_id,
            'content' => array(
             'secondary_content' => 'Report Sent Successfully.'
            )
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}