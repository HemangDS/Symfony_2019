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
use IFlairFestivalBundle\Entity\FestivalInfoEditConfirmation;

class FestivalinfoeditconfirmationService
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
    public function festivalinfoeditconfirmation($user_id,$festival_id,$festival_info)
    {
        $em = $this->doctrine->getManager();
        
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $user_id));
        $festival = $em->getRepository('IFlairFestivalBundle:festival')->findOneBy(array('id' => $festival_id));

        $festivalInformation = new FestivalInfoEditConfirmation();        
        $festivalInformation->setFestivalInfo($festival_info);
        $festivalInformation->setStatus(0);
        $festivalInformation->setUserId($user);
        $festivalInformation->setFestivalId($festival);
        $em->persist($festivalInformation);
        $em->flush();

        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'Festival information will be update after admin confirmation.'
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}