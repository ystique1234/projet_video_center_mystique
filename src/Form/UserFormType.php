<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints as Assert;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstname', TextType::class, [
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le prénom ne doit pas être vide',
                    'groups' => ['profile']
                ]),
                new Assert\Length([
                    'min' => 3,
                    'minMessage' => 'Le prénom doit faire au moins {{ limit }} caractères',
                    'groups' => ['profile']
                ]),
            ],
        ])
        ->add('lastname', TextType::class, [
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le nom ne doit pas être vide'
                   
                ]),
                new Assert\Length([
                    'min' => 3,
                    'minMessage' => 'Le nom doit faire au moins {{ limit }} caractères'
                ]),
            ],
        ])
            // ->add('firstname',TextType::class,[
            //     'label'=>'userForm.firstname'
            // ])
            // ->add('lastname',TextType::class,[
            //     'label'=>'userForm.lastname'
            //     ])

            ->add('imageFile', VichImageType::class, [
                'required'=>false,
                'allow_delete'=>true,
                'delete_label'=> 'Delete profile image',
                'download_uri'=>true,
                'image_uri'=>true,
                'asset_helper'=>true,
                'imagine_pattern'=>'avatar_thumbnail'
            ])
           ;
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
