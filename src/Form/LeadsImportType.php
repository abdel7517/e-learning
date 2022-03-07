<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LeadsImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fichier', FileType::class, [
                'mapped' => 'false',
                'attr' => ['class' => 'leads_field'],
            ])
            ->add('nom', ChoiceType::class, [
                'mapped' => 'false',
                'attr' => ['class' => 'leads_options'],
            ])
            ->add('tel', ChoiceType::class, [
                'mapped' => 'false',
                'attr' => ['class' => 'leads_options'],
            ])
            ->add('mail', ChoiceType::class, [
                'mapped' => 'false',
                'attr' => ['class' => 'leads_options'],
            ])
            ->add('formation', ChoiceType::class, [
                'mapped' => 'false',
                'attr' => ['class' => 'leads_options'],
            ])
            
            
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
