<?php

namespace AFG\Model\Employers\BusinessArea;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="business_area")
 * @ORM\Entity()
 */
class BusinessArea
{
    use \AFG\Model\UUIDTrait;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $code;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;

    /**
     * BusinessArea constructor.
     */
    public function __construct(?BusinessAreaBuilder $builder = null)
    {
        $this->setOrGenerateUuid($builder);

        if ($builder) {
            $this->update($builder);
        }
    }

    public function update(BusinessAreaBuilder $builder): void
    {
        $builder->has('code') ? $this->code = $builder->getCode() : null;
        $builder->has('description') ? $this->description = $builder->getDescription() : null;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->getCode(),
            'description' => $this->getDescription(),
        ];
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
