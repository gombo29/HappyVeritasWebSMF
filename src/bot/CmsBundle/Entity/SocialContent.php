<?php

namespace bot\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SocialContent
 *
 * @ORM\Table(name="SocialContent")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class SocialContent
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
     * @ORM\Column(name="title", type="string", length=100, nullable=true)
     */
    private $title;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_embed", type="boolean", nullable=true)
     */
    private $isEmbed; // 1 - MN 0 - EN

    /**
     * @var string
     *
     * @ORM\Column(name="embed", type="text",nullable=true)
     */
    private $embed;

    /**
     * @var string
     *
     * @ORM\Column(name="video", type="string", length=100, nullable=true)
     */
    private $video;


    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=100, nullable=true)
     */
    private $link;


    /**
     * @var string
     *
     * @ORM\Column(name="img_url", type="text",  nullable=true)
     */
    private $img;

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
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;

    /**
     * @ORM\ManyToOne(targetEntity="SocialTabCategory")
     * @ORM\JoinColumn(name="tab", referencedColumnName="id")
     */
    private $tab;


    /**
     * @var integer
     *
     * @ORM\Column(name="v_count", type="integer", nullable=true)
     */
    private $vCount;


    /**
     * @var integer
     *
     * @ORM\Column(name="pro_img_id", type="integer", nullable=true)
     */
    private $proImgId;


    /**
     * @var integer
     *
     * @ORM\Column(name="card_id", type="integer", nullable=true)
     */
    private $cardId;


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
     * @var bool
     *
     * @ORM\Column(name="is_upload", type="boolean", nullable=true)
     */
    private $isUpload; // 1 - MN 0 - EN

    /**
     * @var bool
     *
     * @ORM\Column(name="is_target", type="boolean", nullable=true)
     */
    private $isTarget;

    /**
     * @var integer
     *
     * @ORM\Column(name="myorder", type="integer", nullable=true)
     */
    private $myorder;

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
            $statfolder, $filename
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
        return '/resources/tab';
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
     * Set body
     *
     * @param string $body
     * @return SocialContent
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set vCount
     *
     * @param integer $vCount
     * @return SocialContent
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
     * Set publishDate
     *
     * @param \DateTime $publishDate
     * @return SocialContent
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
     * @return SocialContent
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

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return SocialContent
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
     * @return SocialContent
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
     * Set tab
     *
     * @param \bot\CmsBundle\Entity\SocialTabCategory $tab
     * @return SocialContent
     */
    public function setTab(\bot\CmsBundle\Entity\SocialTabCategory $tab = null)
    {
        $this->tab = $tab;

        return $this;
    }

    /**
     * Get tab
     *
     * @return \bot\CmsBundle\Entity\SocialTabCategory
     */
    public function getTab()
    {
        return $this->tab;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return SocialContent
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
     * Set link
     *
     * @param string $link
     * @return SocialContent
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
     * Set img
     *
     * @param string $img
     * @return SocialContent
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
     * Set isUpload
     *
     * @param boolean $isUpload
     * @return SocialContent
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
     * Set video
     *
     * @param string $video
     * @return SocialContent
     */
    public function setVideo($video)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get video
     *
     * @return string
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set proImgId
     *
     * @param integer $proImgId
     * @return SocialContent
     */
    public function setProImgId($proImgId)
    {
        $this->proImgId = $proImgId;

        return $this;
    }

    /**
     * Get proImgId
     *
     * @return integer
     */
    public function getProImgId()
    {
        return $this->proImgId;
    }

    /**
     * Set isTarget
     *
     * @param boolean $isTarget
     * @return SocialContent
     */
    public function setIsTarget($isTarget)
    {
        $this->isTarget = $isTarget;

        return $this;
    }

    /**
     * Get isTarget
     *
     * @return boolean
     */
    public function getIsTarget()
    {
        return $this->isTarget;
    }

    /**
     * Set myorder
     *
     * @param integer $myorder
     * @return SocialContent
     */
    public function setMyorder($myorder)
    {
        $this->myorder = $myorder;

        return $this;
    }

    /**
     * Get myorder
     *
     * @return integer
     */
    public function getMyorder()
    {
        return $this->myorder;
    }

    /**
     * Set isEmbed
     *
     * @param boolean $isEmbed
     * @return SocialContent
     */
    public function setIsEmbed($isEmbed)
    {
        $this->isEmbed = $isEmbed;

        return $this;
    }

    /**
     * Get isEmbed
     *
     * @return boolean
     */
    public function getIsEmbed()
    {
        return $this->isEmbed;
    }

    /**
     * Set embed
     *
     * @param string $embed
     * @return SocialContent
     */
    public function setEmbed($embed)
    {
        $this->embed = $embed;

        return $this;
    }

    /**
     * Get embed
     *
     * @return string
     */
    public function getEmbed()
    {
        return $this->embed;
    }

    /**
     * Set cardId
     *
     * @param integer $cardId
     * @return SocialContent
     */
    public function setCardId($cardId)
    {
        $this->cardId = $cardId;

        return $this;
    }

    /**
     * Get cardId
     *
     * @return integer 
     */
    public function getCardId()
    {
        return $this->cardId;
    }
}
