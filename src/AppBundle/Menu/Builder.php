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
}