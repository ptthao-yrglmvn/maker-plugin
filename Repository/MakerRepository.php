<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Maker4\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Plugin\Maker4\Entity\Maker;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Maker.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MakerRepository extends ServiceEntityRepository
{
    /**
     * MakerRepository constructor.
     *
     * @param RegistryInterface $registry
     * @param string $entityClass
     */
    public function __construct(RegistryInterface $registry, $entityClass = Maker::class)
    {
        parent::__construct($registry, $entityClass);
    }

    /**
     * Maker create/update
     *
     * @param $Maker
     *
     * @return bool
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save($Maker)
    {
        $em = $this->getEntityManager();

        if (!$Maker->getId()) {
            $sortNo = $this->createQueryBuilder('m')
                ->select('MAX(m.sort_no)')
                ->getQuery()
                ->getSingleScalarResult();
            if (!$sortNo) {
                $sortNo = 0;
            }
            $Maker->setSortNo($sortNo + 1);

            $em->createQueryBuilder()
                ->update(Maker::class, 'm')
                ->set('m.sort_no', 'm.sort_no + 1')
                ->where('m.sort_no > :sort_no')
                ->setParameter('sort_no', $sortNo)
                ->getQuery()
                ->execute();
        }

        $em->persist($Maker);
        $em->flush($Maker);

        return true;
    }

    /**
     * Delete maker.
     *
     * @param $Maker
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete($Maker)
    {
        $em = $this->getEntityManager();
        $em->remove($Maker);
        $em->flush($Maker);
    }

    /**
     * Move sortNo.
     *
     * @param array $sortNos
     *
     * @return array
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function moveSortNo(array $sortNos)
    {
        $results = [];
        foreach ($sortNos as $id => $sortNo) {
            /* @var $Maker Maker */
            $Maker = $this->find($id);
            if ($Maker->getSortNo() == $sortNo) {
                continue;
            }
            $results[$id] = $sortNos;
            $Maker->setSortNo($sortNo);
            $this->getEntityManager()->persist($Maker);
            $this->getEntityManager()->flush($Maker);
        }

        return $results;
    }
}
