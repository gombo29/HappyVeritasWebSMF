<?php

namespace bot\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BotBlock
 *
 * @ORM\Table(name="BotBlock")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class BotBlock
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
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;


    /**
     * @ORM\ManyToOne(targetEntity="BotGroup")
     * @ORM\JoinColumn(name="bot_group", referencedColumnName="id")
     */
    private $botGroup;

    /**
     * @ORM\OneToMany(targetEntity="BotContent",mappedBy="botBlock", cascade={"persist","remove"})
     */
    public $botContent;

    /**
     * @ORM\OneToMany(targetEntity="BotButton",mappedBy="botBlock", cascade={"persist","remove"})
     */
    public $botButton;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_special", type="boolean", nullable=true)
     */
    private $isSpecial;

    /**
     * @var integer
     *
     * @ORM\Column(name="v_count", type="integer", nullable=true)
     */
    private $vCount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=true)
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_date", type="datetime", nullable=true)
     */
    private $updatedDate;

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->setCreatedDate(new \DateTime("now"));
        $this->setUpdatedDate(new \DateTime("now"));
        $this->setIsSpecial(0);
        $this->setVCount(0);
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate() {
        $this->setUpdatedDate(new \DateTime("now"));
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
     * Set name
     *
     * @param string $name
     * @return BotBlock
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set isSpecial
     *
     * @param boolean $isSpecial
     * @return BotBlock
     */
    public function setIsSpecial($isSpecial)
    {
        $this->isSpecial = $isSpecial;

        return $this;
    }

    /**
     * Get isSpecial
     *
     * @return boolean 
     */
    public function getIsSpecial()
    {
        return $this->isSpecial;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return BotBlock
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
     * Set updatedDate
     *
     * @param \DateTime $updatedDate
     * @return BotBlock
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;

        return $this;
    }

    /**
     * Get updatedDate
     *
     * @return \DateTime 
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    /**
     * Set botGroup
     *
     * @param \bot\CmsBundle\Entity\BotGroup $botGroup
     * @return BotBlock
     */
    public function setBotGroup(\bot\CmsBundle\Entity\BotGroup $botGroup = null)
    {
        $this->botGroup = $botGroup;

        return $this;
    }

    /**
     * Get botGroup
     *
     * @return \bot\CmsBundle\Entity\BotGroup 
     */
    public function getBotGroup()
    {
        return $this->botGroup;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->botContent = new \Doctrine\Common\Collections\ArrayCollection();
        $this->botButton = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set vCount
     *
     * @param integer $vCount
     * @return BotBlock
     */
    public function setVCount($vCount)
    {
        $this->vCount = $vCount;

        return $this;
    }

    /**
     * Get vCount
     *
     * @return integer 
     */
    public function getVCount()
    {
        return $this->vCount;
    }

    /**
     * Add botContent
     *
     * @param \bot\CmsBundle\Entity\BotContent $botContent
     * @return BotBlock
     */
    public function addBotContent(\bot\CmsBundle\Entity\BotContent $botContent)
    {
        $this->botContent[] = $botContent;

        return $this;
    }

    /**
     * Remove botContent
     *
     * @param \bot\CmsBundle\Entity\BotContent $botContent
     */
    public function removeBotContent(\bot\CmsBundle\Entity\BotContent $botContent)
    {
        $this->botContent->removeElement($botContent);
    }

    /**
     * Get botContent
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBotContent()
    {
        return $this->botContent;
    }

    /**
     * Add botButton
     *
     * @param \bot\CmsBundle\Entity\BotButton $botButton
     * @return BotBlock
     */
    public function addBotButton(\bot\CmsBundle\Entity\BotButton $botButton)
    {
        $this->botButton[] = $botButton;

        return $this;
    }

    /**
     * Remove botButton
     *
     * @param \bot\CmsBundle\Entity\BotButton $botButton
     */
    public function removeBotButton(\bot\CmsBundle\Entity\BotButton $botButton)
    {
        $this->botButton->removeElement($botButton);
    }

    /**
     * Get botButton
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBotButton()
    {
        return $this->botButton;
    }
}
