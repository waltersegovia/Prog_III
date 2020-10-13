<?php

class Auto{
  
    public $patente;
    public $modelo;
    public $marca;  
    public $precio;

    public function __construct($patente,$modelo,$marca,$precio){

        $this->patente = $patente;
        $this->modelo = $modelo;
        $this->marca = $marca;
        $this->importe = $precio;
    }

    public function __toString(){
        return $this->patente.'*'.$this->modelo.'*'.$this->marca.'*'.$this->precio;
    }
}

?>