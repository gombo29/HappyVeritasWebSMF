<?php

namespace bot\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BotSenderNews
 *
 * @ORM\Table(name="BotSenderNews")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class BotSenderNews
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
     * @ORM\ManyToOne(targetEntity="BotBlock")
     * @ORM\JoinColumn(name="bot_block", referencedColumnName="id")
     */
    private $botBlock;

    /**
     * @ORM\ManyToOne(targetEntity="BotSender")
     * @ORM\JoinColumn(name="bot_sender", referencedColumnName="id")
     */
    private $botSender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=true)
     */
    private $createdDate;

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
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return BotSenderNews
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
     * Set botSender
     *
     * @param \bot\CmsBundle\Entity\BotSender $botSender
     * @return BotSenderNews
     */
    public function setBotSender(\bot\CmsBundle\Entity\BotSender $botSender = null)
    {
        $this->botSender = $botSender;

        return $this;
    }

    /**
     * Get botSender
     *
     * @return \bot\CmsBundle\Entity\BotSender 
     */
    public function getBotSender()
    {
        return $this->botSender;
    }

    /**
     * Set botBlock
     *
     * @param \bot\CmsBundle\Entity\BotBlock $botBlock
     * @return BotSenderNews
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
}
