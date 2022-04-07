<?php

namespace App\Controller\Admin;

use App\Entity\BoardMember;
use App\Entity\BoardRole;
use App\Form\Embeddable\AddressType;
use App\Form\Embeddable\IdentificationType;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Exception\EntityRemoveException;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

class BoardMemberCrudController extends AbstractCrudController
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public static function getEntityFqcn(): string
    {
        return BoardMember::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('new', 'Add boardmember')
            ->setEntityLabelInSingular('Boardmember')
            ->setEntityLabelInPlural('Boardmembers')
            ->setSearchFields(['name'])
            ->setDefaultSort(['name' => 'ASC'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'Name');
        yield Field::new('identification', 'Identification')
            ->setFormType(IdentificationType::class)
            ->onlyOnForms()
        ;
        yield Field::new('address', 'Address')
            ->setFormType(AddressType::class)
        ;
        yield AssociationField::new('boardRoles', 'BoardRole')
            ->setFormTypeOptions([
                'by_reference' => false,
            ])
            ->setQueryBuilder(function (QueryBuilder $queryBuilder) {
                return $queryBuilder->orderBy('entity.title', 'ASC');
            })
            ->formatValue(function ($value, BoardMember $member) {
                $roles = $member->getBoardRoles()->map(function (BoardRole $boardRole) {
                    return $boardRole->__toString();
                });

                return implode(', ', $roles->getValues());
            })
            ->setHelp($this->translator->trans('Remember that a boardmember may only have one role per board. ', [], 'admin'))
        ;
    }

    public function delete(AdminContext $context)
    {
        try {
            parent::delete($context);
        } catch (EntityRemoveException $e) {
            // Display flash message
            if (str_contains($e->getMessage(), 'ForeignKeyConstraintViolationException')) {
                $this->addFlash('danger', new TranslatableMessage('Could not delete, as one or more other entities is related to this entity.', [], 'admin'));
            } else {
                $this->addFlash('danger', new TranslatableMessage('Something went wrong when attempting to delete complaint category.', [], 'admin'));
            }
        }

        return $this->redirect($this->container->get(AdminUrlGenerator::class)->setAction(Action::INDEX)->unset(EA::ENTITY_ID)->generateUrl());
    }
}
