<?php

namespace CommerceBundle\Repository;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return array
     */
    public function getCategory()
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('c')
            ->join('c.products', 'p')
            ->where('p.id IS NOT NULL');

        return $qb->getQuery()->getresult();
    }
}
