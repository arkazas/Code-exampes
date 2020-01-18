<?php

namespace AFG\Model\Employers\MailAddress;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class MailAddress
{
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $street;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $city;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $zip;


    public function update(MailAddressBuilder $builder)
    {
        $builder->has('street') ? $this->street = $builder->getStreet() : null;
        $builder->has('city') ? $this->city = $builder->getCity() : null;
        $builder->has('zip') ? $this->zip = $builder->getZip() : null;
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
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getZip(): ?string
    {
        return $this->zip;
    }


}
