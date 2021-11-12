<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Language;
use App\Entity\LearningModule;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    private $em; 

    public function __construct( EntityManagerInterface $em )
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('avatar', FileType::class, [
//                'required' => false,
//                'attr'=>
//                    array(
//                        'placeholder'=>'Avatar',
//                        'class'=>'uploader'),
//                'mapped' => false,
//                'label' => false,
//                'constraints' => [new Image([
//                    'maxSize' => '5m',
//                    'mimeTypes' => [
//                        'image/jpeg',
//                        'image/png',
//                        'image/gif',
//                    ]
//                ])]
//            ])
            ->add('name', null, [
                'attr'=>
                    array(
                        'placeholder'=>'Name',
                        'class'=>'registerInput'),
                'label'=> false
            ])
            ->add('email', null, [
                'attr'=>
                    array(
                        'placeholder'=>'Email',
                        'class'=>'registerInput'),
                'label'=> false
            ])
            ->add('username', null, [
                'attr'=>
                    array(
                        'placeholder'=>'Username',
                        'class'=>'registerInput'),
                'label'=> false
            ])
            //->add('language', LanguageType::class, [])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr'=>
                    array(
                        'placeholder'=>'Password',
                        'class'=>'registerInput'),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'label'=> false
            ])
            ->add('passwordRepeat', PasswordType::class, [
                'mapped' => false,
                'attr'=>
                    array(
                        'placeholder'=>'Re-enter password',
                        'class'=>'registerInput'),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please repeat your password',
                    ]),
                ],
                'label'=> false
            ])
            ->add('formation', EntityType::class, [
            
                'class' => LearningModule::class,
                'choice_label' => function($LearningModule){
                    $defaultLang = $this->em->getRepository(Language::class)->findOneBy(['code' => 'en']);
                    return $LearningModule->getTitle($defaultLang);
                },
                'data_class' => null,
                'mapped'=>false,
            ])
            ->add('start', DateType::class, [
                'data_class' => null,
                'label' => 'Début de formation'
            ])
            ->add('end', DateType::class, [
                'data_class' => null,
                'label' => 'Fin de formation'

            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
