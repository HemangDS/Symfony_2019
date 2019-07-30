<?php

namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\Query\Expr;
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Partyfinderviews;
use IFlairSoapBundle\Entity\Partyfinder;
use IFlairFestivalBundle\Entity\festival_rating_type;
use IFlairFestivalBundle\Entity\festival_type_ratings;
use IFlairSoapBundle\Entity\Partyfinderfavorite;
use IFlairSoapBundle\Entity\PartyTypeRatings;
use IFlairSoapBundle\Entity\Partyfindertype;
use IFlairSoapBundle\Entity\Partyfinderratings;
use IFlairSoapBundle\Entity\Partyfinderlocation;

class PartyfinderdeleteratingsService
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
    public function getPartyfinderdeleteratings($type, $fest_or_party_id, $user_id)
    {
    	$request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();

        if($type == '1') // festival
        {
            $festival_data = $em->getRepository('IFlairFestivalBundle:festival_type_ratings')->findBy(array('festivalId' => $fest_or_party_id, 'userId' => $user_id));
            foreach($festival_data as $festival_rating)
            {
                $Rating_record = $em->getReference('IFlairFestivalBundle:festival_type_ratings', $festival_rating->getId());
                $em->remove($Rating_record);
                $em->flush();
            }
        }else{ // party
            $party_data = $em->getRepository('IFlairSoapBundle:PartyTypeRatings')->findBy(array('partyFinderId' => $fest_or_party_id, 'userId' => $user_id));
            foreach($party_data as $party_rating)
            {   
                $Rating_record = $em->getReference('IFlairSoapBundle:PartyTypeRatings', $party_rating->getId());
                $em->remove($Rating_record);
                $em->flush();
            }
        }

        $myresponse = array(
            'message' => 'Rating data successfully deleted.',
            'success' => true,
            'status' => Response::HTTP_OK,
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}