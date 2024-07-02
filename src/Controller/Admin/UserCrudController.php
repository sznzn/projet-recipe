<?php

namespace App\Controller\Admin;

use App\Entity\User;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->setEntityLabelInPlural('Utilisateurs')
        ->setEntityLabelInSingular('Utilisateur')
        ->setPageTitle("index", "EasyRecipe - Administration")
        ->setPaginatorPageSize(10)
        ;
        
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
            ->hideOnForm(),
            TextField::new('email')
            ->setFormTypeOption('disabled', 'disabled'),
            TextField::new('fullName'),
            TextField::new('pseudo')
            ->hideOnIndex(),
            ArrayField::new('roles')
            ->hideOnIndex(),
            DateTimeField::new('createdAt')
            ->setFormTypeOption('disabled', 'disabled'),
        ];
    }

    
}
