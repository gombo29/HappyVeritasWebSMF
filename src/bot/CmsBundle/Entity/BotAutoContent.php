<?php

namespace bot\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BotAutoContent
 *
 * @ORM\Table(name="BotAutoContent")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class BotAutoContent
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
     * @var \DateTime
     *
     * @ORM\Column(name="send_date", type="datetime", nullable=true)
     */
    private $sendDate;

    /**
     * @ORM\ManyToOne(targetEntity="BotContent")
     * @ORM\JoinColumn(name="bot_content_text", referencedColumnName="id")
     */
    private $botContentText;

    /**
     * @ORM\ManyToOne(targetEntity="BotContent")
     * @ORM\JoinColumn(name="bot_content", referencedColumnName="id")
     */
    private $botContent;
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
     * Set sendDate
     *
     * @param \DateTime $sendDate
     * @return BotAutoContent
     */
    public function setSendDate($sendDate)
    {
        $this->sendDate = $sendDate;

        return $this;
    }

    /**
     * Get sendDate
     *
     * @return \DateTime 
     */
    public function getSendDate()
    {
        return $this->sendDate;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return BotAutoContent
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
     * @return BotAutoContent
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
     * Set botContent
     *
     * @param \bot\CmsBundle\Entity\BotContent $botContent
     * @return BotAutoContent
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
     * Set botContentText
     *
     * @param \bot\CmsBundle\Entity\BotContent $botContentText
     * @return BotAutoContent
     */
    public function setBotContentText(\bot\CmsBundle\Entity\BotContent $botContentText = null)
    {
        $this->botContentText = $botContentText;

        return $this;
    }

    /**
     * Get botContentText
     *
     * @return \bot\CmsBundle\Entity\BotContent 
     */
    public function getBotContentText()
    {
        return $this->botContentText;
    }
}
