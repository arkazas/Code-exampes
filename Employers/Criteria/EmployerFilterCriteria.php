<?php

namespace AFG\Model\Employers\Criteria;

use AFG\Common\Criteria\FilterCriteria;
use AFG\Model\Exception\ConflictException;
use Doctrine\ORM\QueryBuilder;

class EmployerFilterCriteria extends FilterCriteria
{
    /**
     * @param QueryBuilder $qb
     * @param string       $dqlAlias
     *
     * @return string|void
     */
    public function getFilter(QueryBuilder $qb, $dqlAlias)
    {
        $filters = $this->filter->getFilters();

        if ($this->dqlAlias !== null) {
            $dqlAlias = $this->dqlAlias;
        }

        foreach ($filters as $key => $filter) {
            $alias = sprintf('%s.%s', $dqlAlias, $key);

            switch ($this->relations->getRelationType($key)) {
                case self::TYPE_STRING:
                    if ($key === 'is_internal') {
                        if (!empty($filters['keep_main_internal'])) {
                            $qb->setParameter($key, !empty($filter));
                            $qb->setParameter('mainInternalTrue', true);
                            $internalAlias = sprintf('%s.%s', $dqlAlias, 'isInternal');
                            $mainInternalAlias = sprintf('%s.%s', $dqlAlias, 'isMainInternal');
                            $expression = $qb->expr()->andX(
                                $qb->expr()->orX(
                                    $qb->expr()->andX("$internalAlias = :$key"),
                                    $qb->expr()->andX("$mainInternalAlias = :mainInternalTrue")
                                )
                            );
                            $this->expressions[] = $expression;
                            break;
                        }
                        $alias = sprintf('%s.%s', $dqlAlias, 'isInternal');
                        $this->criteriaString($qb, $alias, $key, !empty($filter));
                        break;
                    }
                    if ($key === 'keep_main_internal') {
                        break;
                    }
                    $this->criteriaString($qb, $alias, $key, $filter);
                    break;
                case self::TYPE_ONE_TO_MANY:
                    $this->criteriaOneToMany($qb, $alias, $key, $filter);
                    break;
                case self::TYPE_MANY_TO_MANY:
                    $this->criteriaManyToMany($qb, $alias, $key, $filter);
                    break;
                case self::TYPE_CUSTOM:
                    break;
                default:
                    throw new ConflictException('error.filter');
            }
        }

        return (string)call_user_func_array([$qb->expr(), 'andX'], $this->expressions);
    }
}
