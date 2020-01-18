<?php

namespace AFG\Model\Employers\Criteria;


use AFG\Model\User\User;
use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

class NonInternalCriteria extends BaseSpecification
{
    protected function getSpec()
    {
        return Spec::andX(
            Spec::eq('isInternal', false)
        );
    }
}