<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Form\ImageType;
use AppBundle\Form\DateTimePickerType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType{
    
    private $seasons;
    
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefined('seasons-available');
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        foreach ($options['seasons-available'] as $season) {
            $this->seasons[$season] = $season;
        }
        
        $builder
            ->add('name', TextType::class, array('label' => 'events.name', 'translation_domain' => 'App'))
            ->add('date', DateTimePickerType::class, array('label' => 'events.date', 'translation_domain' => 'App'))
            ->add('description', CKEditorType::class, array('label' => 'events.description', 'translation_domain' => 'App'))
            ->add('season', ChoiceType::class, array('choices'  => $this->seasons, 'label' => 'events.season', 'translation_domain' => 'App'))
            ->add('picture', ImageType::class, array('label' => 'users.picture', 'translation_domain' => 'App'))
            ->add('save', SubmitType::class, array('label' => 'events.save', 'translation_domain' => 'App'));
    }
}