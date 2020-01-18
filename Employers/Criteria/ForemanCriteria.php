<?php

namespace AFG\Model\Employers\Criteria;


use AFG\Model\User\User;
use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

class ForemanCriteria extends BaseSpecification
{
    /**
     * @var null|string
     */
    private $foreman;

    /**
     * @var null|string
     */
    private $dqlAlias;

    public function __construct(User $foreman, ?string $dqlAlias = null)
    {
        $this->dqlAlias = $dqlAlias;
        $this->foreman = $foreman;

        parent::__construct($dqlAlias);
    }

    protected function getSpec()
    {
        return Spec::andX(
            Spec::leftJoin('teams', 'team', 'user'),
            Spec::eq('foreman', $this->foreman, 'team')
        );
    }
}