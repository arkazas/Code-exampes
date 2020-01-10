<?php

namespace App\Domain\Membership;

use App\Domain\Common\AbstractEntity;
use App\Domain\Common\Interfaces\Common\StatusInterface;
use App\Domain\Member\Member;
use App\Domain\Product\Product;
use App\Domain\User\UserIdentity;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="membership")
 */
class Membership extends AbstractEntity
{
    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELED = 'canceled';
    const STATUS_EXPIRED = 'expired';
    const STATUS_UPGRADED = 'upgraded';

    /**
     * @var Member
     * @ORM\ManyToOne(targetEntity="App\Domain\Member\Member", inversedBy="memberships")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $member;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $expiredAt;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $status = StatusInterface::STATUS_ACTIVE;

    /**
     * @var Product
     * @ORM\OneToOne(targetEntity="App\Domain\Product\Product", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * Membership constructor.
     *
     * @param MembershipBuilder $builder
     *
     * @throws \Exception
     */
    public function __construct(MembershipBuilder $builder)
    {
        parent::__construct($builder);

        $this->member = $builder->getMember();
        $this->product = $builder->getProduct();
        $this->expiredAt = (new DateTimeImmutable())->add($this->product->getDuration());
    }

    /**
     * @return Member
     */
    public function getMember(): Member
    {
        return $this->member;
    }

    /**
     * @return DateTime
     */
    public function getExpiredAt(): DateTime
    {
        return $this->expiredAt;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @return Membership
     */
    public function cancel(): self
    {
        $this->status = self::STATUS_CANCELED;

        return $this;
    }
}
