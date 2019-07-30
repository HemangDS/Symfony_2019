<?php
namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use IFlairSoapBundle\Entity\Settings;
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Partyfinderfavorite;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PartyfavouriteService
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
    public function setpartyfavourite($user_id,$party_finder_id)
    {
        $em = $this->doctrine->getManager();
        $partyfinder = $em->getRepository('IFlairSoapBundle:Partyfinder')->findById($party_finder_id);
        $user = $em->getRepository('AppBundle:User')->findById($user_id);
        $party_obj = '';
        foreach($partyfinder as $party)
        {
            $party_obj = $party;
        }
        $user_obj = '';
        foreach($user as $usr)
        {
            $user_obj = $usr;
        }
        $favourite = new Partyfinderfavorite();
        $favourite->setPartyFinderId($party_obj);
        $favourite->setUserId($user_obj);
        $em->persist($favourite);
        $em->flush();
        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'Set Favourite Successfully.'
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
    public function deletepartyfavourite($user_id,$party_finder_id)
    {
        $em = $this->doctrine->getManager();
        $favourite = $em->getRepository('IFlairSoapBundle:Partyfinderfavorite')->findBy(array('userId' => $user_id, 'partyFinderId' => $party_finder_id));
        $favourite_id = '';
        foreach($favourite as $fvurite)
        {
            $favourite_id = $fvurite->getId();
        }
        $favourite = $em->getReference('IFlairSoapBundle:Partyfinderfavorite', $favourite_id);
        $em->remove($favourite);
        $em->flush();
        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'removed favourite successfully.'
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}
