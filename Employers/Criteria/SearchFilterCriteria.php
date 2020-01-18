<?php

namespace AFG\Model\Employers\Criteria;

use AFG\Common\Criteria\FilterCriteria;
use AFG\Model\Exception\ConflictException;
use Doctrine\ORM\QueryBuilder;

class SearchFilterCriteria extends FilterCriteria
{
    /**
     * @param QueryBuilder $qb
     * @param string $dqlAlias
     * @return string|null
     */
    public function getFilter(QueryBuilder $qb, $dqlAlias)
    {
        $filters = $this->filter->getFilters();

        foreach ($filters as $key => $filter) {
            $alias = sprintf('%s.%s', $dqlAlias, $key);

            switch ($this->relations->getRelationType($key)) {
                case self::TYPE_STRING:
                    if ($key === 'q') {
                        if (empty($filter)) {
                            break;
                        }
                        $this->expressions[] = $qb->expr()->like(sprintf(
                            'concat(%s.title, \' \', %1$s.number)', $dqlAlias
                        ), "'%$filter%'");
                    } elseif ($key === 'is_internal') {
                        $this->expressions[] = $qb->expr()->eq(sprintf('%s.isInternal', $dqlAlias), ':isInternal');
                        $qb->setParameter('isInternal', $filter);
                    } elseif ($key === 'is_main_internal') {
                        $this->expressions[] = $qb->expr()->eq(sprintf('%s.isMainInternal', $dqlAlias), ':isMainInternal');
                        $qb->setParameter('isMainInternal', $filter);
                    } elseif ($key === 'keep_main_internal') {
                        break;
                    } else {
                        $this->criteriaString($qb, $alias, $key, $filter);
                    }
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

        return $this->expressions ? (string) call_user_func_array([$qb->expr(), 'andX'], $this->expressions) : null;
    }
}
