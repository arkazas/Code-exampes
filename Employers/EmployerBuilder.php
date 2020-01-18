<?php

namespace AFG\Model\Employers;

use AFG\Common\Model\UuidBuilderTrait;
use AFG\Model\Builder;
use AFG\Model\Employers\BusinessArea\BusinessArea;
use AFG\Model\Vendor\Vendor;
use AFG\Common\Model\UuidBuilderInterface;

class EmployerBuilder extends Builder implements UuidBuilderInterface
{
    use UuidBuilderTrait;

    protected $booleanFields = [
        'isInternal',
        'isMainInternal',
    ];

    /**
     * @var string|false
     */
    protected $number = false;

    /**
     * @var string|false
     */
    protected $title = false;

    /**
     * @var string|false
     */
    protected $type = false;

    /**
     * @var BusinessArea[]|false
     */
    protected $businessAreas = false;

    /**
     * @var Vendor|false
     */
    protected $vendor = false;

    protected $country = false;

    protected $isInternal;

    protected $isMainInternal;

    public function build(): Employer
    {
        $employer = new Employer($this);
        $employer->update($this);

        return $employer;
    }

    /**
     * @param $number
     *
     * @return $this
     */
    public function withNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    public function withCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    public function withTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function withType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function withIsInternal($flag)
    {
        $this->isInternal = $flag;
        return $this;
    }

    public function withIsMainInternal($flag)
    {
        $this->isMainInternal = $flag;
        return $this;
    }

    public function withVendor($vendor)
    {
        $this->vendor = $vendor;

        return $this;
    }

    public function withBusinessAreas($businessAreas)
    {
        $this->businessAreas = $businessAreas;

        return $this;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getBusinessAreas()
    {
        return $this->businessAreas;
    }

    public function getVendor()
    {
        return $this->vendor;
    }

    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return mixed
     */
    public function getIsInternal()
    {
        return $this->isInternal;
    }

    /**
     * @return mixed
     */
    public function getIsMainInternal()
    {
        return $this->isMainInternal;
    }
}
