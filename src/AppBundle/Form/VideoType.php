<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class VideoType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('title', TextType::class, array('label' => 'video.title', 'translation_domain' => 'App'))
            ->add('link', TextType::class, array('label' => 'video.link', 'translation_domain' => 'App'))
            ->add('save', SubmitType::class, array('label' => 'video.save', 'translation_domain' => 'App'));
    }
}