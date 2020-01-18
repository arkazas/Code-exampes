<?php

namespace AFG\Model\Employers\Criteria;

use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\BaseSpecification;

class SearchByQueryCriteria extends BaseSpecification
{
    /**
     * @var string
     */
    private $searchQuery;

    /**
     * SearchByTitleCriteria constructor.
     *
     * @param int  $searchQuery
     * @param null $dqlAlias
     */
    public function __construct($searchQuery = null, $dqlAlias = null)
    {
        $this->searchQuery = strtolower($searchQuery);
        parent::__construct($dqlAlias);
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $dqlAlias
     *
     * @return string|null
     */
    public function getFilter(QueryBuilder $qb, $dqlAlias)
    {
        $qb->expr()->orX(
            $qb->expr()->like(sprintf('%s.title', $dqlAlias), ':q'),
            $qb->expr()->like(sprintf('cast(%s.number, string)', $dqlAlias), ':q')
        );

        $qb->setParameter('q', '%'.$this->searchQuery.'%');
    }
}
