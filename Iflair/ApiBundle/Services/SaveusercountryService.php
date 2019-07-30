<?php
namespace Iflair\ApiBundle\Services;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Entity\User;
use IFlairSoapBundle\Entity\Settings;

class SaveusercountryService
{
    protected $doctrine;
    public function __construct(RequestStack $request,Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    /**
     * user save country
     * @param string $name 
     * @return mixed
     */
    public function saveusercountry($user_id, $country_id)
    {
        $em = $this->doctrine->getManager();
        $user = $this->doctrine->getRepository('AppBundle:User')->findOneById($user_id);
        $country_id = $this->doctrine->getRepository('IFlairSoapBundle:Partyfindercountry')->findOneBy(array('id' => $country_id));
        $user->setCountryId($country_id);
        $em->persist($user);
        $em->flush();
        $myresponse = array(
            'success' => true,
            'status' => Response::HTTP_OK,
            'content' => array(
             'secondary_content' => 'user country updated.'
            )
        );
        $finalResponse = json_encode($myresponse);
        return $finalResponse;
        
    }
}
