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

class PartysearchlistfestivalService
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
    public function setpartyfestivalsearchlist($user_id)
    {
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();       

        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('pf.id', 'pf.title', 'fi.imageName', 'fs.modifiedDate')
            ->from('IFlairFestivalBundle\Entity\festival_search', 'fs')->where("fs.userId = ".$user_id)
            ->leftJoin('IFlairFestivalBundle\Entity\festival', 'pf', Expr\Join::WITH, 'pf.id = fs.festivalId')
            ->leftJoin('IFlairFestivalBundle\Entity\festival_image', 'fi', Expr\Join::WITH, 'fi.festivalId = fs.festivalId')->where("fi.imageType = 'logo'")
            ->orderBy('fs.modifiedDate', 'DESC');
        $festival_result = $queryBuilder->getQuery()->getResult();
        
        $festival_first_three_search = array();
        $check = array();
        $count = 0;
        foreach($festival_result as $res)
        {
            if(!in_array($res['title'], $check))
            {
                $check[] = $res['title'];
                if($count < 3)
                {
                    $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/';
                    $res['imageName'] = $image_path.$res['imageName'];
                    $res['modifiedDate'] = $res['modifiedDate']->format('Y-m-d H:i:s');
                    $festival_first_three_search[] = $res;
                    $count++;
                }
            }
        }

        $queryBuilder1 = $em->createQueryBuilder();
        $queryBuilder1->select('pf.id','pf.id', 'pf.title', 'fi.imageName', 'fs.modifiedDate');
        $queryBuilder1->addSelect('count(pf.id) as partycount')
            ->from('IFlairFestivalBundle\Entity\festival_search', 'fs')->where("fs.userId = ".$user_id)
            ->leftJoin('IFlairFestivalBundle\Entity\festival', 'pf', Expr\Join::WITH, 'pf.id = fs.festivalId')
            ->leftJoin('IFlairFestivalBundle\Entity\festival_image', 'fi', Expr\Join::WITH, 'fi.festivalId = fs.festivalId')->where("fi.imageType = 'logo'")
            ->groupBy('pf.id')
            ->addOrderby('partycount', 'DESC');

        $trending_festival = $queryBuilder1->getQuery()->getResult();
        $trending = array();        
        
        $total_trending_festival = array();
        $count = 0;
        foreach($trending_festival as $festival)
        {
            if($count < 10)
            {
                $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/bundles/iflairfestival/images/';
                $festival['imageName'] = $image_path.$festival['imageName'];
                $festival['modifiedDate'] = $festival['modifiedDate']->format('Y-m-d H:i:s');
                $total_trending_festival[] = $festival;
                $count++;
            }
        }

        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'festival search list successfully.',
            'recentsearch_data' => $festival_first_three_search,
            'trending_data' => $total_trending_festival,
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}