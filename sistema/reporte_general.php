<?php

session_start();

if ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2) {
    # code...

    header("location: ../");
}



 include "../conection.php";
 $busqueda = '';
 $fecha_de = '';
 $fecha_a = '';

 if (isset($_REQUEST['busqueda']) && $_REQUEST['busqueda']=='') {
     header("location: buscar_transaccion.php");
 }

 if (isset($_REQUEST['fecha_de']) || isset($_REQUEST['fecha_a'])) {
     if ($_REQUEST['fecha_de'] == '' || $_REQUEST['fecha_a'] == '') {
         header("location: buscar_transaccion.php");
     }
 }

 if(!empty($_REQUEST['busqueda'])){
     if(!is_numeric($_REQUEST['busqueda'])){
         header("location: buscar_transaccion.php");
     }
     $busqueda = strtolower($_REQUEST['busqueda']);
     $where = "nofactura = $busqueda";
     $buscar = "busqueda = $busqueda";

 }
 if (!empty($_REQUEST['fecha_de']) && !empty($_REQUEST['fecha_a'])) {
     $fecha_de = $_REQUEST['fecha_de'];
     $fecha_a = $_REQUEST['fecha_a'];
     $buscar = '';

     if ($fecha_de > $fecha_a) {
         header("location: buscar_trasaccion.php");
         # code...
     }else if($fecha_de ==$fecha_a){
         $where = "fecha LIKE '$fecha_de%'";
         $buscar = "fecha_de = $fecha_de&fecha_a=$fecha_a";
     }else {
         $f_de = $fecha_de.' 00:00:00';
         $f_a = $fecha_a.' 23:59:59';
         $where = "fecha BETWEEN '$f_de' AND '$f_a'";
         $buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";
     }

 }
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"?>
	<title>REPORTE GENERAL</title>
</head>
<body>
 <?php include "includes/header.php"?>
	<section id="container">
		
        <h1>Reporte General de Transacciones</h1>

        <a href="" class="btn_new rep_total"><i class="fas fa-print"></i> IMPRIMIR</a>

    
    <div>
        <h5>Reporte por fecha</h5>
        <form action="reporte_por_fecha/generaFactura.php" method="get" class="form_search_date">
            <label for="">De: </label>
            <input type="date" name="fecha_de" id="fecha_de" value="<?php echo $fecha_de;?>" required>
            <label for="">A</label>
            <input type="date" name="fecha_a" id="fecha_a" value="<?php echo $fecha_a;?>"required>
            <button type="submit" class="btn_view">GENERAR</button>
        </form>
    </div>

       


       
	</section>
<?php include "includes/footer.php"?>
</body>
</html>