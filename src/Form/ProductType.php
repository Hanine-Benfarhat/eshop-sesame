<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter product name']
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Describe your product in detail']
            ])
            ->add('price', NumberType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => '0.00', 'step' => '0.01']
            ])
            ->add('quantity', NumberType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => '1']
            ])
            ->add('category', ChoiceType::class, [
                'attr' => ['class' => 'form-select'],
                'choices' => [
                    'Select Category' => '',
                    'Electronics' => 'Electronics',
                    'Fashion & Clothing' => 'Fashion',
                    'Home & Furniture' => 'Furniture',
                    'Sports & Outdoors' => 'Sports',
                    'Books & Media' => 'Books',
                    'Automotive' => 'Automotive',
                    'Beauty & Health' => 'Beauty',
                    'Toys & Games' => 'Toys',
                    'Other' => 'Other',
                ],
                'placeholder' => 'Choose a category',
            ])
            ->add('condition', ChoiceType::class, [
                'attr' => ['class' => 'form-select'],
                'choices' => [
                    'Brand New' => 'new',
                    'Like New' => 'like_new',
                    'Used - Good Condition' => 'used',
                ],
                'placeholder' => 'Select condition',
            ])
            ->add('location', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g., Tunis, Sfax, Sousse'],
                'required' => false,
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Product Image',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPG, PNG, or GIF)',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
