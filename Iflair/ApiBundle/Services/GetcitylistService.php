<?php
namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Settings;

class GetcitylistService
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
    public function getcitylist($country_id)
    {
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('pfc.id','pfc.cityName')
        ->where("pfc.countryId = '".$country_id."'")
        ->from('IFlairSoapBundle\Entity\Partyfindercity', 'pfc');
        $queryBuilder->orderBy('pfc.cityName', 'ASC');
        $result = $queryBuilder->getQuery()->getResult();
        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'List of city.',
            'country_data' => $result
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}
