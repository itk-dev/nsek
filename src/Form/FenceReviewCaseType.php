<?php

namespace App\Form;

use App\Entity\FenceReviewCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FenceReviewCaseType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FenceReviewCase::class,
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
            ->add('complainantAddress')
            ->add('complainantZip')
            ->add('complainantCPR')
            ->add('complainantCadastralNumber')
            ->add('accused')
            ->add('accusedAddress')
            ->add('accusedZip')
            ->add('accusedCPR')
            ->add('accusedCadastralNumber')
            ->add('conditions')
            ->add('complainantClaim')
            ->add('caseType', ChoiceType::class, [
                'choices' => [
                    $caseTypesAssociative,
                ],
            ])
            ->add('createCase', SubmitType::class, ['label' => 'Create case'])
        ;
    }
}
