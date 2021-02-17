<?php

	session_start();
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
			<h3>CURSO: ORACLE DB</h3>
			<button class="btn_cambiar">Cambiar</button>
		</div>
		
	</div>

	<div class="grid_datos">
		
		<br>
		<hr>
		<br>
		<input type="hidden" id="codigo_acceso" name="codigo_acceso" value="">
		<div class="cedula">PASE LA TARJETA DE ACCESO POR EL IDENTIFICADOR</div>	
		
		<br>
		<div class="nombre"></div>	
		
		<br>
		<div class="telefono"></div>	
		
		<br>
		<div class="codigo"></div>	
		
		<br>
	</div>
		
		<div class=grid_prueba>
			
				

                <div class="wd30">
                
                	
                
					<div class="alert alertErrorAcceso"></div>

                
                </div>
			
		</div>

	</section>
<?php include "includes/footer.php"?>
</body>
</html>