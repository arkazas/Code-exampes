<?php

namespace AFG\Model\Employers;

use AFG\Common\Model\ForcedUpdateInterface;
use AFG\Common\Model\ForcedUpdateTrait;
use AFG\Common\Model\SoftDeleteChildrenTrait;
use AFG\Model\Certificate\UserCertificateCategory;
use AFG\Model\ChangeableInterface;
use AFG\Model\Course\UserCourse;
use AFG\Model\Employers\BusinessArea\BusinessArea;
use AFG\Model\Employers\MailAddress\MailAddress;
use AFG\Model\Employers\MailAddress\MailAddressBuilder;
use AFG\Model\Project\Project;
use AFG\Model\SynchronizableInterface;
use AFG\Model\User\User;
use AFG\Model\User\UserBuilder;
use AFG\Model\Vendor\Vendor;
use AFG\Service\Employers\InternalDetector\InternalDetector;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="employer",indexes={
 *     @ORM\Index(name="employer_number_idx", columns={"number"}),
 *     @ORM\Index(name="employer_title_idx", columns={"title"}),
 *     @ORM\Index(name="employer_complex_idx", columns={"title", "number"}),
 *     @ORM\Index(name="employer_main_internal", columns={"isMainInternal"})
 * }
 * )
 */
class Employer implements ChangeableInterface, SynchronizableInterface, ForcedUpdateInterface
{
    use \AFG\Model\UUIDTrait;
    use ForcedUpdateTrait;
    use SoftDeleteChildrenTrait;

    public const ENTITY_KEY = 'employer';

    public const TYPE_FOREIGN = 'Foreign';

    public const AFG_ORG_NUMBER = '938333572';


    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $number;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity="AFG\Model\Employers\BusinessArea\BusinessArea", cascade={"persist"})
     * @ORM\JoinTable(name="employer_businessarea",
     *      joinColumns={@ORM\JoinColumn(name="employer_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="businessArea_id", referencedColumnName="id")}
     * )
     */
    private $businessAreas;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AFG\Model\User\User", mappedBy="employer", cascade={"persist"})
     */
    private $employees;

    /**
     * @var MailAddress
     * @ORM\Embedded(class="AFG\Model\Employers\MailAddress\MailAddress")
     */
    private $mailAddress;

    /**
     * @var string|false
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isHolding = false;

    /**
     * @var string|false
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $fromBregg = false;

    /**
     * @var ArrayCollection|Vendor[]
     *
     * @ORM\OneToMany(targetEntity="AFG\Model\Vendor\Vendor", mappedBy="employer")
     */
    private $vendors;

    /**
     * @var UserCourse[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AFG\Model\Course\UserCourse", cascade={"persist", "remove"}, mappedBy="provider", orphanRemoval=true)
     */
    private $userCourses;

    /**
     * @var UserCertificateCategory[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AFG\Model\Certificate\UserCertificateCategory", cascade={"persist", "remove"}, mappedBy="provider", orphanRemoval=true)
     */
    private $userCertificatesCategories;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, options={"default": "NO"})
     */
    private $country = 'NO';

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private $isInternal = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private $isMainInternal = false;

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'number' => $this->getNumber(),
        ];
    }

    public function toShortArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
        ];
    }

    public function __construct(EmployerBuilder $builder)
    {
        $this->setOrGenerateUuid($builder);

        $this->type = $builder->getType();
        $this->businessAreas = new ArrayCollection();
        $this->employees = new ArrayCollection();
        $this->mailAddress = new MailAddress();
        $this->vendors = new ArrayCollection();
        $builder->has('isInternal') ? $this->isInternal = $builder->getIsInternal() : null;
        $builder->has('isMainInternal') ? $this->isMainInternal = $builder->getIsMainInternal() : null;

        $this->update($builder);
    }

    public function getEntityKey(): string
    {
        return self::ENTITY_KEY;
    }

    public function isSynchronizable(): bool
    {
        return $this->getEmployeesNumber() > 0 && (!$this->isInternal() || $this->isMainInternal()); //TODO implement if needed
    }

    public function forUser(): ?User
    {
        return null;
    }

    public function update(EmployerBuilder $builder): void
    {
        $builder->has('businessAreas') ? $this->setBusinessAreas($builder->getBusinessAreas()) : null;
        $builder->has('country') ? $this->country = $builder->getCountry() : null;
        $builder->has('number') ? $this->number = $builder->getNumber() : null;
        $builder->has('title') ? $this->title = $builder->getTitle() : null;
    }

    public function updateMailAddress(MailAddressBuilder $builder)
    {
        $this->mailAddress->update($builder);
    }

    /**
     * @return string
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function getIsHolding(): ?bool
    {
        return $this->isHolding;
    }

    public function setIsHolding(): void
    {
        $this->isHolding = true;
    }

    public function setIsNotHolding(): void
    {
        $this->isHolding = false;
    }

    public function isImportedFromBregg(): ?bool
    {
        return $this->fromBregg;
    }

    public function setIsImportedFromBregg(): void
    {
        $this->fromBregg = true;
    }

    public function setIsNotImportedFromBregg(): void
    {
        $this->fromBregg = false;
    }

    /**
     * @return MailAddress
     */
    public function getMailAddress(): ?MailAddress
    {
        return $this->mailAddress;
    }

    public function getBusinessAreas()
    {
        return $this->businessAreas;
    }

    public function getBusinessAreasArray()
    {
        $businessAreas = [];
        foreach ($this->businessAreas as $area) {
            $businessAreas[] = $area->toArray();
        }

        return $businessAreas;
    }

    public function getUsersInfoByProject(Project $project): ArrayCollection
    {
        $users = new ArrayCollection();
        /** @var User $employee */
        foreach ($this->employees as $employee) {
            foreach ($employee->getCurrentProjects() as $currentProject) {
                if ($currentProject->getProject()->getId() === $project->getId()) {
                    $users->add($employee->toShortArray());
                }
            }
        }

        return $users;
    }

    /**
     * @return ArrayCollection
     */
    public function getEmployees()
    {
        return $this->employees;
    }

    /**
     * @return int
     */
    public function getEmployeesNumber(): int
    {
        return $this->employees->count();
    }

    private function setBusinessAreas(array $areas)
    {
        if (!$this->businessAreas->isEmpty()) {
            $this->businessAreas->map(function(BusinessArea $businessArea){
                $this->businessAreas->removeElement($businessArea);
            });
        }
        foreach ($areas as $area) {
            $this->businessAreas->add($area);
        }
    }

    /**
     * @return Vendor
     */
    public function getVendor(): ?Vendor
    {
        return $this->getActiveList($this->vendors)->first() ?: null;
    }

    public function addVendor(Vendor $vendor)
    {
        if (!$this->vendors->contains($vendor)) {
            $this->vendors->add($vendor);
        }
    }

    public function getVendorStatus()
    {
        return $this->getVendor() ? $this->getVendor()->getStatus() : null;
    }

    public function setInternalFromResource(InternalDetector $detector)
    {
        $detector->isInternal($this) ? $this->setIsHolding() : $this->setIsNotHolding();
    }

    /**
     * @param UserBuilder $builder
     *
     * @return User
     */
    public function addEmployee(UserBuilder $builder, bool $withVerification = true): User
    {
        $builder->withEmployer($this);

        $user = $withVerification ? $builder->buildUser() : $builder->buildUserWithoutVerification();
        $this->addUser($user);

        return $user;
    }

    public function addUser(User $user)
    {
        if (!$this->employees->contains($user)) {
            $this->employees->add($user);
            $this->forceUpdate();
        }
        return $this;
    }

    public function isForeign(): bool
    {
        return $this->type === self::TYPE_FOREIGN;
    }

    /**
     * @return bool
     */
    public function isInternal(): bool
    {
        return (bool) $this->isInternal;
    }

    /**
     * @return bool
     */
    public function isMainInternal(): bool
    {
        return (bool) $this->isMainInternal;
    }
}
