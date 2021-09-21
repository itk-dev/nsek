<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class PartyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('cpr')
            ->add('address')
            ->add('phoneNumber', IntegerType::class)
            ->add('journalNumber', null, [
                'required' => false,
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Tenant' => 'Tenant',
                    'Tenant (representative)' => 'Representative',
                    'Landlord' => 'Landlord',
                    'Landlord (administrator)' => 'Administrator',
                ],
                'translation_domain' => 'party',
            ]);
    }
}