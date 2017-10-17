<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
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
