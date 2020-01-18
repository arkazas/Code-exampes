<?php

namespace AFG\Model\Employers\BusinessArea;

use AFG\Model\Builder;

class BusinessAreaBuilder extends Builder
{
    /**
     * @var string|false
     */
    protected $code = false;

    /**
     * @var string|false
     */
    protected $description = false;

    public function withCode($code)
    {
        $this->code = $code;

        return $this;
    }

    public function withDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
