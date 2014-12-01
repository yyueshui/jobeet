<?php
namespace Ibw\JobeetBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Ibw\JobeetBundle\IbwJobeetBundle;

class CategoryRepository extends EntityRepository
{
	public function getWithJobs()
	{
		$query = $this->getEntityManager()->createQuery('
			SELECT c FROM IbwJobeetBundle:Category c LEFT JOIN c.jobs j WHERE j.expires_at > :date	
		')->setParameter('date', date('Y-m-d H:i:s', time()));
		return $query->getResult();
	}
}