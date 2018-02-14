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
    
}
