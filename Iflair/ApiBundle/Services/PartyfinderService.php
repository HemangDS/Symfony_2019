<?php

namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\Query\Expr;
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Partyfindertiming;
use DoctrineExtensions\Tests\Query\Mysql;


class PartyfinderService
{
	protected $doctrine;
    public function __construct(RequestStack $request,Registry $doctrine)
    {
        $this->request = $request;
        $this->doctrine = $doctrine;
    }
    /**
     * Listing all Night Club and Bar when called.
     * @param string $name 
     * @return mixed
     */
    public function partyfinder($requseted_date, $offset, $limit, $partytype = 'all', $order = 'default', $cur_lat = '', $cur_lag = '',$requestedDistance = 2,$user_id)
    {
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();

        $cuser = $this->doctrine->getRepository('AppBundle:User')->findOneBy(array('id' => $user_id));
        $setting_loaded = $this->doctrine->getRepository('IFlairSoapBundle:Settings')->findOneBy(array('userId' => $cuser));
        $range_type = $setting_loaded->getDistance();
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder
            ->select('pf.id', 'e.latitude', 'e.longitude', 'pf.clubTitle')
            ->from('IFlairSoapBundle\Entity\Partyfinder', 'pf')
            ->leftJoin('IFlairSoapBundle\Entity\Partyfinderratings', 'pfr', Expr\Join::WITH, 'pfr.partyFinderId = pf.id');
            if($partytype != 'all')
            {
                if($partytype  == 'Nightclub')
                {
                    $queryBuilder->join("pf.clubTypeId","t")->where("t.name = 'Nightclub'");
                }else if($partytype  == 'Bar')
                {
                    $queryBuilder->join("pf.clubTypeId","t")->where("t.name = 'Bar'");
                }else{}
            }

            $queryBuilder->addSelect('avg(pfr.userRatings) as user_ratings')
            ->groupBy('pf.id')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'e', Expr\Join::WITH, 'e.id = pf.clubLocationId');            
            if($range_type == 'Km')
            {            
            $queryBuilder->addSelect('( 6371 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_lag . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm')
            ->having('distanceinkm <= :kilometer')
            ->setParameter('kilometer', $requestedDistance);
            }else{
            $queryBuilder->addSelect('( 3959 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_lag . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm')
            ->having('distanceinkm <= :miles')
            ->setParameter('miles', $requestedDistance);
            }
        switch ($order) {
            case 'name':
            default:
                $queryBuilder->orderBy('pf.clubTitle', 'ASC');
                break;
            case 'rating':
                $queryBuilder->orderBy('user_ratings', 'DESC');
                break;
            case 'distance':
                $queryBuilder->orderBy('distanceinkm', 'ASC');
                break;
        }
        $result = $queryBuilder->getQuery()->getResult();


        /*=============== Query for total count no of record ===============*/
        $queryBuilder1 = $em->createQueryBuilder();
        $queryBuilder1
            ->select('pf.id', 'e.latitude', 'e.longitude', 'pf.clubTitle')
            ->from('IFlairSoapBundle\Entity\Partyfinder', 'pf')
            ->leftJoin('IFlairSoapBundle\Entity\Partyfinderratings', 'pfr', Expr\Join::WITH, 'pfr.partyFinderId = pf.id');
            if($partytype != 'all')
            {
                if($partytype  == 'Nightclub')
                {
                    $queryBuilder1->join("pf.clubTypeId","t")->where("t.name = 'Nightclub'");
                }else if($partytype  == 'Bar')
                {
                    $queryBuilder1->join("pf.clubTypeId","t")->where("t.name = 'Bar'");
                }else{}
            }

            $queryBuilder1->addSelect('avg(pfr.userRatings) as user_ratings')
            ->groupBy('pf.id')
            ->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'e', Expr\Join::WITH, 'e.id = pf.clubLocationId');            
            if($range_type == 'Km')
            {            
            $queryBuilder1->addSelect('( 6371 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_lag . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm')
            ->having('distanceinkm <= :kilometer')
            ->setParameter('kilometer', $requestedDistance);
            }else{
            $queryBuilder1->addSelect('( 3959 * acos(cos(radians(' . $cur_lat . ')) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $cur_lag . ') ) + sin( radians(' . $cur_lat . ') ) * sin( radians( e.latitude ) ) ) ) as distanceinkm')
            ->having('distanceinkm <= :miles')
            ->setParameter('miles', $requestedDistance);
            }
        switch ($order) {
            case 'name':
            default:
                $queryBuilder1->orderBy('pf.clubTitle', 'ASC');
                break;
            case 'rating':
                $queryBuilder1->orderBy('user_ratings', 'DESC');
                break;
            case 'distance':
                $queryBuilder1->orderBy('distanceinkm', 'ASC');
                break;
        }
        $result1 = $queryBuilder1->getQuery()->getResult();
        $total_party = count($result1);
        /*=============== Query for total count no of record ===============*/

        /* Club Logo */
        $count = 0;
        foreach($result as $club)
        {
            $result[$count]['distanceinkm'] = number_format((float)$club['distanceinkm'], 2, '.', '');
            $count++;
        }

        /* Club Logo */
        $count = 0;
        foreach($result as $club)
        {
            $image_name = $em->getRepository('IFlairSoapBundle:Partyfinder')->getImage($em, $club['id'], $request);
            $result[$count]['club_logo'] = $image_name;
            $count++;
        }
        /* Start time, End time, Special Occassion */
        $count = 0;
        foreach($result as $club)
        {
            $start_time = $em->getRepository('IFlairSoapBundle:Partyfinder')->getStarttime($em, $requseted_date, $club['id'], $request);
            $result[$count]['start_time'] = $start_time['start_time'];
            $result[$count]['end_time'] = $start_time['end_time'];
            if($start_time['occassion'])
                $result[$count]['special_occassion'] = $start_time['occassion'];
            $count++;
        }

        $count1 = 0;        
        foreach($result as $res)
        {
            $music_name = array();
            $music_name = $em->getRepository('IFlairSoapBundle:Partyfinder')->getListMusicGenre($em, $res['id']);
            $result[$count1]['music_genre'] = $music_name;
            $count1++;
        }

        // Total approved uploaded images.
        $partyuploaded_images_entity = $em->getRepository('IFlairFestivalBundle:FestivalInprogressMultipleImageUpload')->findBy(array('userId' => $user_id,'isApproved' => '1'));
        $uploaded_ids = array();
        foreach($partyuploaded_images_entity as $uploaded_image)
        {
            $uploaded_ids[] = $uploaded_image->getId();
        }
        $festivaluploaded_images_entity = $em->getRepository('IFlairSoapBundle:PartyfinderInprogressMultipleImageUpload')->findBy(array('userId' => $user_id,'isApproved' => '1'));        
        foreach($festivaluploaded_images_entity as $uploaded_image)
        {
            $uploaded_ids[] = $uploaded_image->getId();
        }

        // Checkins count
        $festivals = $em->getRepository('IFlairFestivalBundle:festival_view')->findBy(array('userId' => $user_id));
        $festival_ids = array();
        foreach($festivals as $festival)
        {
            if($festival->getFestivalId())
                $festival_ids[] = $festival->getFestivalId()->getId();
        }
        $partyfinders = $em->getRepository('IFlairSoapBundle:Partyfinderviews')->findBy(array('viewedUserId' => $user_id));
        $viewed_party_locations = array();
        $bar = array(); $nightclub = array();
        foreach($partyfinders as $party)
        {
            $type = $party->getPartyFinderId()->getclubtypeid()->getName();
            if($type == 'Bar'){
                if($party->getPartyFinderId())
                    $bar[] = $party->getPartyFinderId()->getId();
            }else{
                if($party->getPartyFinderId())
                $nightclub[] = $party->getPartyFinderId()->getId();
            }
        }
        $total_checkin_count = 0; 
        $total_checkin_count = count(array_unique($festival_ids));
        $total_checkin_count += count(array_unique($bar));
        $total_checkin_count += count(array_unique($nightclub));

        // Contribution Data
        $total_contribution = 0;
        $festival_contribution = $em->getRepository('IFlairFestivalBundle:ContributionAdddFestival')->findBy(array('userId' => $user_id));
        $festival_id = array();
        foreach($festival_contribution as $contribution)
        {
            $festival_id[] = $contribution->getId();
        }
        $party_contribution = $em->getRepository('IFlairSoapBundle:ContributionAddParty')->findBy(array('userId' => $user_id));
        $party_ids = array();
        foreach($party_contribution as $party)
        {
            $party_ids[] = $party->getId();
        }
        $total_contribution = count($festival_id) + count($party_ids);

        if($total_party == 0)
        {
            $myresponse = array(
                'message' => 'sorry no bar/club found',
                'total_club_count' => $total_party,
                'total_no_of_clubs' => count($result),
                'range_type' => $range_type,
                'uploaded_images_count' => count($uploaded_ids),
                'checkin_count' => $total_checkin_count,
                'contribution_count' => $total_contribution,
                'success' => true,                
                'status' => Response::HTTP_OK,
                'content' => array(
                 'listing_data' => $result
                )
            );
        }else{
            $myresponse = array(
                'message' => 'listing successfully..',
                'total_club_count' => $total_party,
                'total_no_of_clubs' => count($result),
                'range_type' => $range_type,
                'uploaded_images_count' => count($uploaded_ids),
                'checkin_count' => $total_checkin_count,
                'contribution_count' => $total_contribution,
                'success' => true,
                'status' => Response::HTTP_OK,
                'content' => array(
                 'listing_data' => $result
                )
            );
        }        
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}