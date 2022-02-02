<?php

namespace App\Form;

use App\Entity\Board;
use App\Entity\Municipality;
use App\Repository\BoardRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CaseEntityType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator, private BoardRepository $boardRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('municipality', EntityType::class, [
            'class' => Municipality::class,
            'label' => $this->translator->trans('Municipality', [], 'case'),
            'placeholder' => $this->translator->trans('Choose a municipality', [], 'case'),
        ]);

        // Adds or hides form children based on provided municipality_id
        $addBoardFormModifier = function (FormInterface $form, $municipality_id = null) {
            if (null != $municipality_id) {
                // No municipality chosen - show board form child
                $boardChoices = $this->boardRepository->findBy(['municipality' => $municipality_id]);

                $form->add('board', EntityType::class, [
                    'class' => Board::class,
                    'choice_label' => 'name',
                    'choices' => $boardChoices,
                    'placeholder' => $this->translator->trans('Choose a board', [], 'case'),
                ]);
            } else {
                // No municipality chosen - hide board and caseEntity form children
                $form->add('board', HiddenType::class);
                $form->add('caseEntity', HiddenType::class, [
                    'mapped' => false,
                ]);
            }
        };

        // Base event listener for adding board child to form
        // @see https://symfony.com/doc/5.4/form/dynamic_form_modification.html#dynamic-generation-for-submitted-forms
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($addBoardFormModifier) {
                $municipality = $event->getData()->getMunicipality();
                $municipality_id = $municipality?->getId();
                $addBoardFormModifier($event->getForm(), $municipality_id);
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($addBoardFormModifier) {
                $data = $event->getData();
                $municipality_id = array_key_exists('municipality', $data) ? $data['municipality'] : null;
                $addBoardFormModifier($event->getForm(), $municipality_id);
            }
        );

        // Adds or hides caseEntity form child based on provided board_id
        $addCaseFormModifier = function (FormInterface $form, $board_id = null) {
            if (null != $board_id) {
                // Board chosen - show caseEntity form child
                $board = $this->boardRepository->findOneBy(['id' => $board_id]);
                $caseFormType = $board->getCaseFormType();

                $form->add('caseEntity', 'App\\Form\\'.$caseFormType, [
                    'mapped' => false,
                    'board' => $board,
                ]);
            } else {
                // No board chosen - hide caseEntity form child
                $form->add('caseEntity', HiddenType::class, [
                    'mapped' => false,
                ]);
            }
        };

        // Base event listener for adding caseEntity child to form
        // @see https://symfony.com/doc/5.4/form/dynamic_form_modification.html#dynamic-generation-for-submitted-forms
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($addCaseFormModifier) {
                $board = $event->getData()->getBoard();
                $board_id = $board?->getId();
                $addCaseFormModifier($event->getForm(), $board_id);
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($addCaseFormModifier) {
                $data = $event->getData();
                $board_id = array_key_exists('board', $data) ? $data['board'] : null;
                $addCaseFormModifier($event->getForm(), $board_id);
            }
        );
    }
}
