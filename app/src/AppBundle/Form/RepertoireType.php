<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class RepertoireType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('title', TextType::class, array('label' => 'repertoire.title', 'translation_domain' => 'App'))
            ->add('description', TextareaType::class, array('label' => 'repertoire.description', 'translation_domain' => 'App'))
            ->add('active', CheckboxType::class, array('required' => FALSE, 'label'=> 'repertoire.current', 'translation_domain' => 'App'))
            ->add('save', SubmitType::class, array('label' => 'repertoire.save', 'translation_domain' => 'App'));
    }
}