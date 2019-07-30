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
use Doctrine\ORM\Query\Expr;
use DoctrineExtensions\Tests\Query\Mysql;
use IFlairSoapBundle\Entity\Partyfindertiming;
use IFlairSoapBundle\Entity\Partyfinder;
use IFlairSoapBundle\Entity\Partyfinderlocation;
use IFlairSoapBundle\Entity\SearchedKeyword;
use IFlairSoapBundle\Entity\SearchedUser;
use IFlairSoapBundle\Entity\Partytypekeyword;
use IFlairSoapBundle\Entity\Partytypesearch;

class GlobalclubbingsearchService
{
    protected $doctrine;
    public function __construct(RequestStack $request,Registry $doctrine)
    {
        $this->request = $request;
        $this->doctrine = $doctrine;
    }
    /**
     * Set user wise Applications settings.
     * @return mixed
     */
    public function globalclubbingsearch($searched_keyword, $user_id, $range_type)
    {
        /* If open set status of open and close */
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();
        $searched_loaded = $em->getRepository('IFlairSoapBundle:Partytypekeyword')->findOneBy(array('searchKeyword' => $searched_keyword, 'userId' => $user_id));
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $user_id));
        $last_inserted_searched_keyword_id = '';
        if(!$searched_loaded)
        {
            $searched_loaded = new Partytypekeyword();
            $searched_loaded->setSearchKeyword($searched_keyword);
            $searched_loaded->setUserId($user);
            $em->persist($searched_loaded);
            $em->flush();
            $last_inserted_searched_keyword_id = $searched_loaded->getId();
        }else{
            $searched_loaded->setSearchKeyword($searched_keyword);
            $searched_loaded->setUserId($user);
            $em->persist($searched_loaded);
            $em->flush();
        }
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('pf.id', 'pf.clubTitle');
        $queryBuilder
        ->from('IFlairSoapBundle\Entity\Partyfinder', 'pf')
        ->leftJoin('IFlairSoapBundle\Entity\Partyfinderratings', 'pfr', Expr\Join::WITH, 'pfr.partyFinderId = pf.id');
        $queryBuilder->join("pf.clubTypeId","t")->where('pf.clubTitle LIKE :word')->setParameter('word', '%'.$searched_keyword.'%');
        $queryBuilder->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'pfli',  Expr\Join::WITH, 'pfli.id = pf.clubLocationId');
        $queryBuilder->join('IFlairSoapBundle\Entity\Partyfindercity', 'pfc', Expr\Join::WITH, 'pfc.id = pfli.cityId');
        $queryBuilder->addSelect('pfc.cityName  as partyfindercty');

        $queryBuilder->join('IFlairSoapBundle\Entity\Partyfindercountry', 'pfctr', Expr\Join::WITH, 'pfctr.id = pfc.countryId');
        $queryBuilder->addSelect('pfctr.countryName as partyfindercntry');

        $queryBuilder->addSelect('avg(pfr.userRatings) as user_ratings')
        ->groupBy('pf.id');
        $queryBuilder->leftJoin('IFlairSoapBundle\Entity\Partyfinderlocation', 'e', Expr\Join::WITH, 'e.id = pf.clubLocationId');
        $result = $queryBuilder->getQuery()->getResult();
        $count = 0;
        foreach($result as $club)
        {
            $image_name = $em->getRepository('IFlairSoapBundle:Partyfinder')->getImage($em, $club['id'], $request);
            $result[$count]['club_logo'] = $image_name;
            $count++;
        }

        foreach($result as $data)
        {
            $loaded_party_type = $em->getRepository('IFlairSoapBundle:Partyfinder')->findOneBy(array('id' => $data['id']));
            $searched_party = new Partytypesearch();
            $searched_party->setPartyFinderId($loaded_party_type);            
            $searched_party->setUsarId($user);
            $em->persist($searched_party);
            $em->flush();
        }

        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'nightclub and bar data successfully inserted.',
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}