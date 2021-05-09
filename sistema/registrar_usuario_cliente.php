<?php

//session_start();
session_start();
if ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2) {
    # code...

    header("location: ./");
}


 include "../conection.php";
 if(!empty($_POST))
 {
     $alert = '';
     if (empty($_POST['usuario'])  || empty($_POST['clave']) ||empty($_POST['nombre']) ||empty($_POST['apellidos']) || empty($_POST['cedula']) || empty($_POST['correo']) ||  empty($_POST['telefono']) || empty($_POST['direccion'])) {
       $alert = '<p class="msg_error"> Todos los campos son obligatorios</p>';  # code...
     }elseif($_POST['clave']!=$_POST['clave2']){
        $alert = '<p class="msg_error"> Las claves no coinciden</p>';
     }
     
     else {
         
       

        //$nombre = $_POST['nombre'];
       // $email = $_POST['correo'];
        $user = $_POST['usuario'];
        $clave = md5($_POST['clave']);
        $cedula     = $_POST['cedula']; 

        $nombre     = $_POST['nombre'];
        $apellido   = $_POST['apellidos'];
        $email      = $_POST['correo'];
        $telefono   = $_POST['telefono'];            
        $direccion  = $_POST['direccion'];
       
        $codTarjeta = $_POST['cod_tarjeta']; # code...
        
       
        $usuario_id = $_SESSION['idUser'];

         //$rol = $_POST['rol'];

         $result = 0;
         $result2 = 0;
         $result3 = 0;
         $result4 = 0;
         

         if (is_numeric($cedula)) 
         {
             

                

                $query      = mysqli_query($conection,"SELECT * FROM cliente WHERE cod_tarjeta = '$codTarjeta' and status=1 "); # code...
                $query2     = mysqli_query($conection,"SELECT * FROM cliente WHERE cedula = '$cedula'  and status=1"); # code...
               
                $query3     = mysqli_query($conection,"SELECT * FROM usuario WHERE correo = '$email' ");
                $query4     = mysqli_query($conection,"SELECT * FROM usuario WHERE usuario = '$user' ");

                if ($codTarjeta =='') {
                $result = 0;
                }else {
                $result     = mysqli_num_rows($query);
                }

                
                $result2    = mysqli_num_rows($query2);
                $result3    = mysqli_num_rows($query3);
                $result4    = mysqli_num_rows($query4);


                if ($result > 0 ) 
                {
                    $alert = '<p class="msg_error"> EL CODIGO DE TARJETA YA ESTA EN USO</p>';
                    
                }

                else if ($result2 > 0 ) 
                {
                    $alert = '<p class="msg_error"> EL NUMERO DE CEDULA YA ESTA EN USO</p>';
                    
                }

                else if ($result3 > 0 ) 
                {
                    $alert = '<p class="msg_error"> EL CORREO YA ESTA EN USO</p>';
                    
                }

                else if ($result4 > 0 ) 
                {
                    $alert = '<p class="msg_error"> EL NOMBRE USUARIO YA ESTA EN USO</p>';
                    
                }


                else if($result == 0 && $result2==0 && $result3 == 0 && $result4==0) {

                    $query_insert = mysqli_query($conection,"INSERT INTO cliente(cedula,nombre,apellidos,Correo,telefono,direccion,usuario_id,cod_tarjeta) 
                                                        VALUES('$cedula', '$nombre', '$apellido', '$email', '$telefono', '$direccion', $usuario_id,'$codTarjeta')") ;

                    $query_insert2 = mysqli_query($conection,"INSERT INTO usuario(nombre,correo,usuario,clave,rol) VALUES('$nombre $apellido', '$email', '$user', '$clave', 5)") ;


                    //print_r($query_insert);
                    if ($query_insert && $query_insert2) {
                        $alert = '<p class="msg_save"> Cliente guardado correctamente</p>';
                        # code...
                    }
                    else{
                        $alert = '<p class="msg_error"> Error al guardar usuario</p>';
                    }
                }
                else {
                    $alert = '<p class="msg_error"> ERROR INESPERADO CONSULTE CON UN ADMINISRADOR</p>';
                }
             

         }
         else {
            $alert = '<p class="msg_error"> FORMATO DE CEDULA INCORRECTO</p>';
         }
         

         
     }
 }


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"?>
	<title>CREAR CUENTA</title>
</head>
<body> 
<?php include "includes/header.php"?>

	<section id="container">
		<div class="form_registre">
 
        <h1><i class="fas fa-user-plus"></i> Crear Nuevo Cliente</h1>
        <hr>
        <div class="alert"> <?php  echo isset($alert) ? $alert : '';   ?></div>

        <form action="" method="post">

            <br>
            <input type="text" name="usuario" id="usuario" placeholder="Nombre de Usuario" autofocus>
            <br>
            <input type="password" name="clave" id="clave" placeholder="Ingrese Clave">
            <br>
            <input type="password" name="clave2" id="clave2" placeholder="Confirmar Clave">
            <br>
            <input type="number" name="cedula" id="cedula" placeholder="Numero de Cedula" required autofocus>
            <br>
            <input type="text" name="nombre" id="nombre" placeholder="Nombres" required pattern="[A-Za-z ]{2,100}" title="Solo se permiten letras!">
            <br>
            <input type="text" name="apellidos" id="apellidos" placeholder="Apellidos" required pattern="[A-Za-z ]{2,100}" title="Solo se permiten letras!">
            <br>
            <input type="email" name="correo" id="correo" placeholder="Correo Electronico" required>
            <br>
            <input type="number" name="telefono" id="telefono" placeholder="Numero de Telefono" required>
            <br>
            
            <input type="text" name="direccion" id="direccion" placeholder="Direccion Completa">
            <br>
            <div class="tarjeta_cod">
                    
                <div>
                    <input type="text" name="cod_tarjeta" id="cod_tarjeta" placeholder="Codigo Tarjeta "  >
                </div>
                <div class="cod_btn" >
                    <button class="btn_view2" id="btn_refresh" type="button" style="margin: 0;" ><i class="fas fa-sync"></i></button>
                   
                </div>
                
                
                    
            </div>
            

            <?php
                $query_rol = mysqli_query($conection, "SELECT * FROM rol");
                mysqli_close($conection);
                $result_rol = mysqli_num_rows($query_rol);
                           
            
            ?>
                      
            <button type="submit"  class="btn_save"><i class="fas fa-sd-card"></i> Guardar Cliente</button>              
            <div class="voler" style="text-align: center;">       
            </div>   
        
        </form>

        </div>
	</section>
<?php include "includes/footer.php"?>
</body>
</html>