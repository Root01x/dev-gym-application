<?php
     session_start();
     if ($_SESSION['rol'] != 1 AND $_SESSION['rol'] != 2) {  ///validacion de roles
         # code...
         header("location: ../");
     }
    include "../conection.php";

    if(!empty($_POST))
    {   

        if (empty($_POST['idcliente'])) {

            header("location: lista_clientes.php");
            exit;
            # code...
        }

        $idcliente = $_POST['idcliente'];
        //$query_delete =mysqli_query($conection,"DELETE FROM usuario WHERE idusuario = $idusuario");
        $query_delete =mysqli_query($conection,"UPDATE cliente SET status = 0 WHERE idcliente = $idcliente");
        if ($query_delete) {
            header("location: lista_clientes.php");   
        }else {
            echo "Error al eliminar!";
        }
    }



     if (empty($_REQUEST['id']) ) {
         header("location: lista_clientes.php");

     }else{
        
        $idcliente = $_REQUEST['id'];
        $query = mysqli_query($conection,"SELECT *
                                          FROM cliente                                          
                                          WHERE idcliente = $idcliente");
        $result = mysqli_num_rows($query);
        if ($result > 0) {
            while ($data = mysqli_fetch_array($query)) {
                $nombre     =$data['nombre'];
                $cedula    =$data['cedula'];
             }
           
        } else {
            
        header("location: lista_clientes.php");

        }


     }


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"?>
	<title>ELIMINAR CLIENTES</title>
</head>
<body>
 <?php include "includes/header.php"?>
	<section id="container">
		<div class="data_delete">
            <h2>
                ESTA SEGURO DE ELIMINAR ESTE REGISTRO?
            </h2>
            <p>Nombre: <span><?php echo $nombre;   ?></span> </p>
            <p>Cedula: <span><?php echo $cedula;   ?></span> </p>
           
           
            <form action="" method="post">
                <input type="hidden" name="idcliente" value="<?php echo $idcliente; ?>">
                <a href="lista_clientes.php" class="btn_cancel">Cancelar</a>
                <input type="submit" value="Aceptar" class="btn_ok">
            </form>
        
        </div>
	</section>
<?php include "includes/footer.php"?>
</body>
</html>