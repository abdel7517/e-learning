<?php


namespace App\Form;


use App\Entity\QuizQuestionTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class QuizQuestionTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextareaType::class, [
                'attr' => ['placeholder' => 'Question', 'class' => 'js-richtexteditor-minimal'],
                'label' => false,
                'required' => false,
                'empty_data' => '',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => QuizQuestionTranslation::class,
        ]);
    }
}