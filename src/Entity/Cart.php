<?php
namespace App\Entity;

class Cart
{
    private $products;
    private $amount;
    private $nbprod;

    private const VAT = 0.2;

    public function __construct(){
        $this->products = [];
        $this->amount = 0;
        $this->nbprod = 0;
    }

    public function add($product)
    {
        if (!array_key_exists($product->getId(), $this->products)) {
            $this->products[$product->getId()] = ["product" => $product, "quantity" => 1];
        }
        else{
            $this->products[$product->getId()]["quantity"]++;
        }

        $this->calculate();
        
        return $this;
    }

    public function remove($product)
    {
       
        if($this->products[$product->getId()]['quantity'] > 1){
            $this->products[$product->getId()]["quantity"]--;
        }
        else{
            unset($this->products[$product->getId()]);
        }
        
        $this->calculate();
        
        return $this;        
    }
    public function delete($product)
    {
      
        unset($this->products[$product->getId()]);
        
        $this->calculate();
        
        return $this;        
    }

    public function clear($idprod = null)
    {
        if($idprod){
            unset($this->products[$idprod]);
        }
        else{
            $this->products = [];
            $this->calculate();
        }

        return $this;
    }

    public function calculate(){

        $this->nbprod = 0;
        $this->amount = 0;

        foreach($this->products as $line){
            $this->nbprod+= $line["quantity"];
            $this->amount+= $line["product"]->getPrice()*$line["quantity"];
        }
    }

    public function getNbprod(){
        return $this->nbprod;
    }

    public function getProducts(){
        return $this->products;
    }

    public function getAmount(){
        return $this->amount;
    }

    public function getTax(){
        return $this->amount/100*self::VAT;
    }

    public function getTotal(){
        return $this->amount/100   /*+ $this->getTax()  */;
    }
}