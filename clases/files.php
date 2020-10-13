<?php


require_once './clases/registro.php';

class Archivos {

    public static function guardarTxt($archivo, $datos) {
        $file = fopen($archivo, 'a');
        $rta = fwrite($file, $datos .PHP_EOL);
        fclose($file);
        return $rta;
    }

    public static function leerTxt($archivo) {
        $file = fopen($archivo, 'r');
        $listaDatos = array();

        while (!feof($file)) {
            $linea = fgets($file);
            $datos = explode('*', $linea);

            if (count($datos)  > 1) {
                $nuevoCliente = new Usuario($datos[0], $datos[1], $datos[2], $datos[3], $datos[4]);
                array_push($listaDatos, $nuevoAuto);
            }
        }

        fclose($file);
        return $listaDatos;
    }

    public static function guardarSerialize($archivo, $datos) {
        $file = fopen($archivo, 'a');
        $rta = fwrite($file,serialize($datos) .PHP_EOL);
        fclose($file);
        return $rta;
    }

    public static function leerSerialize($ruta) {
        $lista = array();
        $file = fopen($ruta, 'r');
            while(!feof($file)){
                $objeto=unserialize(fgets($file));
                if($objeto!=null){
                    array_push($lista,$objeto);
                }
            }
        fclose($file);
        return $lista;
    }

    //Guardar Json
    public static function guardarJSON($ruta, $objeto)
    {
        switch ($ruta) {
            case './users.txt':
                $b=false;
                $array=Archivos::leerJSON($ruta);
                var_dump($array);
                if (isset($array)) {
                    foreach ($array as $value) {
                        var_dump($value->email);
                        var_dump($objeto->email);
                        if ($value->email==$objeto->email) {
                            $b=true;
                        }
                    }
        
                    if ($b==false) {
                        $ar=fopen($ruta,"w");
                        array_push($array,$objeto);
                        fwrite($ar,json_encode($array));
                        //echo"Se guardó correctamente.".PHP_EOL;
                        fclose($ar);
                    }
        
                }else {
                    $array2=array();
                    $ar=fopen($ruta,"w");
                    array_push($array2,$objeto);
                    fwrite($ar,json_encode($array2));
                    //echo"Se guardó correctamente.".PHP_EOL;
                    fclose($ar);
                }
                break;
            
            case './autos.txt':
                $b=false;
                $array=Archivos::leerJSON($ruta);
                var_dump($array);
                if (isset($array)) {
                    foreach ($array as $value) {
                        //var_dump($value->email);
                        //var_dump($objeto->email);
                        if ($value->patente==$objeto->patente) {
                            $b=true;
                        }
                    }
        
                    if ($b==false) {
                        $ar=fopen($ruta,"w");
                        array_push($array,$objeto);
                        fwrite($ar,json_encode($array));
                        //echo"Se guardó correctamente.".PHP_EOL;
                        fclose($ar);
                    }
        
                }else {
                    $array2=array();
                    $ar=fopen($ruta,"w");
                    array_push($array2,$objeto);
                    fwrite($ar,json_encode($array2));
                    //echo"Se guardó correctamente.".PHP_EOL;
                    fclose($ar);
                }
                break;

                case './outAutos.txt':
                    $b=false;
                    $array=Archivos::leerJSON($ruta);
                    var_dump($array);
                    if (isset($array)) {
                        foreach ($array as $value) {
                            //var_dump($value->email);
                            //var_dump($objeto->email);
                            if ($value->patente==$objeto->patente) {
                                $b=true;
                            }
                        }
            
                        if ($b==true) {
                            $ar=fopen($ruta,"w");
                            array_push($array,$objeto);
                            fwrite($ar,json_encode($array));
                            //echo"Se guardó correctamente.".PHP_EOL;
                            fclose($ar);
                        }else{
                            echo "El auto no existe con patente: $objeto->patente";
                        }
            
                    }else {
                        $array2=array();
                        $ar=fopen($ruta,"w");
                        array_push($array2,$objeto);
                        fwrite($ar,json_encode($array2));
                        //echo"Se guardó correctamente.".PHP_EOL;
                        fclose($ar);
                    }
                break;
        }
     

    }

    //Traer Json
    static public function leerJSON($ruta) {
        //$lista=array();
        if (file_exists($ruta)) {
            $ar=fopen($ruta,"r");
            $lista=json_decode(fgets($ar));
            fclose($ar);
            
            if (isset($lista)) {
                return $lista;
            }       
            else {
                //echo"La lista esta vacia.";
            }
    
        }else {
             //echo"El archivo no existe.";
          }

    }

    static function carga($archivoFoto,$nombre){ 

          
          if ($archivoFoto['size'] > 35840) {
          //if (filesize($archivoFoto) > 3584) {
            echo $archivoFoto['size']; 
            echo "El tamaño es mayor a 3.5MB";
            $b='';
        }else{
            $origen = $archivoFoto['tmp_name'];
            $extArray=explode('.',$archivoFoto['name']);//Genero el arrays y elijo la última ext
            $ext=count($extArray);
            $extension = $extArray[$ext-1]; //elijo la última ext
    
            $nombreAr = $nombre.time().'.'.$extension; 
            $folder = 'img/';
            $subido=move_uploaded_file($origen, $folder.$nombreAr);
    
            echo "Subido: $subido".PHP_EOL;
            $b=$nombreAr;
        }       
        return $b;
          
        }

    static public function moverArchivo($archivoFoto,$nombreImagen){
        
        if ($archivoFoto['size']  > 358400) {
            echo "El tamaño es mayor a 3.5MB";

        }else{

            $origen = "img/$nombreImagen";
            $copia = "backups/$nombreImagen";

            var_dump($copia);

            if (copy($origen, $copia)){
                unlink($origen);


            }

            var_dump($archivoFoto);

            $tmp_name = $archivoFoto['tmp_name'];
            $folder = 'img/';
            $subido=move_uploaded_file($tmp_name, $folder.$nombreImagen);

            echo "Removido: $subido ok ".$nombreImagen;
        }
    }

    static public function encriptarClave($clave){
	    $cv = "clave-1";
        $retornoclave = sha1($cv.$clave);
        return $retornoclave;

        
  
    }

    static function comparar($a, $b) {
        return strcmp($a->alias, $b->alias);

      /*usort($arr, 'comparar'); desde el index*/
    }

    static function diferenciaHora($hora,$hora2){
        //codogo base
        // Utiliza esta declaracion
        //date_default_timezone_set("America/Argentina"); 
        $cadena = strtotime($seguimiento->horaInicio);
        $cadena = date("H:i", $cadena);
        echo $cadena;
    
        $cadena2 = strtotime($seguimiento->horaTermino);
        $cadena2 = date("H:i", $cadena2);
        echo $cadena2;
    
        $res = abs($cadena2 - $cadena);
        echo $res;
        

    }
}


?>