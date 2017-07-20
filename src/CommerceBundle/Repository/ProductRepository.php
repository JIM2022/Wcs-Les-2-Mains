<?php

namespace CommerceBundle\Repository;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends \Doctrine\ORM\EntityRepository
{
	/**
	 * Get all product for one categorie
	 * @param $idCateg
	 * @return array
	 */
	public function getProductByCateg($idCateg)
	{
		$qb = $this->createQueryBuilder('p');
		$qb->join('p.categories', 'c')
			->where('c.id = :id')
			->select('p.id', 'p.name')
			->setParameter('id', $idCateg);
		return $qb->getQuery()->getResult();
	}
}
