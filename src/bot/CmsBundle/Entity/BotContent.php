<?php

namespace bot\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * BotContent
 *
 * @ORM\Table(name="BotContent")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class BotContent
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
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="title_en", type="string", length=255, nullable=true)
     */
    private $titleEn;


    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="descr", type="text",  nullable=true)
     */
    private $desc;

    /**
     * @var string
     *
     * @ORM\Column(name="desc_en", type="text",  nullable=true)
     */
    private $descEn;


    /**
     * @ORM\ManyToOne(targetEntity="BotBlock", inversedBy="botContent")
     * @ORM\JoinColumn(name="bot_block", referencedColumnName="id")
     */
    private $botBlock;

    /**
     * @ORM\OneToMany(targetEntity="BotButton",mappedBy="botContent", cascade={"persist","remove"})
     */
    public $botButton;

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
     * @var string
     *
     * @ORM\Column(name="img_url", type="text",  nullable=true)
     */
    private $img;


    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type; //1 - Medee 2 - Text 3 - Zurag

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
     * @var integer
     *
     * @ORM\Column(name="v_count", type="integer", nullable=true)
     */
    private $vCount;

    /**
     *
     * @Assert\Image(
     *        mimeTypesMessage = "Зурган файл биш байна!"
     * )
     *
     *
     */
    public $imagefile;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publish_date", type="datetime", nullable=true)
     */
    private $publishDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_upload", type="boolean", nullable=true)
     */
    private $isUpload; // 1 - MN 0 - EN

    /**
     *
     * @param $container
     */
    public function uploadImage(Container $container)
    {
        if (null === $this->getImagefile()) {
            return;
        }
        $statfolder = $this->getUploadRootDir($container);
        $filename = $this->getImageFile()->getFilename() . '.' . $this->getImagefile()->guessExtension();
        $this->getImagefile()->move(
            $statfolder , $filename
        );
        $this->setImg($this->getUploadDir() . '/' . $filename);
        $this->imagefile = null;
    }

    private function getUploadRootDir(ContainerInterface $container)
    {
        if ($container->get('kernel')->getEnvironment() == 'dev') {
            $statfolder = $container->getParameter('webfolder') . $this->getUploadDir();
            return $statfolder;
        } else {
            return '/opt' . $this->getUploadDir();
        }

    }

    public function getUploadDir()
    {
        return '/resources/bot';
    }


    /**
     * Set Image
     *
     * @param UploadedFile $file
     */
    public function setImageFile(UploadedFile $file = null)
    {
        $this->imagefile = $file;
    }

    /**
     * Get Image
     *
     * @return UploadedFile
     */
    public function getImageFile()
    {
        return $this->imagefile;
    }


    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->setCreatedDate(new \DateTime("now"));
        $this->setUpdatedDate(new \DateTime("now"));
        $this->setVCount(0);
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
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
     * Set title
     *
     * @param string $title
     * @return BotContent
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set titleEn
     *
     * @param string $titleEn
     * @return BotContent
     */
    public function setTitleEn($titleEn)
    {
        $this->titleEn = $titleEn;

        return $this;
    }

    /**
     * Get titleEn
     *
     * @return string
     */
    public function getTitleEn()
    {
        return $this->titleEn;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return BotContent
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set desc
     *
     * @param string $desc
     * @return BotContent
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;

        return $this;
    }

    /**
     * Get desc
     *
     * @return string
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * Set descEn
     *
     * @param string $descEn
     * @return BotContent
     */
    public function setDescEn($descEn)
    {
        $this->descEn = $descEn;

        return $this;
    }

    /**
     * Get descEn
     *
     * @return string
     */
    public function getDescEn()
    {
        return $this->descEn;
    }

    /**
     * Set img
     *
     * @param string $img
     * @return BotContent
     */
    public function setImg($img)
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Get img
     *
     * @return string
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return BotContent
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return BotContent
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
     * @return BotContent
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
     * Set isUpload
     *
     * @param boolean $isUpload
     * @return BotContent
     */
    public function setIsUpload($isUpload)
    {
        $this->isUpload = $isUpload;

        return $this;
    }

    /**
     * Get isUpload
     *
     * @return boolean
     */
    public function getIsUpload()
    {
        return $this->isUpload;
    }

    /**
     * Set botBlock
     *
     * @param \bot\CmsBundle\Entity\BotBlock $botBlock
     * @return BotContent
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
     * Constructor
     */
    public function __construct()
    {
        $this->botButton = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set vCount
     *
     * @param integer $vCount
     * @return BotContent
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
     * Add botButton
     *
     * @param \bot\CmsBundle\Entity\BotButton $botButton
     * @return BotContent
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

    /**
     * Set publishDate
     *
     * @param \DateTime $publishDate
     * @return BotContent
     */
    public function setPublishDate($publishDate)
    {
        $this->publishDate = $publishDate;

        return $this;
    }

    /**
     * Get publishDate
     *
     * @return \DateTime
     */
    public function getPublishDate()
    {
        return $this->publishDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return BotContent
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
}
