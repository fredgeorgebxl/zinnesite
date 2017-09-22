<?php

namespace AppBundle\Entity;

use AppBundle\Entity\ContentBase;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="zsf_events")
 */
class Event extends ContentBase{
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=200)
     */
    private $name;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    private $description;
    
    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="datetime")
     */
    private $date;
    
     /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    private $season;
    
    /**
     * @ORM\OneToOne(targetEntity="ResponsiveImage", inversedBy="event", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="picture_id", referencedColumnName="id", nullable=true)
     * @Assert\Type(type="AppBundle\Entity\ResponsiveImage")
     * @Assert\Valid()
     */
    protected $picture;
    
    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=200, unique=true, nullable=true)
     */
    private $slug;

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
     *
     * @return Event
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
     * Set description
     *
     * @param string $description
     *
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Event
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set season
     *
     * @param string $season
     *
     * @return Event
     */
    public function setSeason($season)
    {
        $this->season = $season;

        return $this;
    }

    /**
     * Get season
     *
     * @return string
     */
    public function getSeason()
    {
        return $this->season;
    }
    
     /**
     * Set picture
     *
     * @param \AppBundle\Entity\ResponsiveImage $picture
     *
     * @return User
     */
    public function setPicture(\AppBundle\Entity\ResponsiveImage $picture = null)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return \AppBundle\Entity\ResponsiveImage
     */
    public function getPicture()
    {
        return $this->picture;
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
