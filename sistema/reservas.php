<?php

session_start();
if ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2) {  ///validacion de roles
    # code...
    header("location: ./");
}

 include "../conection.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"?>
	<title>Nueva Transaccion</title>
</head>
<body>
 <?php include "includes/header.php"?>
	<section id="container">
        <div class="title_page"><h1><i class="fas fa-check-circle"></i> Reservas de Usuarios</h1></div>

        <hr>
		
        <div class="datos_cliente">
            <div class="action_cliente">
                <h4>DATOS DEL CLIENTE:</h4>
                

            </div>
            <form name="form_new_cliente_venta" id="form_new_cliente_venta" class="datos" action="">
                
                <input type="hidden" name="action" value="addCliente">
                
                <input type="hidden" id="idcliente" name="idcliente" value="" required> 
                

                <div class="wd30">
                    <label for="">Cedula</label>
                    <input type="number" name="nit_cliente2" id="nit_cliente2">
                
                </div>
                <div class="wd30">
                    <label for="">Nombre</label>
                    <input type="text" name="nom_cliente" id="nom_cliente" disabled required>
                
                </div>
                <div class="wd30">
                    <label for="">Telefono</label>
                    <input type="number" name="tel_cliente" id="tel_cliente" disabled required>
                
                </div>
                <div class="wd100">
                    <label for="">Correo</label>
                    <input type="email" name="correo_cliente" id="correo_cliente" disabled required>
                
                </div>
                <div class="wd100">
                    <label for="">Direccion</label>
                    <input type="text" name="dir_cliente" id="dir_cliente" disabled required>
                
                </div>
 
                
                <div class="wd30">
                    <label for="">Codigo Tarjeta</label>
                    <input type="text" name="cod_tarjeta" id="cod_tarjeta" disabled required >
                   

                </div>

                <div class="wd30">
                
                <button class="btn_view inactive" id="btn_refresh" type="button" style="margin: 25px auto 10px auto;" disabled><i class="fas fa-sync"></i></button>
                <div class="alert alertErrorEvento"></div>

                
                </div>
                <div class="wd30">
                    
                </div>
                <div class="wd30">
                
                </div>
             
                
                <div id="div_registro_cliente2" class="wd100">
                    
                    
                
                </div>
                
                            
               

                
            </form>
        </div>
        <div class="datos_venta">
            <h4>DATOS DE TRANSACCION:</h4>
            <div class="datos">
                <div class="wd50">
                    <label for="">USUARIO A CARGO:</label>
                    <p><?php echo $_SESSION['nombre'];   ?></p>                 
                
                </div>
                <div class="wd50">
                    <label for="" Acciones></label>
                    <div id="acciones_venta">
                        <a href="#" class="btn_ok textcenter" id="btn_anular_venta_reservas"><i class="fas fa-ban"></i> Anular Reservas</a>
                        <a href="#" class="btn_ok textcenter" id="btn_factura_venta_reservas" ><i class="far fa-edit"></i> Procesar Reservas</a>
                       
                    </div>
                </div>
            </div>
        </div>
        
        <table class="tbl_venta">
          
            <thead>
                
                
                <tr>
                    <th>Codigo</th>
                    <th colspan="2">Descripcion</th>
                  
                    <th class="textright">Precio</th>
                   
                    
                </tr>
            </thead>
            <tbody id="detalle_venta2">
                <!-- CONTINO AJAX  -->

                

            </tbody>
            <tfoot id="detalle_totales2">

                <!-- CONTINO AJAX  -->
               
            </tfoot>
        </table>
	</section>
<?php include "includes/footer.php";

//$query2 = mysqli_query($conection,"SELECT u.idusuario as idusuario FROM cliente c INNER JOIN usuario u on c.Correo=u.correo WHERE c.idcliente = $cliente");




?>

</body>
</html>