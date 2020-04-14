<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use LOCKSSOMatic\UserBundle\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;

/**
 * Deposit made to LOCKSSOMatic.
 *
 * @ORM\Table(name="deposits")
 * @ORM\Entity(repositoryClass="DepositRepository")
 * @Assert\UniqueEntity("uuid")
 */
class Deposit implements GetPlnInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * The UUID for the deposit. Should be UPPERCASE.
     *
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, nullable=false)
     */
    private $uuid;

    /**
     * The title of the deposit.
     *
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * The amount of agreement for the deposit's content URLs in the lockss boxes.
     *
     * @var float
     *
     * @ORM\Column(name="agreement", type="float", nullable=true)
     */
    private $agreement;

    /**
     * A summary/description of the deposit.
     *
     * @var string
     *
     * @ORM\Column(name="summary", type="string", length=255, nullable=true)
     */
    private $summary;

    /**
     * The date LOCKSSOMatic recieved the deposit.
     *
     * @var DateTime
     *
     * @ORM\Column(name="date_deposited", type="datetime", nullable=false)
     */
    private $dateDeposited;

    /**
     * The date the deposit's status was last checked.
     *
     * @var DateTime
     * @ORM\Column(name="checked", type="datetime", nullable=true)
     */
    private $checked;

    /**
     * The content provider that created the deposit.s.
     *
     * @var ContentProvider
     *
     * @ORM\ManyToOne(targetEntity="ContentProvider", inversedBy="deposits")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_provider_id", referencedColumnName="id")
     * })
     */
    private $contentProvider;

    /**
     * The (optional) user making the deposit, perhaps via the gui.
     *
     * @ORM\ManyToOne(targetEntity="LOCKSSOMatic\UserBundle\Entity\User", inversedBy="deposits")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     * })
     *
     * @var User
     */
    private $user;

    /**
     * The content for the deposit.
     *
     * @var Content[]
     * @ORM\OneToMany(targetEntity="Content", mappedBy="deposit")
     */
    private $content;

    /**
     * The statuses from LOCKSS for the deposit.
     *
     * @var DepositStatus
     *
     * @ORM\OneToMany(targetEntity="DepositStatus", mappedBy="deposit")
     */
    private $status;

    /**
     * Build a new deposit.
     */
    public function __construct() {
        $this->content = new ArrayCollection();
        $this->status = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set uuid.
     *
     * @param string $uuid
     *
     * @return Deposit
     */
    public function setUuid($uuid) {
        $this->uuid = strtoupper($uuid);

        return $this;
    }

    /**
     * Get uuid.
     *
     * @return string
     */
    public function getUuid() {
        return strtoupper($this->uuid);
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Deposit
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set summary.
     *
     * @param string $summary
     *
     * @return Deposit
     */
    public function setSummary($summary) {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary.
     *
     * @return string
     */
    public function getSummary() {
        return $this->summary;
    }

    /**
     * Set dateDeposited.
     *
     * @param DateTime $dateDeposited
     *
     * @return Deposit
     */
    public function setDateDeposited($dateDeposited) {
        $this->dateDeposited = $dateDeposited;

        return $this;
    }

    /**
     * Get dateDeposited.
     *
     * @return DateTime
     */
    public function getDateDeposited() {
        return $this->dateDeposited;
    }

    /**
     * Set contentProvider.
     *
     * @param ContentProvider $contentProvider
     *
     * @return Deposit
     */
    public function setContentProvider(ContentProvider $contentProvider = null) {
        $this->contentProvider = $contentProvider;
        $contentProvider->addDeposit($this);

        return $this;
    }

    /**
     * Get contentProvider.
     *
     * @return ContentProvider
     */
    public function getContentProvider() {
        return $this->contentProvider;
    }

    /**
     * Add content.
     *
     * @param Content $content
     *
     * @return Deposit
     */
    public function addContent(Content $content) {
        $this->content[] = $content;

        return $this;
    }

    /**
     * Remove content.
     *
     * @param Content $content
     */
    public function removeContent(Content $content) {
        $this->content->removeElement($content);
    }

    /**
     * Count the content items in this deposit.
     *
     * @return int
     */
    public function countContent() {
        return $this->content->count();
    }

    /**
     * Get deposits.
     *
     * @return ArrayCollection|Content[]
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Set the deposit date. It can't be altered once it's set.
     */
    public function setDepositDate() {
        if ($this->dateDeposited === null) {
            $this->dateDeposited = new DateTime();
        }
    }

    /**
     * Set user.
     *
     * @param mixed $user
     *
     * @return User
     */
    public function setUser(User $user) {
        $this->user = $user;
        $user->addDeposit($this);

        return $this;
    }

    /**
     * Get user.
     *
     * @return User|null
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function getPln() {
        return $this->getContentProvider()->getPln();
    }

    /**
     * Set agreement.
     *
     * @param float $agreement
     *
     * @return Deposit
     */
    public function setAgreement($agreement) {
        $this->agreement = $agreement;

        return $this;
    }

    /**
     * Get agreement.
     *
     * @return float
     */
    public function getAgreement() {
        return $this->agreement;
    }

    /**
     * Add status.
     *
     * @param DepositStatus $status
     *
     * @return Deposit
     */
    public function addStatus(DepositStatus $status) {
        $this->status[] = $status;

        return $this;
    }

    /**
     * Remove status.
     *
     * @param DepositStatus $status
     */
    public function removeStatus(DepositStatus $status) {
        $this->status->removeElement($status);
    }

    /**
     * Get status.
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    public function getChecked() {
        return $this->checked;
    }

    public function setChecked($checked) {
        $this->checked = $checked;
    }
}
