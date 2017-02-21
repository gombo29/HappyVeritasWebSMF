<?php

namespace bot\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BotSender
 *
 * @ORM\Table(name="BotSender")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class BotSender
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_id", type="string", length=40, nullable=true, unique=true)
     */
    private $senderId;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=50, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=50, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="profile_img", type="string", length=200, nullable=true)
     */
    private $profileImg;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=10, nullable=true)
     */
    private $gender;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_last", type="boolean", nullable=true)
     */
    private $isLast;


    /**
     * @var bool
     *
     * @ORM\Column(name="lang", type="boolean", nullable=true)
     */
    private $lang; // 1 - MN 0 - EN

    /**
     * @var bool
     *
     * @ORM\Column(name="is_first", type="boolean", nullable=true)
     */
    private $isFirst; // 1 - first 0 - none


    /**
     * @var \DateTime
     *
     */
    public $sdate;

    /**
     * @var \DateTime
     *
     */
    public $edate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=true)
     */
    private $createdDate;

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist() {
        $this->setCreatedDate(new \DateTime("now"));
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set senderId
     *
     * @param string $senderId
     * @return BotSender
     */
    public function setSenderId($senderId)
    {
        $this->senderId = $senderId;

        return $this;
    }

    /**
     * Get senderId
     *
     * @return string 
     */
    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return BotSender
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return BotSender
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set profileImg
     *
     * @param string $profileImg
     * @return BotSender
     */
    public function setProfileImg($profileImg)
    {
        $this->profileImg = $profileImg;

        return $this;
    }

    /**
     * Get profileImg
     *
     * @return string 
     */
    public function getProfileImg()
    {
        return $this->profileImg;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return BotSender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return BotSender
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime 
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set lang
     *
     * @param boolean $lang
     * @return BotSender
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return boolean 
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     * @return BotSender
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime 
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set isFirst
     *
     * @param boolean $isFirst
     * @return BotSender
     */
    public function setIsFirst($isFirst)
    {
        $this->isFirst = $isFirst;

        return $this;
    }

    /**
     * Get isFirst
     *
     * @return boolean 
     */
    public function getIsFirst()
    {
        return $this->isFirst;
    }

    /**
     * Set isLast
     *
     * @param boolean $isLast
     * @return BotSender
     */
    public function setIsLast($isLast)
    {
        $this->isLast = $isLast;

        return $this;
    }

    /**
     * Get isLast
     *
     * @return boolean 
     */
    public function getIsLast()
    {
        return $this->isLast;
    }
}
