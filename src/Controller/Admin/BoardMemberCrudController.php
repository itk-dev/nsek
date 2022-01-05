<?php

namespace App\Controller\Admin;

use App\Entity\Board;
use App\Entity\BoardMember;
use App\Entity\BoardRole;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BoardMemberCrudController extends AbstractCrudController
{
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
        yield AssociationField::new('boards', 'Board')
            ->setRequired(true)
            ->formatValue(function ($value, BoardMember $member) {
                $roles = $member->getBoards()->map(function (Board $board) {
                    return $board->__toString();
                });

                return implode(', ', $roles->getValues());
            })
        ;
        yield AssociationField::new('municipality', 'Municipality')
            ->setRequired(true)
        ;
        yield AssociationField::new('boardRoles', 'BoardRole')
            ->setFormTypeOptions([
                'by_reference' => false,
            ])
            ->formatValue(function ($value, BoardMember $member) {
                $roles = $member->getBoardRoles()->map(function (BoardRole $boardRole) {
                    return $boardRole->__toString();
                });

                return implode(', ', $roles->getValues());
            })
        ;
    }
}
