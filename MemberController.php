<?php

namespace App\Application\Http\Controller;

use App\Application\Http\Request\Member\CreateAvatarRequest;
use App\Application\Http\Request\Member\CreateProfileRequest;
use App\Application\Http\Request\Member\MembersTypeRequest;
use App\Application\Http\Request\Member\UpdateBillingAddressRequest;
use App\Application\Http\Request\Member\UpdateProfileRequest;
use App\Application\Http\Request\Review\CreateReviewRequest;
use App\Application\Http\Transformer\Member\MembersListTransformer;
use App\Application\Http\Transformer\Member\MemberTransformer;
use App\Application\Http\Transformer\Review\ReviewTransformer;
use App\Application\Model\Filter;
use App\Application\Model\OrderBy;
use App\Application\Model\Pagination;
use App\Domain\Common\Interfaces\Common\StatusInterface;
use App\Domain\Common\Interfaces\Entity\FileInterface;
use App\Domain\Common\Traits\Entity\ReferrerableInterface;
use App\Domain\Member\Criteria\ByFullNameCriteria;
use App\Domain\Member\Criteria\ByMemberTypeCriteria;
use App\Domain\Member\Criteria\ByReferralsCriteria;
use App\Domain\Member\Member;
use App\Domain\Member\MemberBuilder;
use App\Domain\Restaurant\Restaurant;
use App\Infrastructure\Persistence\Doctrine\MemberRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Config\Route("/members")
 */
class MemberController extends ApiController
{
    /**
     * @Config\Route(path="/me/profile", methods={"POST"})
     * @Config\Security("is_granted('ROLE_MEMBER')")
     *
     * @param CreateProfileRequest       $request
     * @param MemberTransformer          $transformer
     * @param ReferrerableInterface|null $referrer
     *
     * @return JsonResponse
     */
    public function createProfile(
        CreateProfileRequest $request,
        MemberTransformer $transformer,
        ?ReferrerableInterface $referrer
    ) {
        /** @var Member $member */
        $member = $this->getUser();
        $builder = $request->getBuilder()
            ->setReferrer($referrer)
            ->setStatus(StatusInterface::STATUS_ACTIVE);

        $member->update($builder);

        $this->flushChanges();

        return $this->resource($this->getUser(), $transformer)
                    ->asResponse(Response::HTTP_CREATED);
    }

    /**
     * @Config\Route(path="/me/profile", methods={"PUT"})
     * @Config\Security("is_granted('ROLE_MEMBER')")
     *
     * @param UpdateProfileRequest $request
     * @param MemberTransformer    $transformer
     *
     * @return JsonResponse
     */
    public function updateProfile(UpdateProfileRequest $request, MemberTransformer $transformer)
    {
        /** @var Member $member */
        $member = $this->getUser();

        $builder = $request->getBuilder();
        $member->update($builder);

        $this->flushChanges();

        return $this->resource($this->getUser(), $transformer)
                    ->asResponse();
    }

    /**
     * @Config\Route(path="/me/profile", methods={"GET"})
     * @Config\Security("is_granted('ROLE_MEMBER')")
     *
     * @param MemberTransformer $transformer
     *
     * @return JsonResponse
     */
    public function viewProfile(MemberTransformer $transformer): JsonResponse
    {
        return $this->resource($this->getUser(), $transformer)
                    ->asResponse();
    }

    /**
     * @Config\Route(path="/{member}", methods={"DELETE"})
     * @Config\Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_ADMIN')")
     *
     * @param Member $member
     *
     * @return JsonResponse
     * @throws \ErrorException
     */
    public function delete(Member $member): JsonResponse
    {
        $member->delete();

        return $this->deleteObject($member);
    }

    /**
     * @Config\Route(path="/{restaurant}/add-review", methods={"POST"})
     * @Config\Security("is_granted('ROLE_MEMBER')")
     *
     * @param Restaurant          $restaurant
     * @param CreateReviewRequest $request
     * @param ReviewTransformer   $transformer
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function addReview(
        Restaurant $restaurant,
        CreateReviewRequest $request,
        ReviewTransformer $transformer
    ) {
        $reviewBuilder = $request->getReviewBuilder();
        $reviewBuilder->setAuthor($this->getUser());
        $review = $restaurant->addReview($reviewBuilder);
        $this->flushChanges();

        return $this->resource($review, $transformer)->asResponse();
    }
}
