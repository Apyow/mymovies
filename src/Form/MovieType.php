<?php

namespace App\Form;

use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('tmdbId', IntegerType::class, array(
                'label' => 'label_tmdb_id'
                ))
            ->add('title', TextType::class, array(
                'label' => 'label_title'
                ))
            ->add('releaseDate', TextType::class, array(
                'label' => 'label_release_date'
                ))
            ->add('duration', IntegerType::class, array(
                'label' => 'label_duration'
                ))
            ->add('genre', TextType::class, array(
                'label' => 'label_genre'
                ))
            ->add('synopsis', TextareaType::class, [
                'label' => 'label_synopsis',
                'attr' => ['rows' => 10]
            ])
            ->add('poster', TextType::class, [
                'label' => 'label_poster',
                'required'=>false
            ])
            ->add('rating', IntegerType::class, [
                'label' => 'label_rating',
                'attr' => [
                    'min' => 0,
                    'max' => 3,
                    'step' => 1
                ]
            ])
            ->add('status', ChoiceType::class, array(
                'label' => 'label_status',
                'choices' => [
                    'status_nothd' => 1,
                    'HD' => 2,
                    'status_todl' => 3,
                    'status_tosee' => 4,
                    'status_seen' => 5
                ])
                )
        ;
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
