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
use IFlairSoapBundle\Entity\partyInfoEditConfirmation;

class PartyinfoeditconfirmationService
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
    public function partyinfoeditconfirmation($user_id,$party_finder_id,$party_info)
    {
        $em = $this->doctrine->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $user_id));
        $partyfinder = $em->getRepository('IFlairSoapBundle:Partyfinder')->findOneBy(array('id' => $party_finder_id));
        $partyInformation = new PartyInfoEditConfirmation();
        $partyInformation->setPartyInfo($party_info);
        $partyInformation->setStatus(0);
        $partyInformation->setUserId($user);
        $partyInformation->setPartyFinderId($partyfinder);
        $em->persist($partyInformation);
        $em->flush();
        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'Party information will be update after admin confirmation.'
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}