<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Builder
{    
    private $factory;
    
    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authchecker)
    {
        $this->factory = $factory;
        $this->authchecker = $authchecker;
    }

    public function adminMenu(array $options)
    {
        
        $menu = $this->factory->createItem('adminroot', array('childrenAttributes' => array('class' => 'nav navbar-nav')));

        $menu->addChild('events.event', array('route' => 'event_list'))
                ->setExtra('translation_domain', 'App');
        $menu->addChild('repertoire.repertoire', array('route' => 'repertoire_list'))
                ->setExtra('translation_domain', 'App');
        
        if($this->authchecker->isGranted('ROLE_SUPER_ADMIN'))
            {
                $menu->addChild('users.users', array('route' => 'user_list'))
                    ->setExtra('translation_domain', 'App');
            }
        
        $menu->addChild('gallery.galleries', array('route' => 'gallery_list'))
                ->setExtra('translation_domain', 'App');
        
        return $menu;
    }
    
    public function mainMenu(array $options)
    {
        $menu = $this->factory->createItem('root', array('childrenAttributes' => array('class' => 'vertical large-horizontal menu')));
        
        $menu->addChild('website.agenda', array('route' => 'agenda'))
                ->setExtra('translation_domain', 'Front');
        $menu->addChild('website.repertoire', array('route' => 'repertoire'))
                ->setExtra('translation_domain', 'Front');
        $menu->addChild('website.membres', array('route' => 'membres'))
                ->setExtra('translation_domain', 'Front');
        $menu->addChild('website.photos', array('route' => 'photos'))
                ->setExtra('translation_domain', 'Front');
        //$menu->addChild('website.videos', array('uri' => 'https://www.youtube.com/channel/UC_tp-m7g0eqs0IwxhA1k9mA'))
        //        ->setLinkAttribute('target', '_blank')
        //        ->setExtra('translation_domain', 'Front');
        $menu->addChild('website.contact', array('route' => 'contact'))
                ->setExtra('translation_domain', 'Front');
        $menu->addChild('website.joinus', array('route' => 'joinus'))
                ->setExtra('translation_domain', 'Front');
        
        return $menu;
    }
}