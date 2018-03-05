<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use AppBundle\Form\DateTimePickerType;

class GalleryType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('title', TextType::class, array('label' => 'gallery.title', 'translation_domain' => 'App'))
            ->add('date', DateTimePickerType::class, array('label' => 'gallery.date', 'translation_domain' => 'App'))
            ->add('dateto', DateTimePickerType::class, array('label' => 'gallery.dateto', 'translation_domain' => 'App'))
            ->add('description', TextareaType::class, array('label' => 'gallery.description', 'translation_domain' => 'App'))
            ->add('addimages', SubmitType::class, array('label' => 'gallery.addimages', 'translation_domain' => 'App'))
            ->add('edit_images', SubmitType::class, array('label' => 'gallery.editimages', 'translation_domain' => 'App'))
            ->add('save', SubmitType::class, array('label' => 'events.save', 'translation_domain' => 'App'));
    }
}