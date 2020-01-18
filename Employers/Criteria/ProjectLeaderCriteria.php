<?php

namespace AFG\Model\Employers\Criteria;


use AFG\Model\User\User;
use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

class ProjectLeaderCriteria extends BaseSpecification
{
    /**
     * @var null|string
     */
    private $leader;

    /**
     * @var null|string
     */
    private $dqlAlias;

    public function __construct(User $leader, ?string $dqlAlias = null)
    {
        $this->leader = $leader;
        $this->dqlAlias = $dqlAlias;

        parent::__construct($dqlAlias);
    }

    protected function getSpec()
    {
        return Spec::andX(
            Spec::leftJoin('currentProjects', 'userProject', 'user'),
            Spec::leftJoin('project', 'project', 'userProject'),
            Spec::eq('projectLeader', $this->leader, 'project')
        );
    }
}