<?php

namespace AppBundle\Entity;

use AppBundle\Entity\ContentBase;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="zsf_textbox",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="name_unique", columns={"name"})
 *     })
 */
class TextBlock extends ContentBase
{
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
    private $content;
    
    /**
     * @ORM\OneToOne(targetEntity="ResponsiveImage", inversedBy="textbox", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="picture_id", referencedColumnName="id", nullable=true)
     * @Assert\Type(type="AppBundle\Entity\ResponsiveImage")
     * @Assert\Valid()
     */
    protected $picture;

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
     * @return Repertoire
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
     * Set content
     *
     * @param string $content
     *
     * @return TextBox
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
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
}
