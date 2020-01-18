<?php

namespace AFG\Model\Employers\MailAddress;

use AFG\Model\Builder;

class MailAddressBuilder extends Builder
{
    /**
     * @var string|false
     */
    protected $street = false;

    /**
     * @var string|false
     */
    protected $zip = false;

    /**
     * @var string|false
     */
    protected $city = false;

    public function withStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    public function withZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    public function withCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getZip(): ?string
    {
        return $this->zip;
    }

    /**
     * @return string
     */
    public function getCity(): ?string
    {
        return $this->city;
    }
}
