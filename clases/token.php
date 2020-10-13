<?php
    use \Firebase\JWT\JWT;
    use \Firebase\JWT\SignatureInvalidException; //Esta Exception la tira en Postman y la uso para el try-catch

    class Token {   
    //var_dump($_SERVER['HTTP_TOKEN']); //Es una de las maneras de tomar el token
    
      static function generarToken($correo,$clave){
       $key = "example_key";
       $payload = array(
        "email" => $correo,
        "password" => $clave
       );
    
      $jwt = JWT::encode($payload, $key);
      
       return $jwt;
      }
    
      
      static function decodeToken($token){
        
        try {
         //$token=$_SERVER['HTTP_TOKEN'];
         $key = "example_key";
         //print_r($jwt);
    
         //$decoded = JWT::decode($jwt, $key, array('HS256'));
         $decoded = JWT::decode($token, $key, array('HS256'));
         //print_r($decoded);

         return $decoded;
        } 
        catch (\Throwable $th) {
         return "Error token";
        }
          

        /****************************** */
        
        /*try {
          // llave
          $key = "pro3-parcial";  
          $headers = $token; //getallheaders(); //Leeo toda mi cabecera
          $miToken = $headers["token"] ?? 'No mando Token'; // Si se genero el Token aca lo obtengo de la cabecera
          if (isset($miToken)){
              $decoded = JWT::decode($miToken, $key, array('HS256'));
              return $decoded;
          }
          
        } 
         catch (\Throwable $th) {
          //echo $th->getMessage() . " Error JWT";
          return "Error token";
         } */

      }
 

    }

?>