<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class, [
                'label' =>'videoForm.title'
            ])
            ->add('videoLink', TextType::class, [
                'label' => 'Lien de la vidéo (YouTube, Vimeo, etc.) : ',
            ])

            ->add('isPremium', CheckboxType::class, [
                'label'    => 'Premium Video',
                'required' => false,
            ])
            ->add('description',TextareaType::class, [
                'label' =>'videoForm.description'
            ])
            // ->add('createdAt', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('updatedAt', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('save',SubmitType::class, [
                'label' => 'videoFom.save'
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->autoSlug(...));
        ;
    }

    public function autoSlug(PreSubmitEvent $event): void {
        $data = $event->getData();
        if (empty($data['title'])) {
            $slugger = new AsciiSlugger();
            $data ['slug'] = strtolower($slugger->slug($data['title']));
            $event->setData($data);
        }

    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
