<nav>
			<ul>
				<li><a href="index.php"><i class="fas fa-laptop-house"></i> Inicio</a></li>
				<?php
					if ($_SESSION['rol'] == 1) {
						# code...
					
				?>
				<li class="principal">
					<a href="#"><i class="fas fa-users"></i> Usuarios</a>
					<ul>
						<li><a href="registro_usuarios.php">Nuevo Usuario</a></li>
						<li><a href="lista_usuarios.php">Lista de Usuarios</a></li>
					</ul>
				</li>
				<?php }?>
				<?php
					if ($_SESSION['rol'] == 1) {
						# code...
					
				?>
				<li class="principal">

					<a href="#"><i class="far fa-address-card"></i> Clientes</a>
					<ul>
						<li><a href="registro_cliente.php">Nuevo Cliente</a></li>

				
						<li><a href="lista_clientes.php">Lista de Clientes</a></li>

				
					</ul>
				</li>
				<?php }?>
				<li class="principal">
					<a href="#"> <i class="far fa-clock"></i> Seminarios</a>
					<ul>
					<?php
					if ($_SESSION['rol'] == 1) {
						# code...
					
				?>
						<li><a href="registro_evento.php">Nuevo Seminario</a></li>

						<?php }?>
						<li><a href="lista_eventos.php">Lista de Seminarios</a></li>
					</ul>
				</li>
				
				<li class="principal">
					<a href="#"><i class="far fa-calendar-check"></i> Transacciones</a>
					<ul>

						<li><a href="Nueva_transaccion.php">Nueva Transacción</a></li>
						<?php
					if ($_SESSION['rol'] == 1) {
						# code...
					
				?>
						<li><a href="transacciones.php">Lista de Transacciones</a></li>
						<?php }?>
					</ul>
				</li>


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