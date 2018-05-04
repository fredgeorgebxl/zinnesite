<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewParameterType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array('label' => 'parameter.name', 'translation_domain' => 'App'));
        $builder->add('value', TextType::class, array('label' => 'parameter.value', 'translation_domain' => 'App'));
        $builder->add('type', ChoiceType::class, array('choices' => array('text' => 'text', 'bool' => 'bool'), 'label' => 'parameter.type', 'translation_domain' => 'App'));
        $builder->add('save', SubmitType::class, array('label' => 'parameter.save', 'translation_domain' => 'App'));
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Parameter',
        ));
    }
}
