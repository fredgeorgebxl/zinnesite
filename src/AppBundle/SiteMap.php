<?php

namespace AppBundle;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\RouterInterface;

/**
 * Description of SiteMap
 *
 * @author f.george
 */
class SiteMap {
    
    private $router;
    private $em;
    
    public function __construct(RouterInterface $router, ObjectManager $em) {
        $this->router = $router;
        $this->em = $em;
    }
    
    public function generateUrls(){
        $urls = array();
        
        // Events
        $events = $this->em->getRepository(\AppBundle\Entity\Event::class)->findAll();
        
        // Photo galleries
        $ph_repository = $this->em->getRepository(\AppBundle\Entity\Gallery::class);
        $qb = $ph_repository->createQueryBuilder('gal');
        $queryEvents = $qb
                ->where('gal.homeslide != 1')
                ->orWhere($qb->expr()->isNull('gal.homeslide'))
                ->orderBy('gal.datecreated', 'DESC')
                ->getQuery();
        $galleries = $queryEvents->getResult();
        
        foreach ($events as $event){
            $urls[] = ['loc' => $this->router->generate('event', ['slug'=> $event->getSlug()], TRUE)];
        }
        
        foreach ($galleries as $gallery){
            $urls[] = ['loc' => $this->router->generate('gallery', ['slug'=> $gallery->getSlug()], TRUE)];
        }
        
        return $urls;
    }
    
}
