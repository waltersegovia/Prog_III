<?php
class Usuario{
    #correo, clave y foto (guarda nombre del archivo).
    public $email;
    public $clave;
    public $tipo;
    public $imagenSubida;

    public function __construct($email,$clave,$tipo,$imagenSubida){
        $this->email = $email;
        $this->clave = $clave;
        $this->tipo = $tipo;
        $this->imagenSubida=$imagenSubida;
    }

    public function __toString(){
        return $this->email.'*'.$this->clave.'*'.$this->tipo.'*'.$this->imagenSubida;
    }
}
?>