<?php

namespace App\Form;

use App\Entity\RentBoardCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RentBoardCaseType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RentBoardCase::class,
            'board' => null,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var $caseTypes array
         */
        $caseTypes = $options['board']->getComplaintCategories()->toArray();

        $caseTypesAssociative = [];

        // Make array contain strings (names) rather then the objects
        foreach ($caseTypes as $value) {
            $name = $value->getName();
            $caseTypesAssociative[$name] = $name;
        }

        $builder
            ->add('complainant')
            ->add('complainantCPR')
            ->add('complainantPhone')
            ->add('complainantAddress')
            ->add('complainantZip')
            ->add('hasVacated')
            ->add('leaseAddress')
            ->add('leaseZip')
            ->add('leaseCity')
            ->add('previousCasesAtLease')
            ->add('caseType', ChoiceType::class, [
                'choices' => [
                    $caseTypesAssociative,
                ],
            ])
            ->add('feePaid')
            ->add('createCase', SubmitType::class, ['label' => 'Create case'])
        ;
    }
}
