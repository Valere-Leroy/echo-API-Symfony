<?php

namespace App\Form;

use App\Entity\EchoPost;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EchoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('isModerated')
            ->add('image')
            ->add('adress')
            ->add('latitude')
            ->add('longitude')
            ->add('tags', null, [
                'expanded' => true,
            ])
            ->add('post', SubmitType::class, [
                'label' => 'Edit this echo',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EchoPost::class,
        ]);
    }
}
