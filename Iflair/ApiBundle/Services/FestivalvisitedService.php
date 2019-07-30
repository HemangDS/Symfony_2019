<?php
namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use IFlairSoapBundle\Entity\Settings;
use AppBundle\Entity\User;
use IFlairFestivalBundle\Entity\festivalFavourite;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use IFlairFestivalBundle\Entity\festivalVisited;

class FestivalvisitedService
{
    protected $doctrine;
    public function __construct(RequestStack $request,Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    /**
     * Set user wise Applications settings.
     * @return mixed
     */
    public function festivalvisited($user_id, $festival_id)
    {
        $em = $this->doctrine->getManager();
        $festival_visit = $em->getRepository('IFlairFestivalBundle:festivalVisited')->findBy(array('userId' => $user_id, 'festivalId' => $festival_id));
        if(!$festival_visit)
        {
            $festivalvisited = $em->getRepository('IFlairFestivalBundle:festival')->findOneById($festival_id);
            $user = $em->getRepository('AppBundle:User')->findOneById($user_id);
            $festival = new festivalVisited();
            $festival->setFestivalId($festivalvisited);
            $festival->setUserId($user);
            $em->persist($festival);
            $em->flush();
        }else{
            $festival_visit->setModifiedDate(new \DateTime());
            $em->persist($festival_visit);
            $em->flush();
        }
        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'visited festival successfully.'
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}