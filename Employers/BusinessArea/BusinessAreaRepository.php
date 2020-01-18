<?php

namespace AFG\Model\Employers\BusinessArea;

use AFG\Model\AbstractRepository;
use AFG\Model\RepositoryTrait;
use Doctrine\Common\Persistence\ObjectRepository;

class BusinessAreaRepository extends AbstractRepository
{
    use RepositoryTrait;

    protected function getRepository(): ObjectRepository
    {
        return $this->em->getRepository(BusinessArea::class);
    }

    /**
     * @param string $code
     *
     * @return null|BusinessArea|object
     */
    public function findByCode(string $code)
    {
        return $this->repo->findOneBy(['code' => $code]);
    }

    /**
     * @param BusinessArea $area
     */
    public function add(BusinessArea $area)
    {
        $this->em->persist($area);
    }
}
