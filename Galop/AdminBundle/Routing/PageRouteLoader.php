<?php
namespace Galop\AdminBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Doctrine\ORM\EntityManager;

class PageRouteLoader extends Loader
{
    private $loaded = false;
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add this loader twice');
        }

        $routes = new RouteCollection();

        $pagesEntity = $this->em->createQueryBuilder('p')
            ->select('pages.urlKey')
            ->from('GalopAdminBundle:Pages','pages')
            ->getQuery()
            ->getResult();
        
        foreach ($pagesEntity as $pages) {
            if(isset($pages['urlKey']) && !empty($pages['urlKey'])) {
    	        $pattern = '/'.$pages['urlKey'];
                $pageIdentifier = str_replace("-", "_", $pages['urlKey']);
    	        $defaults = array(
    	            '_controller' => 'GalopFrontBundle:Pages:view',
    	        );

    	        $route = new Route($pattern, $defaults);
    	        $routes->add(sprintf('%s', $pageIdentifier), $route);
            }
        }
        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return 'pageroutes' === $type;
    }

    
}
