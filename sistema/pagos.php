<?php

session_start();
/*
if ($_SESSION['rol'] != 1) {
    # code...

    header("location: ../");
}

*/

 include "../conection.php";

?>


<!DOCTYPE html>
<html>
<head>
	<title>Pagos</title>
  <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
  <?php include "includes/scripts.php"?> 


</head>
<body>

<?php include "includes/header.php"?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h1></h1>
			<br>
<br>
      <table class="table">
            <thead>
                
            <h2 style="text-align: center;  color: #0a4661; padding: 10px;
    font-size: 20pt;"><i class="fas fa-cart-arrow-down"></i> Detalle de Compra</h2>
                    
               
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


<form action="process.php" method="post"  id="payment-form">

<div class="form-group">
    <label for="exampleInputEmail1">Email de cliente</label>
    <input type="email" required name="email" class="form-control" id="exampleInputEmail1" placeholder="Email">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Total a pagar</label>
    <input type="text" name="total" required class="form-control" id="exampleInputPassword1" placeholder="Total a pagar">
  </div>

    <label for="card-element">Tarjeta de credito o debito</label>
    <div id="card-element">
      <!-- a Stripe Element will be inserted here. -->
    </div>
    <!-- Used to display form errors -->
    <div id="card-errors"></div>


<input type="hidden" class="form-control" required name="paymethod_id" value="stripe">
<br>
<button class="btn btn-primary btn-block">Procesar Pago</button>
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