<?php

namespace AFG\Model\Employers;

use AFG\Common\Criteria\FilterCriteria;
use AFG\Model\AbstractRepository;
use AFG\Model\Employers\Criteria\SearchFilterCriteria;
use AFG\Model\Filter;
use AFG\Model\Pagination;
use AFG\Model\RepositoryTrait;
use Doctrine\Common\Persistence\ObjectRepository;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Term;
use Elastica\Query\Wildcard;
use FOS\ElasticaBundle\Repository;
use Pagerfanta\Pagerfanta;

class EmployerSearchRepository extends AbstractRepository
{
    use RepositoryTrait;

    protected static function getRelations()
    {
        return [
            FilterCriteria::TYPE_STRING => ['q', 'is_internal', 'is_main_internal', 'keep_main_internal']
        ];
    }

    /**
     * @param Filter|null $filter
     * @return SearchFilterCriteria
     */
    protected function specFilter(Filter $filter = null)
    {
        return new SearchFilterCriteria($filter, null, self::getRelations());
    }

    /**
     * @return ObjectRepository
     */
    protected function getRepository(): ObjectRepository
    {
        return $this->em->getRepository(Employer::class);
    }
}
