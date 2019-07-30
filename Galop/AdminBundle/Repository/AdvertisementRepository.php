<?php

namespace Galop\AdminBundle\Repository;

/**
 * AdvertisementRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdvertisementRepository extends \Doctrine\ORM\EntityRepository
{
		public function findByWithBeetween($value)
		{
			
		    $query = $this->createQueryBuilder('a');
		    $query->where('a.zone = :zoneID');
		    $query->andWhere('a.startdate <= :currentDate');
		    $query->andWhere('a.enddate >= :currentDate');
		    $query->andWhere('a.status = :status');
		    $query->setParameter('zoneID', $value);
		    $query->setParameter('status', '1');
		    $query->setParameter('currentDate',date('Y-m-d'));

		    return $query->getQuery()
		        ->getResult();
		}
}