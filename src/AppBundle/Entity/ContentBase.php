<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

abstract class ContentBase {
    /**
     * @ORM\Column(type="datetime")
     */
    protected $datecreated;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $datemodified;
    
    /**
     * @ORM\Column(type="boolean", nullable=TRUE)
     */
    protected $published;
    
    public function getDateCreated(){
        return $this->datecreated;
    }
    
    public function getDateModified(){
        return $this->datemodified;
    }
    
    public function getPublished(){
        return $this->published;
    }

    public function setDateCreated(\DateTime $date = null){
        $this->datecreated = $date;
    }
    
    public function setDateModified(\DateTime $date = null){
        $this->datemodified = $date;
    }
    
    public function setPublished($bool){
        $this->published = $bool;
    }
}
