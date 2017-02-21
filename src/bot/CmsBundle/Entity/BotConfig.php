<?php

namespace bot\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BotBlock
 *
 * @ORM\Table(name="BotConfig")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class BotConfig
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
     * @var integer
     *
     * @ORM\Column(name="content_total", type="integer", nullable=true)
     */
    private $contentTotal;

    /**
     * @var integer
     *
     * @ORM\Column(name="diff_total", type="integer", nullable=true)
     */
    private $diffTotal;

    /**
     * @var string
     *
     * @ORM\Column(name="cron_date", type="string", length=255, nullable=true)
     */
    private $cronDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="end_msg_date", type="integer", nullable=true)
     */
    private $endMsgDate;

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
     * Set contentTotal
     *
     * @param integer $contentTotal
     * @return BotConfig
     */
    public function setContentTotal($contentTotal)
    {
        $this->contentTotal = $contentTotal;

        return $this;
    }

    /**
     * Get contentTotal
     *
     * @return integer 
     */
    public function getContentTotal()
    {
        return $this->contentTotal;
    }

    /**
     * Set diffTotal
     *
     * @param integer $diffTotal
     * @return BotConfig
     */
    public function setDiffTotal($diffTotal)
    {
        $this->diffTotal = $diffTotal;

        return $this;
    }

    /**
     * Get diffTotal
     *
     * @return integer 
     */
    public function getDiffTotal()
    {
        return $this->diffTotal;
    }

    /**
     * Set cronDate
     *
     * @param string $cronDate
     * @return BotConfig
     */
    public function setCronDate($cronDate)
    {
        $this->cronDate = $cronDate;

        return $this;
    }

    /**
     * Get cronDate
     *
     * @return string 
     */
    public function getCronDate()
    {
        return $this->cronDate;
    }

    /**
     * Set endMsgDate
     *
     * @param integer $endMsgDate
     * @return BotConfig
     */
    public function setEndMsgDate($endMsgDate)
    {
        $this->endMsgDate = $endMsgDate;

        return $this;
    }

    /**
     * Get endMsgDate
     *
     * @return integer 
     */
    public function getEndMsgDate()
    {
        return $this->endMsgDate;
    }

    /**
     * Set updatedDate
     *
     * @param \DateTime $updatedDate
     * @return BotConfig
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
}
