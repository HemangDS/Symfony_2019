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
use IFlairSoapBundle\Entity\partyVisited;

class PartyvisitedService
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
    public function partyvisited($user_id, $party_id)
    {
        $em = $this->doctrine->getManager();
        $party_visit = $em->getRepository('IFlairSoapBundle:partyVisited')->findBy(array('userId' => $user_id, 'partyFinderId' => $party_id));
        if(!$party_visit)
        {
            $partyvisited = $em->getRepository('IFlairSoapBundle:Partyfinder')->findOneById($party_id);
            $user = $em->getRepository('AppBundle:User')->findOneById($user_id);
            $party = new partyVisited();
            $party->setPartyFinderId($partyvisited);
            $party->setUserId($user);
            $em->persist($party);
            $em->flush();
        }
        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'visited party successfully.'
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}