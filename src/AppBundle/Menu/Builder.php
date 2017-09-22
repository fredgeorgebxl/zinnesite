<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function adminMenu(FactoryInterface $factory, array $options)
    {
        $accessCheck = $this->container->get('security.authorization_checker');
        
        $menu = $factory->createItem('adminroot', array('childrenAttributes' => array('class' => 'nav navbar-nav')));

        $menu->addChild('events.event', array('route' => 'event_list'))
                ->setExtra('translation_domain', 'App');
        $menu->addChild('repertoire.repertoire', array('route' => 'repertoire_list'))
                ->setExtra('translation_domain', 'App');
        
        if($accessCheck->isGranted('ROLE_SUPER_ADMIN'))
            {
                $menu->addChild('users.users', array('route' => 'user_list'))
                    ->setExtra('translation_domain', 'App');
            }
        
        $menu->addChild('gallery.galleries', array('route' => 'gallery_list'))
                ->setExtra('translation_domain', 'App');
        
        return $menu;
    }
    
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', array('childrenAttributes' => array('class' => 'menu')));
        
        $menu->addChild('website.agenda', array('route' => 'agenda'))
                ->setExtra('translation_domain', 'App');
        $menu->addChild('website.repertoire', array('route' => 'repertoire'))
                ->setExtra('translation_domain', 'App');
        $menu->addChild('website.membres', array('route' => 'membres'))
                ->setExtra('translation_domain', 'App');
        $menu->addChild('website.photos', array('route' => 'photos'))
                ->setExtra('translation_domain', 'App');
        $menu->addChild('website.contact', array('route' => 'contact'))
                ->setExtra('translation_domain', 'App');
        
        return $menu;
    }
}