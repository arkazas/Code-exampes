<?php

namespace AFG\Model\Employers;

use AFG\Common\Model\UuidBuilderTrait;
use AFG\Model\Builder;

class EmployerTypeBuilder extends Builder
{
    use UuidBuilderTrait;

    protected $title;

    /**
     * @param string $title
     * @return EmployerTypeBuilder
     */
    public function withTitle(string $title): self
    {
        return $this->setAttribute('title', $title);
    }

    /**
     * @return EmployerType
     */
    public function build(): EmployerType
    {
        return new EmployerType($this);
    }
}
