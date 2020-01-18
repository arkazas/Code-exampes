<?php

namespace AFG\Model\Employers;

use AFG\Model\AbstractRepository;
use AFG\Model\RepositoryTrait;
use Doctrine\Common\Persistence\ObjectRepository;

class EmployerTypeRepository extends AbstractRepository
{
    use RepositoryTrait;

    private $store = [];

    protected function getRepository(): ObjectRepository
    {
        return $this->em->getRepository(EmployerType::class);
    }

    public function addIfNotExists(string $title)
    {
        if (empty($this->store)) {
            $this->collectStore();
        }

        if (!isset($this->store[$title])) {
            $employerType = EmployerTypeBuilder::create(['title' => $title])->build();
            $this->store[$title] = $employerType;
            $this->em->persist($employerType);
        }
    }

    private function collectStore()
    {
        /** @var EmployerType $employerType */
        foreach ($this->getRepository()->findAll() as $employerType) {
            $this->store[$employerType->getTitle()] = $employerType;
        }
    }
}
