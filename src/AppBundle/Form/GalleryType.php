<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class GalleryType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('title', TextType::class, array('label' => 'gallery.title', 'translation_domain' => 'App'))
            ->add('addimages', SubmitType::class, array('label' => 'events.addimages', 'translation_domain' => 'App'))
            ->add('edit_images', SubmitType::class, array('label' => 'gallery.edit_images', 'translation_domain' => 'App'))
            ->add('save', SubmitType::class, array('label' => 'events.save', 'translation_domain' => 'App'));
    }
}