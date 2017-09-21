<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="zsf_fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=200)
     */
    protected $firstname;
    
    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=200)
     */
    protected $lastname;
    
    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    protected $phone;
    
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $email;
    
    /**
     * @Assert\NotBlank(groups={"registration"})
     */
    protected $plainPassword;
    
    /**
     * @ORM\Column(type="string", length=4)
     * @Assert\Choice({"chef", "teno", "bass", "sopr", "alto"})
     */
    protected $voice;

    /**
     * @ORM\OneToOne(targetEntity="ResponsiveImage", inversedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="picture_id", referencedColumnName="id", nullable=true)
     * @Assert\Type(type="AppBundle\Entity\ResponsiveImage")
     * @Assert\Valid()
     */
    protected $picture;

    public function __construct()
    {
        parent::__construct();
        
        $this->enabled = true;
        $this->roles = ['ROLE_USER'];
    }
    
    
    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
    
    /**
     * Set voice
     *
     * @param string $voice
     *
     * @return User
     */
    public function setVoice($voice)
    {
        $this->voice = $voice;

        return $this;
    }

    /**
     * Get voice
     *
     * @return string
     */
    public function getVoice()
    {
        return $this->voice;
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
