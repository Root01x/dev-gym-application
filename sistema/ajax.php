<?php
    include "../conection.php";
    session_start();
    //print_r($_POST);
    //exit;
    //extraer datos del evento
    if (!empty($_POST)) {
        # code...
    
    if ($_POST['action'] == 'infoEvent') 
    {
        $evento_id = $_POST['evento'];
        $query = mysqli_query($conection,"SELECT codevento,descripcion,capMax,precio FROM evento
                                          WHERE codevento = $evento_id AND status = 1");
        mysqli_close($conection);
        $result = mysqli_num_rows($query);
        if ($result > 0) {
            $data = mysqli_fetch_assoc($query);
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
            exit;# code...
        }
        echo "ERROR";
        exit;
        # code...
    }
    //eliminar datos del evento
    if ($_POST['action'] == 'delEvent') 
    {
        //echo "eliminar producto";
        if (empty($_POST['evento_id']) || !is_numeric($_POST['evento_id'])) {

            echo 'error';

        }
        
        else {
            # code...
        
                $idevento = $_POST['evento_id'];

                $query_delete =mysqli_query($conection,"UPDATE evento SET status = 0 WHERE codevento = $idevento");
                if ($query_delete) {
                    echo "ok";  
                }else {
                    echo "Error al eliminar!";
                }

            }

            echo 'error';
            exit;
        }
    //print_r($_POST);
    //buscar cliente
    if($_POST['action'] == 'searchCliente')
    {
       if (!empty($_POST['cliente'])) {
           $cedula = $_POST['cliente'];
           $query = mysqli_query($conection,"SELECT * FROM cliente WHERE cedula LIKE '$cedula' and status = 1 ");
           mysqli_close($conection);
           $result = mysqli_num_rows($query);
           $data = '';
           if ($result >0) {
                $data = mysqli_fetch_assoc($query);   
            # code...
           }else {
               $data = 0;
           }
           echo json_encode($data,JSON_UNESCAPED_UNICODE);
           # code...
       }
        exit;
    }
    //AGREGAR CLIENTE
    if($_POST['action'] == 'addCliente')
    {
        $cedula          =$_POST['nit_cliente'];
        $nombre          =$_POST['nom_cliente'];
        $email           =$_POST['correo_cliente'];
        $telefono        =$_POST['tel_cliente'];
        $direccion       =$_POST['dir_cliente'];
        $codTarjeta      =$_POST['cod_tarjeta'];
        $usuario_id      =$_SESSION['idUser'];

     

        if (!empty($cedula)|| !empty($codTarjeta)) {
            # code...
        
        if (is_numeric($cedula)) 
        {

            $query      = mysqli_query($conection,"SELECT * FROM cliente WHERE (cedula = '$cedula' or cod_tarjeta = '$codTarjeta') and status=1"); # code...
            $result     = mysqli_fetch_array($query);
        

        if ($result > 0) 
        {
            $msg = 'error';
            echo $msg;
            exit;
        
        }else {

        $query_insert = mysqli_query($conection,"INSERT INTO cliente(cedula,nombre,Correo,telefono,direccion,usuario_id,cod_tarjeta) 
                                                      VALUES('$cedula', '$nombre', '$email', '$telefono', '$direccion', '$usuario_id', '$codTarjeta')") ;
          
          if ($query_insert) {
              $codCliente = mysqli_insert_id($conection);
              $msg = $codCliente;
            }else {

                $msg = 'error';
                # code...
            }


        mysqli_close($conection);  
        echo $msg;
        exit;
        }}}
        echo 'error';
        exit;
        
    }

    //AGREGAR EVENTO A DETALL
    if($_POST['action'] == 'addEventoDetalle')
    {
        if (empty($_POST['evento'])||empty($_POST['cantidad'])) {
            
            echo "ERROR";
        }else{
            $codevento  = $_POST['evento'];
            $cantidad   = $_POST['cantidad'];
            $token      = md5($_SESSION['idUser']);

            $query_iva = mysqli_query($conection,"SELECT iva FROM configuracion");
            $result_iva = mysqli_num_rows($query_iva);

            $query_detalle_temp = mysqli_query($conection,"CALL add_detalle_temp($codevento,$cantidad,'$token')");
            $result = mysqli_num_rows($query_detalle_temp);

            $detalleTabla = '';
            $sub_total = 0;
            $iva       = 0;
            $total     = 0;
            $arrayData = array();

            if ($result > 0) {

                if ($result_iva > 0) {
                    $info_iva = mysqli_fetch_assoc($query_iva);
                    $iva = $info_iva['iva'];
                    # code...
                }
                    while ($data = mysqli_fetch_assoc($query_detalle_temp)) {
                        $precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
                        $sub_total = round($sub_total + $precioTotal, 2);
                        $total = round($total + $precioTotal, 2);

                        $detalleTabla .= '<tr>
                                            <td>'.$data['codevento'].'</td>
                                            <td colspan="2">'.$data['descripcion'].'</td>
                                            <td class="textcenter">'.$data['cantidad'].'</td>
                                            <td class="textright">'.$data['precio_venta'].'</td>
                                            <td class="textcenter">'.$precioTotal.'</td>
                                            <td class="">
                                                <a href="#" class="link_delete" onclick="event.preventDefault(); del_evento_detalle('.$data['correlativo'].','.$data['codevento'].');"><i class="far fa-trash-alt"></i> Eliminar</a>
                                            </td>
                                        </tr>';
                        # code...
                    }

                    $impuesto = round($sub_total * ($iva / 100), 2);
                    $tl_sniva = round($sub_total - $impuesto, 2);
                    $total = round($tl_sniva + $impuesto, 2);

                    $detalleTotales = ' 
                                        <tr>
                                            <td colspan="5" class="textright" style="text-align: right;font-size:20pt;color: #0a4661;">TOTAL : </td> 
                                            <td class="textright" style="text-align: center;font-size:20pt;color: #0a4661;">$ '.$total.' </td>        
                                        
                                        </tr>
                                        
                                        <tr>
                                            <td colspan="5" class="textright"><button class="btn_view view_factura" ><i class="fas fa-eye"></i> PROCESAR PAGO</button </td> 
                    
                         
                                        
                                        </tr>
                                        ';

                                        /*<tr>
                                            <td colspan="5" class="textright">SUBTOTAL $ </td> 
                                            <td class="textright">'.$tl_sniva.' </td>        
                                        
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="textright">SUBTOTAL $ </td> 
                                            <td class="textright">'.$tl_sniva.' </td>        
                                        
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="textright">IVA ('.$iva.'%) $ </td> 
                                            <td class="textright">'.$impuesto.'</td>        
                                        
                                        </tr> */

                   $arrayData['detalle'] = $detalleTabla;
                   $arrayData['totales'] = $detalleTotales;       
                   
                   echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
                # code...
            }else {
                echo "ERROR GARRAFAL";
            }

            mysqli_close($conection);


        }
        exit;
    }

    //extraer datos del detalle temp
    if($_POST['action'] == 'serchForDetalle')
    {
        if (empty($_POST['user'])) {
            
            echo "ERROR";
        }else{
           
            $token      = md5($_SESSION['idUser']);


            $query = mysqli_query($conection,"SELECT    tmp.correlativo,
                                                        tmp.token_user,
                                                        tmp.cantidad,
                                                        tmp.precio_venta,
                                                        p.codevento,
                                                        p.descripcion
                                              FROM detalle_temp tmp
                                              INNER JOIN evento p
                                              ON tmp.codevento = p.codevento 
                                              WHERE token_user = '$token' ");         
                                                        
                                                        
            $result = mysqli_num_rows($query);

            $query_iva = mysqli_query($conection,"SELECT iva FROM configuracion");
            $result_iva = mysqli_num_rows($query_iva);
            
           

            $detalleTabla = '';
            $sub_total = 0;
            $iva       = 0;
            $total     = 0;
            $arrayData = array();

            if ($result > 0) {

                if ($result_iva > 0) {
                    $info_iva = mysqli_fetch_assoc($query_iva);
                    $iva = $info_iva['iva'];
                    # code...
                }
                    while ($data = mysqli_fetch_assoc($query)) {
                       
                        $precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
                        $sub_total = round($sub_total + $precioTotal, 2);
                        $total = round($total + $precioTotal, 2);

                        $detalleTabla .= '<tr>
                                            <td>'.$data['codevento'].'</td>
                                            <td colspan="2">'.$data['descripcion'].'</td>
                                            <td class="textcenter">'.$data['cantidad'].'</td>
                                            <td class="textright">'.$data['precio_venta'].'</td>
                                            <td class="textcenter">'.$precioTotal.'</td>
                                            <td class="">
                                                <a href="#" class="link_delete" onclick="event.preventDefault(); del_evento_detalle('.$data['correlativo'].','.$data['codevento'].');"><i class="far fa-trash-alt"></i> Eliminar</a>
                                            </td>
                        
                                        </tr>';
                                          # code...
                    }
                    

                    $impuesto = round($sub_total * ($iva / 100), 2);
                    $tl_sniva = round($sub_total - $impuesto, 2);
                    $total = round($tl_sniva + $impuesto, 2);

                    $detalleTotales = ' 
                                        <tr>
                                            <td colspan="5" class="textright" style="text-align: right;font-size:20pt;color: #0a4661;">TOTAL : </td> 
                                            <td class="textright" style="text-align: center;font-size:20pt;color: #0a4661;">$ '.$total.' </td>        
                                        
                                        </tr>';

                                        /*<tr>
                                            <td colspan="5" class="textright">SUBTOTAL $ </td> 
                                            <td class="textright">'.$tl_sniva.' </td>        
                                        
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="textright">IVA ('.$iva.'%) $ </td> 
                                            <td class="textright">'.$impuesto.'</td>        
                                        
                                        </tr> */

                   $arrayData['detalle'] = $detalleTabla;
                   $arrayData['totales'] = $detalleTotales;       
                   
                   echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
                # code...
            }else {
                echo "ERROR GARRAFAL";
            }

            mysqli_close($conection);


        }
        exit;
    }

    //BORRAR DEL DETALLE
    if($_POST['action'] == 'delEventoDetalle')
    {
        if (empty($_POST['id_detalle'])) {
            
            echo "error";

        }else{
            $id_detalle = $_POST['id_detalle'];
            $token      = md5($_SESSION['idUser']);



            $query_iva = mysqli_query($conection,"SELECT iva FROM configuracion");
            $result_iva = mysqli_num_rows($query_iva);

            $query_detalle_temp = mysqli_query($conection,"CALL del_detalle_temp($id_detalle,'$token')");
            $result = mysqli_num_rows($query_detalle_temp);
            
           

            $detalleTabla = '';
            $sub_total = 0;
            $iva       = 0;
            $total     = 0;
            $arrayData = array();

            if ($result > 0) {

                if ($result_iva > 0) {
                    $info_iva = mysqli_fetch_assoc($query_iva);
                    $iva = $info_iva['iva'];
                    # code...
                }
                    while ($data = mysqli_fetch_assoc($query_detalle_temp)) {
                       
                        $precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
                        $sub_total = round($sub_total + $precioTotal, 2);
                        $total = round($total + $precioTotal, 2);

                        $detalleTabla .= '<tr>
                                            <td>'.$data['codevento'].'</td>
                                            <td colspan="2">'.$data['descripcion'].'</td>
                                            <td class="textcenter">'.$data['cantidad'].'</td>
                                            <td class="textright">'.$data['precio_venta'].'</td>
                                            <td class="textcenter">'.$precioTotal.'</td>
                                            <td class="">
                                                <a href="#" class="link_delete" onclick="event.preventDefault(); del_evento_detalle('.$data['correlativo'].','.$data['codevento'].');"><i class="far fa-trash-alt"></i> Eliminar</a>
                                            </td>
                        
                                        </tr>';
                                          # code...
                    }
                    

                    $impuesto = round($sub_total * ($iva / 100), 2);
                    $tl_sniva = round($sub_total - $impuesto, 2);
                    $total = round($tl_sniva + $impuesto, 2);

                    $detalleTotales = ' 
                                        <tr>
                                            <td colspan="5" class="textright" style="text-align: right;font-size:20pt;color: #0a4661;">TOTAL : </td> 
                                            <td class="textright" style="text-align: center;font-size:20pt;color: #0a4661;"">$ '.$total.' </td>        
                                        
                                        </tr>';


                                        /*<tr>
                                            <td colspan="5" class="textright">SUBTOTAL $ </td> 
                                            <td class="textright">'.$tl_sniva.' </td>        
                                        
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="textright">IVA ('.$iva.'%) $ </td> 
                                            <td class="textright">'.$impuesto.'</td>        
                                        
                                        </tr>*/

                   $arrayData['detalle'] = $detalleTabla;
                   $arrayData['totales'] = $detalleTotales;       
                   
                   echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
                # code...
            }else {
                echo "error";
            }

            mysqli_close($conection);


        }
        exit;
    }

    //anular venta
    if($_POST['action'] == 'anularVenta'){

        $token     = md5($_SESSION['idUser']);
        $query_del = mysqli_query($conection,"DELETE FROM detalle_temp WHERE token_user = '$token'");
        mysqli_close($conection);
        if ($query_del) {
            echo 'ok';
            # code...
        }else {
            echo 'error';
        }
        exit;
    }

    //procesar venta
    if($_POST['action'] == 'procesarVenta'){
       if (empty($_POST['codcliente'])) {
           //$codcliente =1;
           echo 'error';
           exit;

       }else {
            $codcliente = $_POST['codcliente'];
       }

       $token = md5($_SESSION['idUser']);
       $usuario = $_SESSION['idUser'];

       $query = mysqli_query($conection,"SELECT * FROM detalle_temp WHERE token_user = '$token'");
       $result = mysqli_num_rows($query);

       if ($result > 0) {
           $query_procesar = mysqli_query($conection,"CALL procesar_transaccion($usuario,$codcliente,'$token')");
           $result_detalle = mysqli_num_rows($query_procesar);

           if ($result_detalle > 0) {
               $data = mysqli_fetch_assoc($query_procesar);
               echo json_encode($data,JSON_UNESCAPED_UNICODE);
           }else {
                echo "error";
           }
       }else {
           echo "error";
       }
       mysqli_close($conection);
       exit;
    
    }


     //INFORMACCIO TRASNACCIONS
    if ($_POST['action'] == 'infoFactura') 
    {
       
         if (!empty($_POST['nofactura'])) {
 
             $nofactura = $_POST['nofactura'];
             $query = mysqli_query($conection,"SELECT * FROM factura WHERE nofactura = '$nofactura' AND status = 1");
             mysqli_close($conection);

             $result = mysqli_num_rows($query);
             if ($result > 0) {

                $data = mysqli_fetch_assoc($query);
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
                exit;
                 # code...
             }
             
         }
         echo 'error';
         exit;
    }

      //ANULAR venta
    if ($_POST['action'] == 'anularFactura') 
    {
          
          if (!empty($_POST['noFactura'])) {
  
              $nofactura = $_POST['noFactura'];

              $query_anular = mysqli_query($conection,"CALL anular_factura($nofactura)");
              mysqli_close($conection);
             
              $result = mysqli_num_rows($query_anular);

              if ($result > 0) {
 
                 $data = mysqli_fetch_assoc($query_anular);
                 echo json_encode($data,JSON_UNESCAPED_UNICODE);
                 exit;
                  # code...
              }
              
          }
          echo 'error';
          exit;
    }

     //refrescar codigo tarjeta
     if($_POST['action'] == 'obtenerCodigo')
     {
        
            //$cedula = $_POST['cliente'];
        
                $query = mysqli_query($conection,"SELECT uid, fecha FROM ingresos WHERE fecha BETWEEN DATE_SUB(NOW(),INTERVAL 10 second) AND NOW()");
                
                mysqli_close($conection);
                $result = mysqli_num_rows($query);
                $data = '';
                if ($result >0) {

                    $data = mysqli_fetch_assoc($query);
                    echo json_encode($data,JSON_UNESCAPED_UNICODE);                    
                    exit;   
                # code...
                }
                //$data = 0;
                echo 'error';
                exit;

         exit;      
        
     }

      //refrescar de acccesso
      if($_POST['action'] == 'obtenerCodigo2')
      {
         
             //$cedula = $_POST['cliente'];
         
                 $query = mysqli_query($conection,"SELECT uid, fecha FROM ingresos WHERE fecha BETWEEN DATE_SUB(NOW(),INTERVAL 10 second) AND NOW()");
                 
                 mysqli_close($conection);
                 $result = mysqli_num_rows($query);
                 $data = '';
                 if ($result >0) {
 
                     $data = mysqli_fetch_assoc($query);
                     echo json_encode($data,JSON_UNESCAPED_UNICODE);                    
                     exit;   
                 # code...
                 }
                 //$data = 0;
                 echo 'error';
                 exit;
 
          exit;      
         
      }
      //OBTNER DATOS DE LA GESTON DE ACESSO
      if($_POST['action'] == 'obtenerAcceso')
      {
         
             $codigo        = $_POST['codigo'];
             $codigoEvento  = $_POST['codigoEvento'];

                if (empty($codigo ) ) {
                    echo 'error3';
                    exit;                    
                }
         
                // $query = mysqli_query($conection,"SELECT uid, fecha, MAX(id) as id_tarjeta FROM ingresos WHERE fecha BETWEEN DATE_SUB(NOW(),INTERVAL 20 second) AND NOW()");
                   $query = mysqli_query($conection,"SELECT * FROM cliente WHERE cod_tarjeta = '$codigo'");

                   $query2 = mysqli_query($conection,"SELECT *
                                                     FROM cliente c 
                                                     INNER JOIN detallefactura d 
                                                     ON cod_cliente = idcliente
                                                     WHERE cod_tarjeta = '$codigo' 
                                                     and d.codevento = $codigoEvento");

                   
                 
                 mysqli_close($conection);
                 $result = mysqli_num_rows($query);
                 $result2 = mysqli_num_rows($query2);
                 $data = '';
                 
                 if ($result > 0 && $result2>0) { // codigo existe y cliente esta autorizado en el curso

                        //if ($result2 > 0) {
                            $myArr1 = array( "op" => "1");                           
                            $data = mysqli_fetch_assoc($query); 
                            $nuevo= array_merge($myArr1, $data); 
                            echo json_encode($nuevo,JSON_UNESCAPED_UNICODE);
                            exit; 
                            
                            # code...
                        /* }else if($result2 == 0){
                        
                            echo 'error4';
                            exit;

                         }*/

                            # code...
        

                 # code...
                 }
                 else if($result > 0 && $result2==0)  // codigo existe y cliente no esta autorizado en el curso

                 {
                            $myArr1 = array( "op" => "2");                           
                            $data = mysqli_fetch_assoc($query); 
                            $nuevo= array_merge($myArr1, $data); 
                            echo json_encode($nuevo,JSON_UNESCAPED_UNICODE);
                            exit; 
                            
                 }
                 
                 else {
                     //$data = 0;
                     echo 'error2';
                     exit;# code...
                 }
 
                 
                 # code...
         
                 exit;
         
      }
}
exit;

?>