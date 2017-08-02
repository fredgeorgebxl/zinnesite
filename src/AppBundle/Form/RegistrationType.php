<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Form\ImageType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, array('label' => 'users.firstname', 'translation_domain' => 'App'))
            ->add('lastname', TextType::class, array('label' => 'users.lastname', 'translation_domain' => 'App'))
            ->add('phone', TextType::class, array('label' => 'users.phone', 'translation_domain' => 'App'))
            ->add('voice', ChoiceType::class, array('label' => 'users.voice.voice', 'translation_domain' => 'App', 
                'choices'  => array(
                    'users.voice.soprane' => 'sopr',
                    'users.voice.alto' => 'alto', 
                    'users.voice.tenor' => 'teno',
                    'users.voice.basse' => 'bass',
                    'users.voice.chef' => 'chef'
                )))
            ->add('picture', ImageType::class, array('label' => 'users.picture', 'translation_domain' => 'App'))
            ->add('save', SubmitType::class, array('label' => 'users.save', 'translation_domain' => 'App'));
    }
    
    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }
    
    public function configureOptions(OptionsResolver $resolver)
{
    $resolver->setDefaults(array(
        'validation_groups' => array('User', 'registration'),
    ));
}
    
    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }
}
