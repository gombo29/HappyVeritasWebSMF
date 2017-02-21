<?php

namespace bot\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BotLog
 *
 * @ORM\Table(name="BotLog")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class BotLog
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
     * @ORM\Column(name="advice_id", type="string", length=100, nullable=true)
     */
    public $advice_id;

    /**
     * @var string
     *
     * @ORM\Column(name="joke_id", type="string", length=100, nullable=true)
     */
    public $joke_id;

    /**
     * @var string
     *
     * @ORM\Column(name="sticker_id", type="string", length=100, nullable=true)
     */
    public $sticker_id;

    /**
     * @var string
     *
     * @ORM\Column(name="news_id", type="string", length=100, nullable=true)
     */
    public $news_id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=100, nullable=true)
     */
    public $username;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="c_ognoo", type="datetime", nullable=true)
     */
    public $cognoo;
    public $fromognoo;
    public $toognoo;

    /**
     * @var string
     *
     * @ORM\Column(name="log", type="text" , nullable=true)
     */
    public $log;

    /**
     *
     * @var string
     */
    public $reqheader;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=100, nullable=true)
     */
    public $ip;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return BotLog
     */
    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Set cognoo
     *
     * @param \DateTime $cognoo
     * @return BotLog
     */
    public function setCognoo($cognoo) {
        $this->cognoo = $cognoo;

        return $this;
    }

    /**
     * Get cognoo
     *
     * @return \DateTime
     */
    public function getCognoo() {
        return $this->cognoo;
    }

    /**
     * Set log
     *
     * @param string $log
     * @return BotLog
     */
    public function setLog($log) {
        $this->log = $log;

        return $this;
    }

    /**
     * Set joke_id
     *
     * @param string $joke_id
     * @return BotLog
     */
    public function setJokeId($joke_id) {
        $this->joke_id = $joke_id;

        return $this;
    }

    /**
     * Get log
     *
     * @return string
     */
    public function getLog() {
        return $this->log;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return BotLog
     */
    public function setIp($ip) {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp() {
        return $this->ip;
    }
    public function getStickerId() {
        return $this->sticker_id;
    }
    public function setStickerId($sticker_id) {
        $this->sticker_id = $sticker_id;
        return $this;
    }
}
