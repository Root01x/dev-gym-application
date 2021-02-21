<?php

session_start();

if ($_SESSION['rol'] != 5) {
    # code...

    header("location: ../");
}


 include "../conection.php";
 $usuario=$_SESSION['idUser'];

 $query2 = mysqli_query($conection,"SELECT c.nombre, c.apellidos, c.cedula, c.Correo, c.telefono FROM cliente c INNER JOIN usuario u on c.Correo=u.correo WHERE u.idusuario = $usuario");
       
 //$result = mysqli_num_rows($query);

 $data = mysqli_fetch_assoc($query2);
 
 $nombre = $data['nombre'];
 $apellidos   = $data['apellidos'];
 $cedula    = $data['cedula'];
 $email   = $data['Correo'];
 $telefono    = $data['telefono'];


?>


<!DOCTYPE html>
<html>
<head>
	<title>Pagos</title>
  <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.cs4s">
  
  <?php include "includes/scripts.php"?> 


</head>
<body>

<?php include "includes/header.php"?>

	
        
<br>
<br>
<br>
<br>
<div class="pagos">

  <div class="tabla_v">
      <table class="tbl_venta">
                <thead>
                    
                <h2 style="text-align: center; margin: 0px 0px 2px 0px;  color: #0a4661; padding: 10px;
                            font-size: 20pt;"><i class="fas fa-cart-arrow-down"></i> Detalle de Compra</h2>
                            <hr>
                              
                    <tr>
                        <th scope="col">Codigo</th>
                        <th scope="col" colspan="2">Descripcion</th>
                        
                        <th scope="col" class="textright">Precio</th>
                        
                        <th scope="col">Accion</th>
                    </tr>
                </thead>
                <tbody id="detalle_venta">
                    <!-- CONTINO AJAX  -->

                    

                </tbody>
                <tfoot id="detalle_totales">

                    <!-- CONTINO AJAX  -->
                  
                </tfoot>
      </table>
  </div>


  <div class="form_r">
  <h2 style="text-align: center;  color: #0a4661; padding: 10px;
                font-size: 20pt;"><i class="fas fa-file-invoice-dollar"></i> Datos de Facturacion</h2>
                <hr>
    <div class="form_registre">
        <form action="process.php" method="post"  id="payment-form">
            
            <div class="form-group">
                <label for="exampleInputEmail1">Nombre:</label>
                <input type="hidden" name="email" class="form-control" id="exampleInputEmail1" placeholder="Email" >
                <input type="text" value="<?php echo $nombre.' '.$apellidos?>" name="email" class="form-control" id="exampleInputEmail1" disabled >

              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">Identificacion:</label>
                <input type="text" value="<?php echo $cedula?>" name="total" required class="form-control" id="exampleInputPassword1" disabled>
              </div>
              <div class="form-group">
                <label for="exampleInputPassword2">Correo:</label>
                <input type="text" value="<?php echo $email?>" name="total" required class="form-control" id="exampleInputPassword1" disabled>
              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">Telefono:</label>
                <input type="text" value="<?php echo $telefono?>" name="total" required class="form-control" id="exampleInputPassword1" disabled>
              </div>

                <label for="card-element">Tarjeta de Credito o Debito:</label>
                <div id="card-element">
                  <!-- a Stripe Element will be inserted here. -->
                </div>
                <!-- Used to display form errors -->
                <div id="card-errors"></div>


            <input type="hidden" class="form-control" required name="paymethod_id" value="stripe">
            <br>
            <button class="btn_save"><i class="fas fa-id-card"></i> Procesar Pago</button>
        </form>
    </div>
  </div>

</div>









<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript" src="js/charge.js"></script>

<script type="text/javascript">

    $(document).ready(function(){
        var usuario_id = '<?php echo $_SESSION['idUser']; ?>';
        serchForDetalle(usuario_id);
    })

</script>

</body>
</html>