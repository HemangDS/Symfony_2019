<?php
namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Settings;

class ContributionpartygetratingService
{
    protected $doctrine;
    public function __construct(RequestStack $request,Registry $doctrine)
    {
        $this->request = $request;
        $this->doctrine = $doctrine;
    }
    /**
     * User Registartion method details, user will be register when called.
     * @param string $name 
     * @return mixed
     */
    public function createcontributionpartyrating($blank_param)
    {
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('frt.id','frt.typeName','frt.mainType')
        ->from('IFlairFestivalBundle\Entity\festival_rating_type', 'frt');
        $result = $queryBuilder->getQuery()->getResult();
        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'List of rating type.',
            'country_data' => $result
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}
