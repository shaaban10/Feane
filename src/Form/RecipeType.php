<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Ingredient;
use App\Entity\Ingriedients;
use App\Entity\Recipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class,[
                'attr' => ['class' => 'form-control']
            ])
            ->add('price',TextType::class,[
                'attr' => ['class' => 'form-control']
            ])
            ->add('discountPrice',TextType::class,[
                'required'=>false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('content', TextareaType::class,[
                'attr' => ['class' => 'form-control']
            ])
            ->add('category', EntityType::class, [
                'attr' => ['class' => 'form-control'],
                'class' => Category::class,
                'choice_label' => 'name',
                'required' => false,
                'expanded' => false,


            ])

            ->add('thumbnailFile', FileType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'label' => 'Add recipe photo',
                'constraints' => [
                    new Image()

            ]])
            ->add('ingredient', EntityType::class, [
                'attr' => ['class' => 'form-control'],
                'class' => Ingredient::class,
                'choice_label' => 'name',
                'required' => false,
                'multiple' => true,
                'expanded' => true,

            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Add recipe',
                'attr' => ['class' => 'btn btn-primary']

            ]);

        // Ajout d'un écouteur d'événement pour gérer les timestamps
        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'attachTimeStamp']);
    }

    public function attachTimeStamp(FormEvent $event): void
    {
        $data = $event->getData();
        if (!($data instanceof Recipe)) {
            return;
        }

        $data->setUpdatedAt(new \DateTimeImmutable());
        if (!$data->getId()) {
            $data->setCreatedAt(new \DateTimeImmutable());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
