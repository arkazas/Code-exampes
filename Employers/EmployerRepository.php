<?php

namespace AFG\Model\Employers;

use AFG\Common\Criteria\FilterCriteria;
use AFG\Model\AbstractRepository;
use AFG\Model\Employers\Criteria\EmployerFilterCriteria;
use AFG\Model\Employers\Criteria\ForemanCriteria;
use AFG\Model\Employers\Criteria\ProjectLeaderCriteria;
use AFG\Model\Filter;
use AFG\Model\RepositoryTrait;
use Doctrine\Common\Persistence\ObjectRepository;
use Happyr\DoctrineSpecification\Spec;

class EmployerRepository extends AbstractRepository
{
    use RepositoryTrait;

    protected static function getRelations()
    {
        return [
            FilterCriteria::TYPE_STRING => ['is_internal', 'keep_main_internal'],
            FilterCriteria::TYPE_CUSTOM => ['manager'],
        ];
    }

    protected function specFilter(Filter $filter = null)
    {
        return new EmployerFilterCriteria($filter, null, self::getRelations());
    }

    protected function getRepository(): ObjectRepository
    {
        return $this->em->getRepository(Employer::class);
    }

    /**
     * @param string $number
     *
     * @return Employer|null|object
     */
    public function findByNumber(string $number)
    {
        return $this->repo->findOneBy(['number' => $number]);
    }

    public function getMainInternal()
    {
        return $this->repo->findOneBy(['isMainInternal' => true]);
    }

    /**
     * @param Employer $employer
     */
    public function add(Employer $employer)
    {
        $this->em->persist($employer);
    }

    public function isForeignEmployerExists(string $country, string $number, Employer $excludedEmployer = null): bool
    {
        $query = $this
            ->em
            ->createQueryBuilder()
            ->from(Employer::class, 'employer')
            ->select('count(employer)')
            ->where('lower(employer.country) = :country')
            ->andWhere('lower(employer.number) = :number')
            ->setParameter('number', trim(strtolower($number)))
            ->setParameter('country', trim(strtolower($country)));

        if ($excludedEmployer) {
            $query
                ->andWhere('employer != :employer')
                ->setParameter('employer', $excludedEmployer);
        }

        return (bool)$query
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Employer
     */
    public function find($id)
    {
        return $id
            ? $this->repo->find($id)
            : null;
    }

    /**
     * @param Spec $spec
     *
     * @return mixed
     */
    protected function setSpecJoin($spec)
    {
        if (null === $this->filter) {
            return $spec;
        }

        if ($this->filter->hasField('manager')) {
            $spec->andX(
                Spec::leftJoin('employees', 'user')
            );
        }

        return $spec;
    }

    /**
     * @param Spec        $spec
     * @param Filter|null $filter
     */
    protected function getSpecFilter($spec, Filter $filter = null)
    {
        if (null === $filter) {
            return;
        }

        if ($filter->has()) {
            $spec->andX(
                $this->specFilter($filter)
            );
        }

        if ($filter->hasField('manager')) {
            $spec->andX(
                Spec::orX(
                    new ForemanCriteria($filter->getField('manager')),
                    new ProjectLeaderCriteria($filter->getField('manager'))
                )
            );
        }
    }
}
