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

class GlobalclubbingsearchlistService
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
    public function globalclubbingsearchlist($user_id)
    {
        $em = $this->doctrine->getManager();
        $request = $this->request->getCurrentRequest();

        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('pfk.id', 'pfk.searchKeyword','pfk.timestamp');
        $queryBuilder
        ->from('IFlairSoapBundle\Entity\Partytypekeyword', 'pfk')
        ->where("pfk.userId = $user_id")
        ->orderBy('pfk.timestamp', 'DESC')
        ->setMaxResults('3');
        $result = $queryBuilder->getQuery()->getResult();
        
        $latest_user_search = array();
        foreach($result as $data)
        {
             $latest_user_search[] = $data['searchKeyword'];
        }
        /* Trending Data */
        $queryBuilder1 = $em->createQueryBuilder();
        $queryBuilder1->select('pf.id', 'pf.clubTitle', 'pfi.imageName');
        $queryBuilder1->addSelect('count(pf.id) as partycount')
            ->from('IFlairSoapBundle\Entity\Partytypesearch', 'pts')
            ->leftJoin('IFlairSoapBundle\Entity\Partyfinder', 'pf', Expr\Join::WITH, 'pf.id = pts.partyFinderId')
            ->leftJoin('IFlairSoapBundle\Entity\Partyfinderimage', 'pfi', Expr\Join::WITH, 'pfi.partyFinderId = pf.id')->where("pfi.imageType = 'logo'")->groupBy('pf.id')
            ->addOrderby('partycount', 'DESC');
        $trending_party = $queryBuilder1->getQuery()->getResult();
        $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairsoap/images/';
        $trending_data = array();
        $count = 0;
        $global_clubbing_id = array();
        foreach($trending_party as $trend)
        {
            $global_clubbing_id[] = $trend['id'];
            $trending_data[$count] = $trend;
            $trending_data[$count]['imageName'] = $image_path.$trend['imageName'];
            $count++;
        }

        $city_sequence = array();
        $count = 0;
        foreach($global_clubbing_id as $id)
        {
            $partyfinder = $em->getRepository('IFlairSoapBundle:Partyfinder')->findById($id);
            foreach($partyfinder as  $club)
            {
                $partyfinder_city = $em->getRepository('IFlairSoapBundle:Partyfinderlocation')->findById($club->getClubLocationId()->getId());
                foreach($partyfinder_city as $city)
                {
                    $city_sequence[$count]['city_name'] = $city->getCityId()->getCityName();
                    $partyfinder_country_names = $em->getRepository('IFlairSoapBundle:Partyfindercountry')->findById($city->getCityId()->getCountryId()->getId());
                    $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/country/';
                    foreach($partyfinder_country_names as $country)
                    {
                        $city_sequence[$count]['country_image'] = $image_path.strtolower(str_replace(' ', '_', $country->getCountryName())).'.png';
                    }
                }
            }
            $count++;
        }
        
        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'latest_user_search' => $latest_user_search,
            'trending_data' => $trending_data,
            'city_data' => $city_sequence
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}