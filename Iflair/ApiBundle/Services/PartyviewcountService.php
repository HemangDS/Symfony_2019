<?php

namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
// ENTITY
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Partyfinderviews;
use IFlairSoapBundle\Entity\Partyfinder;

class PartyviewcountService
{
	protected $doctrine;
    public function __construct(RequestStack $request,Registry $doctrine)
    {
        $this->request = $request;
        $this->doctrine = $doctrine;
    }
    /**
     * View NightClub or Bar when called
     * @param string $name 
     * @return mixed
     */
    public function partyviewcount($party_id, $user_id)
    {
    	$request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $user_id));        
        $party = $em->getRepository('IFlairSoapBundle:Partyfinder')->findOneBy(array('id' => $party_id));
        $partyfinderviews = new Partyfinderviews();
        $partyfinderviews->setPartyFinderId($party);
        $partyfinderviews->setViewedUserId($user);
        $partyfinderviews->setCreatedDate(new \DateTime());
        $em->persist($partyfinderviews);
        $em->flush();
        $myresponse = array(
            'message' => 'view entry inserted successfully..',
            'success' => true,
            'status' => Response::HTTP_OK,
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}