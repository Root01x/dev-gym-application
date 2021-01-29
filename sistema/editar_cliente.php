<?php

    session_start();
  /*  if ($_SESSION['rol'] != 1) {  ///validacion de roles
        # code...
        header("location: ./");
    }*/
    
 include "../conection.php";
 if(!empty($_POST))
 {
     $alert = '';
     if (empty($_POST['nombre']) || empty($_POST['cedula']) || empty($_POST['correo']) ||  empty($_POST['telefono']) || empty($_POST['direccion']) || empty($_POST['cod_tarjeta'] )) {

     $alert = '<p class="msg_error"> Todos los campos son obligatorios</p>';  # code...
    }
    else{
         
        $idcliente  = $_POST['id'];
        $cedula     = $_POST['cedula'];        
        $nombre     = $_POST['nombre'];
        $email      = $_POST['correo'];
        $telefono   = $_POST['telefono'];            
        $direccion  = $_POST['direccion'];
        $codTarjeta = $_POST['cod_tarjeta'];

        $result = 0;
        if (is_numeric($cedula) and $cedula !=0) {

           $query = mysqli_query($conection,"SELECT * FROM cliente
                                             WHERE (cedula = '$cedula'  OR cod_tarjeta = '$codTarjeta') AND idcliente != $idcliente");
           
           $result = mysqli_fetch_array($query);
           
           //$result = count($result);
           # code...
        }
        
        

         if($result > 0)
         {

            $alert = '<p class="msg_error"> LA CEDULA O CODIGO DE TARJETA YA ESTA EN USO</p>';

         }else{
            
            
               $sql_update = mysqli_query($conection,"UPDATE    cliente
                                                      SET       cedula = '$cedula', nombre = '$nombre', Correo = '$email', telefono='$telefono', direccion='$direccion', cod_tarjeta = '$codTarjeta'
                                                      WHERE     idcliente = $idcliente");
            
            
            
            if ($sql_update) {
                $alert = '<p class="msg_save"> Cliente Actualizado Correctamente</p>';
                # code...
            }
            else{
                $alert = '<p class="msg_error"> Error al Actualizar Cliente</p>';
            }
            }
         }
     
 
 }
//mostrar datos
if (empty($_REQUEST['id'])) {
    header('Location: lista_clientes.php');
    mysqli_close($conection);
    # code...
}

$idcliente = $_REQUEST['id'];
$sql = mysqli_query($conection,"SELECT *
                                FROM cliente
                                WHERE idcliente = $idcliente AND status = 1");
mysqli_close($conection);  

$result_sql = mysqli_num_rows($sql);
if($result_sql == 0){
    header('Location: lista_clientes.php');

}else{
    $option = '';
    while ($data = mysqli_fetch_array($sql)) {
            $idcliente     = $data['idcliente'];
            $cedula     = $data['cedula'];        
            $nombre     = $data['nombre'];
            $email      = $data['Correo'];
            $telefono   = $data['telefono'];            
            $direccion  = $data['direccion'];
            $codTarjeta  = $data['cod_tarjeta'];
            //$usuario_id = $data['idUser'];
        
       
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"?>
	<title>ACTUALIZAR CLIENTE</title>
</head>
<body>
 <?php include "includes/header.php"?>
	<section id="container">
		<div class="form_registre">

        <h1>ACTUALIZAR CLIENTE</h1>
        <hr>
        <div class="alert"> <?php  echo isset($alert) ? $alert : '';   ?></div>

        <form action="" method="post">
            <input type="hidden" name="id" id="id" value="<?php echo $idcliente?>">

            <label for="cedula">Cedula</label>
            <input type="text" name="cedula" id="cedula" placeholder="Numero de Cedula" value="<?php echo $cedula?>">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" placeholder="Nombre Completo" value="<?php echo $nombre?>">
            <label for="correo">Correo Electronico</label>
            <input type="email" name="correo" id="correo" placeholder="Correo Electronico" value="<?php echo $email?>">
            <label for="telefono">Telefono</label>
            <input type="number" name="telefono" id="telefono" placeholder="Numero de Telefono" value="<?php echo $telefono?>">
            <label for="direccion">Direccion</label>
            <input type="text" name="direccion" id="direccion" placeholder="Direccion Completa" value="<?php echo $direccion?>">

            <div class="wd30">
                    <label for="">Codigo Tarjeta</label>
                    <input type="text" name="cod_tarjeta" id="cod_tarjeta"  value="<?php echo $codTarjeta?>" required >
                   

                </div>

                <div class="wd30">
                
                <button class="btn_view " id="btn_refresh" type="button" style="margin: 25px auto 10px auto;" ><i class="fas fa-sync"></i></button>
                

                
                </div>
            
            <input type="submit" value="Actualizar Cliente" class="btn_save">
        
        </form>


        </div>
	</section>
<?php include "includes/footer.php"?>
</body>
</html>