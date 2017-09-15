<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Form\DateTimePickerType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class EventType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, array('label' => 'events.name', 'translation_domain' => 'App'))
            ->add('date', DateTimePickerType::class, array('label' => 'events.date', 'translation_domain' => 'App'))
            ->add('description', CKEditorType::class, array('label' => 'events.description', 'translation_domain' => 'App'))
            ->add('season', ChoiceType::class, array('choices'  => array('2016-17' => '2016-17'), 'label' => 'events.season', 'translation_domain' => 'App'))
            ->add('save', SubmitType::class, array('label' => 'events.save', 'translation_domain' => 'App'));
    }
}