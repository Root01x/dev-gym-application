<?php

	session_start();

	
if ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2) {
    # code...

    header("location: ./lista_eventos.php");
}

	include "../conection.php";
	
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"?>
	<title>SISTEMA EVENTOS</title>
</head>
<body>
 <?php include "includes/header.php"?>
	<section id="container">
		
	<div class="acceso">

		<div class="grid_1">
			<h1>Bienvenido al Sistema</h1>
			<h2>GESTIÓN DE ACCESO</h2>
		</div>
		<div class="grid_2">
			<h3>CURSO: <span id="txt_descripcion">-</span></h3>
			<input type="text" name="cod_evento_acesso" id="cod_evento_acesso" placeholder="CODIGO SEMINARIO">
			<input type="hidden" id="seminario" name="seminario" value="11">
			
		</div>
		
	</div>

	<div class="grid_datos">
		
		<br>
		<hr>
		<br>
		<input type="hidden" id="codigo_acceso" name="codigo_acceso" value="">
		
		
		<div class="datos_venta">

			
			<div class="datos">
				<div class="cedula" style="text-aling:center">PASE LA TARJETA DE ACCESO POR EL IDENTIFICADOR</div>	
				<br>
				<div class="nombre"></div>	
				
				<br>
				<div class="telefono"></div>	
				
				<br>
				<div class="codigo"></div>	
				
				<br>
				
			</div>
		</div>
	</div>
		
		<div class=grid_prueba style="text-align: center; font-weight: bold;">
			
				

                <div class="wd100">
                
                	
                
					<div class="alertErrorAcceso"></div>

                
                </div>
			
		</div>

	</section>
<?php include "includes/footer.php"?>
</body>
</html>