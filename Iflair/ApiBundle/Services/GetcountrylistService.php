<?php
namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Settings;

class GetcountrylistService
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
    public function getcountrylist($blank_param)
    {
        $request = $this->request->getCurrentRequest();
        $em = $this->doctrine->getManager();
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('pfc.id','pfc.countryName')
        ->from('IFlairSoapBundle\Entity\Partyfindercountry', 'pfc');
        $queryBuilder->orderBy('pfc.countryName', 'ASC');
        $result = $queryBuilder->getQuery()->getResult();
        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'List of country.',
            'country_data' => $result
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
    }
}
