<?php

declare(strict_types=1);

namespace BMClientBundle\Client\Form;

use BMClientBundle\Client\Entity\Item;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'attr' => ['maxlength' => 255],
                'label' => 'Product name',
            ])
            ->add('amount', IntegerType::class, [
                'required' => true,
                'attr' => ['min' => 0],
                'label' => 'Amount',
            ])
            ->add('save', SubmitType::class, ['label' => 'Save']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
            'role' => ['ROLE_USER'],
        ]);
    }
}
