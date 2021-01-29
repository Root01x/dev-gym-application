<?php
 $host = 'localhost';
 $user = 'root';
 $password = '';
 $db = 'eventos';

 $conection = @mysqli_connect($host,$user,$password,$db);
 

 if(!$conection){
     echo "Something went wrong with the connection";

 } else{
     //echo "CONECCION EXITOSA";
 }
?>