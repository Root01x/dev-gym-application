<?php 
 
 	

 if (empty($_SESSION['active'])) {
     header('location: ../');
 }

?>

<header>
		<div class="header">
			
			<h1>Sistema de Control de Gym</h1>
			<div class="optionsBar">
				<p>Ecuador, <?php echo fechaC();  ?></p>
				<span>|</span>
				<span class="user"><?php echo $_SESSION['user'].' - '.$_SESSION['rol'] ?></span>
				<img class="photouser" src="img/user.png" alt="Usuario">
				<a href="salir.php"><img class="close" src="img/salir.png" alt="Salir del sistema" title="Salir"></a>
			</div>
		</div>
    <?php  include "nav.php" ?>
</header>
<div class="modal">
	<div class="bodyModal">
<!--
 		<form action="" method="post" name="form_add_event" id="form_add_event" onsubmit="event.preventDefault(); sendDataEvent();">
 			<h1><i class="fas fa-cubes" style="font-size: 45pt;"></i><br> Eliminar Evento</h1>
			 <h2 class="nameEvento"></h2><br>
			 <input type="number" name="canidad" id="txtCantidad" placeholder="evento" required><br>
 			<input type="text" name="cantidad" id="txtCantidad" placeholder="Precio de preba" required><br>
 			<input type="hidden" name="evento_id" id="evento_id" required>
			<input type="hidden" name="action" value="addEvent" required>
			<div class="alert alertAddEvento"><p></p></div>
			<button type="submit" class="btn_new">Eliminar</button>
			<a href="#" class="btn_ok closeModal" onclick="closeModal();">Cerrar</a>

		 </form>
-->
	</div>
</div>