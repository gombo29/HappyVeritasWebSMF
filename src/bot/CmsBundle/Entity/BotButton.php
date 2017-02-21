<?php

namespace bot\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BotButton
 *
 * @ORM\Table(name="BotButton")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class BotButton
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
     * @var string
     *
     * @ORM\Column(name="name_en", type="string", length=100, nullable=true)
     */
    private $nameEn;


    /**
     * @ORM\ManyToOne(targetEntity="BotContent" , inversedBy="botButton")
     * @ORM\JoinColumn(name="bot_content", referencedColumnName="id")
     */
    private $botContent;

    /**
     * @ORM\ManyToOne(targetEntity="BotBlock", inversedBy="botButton")
     * @ORM\JoinColumn(name="bot_block", referencedColumnName="id")
     */
    private $botBlock;

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
     * @return BotButton
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
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return BotButton
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
     * @return BotButton
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
     * Set botBlock
     *
     * @param \bot\CmsBundle\Entity\BotBlock $botBlock
     * @return BotButton
     */
    public function setBotBlock(\bot\CmsBundle\Entity\BotBlock $botBlock = null)
    {
        $this->botBlock = $botBlock;

        return $this;
    }

    /**
     * Get botBlock
     *
     * @return \bot\CmsBundle\Entity\BotBlock 
     */
    public function getBotBlock()
    {
        return $this->botBlock;
    }

    /**
     * Set botContent
     *
     * @param \bot\CmsBundle\Entity\BotContent $botContent
     * @return BotButton
     */
    public function setBotContent(\bot\CmsBundle\Entity\BotContent $botContent = null)
    {
        $this->botContent = $botContent;

        return $this;
    }

    /**
     * Get botContent
     *
     * @return \bot\CmsBundle\Entity\BotContent 
     */
    public function getBotContent()
    {
        return $this->botContent;
    }

    /**
     * Set nameEn
     *
     * @param string $nameEn
     * @return BotButton
     */
    public function setNameEn($nameEn)
    {
        $this->nameEn = $nameEn;

        return $this;
    }

    /**
     * Get nameEn
     *
     * @return string 
     */
    public function getNameEn()
    {
        return $this->nameEn;
    }
}
