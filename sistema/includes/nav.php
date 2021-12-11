<nav>
			<ul>
			<?php	if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {				?>

				<li><a href="index.php"><i class="fas fa-laptop-house"></i> Inicio</a></li>
				

				<li class="principal">
					<a href="#"><i class="fas fa-users"></i> Usuarios</a>
					<ul>
					<?php	if ($_SESSION['rol'] == 1) {				?>
						<li><a href="registro_usuarios.php">Nuevo Usuario</a></li>
						<?php }?>
						
						<li><a href="lista_usuarios.php">Lista de Usuarios</a></li>
					</ul>
				</li>
				<?php }?>
				<?php
					if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
						# code...
					
				?>
				<li class="principal">

					<a href="#"><i class="far fa-address-card"></i> Clientes</a>
					<ul>
						<li><a href="registrar_usuario_cliente.php">Nuevo Cliente</a></li>

				
						<li><a href="lista_clientes.php">Lista de Clientes</a></li>

				
					</ul>
				</li>
				<?php }?>
				<li class="principal">
					<a href="#"> <i class="fas fa-cogs"></i> Configuracion</a>
					<ul>
					<?php
					if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
						# code...
					
				?>
						<li><a href="registro_evento.php">Configuracion General</a></li>

						<?php }?>
						<li><a href="lista_eventos.php">Configuracion Precios</a></li>
					</ul>
				</li>
				<?php if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) { ?>
				<li class="principal">
					<a href="#"><i class="far fa-calendar-check"></i> Transacciones</a>
					<ul>

						<li><a href="recargar_tarjeta.php">Recargar Tarjeta</a></li>
						<!-- <li><a href="Nueva_transaccion.php">Nueva Transacción</a></li>
						<li><a href="reservas.php">Procesar Reservas</a></li>
						
					
						
					
				
						<li><a href="transacciones.php">Lista de Transacciones</a></li> -->
						
					</ul>
				</li>
				<?php }?>

				<?php if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) { ?>
				<li class="principal">
					<a href="#"><i class="far fa-address-book"></i> Reportes</a>
					<ul>

						<li><a href="lista_eventos.php">Reportes Seminarios</a></li>
						<li><a href="reporte_general.php">Reporte General</a></li>
						
						
					</ul>
					
				</li>
				<?php }?>
					
				<?php if ($_SESSION['rol'] == 5) { ?>
				<li class="principal">
					<a href="#"><i class="far fa-calendar-check"></i> Pagos</a>
					<ul>

						<li><a href="pagos.php">Procesar Pago</a></li>
						
					
						
						
					</ul>
				</li>
				<?php }?>

				<!--
				
				<li class="principal">
					<a href="#">Facturas</a>
					<ul>
						<li><a href="#">Nuevo Factura</a></li>
						<li><a href="#">Facturas</a></li>
					</ul>
				</li>
					-->

			</ul>
		</nav>