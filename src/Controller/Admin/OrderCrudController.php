<?php

namespace App\Controller\Admin;

use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class OrderCrudController extends AbstractCrudController
{
    private $entityManager;
    private $crudUrlGenerator;
    public function __construct(EntityManagerInterface $entityManager, CrudUrlGenerator $crudUrlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->crudUrlGenerator = $crudUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }
    public function configureActions(Actions $actions): Actions
    {   
        $updatePreparation = Action::NEW('updatePreparation', 'Préparation en cours', "fas fa-truck")->linkToCrudAction('updatePreparation');
        $updateDelivery = Action::NEW('updateDelivery', 'Livraison en cours', "fas fa-truck-loading")->linkToCrudAction('updateDelivery');

        return $actions
                ->add('detail', $updatePreparation)
                ->add('detail', $updateDelivery)
                ->add('index', 'detail')
                ;
    }
    public function updatePreparation(AdminContext $context){
        $order = $context->getEntity()->getInstance();
        
            if ($order->getState() == 1) {
                
                $order->setState(2);
                $this->entityManager->flush();
        
                $this->addFlash('notice', "<p style='color:green;'> <strong>La commande ".$order->getReference()." a bien été mise en cours de préparation </strong>");
        
            }elseif ($order->getState() == 0) {
                $this->addFlash('notice', "<p style='color:red;'> <strong>La commande ".$order->getReference()." n'a pas encore été payée </strong>");
            }
            else {
                $this->addFlash('notice', "<p style='color:red;'> <strong>La commande ".$order->getReference()." a déja été mise en cours de préparation </strong>");
            }
            $url = $this->get(AdminUrlGenerator::class);
     
            return $this->redirect($url->setController(OrderCrudController::class)->setAction('index')->generateUrl());
        

    }
    public function updateDelivery(AdminContext $context){
        $order = $context->getEntity()->getInstance();
        
            if ($order->getState() == 2 ) {
                
                $order->setState(3);
                $this->entityManager->flush();
        
                $this->addFlash('notice', "<p style='color:blue;'> <strong>La commande ".$order->getReference()." a bien été mise en cours de livraison </strong>");
        
                $mail = new Mail();
                $content = "Bonjour ".$order->getUser()->getFirstname()."<br>Votre commande n° ".$order->getReference()." est en cours de livraison à l'adresse suivante : <br><br>".$order->getDelivery()."<br>Lorem ipsum, dolor sit amet consectetur adipisicing elit. 
                ipsum laudantium rem soluta eius!";
                $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname(),'Votre commande LeBipBip',$content);
            }elseif ($order->getState() == 0) {
                $this->addFlash('notice', "<p style='color:red;'> <strong>La commande ".$order->getReference()." n'a pas encore été payée </strong>");
            }
            else {
                $this->addFlash('notice', "<p style='color:red;'> <strong>La commande ".$order->getReference()." a déja été mise en cours de livraison </strong>");
            }
            
            $url = $this->get(AdminUrlGenerator::class);
            return $this->redirect($url->setController(OrderCrudController::class)->setAction('index')->generateUrl());
        

    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt', 'Passée le :'),
            TextField::new('user.getFullName', 'Utilisateur'),
            TextEditorField::new('delivery', 'Adresse de livraison')->formatValue(function ($value) { return $value; })->onlyOnDetail(),
            MoneyField::new('getTotal', 'TOTAL')->setCurrency('EUR'),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new('carrierPrice', 'Frais de port')->setCurrency('EUR'),
            ChoiceField::new('state')->setChoices([
                'Non payée' => 0,
                'Payée' => 1,
                'Préparation en cours' => 2,
                'Livraison en cours' => 3
            ]),
            ArrayField::new('orderDetails', 'Produits achetés')->hideOnIndex()
        ];
    }
    
}
