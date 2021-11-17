<?php

namespace App\Form;

use App\Entity\Board;
use App\Entity\ComplaintCategory;
use App\Entity\FenceReviewCase;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FenceReviewCaseType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

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
         * @var $board Board
         */
        $board = $options['board'];

        $builder
            ->add('complainant', TextType::class, [
            'label' => $this->translator->trans('Complainant', [], 'case'),
            ])
            ->add('complainantCPR', IntegerType::class, [
                'label' => $this->translator->trans('Complainant CPR', [], 'case'),
            ])
            ->add('complainantStreetNameAndNumber', TextType::class, [
                'label' => $this->translator->trans('Complainant street name and number', [], 'case'),
            ])
            ->add('complainantZip', TextType::class, [
                'label' => $this->translator->trans('Complainant postal code', [], 'case'),
            ])
            ->add('complainantCity', TextType::class, [
                'label' => $this->translator->trans('Complainant city', [], 'case'),
            ])
            ->add('complainantCadastralNumber', TextType::class, [
                'label' => $this->translator->trans('Complainant cadastral number', [], 'case'),
            ])
            ->add('accused', TextType::class, [
                'label' => $this->translator->trans('Accused', [], 'case'),
            ])
            ->add('accusedCPR', IntegerType::class, [
                'label' => $this->translator->trans('Accused CPR', [], 'case'),
            ])
            ->add('accusedStreetNameAndNumber', TextType::class, [
                'label' => $this->translator->trans('Accused street name and number', [], 'case'),
            ])
            ->add('accusedZip', TextType::class, [
                'label' => $this->translator->trans('Accused postal code', [], 'case'),
            ])
            ->add('accusedCity', TextType::class, [
                'label' => $this->translator->trans('Accused city', [], 'case'),
            ])
            ->add('accusedCadastralNumber', TextType::class, [
                'label' => $this->translator->trans('Accused cadastral number', [], 'case'),
            ])
            ->add('complaintCategory', EntityType::class, [
                'class' => ComplaintCategory::class,
                'choices' => $board->getComplaintCategories(),
                'label' => $this->translator->trans('Complaint category', [], 'case'),
                'placeholder' => $this->translator->trans('Select a complaint category', [], 'case'),
            ])
            ->add('conditions', TextareaType::class, [
                'label' => $this->translator->trans('Conditions', [], 'case'),
                'attr' => [
                    'rows' => 8,
                ],
            ])
            ->add('complainantClaim', TextareaType::class, [
                'label' => $this->translator->trans('Claim', [], 'case'),
                'attr' => [
                    'rows' => 6,
                ],
            ])
        ;
    }
}