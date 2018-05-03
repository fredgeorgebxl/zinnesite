<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Form\ParameterType;

class ParameterListType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('parameters', CollectionType::class, array(
            'entry_type' => ParameterType::class,
            'entry_options' => array('label' => false),
            'allow_add' => true,
        ))
        ->add('save', SubmitType::class, array('label' => 'parameter.save', 'translation_domain' => 'App'));
    }
}
