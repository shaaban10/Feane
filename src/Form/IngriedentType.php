<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use function Sodium\add;

class IngriedentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'attr'=>['class'=>'form-control'],
             'required' => true,

    ])
            ->add('slug',TextType::class,[

                'required' => true,
                'attr'=>['class'=>'form-control'],
            ])

            ->add('thumbnailFile', FileType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'label' => 'Add recipe photo',
                'constraints' => [
                    new Image()]])
        ->add('submit', SubmitType::class,[
            'label' => 'Add ingredient',
            'attr' => ['class' => 'btn btn-primary'],
    ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
        ]);
    }
}
