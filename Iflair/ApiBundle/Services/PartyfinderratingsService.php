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

class PartyfinderratingsService
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
    public function getPartyfinderratings($type, $fest_or_party_id, $user_id, $json_data)
    {
    

    	$request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();

        $rating_data = json_decode($json_data, true);
        
        /*$myfile = fopen("/home1/devkj71web/public_html/captainnightlife/test.txt", "w") or die("Unable to open file!");
        fwrite($myfile, var_export($rating_data, true));*/

        
        if($type == '1') // festival
        {
            $avg = 0; $avg_total = 0; $devision = 0;
            foreach($rating_data as $rating)
            {
                if($rating['type'] == '2')
                {
                    $avg_total+=$rating['rating'];
                    $devision++;
                }
            }
            $avg = $avg_total/$devision;
            

            foreach($rating_data as $rating)
            {
                if($rating['type'] == '2')
                {
                    $FestivalTypeRatings = new festival_type_ratings();
                    $FestivalTypeRatings->setUserRatings($rating['rating']); // rating count 
                    $FestivalTypeRatings->setModifiedDate(new \DateTime());
                    $user = $em->getRepository('AppBundle:User')->findOneById($user_id);
                    $FestivalTypeRatings->setUserId($user);
                    $rating_id = $em->getRepository('IFlairFestivalBundle:festival_rating_type')->findOneById($rating['id']);
                    $FestivalTypeRatings->setFestivalTypeId($rating_id); // rating id (festival type id)
                    $festival = $em->getRepository('IFlairFestivalBundle:festival')->findOneById($fest_or_party_id);
                    $FestivalTypeRatings->setFestivalId($festival);
                    $FestivalTypeRatings->setAvgRatings($avg);
                    $em->persist($FestivalTypeRatings);
                    $em->flush();
                    // echo $last_inserted_id = $FestivalTypeRatings->getId();
                }
            }

        }else{ // partyfinder

            $avg = 0; $avg_total = 0; $devision = 0;
            foreach($rating_data as $rating)
            {
                if($rating['type'] == '2')
                {
                    $avg_total+=$rating['rating'];
                    $devision++;
                }
            }
            $avg = $avg_total/$devision;


            foreach($rating_data as $rating)
            {
                if($rating['type'] == '2') // allowed rating type
                {
                    $PartyTypeRating = new PartyTypeRatings();                 
                    $PartyTypeRating->setUserRatings($rating['rating']); // rating count 
                    $PartyTypeRating->setModifiedDate(new \DateTime());
                    $user = $em->getRepository('AppBundle:User')->findOneById($user_id);
                    $PartyTypeRating->setUserId($user);
                    $rating_id = $em->getRepository('IFlairFestivalBundle:festival_rating_type')->findOneById($rating['id']);
                    $PartyTypeRating->setFestivalTypeId($rating_id); // rating id (festival type id)
                    $partyfinder = $em->getRepository('IFlairSoapBundle:Partyfinder')->findOneById($fest_or_party_id);
                    $PartyTypeRating->setPartyFinderId($partyfinder);
                    $PartyTypeRating->setAvgRatings($avg);
                    $em->persist($PartyTypeRating);
                    $em->flush();
                    // echo $last_inserted_id = $PartyTypeRatings->getId();
                }
            }
        }

        $myresponse = array(
            'message' => 'Rating data successfully inserted.',
            'success' => true,
            'status' => Response::HTTP_OK,
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}