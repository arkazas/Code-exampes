<?php

namespace App\Domain\Membership;

use App\Domain\Common\AbstractEntityBuilder;
use App\Domain\Member\Member;
use App\Domain\Product\Product;
use App\Domain\User\UserIdentity;

/**
 * Class MembershipBuilder
 */
class MembershipBuilder extends AbstractEntityBuilder
{
    /**
     * @var Member
     */
    private $member;

    /**
     * @var \DateTime
     */
    private $expiredAt;

    /**
     * @var Product
     */
    private $product;

    /**
     * @return Member
     */
    public function getMember(): Member
    {
        return $this->member;
    }

    /**
     * @param Member $member
     *
     * @return MembershipBuilder
     */
    public function setMember(Member $member): MembershipBuilder
    {
        $this->member = $member;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpiredAt(): \DateTime
    {
        return $this->expiredAt;
    }

    /**
     * @param \DateTime $expiredAt
     *
     * @return MembershipBuilder
     */
    public function setExpiredAt(\DateTime $expiredAt): MembershipBuilder
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     *
     * @return MembershipBuilder
     */
    public function setProduct(Product $product): MembershipBuilder
    {
        $this->product = $product;

        return $this;
    }
}
