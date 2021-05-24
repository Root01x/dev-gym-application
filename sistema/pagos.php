<?php

session_start();

if ($_SESSION['rol'] != 5) {
    # code...

    header("location: ../");
}


 include "../conection.php";
 $usuario=$_SESSION['idUser'];

 $query2 = mysqli_query($conection,"SELECT c.idcliente as idcliente, c.nombre, c.apellidos, c.cedula, c.Correo, c.telefono FROM cliente c INNER JOIN usuario u on c.token_user=u.token_user WHERE u.idusuario = $usuario");
       
 //$result = mysqli_num_rows($query);

 $data = mysqli_fetch_assoc($query2);
 
 $nombre        = $data['nombre'];
 $apellidos     = $data['apellidos'];
 $cedula        = $data['cedula']; 
 $email         = $data['Correo'];
 $telefono      = $data['telefono'];
 $codcliente    = $data['idcliente'];


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
                    
                <h2 style="text-align: left; margin: 0px 0px 2px 0px;  color: #0a4661; padding: 10px;
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
  <h2 style="text-align: left;  color: #0a4661; padding: 10px;
                font-size: 20pt;"><i class="fas fa-wallet"></i> Método de Pago</h2>
                <hr>
    <div class="form_registre24">
    <label for="tipoP">PAGAR CON:</label>
      <select name="metodo_pago" id="select" onclick="toggle(this)">
        <option value="value1" selected>Tarjeta de crédito o debito</option>
        <option value="value2" >Transferencia o deposito bancario</option>        
      </select>
    </div>
    <br>
  <hr>
    <h2 style="text-align: left;  color: #0a4661; padding: 10px;
                font-size: 20pt;"><i class="fas fa-file-invoice-dollar"></i> Detalles</h2>
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
   

    <div class="form_registre_deposito" >

       
        <form action="" method="post"  id="trans">
        <input type="hidden" id="idcliente" name="idcliente" value="<?php echo $codcliente?>" required>
          <div class="leyend" style="text-align: justify; font-family: 'arial';padding-top: 35px;color:#515e80;padding-bottom: 12px;">Para transferencias bancarias depositar a la cuenta 2206100219 del banco Pichincha a nombre de
          Gerardo Veliz con cedula de identidad  1314286947 e ingresar el boucher en el formulario siguiente.
          </div>

  
        <div class="form-group">
                <label for="exampleInputPassword1">Numero de Boucher:</label>
                <input type="number" value="" name="total" class="form-control" id="boucher" required >
              </div>
            <br>
            <button class="btn_save" id="btn_factura_deposito"><i class="fas fa-id-card"></i> Procesar Pago</button>
            

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
<script>
  function toggle(o) {
  var el=document.querySelector(".form_registre");
  var el2=document.querySelector(".form_registre_deposito")
  


  if (o.value=="value1") {

    el.style.display="block"; 
    el2.style.display="none";
    
  } 

  else if(o.value=="value2"){
    el.style.display="none"; 
    el2.style.display="block";
  }
  
  }
</script>

</body>
</html>