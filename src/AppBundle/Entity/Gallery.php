<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Gallery
 * 
 * @ORM\Entity
 * @ORM\Table(name="zsf_gallery")
 * 
 */
class Gallery extends ContentBase
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;
    
    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="datetime")
     */
    private $date;
    
    /**
     *
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    private $dateto;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=TRUE)
     */
    private $description;
    
    /**
     * @ORM\Column(type="boolean", nullable=TRUE)
     */
    private $homeslide;

    /**
     * 
     * @ORM\OneToMany(targetEntity="ResponsiveImage", mappedBy="gallery")
     * @ORM\OrderBy({"weight" = "ASC"})
     * 
     */
    private $pictures;
    
    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=200, unique=true, nullable=true)
     */
    private $slug;

    public function __construct() {
        $this->pictures = new ArrayCollection();
    }

     /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Gallery
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
    
    /**
     * Set date
     *
     * @param datetime $date
     *
     * @return Gallery
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }
    
    /**
     * Set dateto
     *
     * @param datetime $dateto
     *
     * @return Gallery
     */
    public function setDateTo($dateto)
    {
        $this->dateto = $dateto;

        return $this;
    }
    
    /**
     * Set description
     *
     * @param string $description
     *
     * @return Gallery
     */
    public function setDescription($description)
    {
        $this->description = $description;

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
     * Get date
     *
     * @return datetime
     */
    public function getDate()
    {
        return $this->date;
    }
    
    /**
     * Get dateto
     *
     * @return datetime
     */
    public function getDateto()
    {
        return $this->dateto;
    }
    
    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Set homeslide
     *
     * @param bool $homeslide
     *
     * @return bool
     */
    public function setHomeslide($homeslide)
    {
        $this->homeslide = $homeslide;

        return $this;
    }
    
    /**
     * Get homeslide
     *
     * @return bool
     */
    public function getHomeslide()
    {
        return $this->homeslide;
    }

    /**
     * Set pictures
     *
     * @param array $pictures
     *
     * @return Gallery
     */
    public function setPictures($pictures)
    {
        $this->pictures = $pictures;

        return $this;
    }

    /**
     * Get pictures
     *
     * @return array
     */
    public function getPictures()
    {
        return $this->pictures;
    }

    /**
     * Add picture
     *
     * @param \AppBundle\Entity\ResponsiveImage $picture
     *
     * @return Gallery
     */
    public function addPicture(\AppBundle\Entity\ResponsiveImage $picture)
    {
        $this->pictures[] = $picture;

        return $this;
    }

    /**
     * Remove picture
     *
     * @param \AppBundle\Entity\ResponsiveImage $picture
     */
    public function removePicture(\AppBundle\Entity\ResponsiveImage $picture)
    {
        $this->pictures->removeElement($picture);
    }
    
    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
