<?php

namespace App\Form;

use App\Entity\Addresses;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Sodium\add;

class AdressesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'attr'=>['class'=>'form-control']
            ])
            ->add('lastName',TextType::class,[
                'attr'=>['class'=>'form-control']
            ])
            ->add('city',TextType::class,[
                'attr'=>['class'=>'form-control']
            ])
            ->add('zipCode',TextType::class,[
                'attr'=>['class'=>'form-control']
            ])
            ->add('street',TextType::class,[
                'attr'=>['class'=>'form-control']
            ])
            ->add('phoneNumber',TextType::class,[
                'attr'=>['class'=>'form-control']
            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Add addresse',
                'attr' => ['class' => 'btn btn-primary']

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Addresses::class,
        ]);
    }
}
