<?php

namespace AFG\Model\Employers;

use AFG\Model\UUIDTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="employer_type")
 */
class EmployerType
{
    use UUIDTrait;

    /**
     * @var string
     * @ORM\Column(type="string", length=10, nullable=false, unique=true)
     */
    private $title;

    /**
     * @param EmployerTypeBuilder $builder
     */
    public function __construct(EmployerTypeBuilder $builder)
    {
        $this->setOrGenerateUuid($builder);
        $this->title = $builder->getAttribute('title', $this->title);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
