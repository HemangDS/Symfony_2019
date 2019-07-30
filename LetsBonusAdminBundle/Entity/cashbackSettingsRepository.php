<?php
namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Shoppiday\CommonBundle\Traits\Repository;

/**
 * cashbackSettingsRepository.
 */
class cashbackSettingsRepository extends EntityRepository
{
    use Repository;

    /**
     * Method that return the number of rows actives on an entity
     * @return int|mixed
     */
    public function countActive()
    {
        try {
            $entity = $this->getEntityName();

            return $this->_em->createQuery('SELECT COUNT(a.id) FROM '.$entity.' a 
            WHERE a.startDate<=:now AND a.endDate>=:now AND a.status = 1')
                ->setParameter('now', date('Y-m-d'))
                ->setCacheable(true)
                ->setQueryCacheLifetime(600)
                ->getSingleScalarResult();
        } catch (\Exception $e) {
            return -1;
        }
    }
}
