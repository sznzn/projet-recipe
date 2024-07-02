<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->setEntityLabelInPlural('Demandes de contact')
        ->setEntityLabelInSingular('Demande de contact')
        ->setPageTitle("index", "EasyRecipe - Administration des demandes de contact")
        ->setPaginatorPageSize(10)
        ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
        ;
        
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
            ->hideOnIndex(),
            TextField::new('email')
            ->setFormTypeOption('disabled', 'disabled'),
            TextField::new('fullName'),
            TextField::new('subject'),
            
            TextareaField::new('message'),
            // ->setFormType(CKEditorType::class)->setFormTypeOptions([
            //     'config_name' => 'main_config'
            // ]),
            DateTimeField::new('createdAt')
            ->setFormTypeOption('disabled', 'disabled'),
        ];
    }
    
}
