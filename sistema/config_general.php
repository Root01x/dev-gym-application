<?php

session_start();
if ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2) {
    # code...

    header("location: ../");
}
 include "../conection.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"?>
	<title>Nueva Recarga</title>
</head>
<body>
 <?php include "includes/header.php"?>
	<section id="container">
        <div class="title_page"><h1><i class="fas fa-check-circle"></i> Configuracion General del Gym</h1></div>

        <hr>
		
        <div class="datos_cliente">
            <div class="action_cliente">
                <h4>DATOS DE GYM:</h4>
                
            </div>
            <form name="form_new_cliente_venta" id="form_new_cliente_venta" class="datos" action="">
                
                <input type="hidden" name="action" value="addCliente">
                <input type="hidden" id="idcliente" name="idcliente" value="" required> 
                <div class="wd30">
                    <label for="">Nombre del Gym :</label>
                    <input type="text" name="name_empresa" id="name_empresa">
                
                </div>
                <div class="wd30">
                    <label for="">Correo :</label>
                    <input type="email" name="email_empresa" id="email_empresa" >
                
                </div>
                <div class="wd30">
                    <label for="">Telefono :</label>
                    <input type="number" name="tel_empresa" id="tel_empresa"  >
                
                </div>
                
 
                <div class="wd30">
                    <label for="">Iva :</label>
                    <input type="number" name="iva_empresa" id="iva_empresa"  >
                   

                </div>

                <div class="wd30">
                    <label for="">RUC :</label>
                    <input type="text" name="ruc_empresa" id="ruc_empresa"  >
                   

                </div>

                <div class="wd30">
                    <label for="">Codigo Del gym :</label>
                    <input type="text" name="cod_empresa" id="cod_empresa" disabled required >
                   

                </div>

                <div class="wd100">
                    <label for="">Direccion :</label>
                    <input type="text" name="dir_empresa" id="dir_empresa"  required>
                
                </div>
                <div class="wd30">
                
                </div>
             
                
                <div id="div_registro_cliente2" class="wd100">
                    <button type="submit" class="btn_save" ><i class="far fa-save fa-lg"></i> Guardar</button>
                
                </div>
                
                            
        
            </form>
        </div>
       
       
        
                 

                

      
	</section>
<?php include "includes/footer.php"?>
<script type="text/javascript">

    $(document).ready(function(){
        
        var usuario_id = '<?php echo $_SESSION['idUser']; ?>';
        serchDatosEmpresa();
    })

</script>
</body>
</html>