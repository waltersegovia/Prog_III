<?php
    //$hora=date("H:m:s");
    //$fecha=date("Y-m-d");
//require __DIR__ . '/vendor/autoload.php';
require_once './clases/registro.php';
require_once './clases/files.php';
require_once './clases/token.php';
require_once './clases/response.php';
require_once './clases/auto.php';

// var_dump($_SERVER);
require __DIR__ . '/vendor/autoload.php';
$method = $_SERVER['REQUEST_METHOD'];
//$pathA = $_SERVER['PATH_INFO']?? ''; 
$path = $_SERVER['PATH_INFO']?? ''; 
//var_dump($pathA);


//***$arrayPath=explode("/", $pathA);
//****$path='/'.$arrayPath[1];

//print_r($path);
//***$pathPatente=$arrayPath[2]?? '';
//print_r($pathPatente);
//$token=$_SERVER['HTTP_TOKEN'];

//Archivos
$fileUsuario = './users.txt';
//$fileAuto = './autos.txt';
$fileServicio = './tiposServicio.txt';
$fileVehiculos='./vehiculos.txt';


  //1. (POST) registro. Registrar un usuario con los siguientes datos: email, tipo de usuario, password
  // y foto. El tipo de usuario puede ser admin o user. Validar que el mail no esté registrado
  // previamente.
switch ($path) {

    case '/registro':
        switch ($method) {
            case 'POST': 
                $clave = $_POST['password']?? '';
                $correo = $_POST['email']?? '';
                $tipo = $_POST['tipo']?? '';
                $archivo = $_FILES['imagen']; //?? '_'
               
                $response = new Response();
                 //Verifica si el usuario existe 
                if (isset($correo) && isset($clave) && isset($archivo) && isset($tipo)) {
                    $b=false;
                    $arrayJson=Archivos::leerJSON($fileUsuario); //leerJSON retorna una lista/array
                    //var_dump($arrayJson);

                    if ($arrayJson != null) {
                        foreach ($arrayJson as $value) {
                            if($value->email==$correo){
                                $b=true;
                                //echo"El usuario $value->mail ya existe";
                                $response->status = "Error en Alta ";
                                $response->data = "Usuario ya registrado"; 
                            }
                        }
 
                    } 
                    
                    if ($b == false) {
                        $nomImagen = explode('@', $correo);
                        $nombreImagen = $nomImagen[0];
                        
                        $imagenSubida = Archivos::carga($archivo,$nombreImagen);//retorna un string
                        if ($imagenSubida!='') {

                            $claveEncriptada=Archivos::encriptarClave($clave);
                            $usuario = new Usuario($correo,$claveEncriptada,$tipo,$imagenSubida);
                            Archivos::guardarJSON($fileUsuario,$usuario);
    
                            $response->data = "Se guardo el usuario";
                        }else {
                            $response->status = "No se puede dar Alta ";
                            $response->data = "Supera el tamaño permitido ";
                        }
                
                    }

                }
                else{
                     $response->data = 'Faltan datos';
                     $response->status = 'Error';    
                } 

                echo json_encode($response);
            break;      
        }
       
    break;


    /***************************************** */
    //2. (POST) login: Los usuarios deberán loguearse y se les devolverá un token con email y tipo en
    //  caso de estar registrados, caso contrario se informará el error.
    case '/login':    
        switch ($method) {
            case 'POST': 
                $correo = $_POST['email']?? '';
                $clave = $_POST['password']?? '';

                $response = new Response();

                if (isset($correo) && isset($clave)) {
                    //Verificar si existe usuario y clave son correctas
                    $b=false;            
                    $arrayJson=Archivos::leerJSON($fileUsuario);
                    var_dump($arrayJson);
                    if ($arrayJson != null) {
                        foreach ($arrayJson as $value) {
                            if($value->email == $correo){
                                $claveEncriptada=Archivos::encriptarClave($clave);
                                //echo $claveEncriptada.PHP_EOL;

                                if ($claveEncriptada == $value->clave) {
                                    $response->status = 'Verificacion exitosa'; 
                                    $b=true;
                                }
                                //$token = Token::generarToken($correo,$clave);
                                //$payload=Token::decodeToken($token);
                            }
                        }
                    }            
                   
                    if ($b == true) {
                        $token = Token::generarToken($correo,$clave);
                        $response->data = $token;
                    }else{
                        $response->status = 'No es un usuario valido';
                        $response->data = 'Error en Usuario o Clave';                       
                    }
                }
                else{
                   $response->data = ''; 
                   $response->status = 'fail';                                         
                } 
            break;      
        }

        echo json_encode($response);
    break;


    //3. (POST) vehiculo: Se deben guardar los siguientes datos: marca, modelo, patente y precio. Los
    //  datos se guardan en el archivo de texto vehiculos.xxx, tomando la patente como identificador(la
    //  patente no puede estar repetida).
    case '/vehiculo':
    $response = new Response();                  
    //Autenticación 
    //$headersEnvio = getallheaders(); //Leeo toda mi cabecera
    $headersEnvio = $_SERVER['HTTP_TOKEN'];

    //print_r($headersEnvio); 
    //die();
    $decoToken = Token::decodeToken($headersEnvio);
    
    //print_r($decoToken); 

    if ($decoToken == "Error token"){
        $response->data = "Error  JWT";
        $response->status = 'fail';
    } 
      
    else
    {    
        switch ($method) {
            case 'POST': //Parametros por el body del usuario Autenticar antes de todo
                
                    $patente = $_POST['patente']?? '';
                    $marca = $_POST['marca']?? '';
                    $modelo = $_POST['modelo']?? '';
                    $precio = $_POST['precio']?? '';

                    $decoded_array = (array) $decoToken;
                 ;

                    $correo = $decoded_array['email'];
       

                    //////***$fecha = date('l jS \of F Y h:i:s A');
                    //$hora=date("H:m:s");
                    //$fecha=date("Y-m-d");
                    if (isset($patente) && isset($marca) && isset($modelo) && isset($precio) ) {  
                            //$id = $nombre.time();
                            $b=false;
                               // Lee Jason            
                               $arrayJson=Archivos::leerJSON($fileVehiculos);
                               $ListaAutos = array();           
                               foreach ($arrayJson as $value) {
                               //array_push($ListaAutos,$value);

                                 if ($value->patente==$patente) {  
                                     $b=true;
                                     $response->data="El vehiculo ya existe";                              
                                   //$outAuto=new Auto($value->patente,$correo,$value->fecha_ingreso,$fecha,"");
                                   //$outAutosJson = Archivos::guardarJSON($fileOutAuto,$outAuto);
                                   //echo "Se encontro auto con patente: $patente".PHP_EOL;
                                 }
                               }

                             //$response->data = $outAuto;

                            if($b==false){
                                $inAuto = new Auto($patente, $modelo, $marca, $precio);

                                $autosJson = Archivos::guardarJSON($fileVehiculos,$inAuto);
                                $response->data = "Se Grabo Auto";

                            }

                                   
                    }
                    else{
                        $response->data = 'Faltan datos';
                        $response->status = 'fail';      
                    }   
            break;


        } 
    }   
    echo json_encode($response); 

  
    break;



      //4. (GET) patente/aaa123: Se ingresa marca, modelo o patente, si coincide con algún registro del
      //   archivo se retorna las ocurrencias, si no coincide se debe retornar “No existe xxx” (xxx es lo
      //   que se buscó) La búsqueda tiene que ser case insensitive
    case '/patente':
 
        echo "Path dentro de /retiro: $path".PHP_EOL;
        $response = new Response();                  
        $headersEnvio = $_SERVER['HTTP_TOKEN'];

        $decoToken = Token::decodeToken($headersEnvio);
    
        if ($decoToken == "Error token"){
            $response->data = "Error  JWT";
            $response->status = 'fail';
        } 

        else
        {    
            switch ($method) {
                case 'GET': //Parameter
                   
                        //*******$patente=$pathPatente;
                        $patente = $_POST['patente']?? '';
                        $marca = $_POST['marca']?? '';
                        $modelo = $_POST['modelo']?? '';

                  


                        $decoded_array = (array) $decoToken;
     
                        $correo = $decoded_array['email'];


                            // Lee Jason            
                            $arrayJson=Archivos::leerJSON($fileVehiculos);
                            $ListaAutos = array();  
                            if(isset($patente)){
                                foreach ($arrayJson as $value) {
                                    //array_push($ListaAutos,$value);
    
                                    if ($value->patente==$patente) {
                                        
                                        $outAuto=new Auto($value->patente,$value->modelo,$value->marca,$value->precio);
                                        //$outAutosJson = Archivos::guardarJSON($fileOutAuto,$outAuto);
                                        echo "Se encontro auto con patente: $patente".PHP_EOL;
                                        echo $outAuto;
                                    }
                                }
                                $response->data = $outAuto;
                                
                            }   
                            
                            if(isset($marca)){
                                foreach ($arrayJson as $value) {
                                    //array_push($ListaAutos,$value);
    
                                    if ($value->marca==$marca) {
                                        
                                        $outAuto=new Auto($value->patente,$value->modelo,$value->marca,$value->precio);
                                        //$outAutosJson = Archivos::guardarJSON($fileOutAuto,$outAuto);
                                       // echo "Se encontro auto con patente: $patente".PHP_EOL;
                                       echo $outAuto;
                                    }
                                }
                                $response->data = $outAuto;
                                
                            }

                            if(isset($marca)){
                                foreach ($arrayJson as $value) {
                                    //array_push($ListaAutos,$value);
    
                                    if ($value->modelo==$modelo) {
                                        
                                        $outAuto=new Auto($value->patente,$value->modelo,$value->marca,$value->precio);
                                        //$outAutosJson = Archivos::guardarJSON($fileOutAuto,$outAuto);
                                       // echo "Se encontro auto con patente: $patente".PHP_EOL;
                                       echo $outAuto;
                                    }
                                }
                                $response->data = $outAuto;
                                
                            }
                      
                        //break;   


                        
                break;
            } 
        }   
        echo json_encode($response); 



    break;

//5. (POST) servicio: Se recibe el nombre del servicio a realizar: id, tipo(de los 10.000km, 20.000km,
//50.000km), precio y demora, y se guardará en el archivo tiposServicio.xxx.
    case '/servicio':
        $response = new Response();                  
        $headersEnvio = $_SERVER['HTTP_TOKEN'];

        $decoToken = Token::decodeToken($headersEnvio);
    
        if ($decoToken == "Error token"){
            $response->data = "Error  JWT";
            $response->status = 'fail';

        }
        else{    
                switch ($method) {
                    case 'POST': 
                     $decoded_array = (array) $decoToken;
                     //print_r($decoded_array);
                       $id = $_POST['servicio'];
                       $tipo=$_POST['tipo'];
                       $precio=$_POST['precio'];
                       $demora=$_POST['demora'];


                        if (isset($id) && isset($tipo) && isset($precio) && isset($demora)) {
                            $correo = $decoded_array['email'];
                            //$clave = $decoded_array['password'];
                             
                      
                            $arrayJson=Archivos::leerJSON($fileVehiculos);
                            
                            if (isset($arrayJson)) {
                                foreach ($arrayJson as $value) {
                                    if( $value->email==$correo ){     
                                        $backupImagen=$value->imagenSubida;

                                        $autosServicios = Archivos::guardarJSON($fileVehiculos,$inAuto);
                                        Archivos::moverArchivo($imagen,$backupImagen);

                                        $response->data = "Se actualizo usuario"; 
                                    }
                                }
                            }                 
                        }else{
                           $response->data = 'Imagen vacia'; 
                           $response->status = 'Error';                         
                        }
                    break;  
                }
        }
        echo json_encode($response);        
    break;

    


}
?>