<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Form\ImageType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TextBlockType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, array('label' => 'textblock.name', 'translation_domain' => 'App'))
            ->add('content', CKEditorType::class, array('label' => 'textblock.content', 'translation_domain' => 'App'))
            ->add('picture', ImageType::class, array('label' => 'textblock.picture', 'translation_domain' => 'App'))
            ->add('save', SubmitType::class, array('label' => 'textblock.save', 'translation_domain' => 'App'));
    }
}