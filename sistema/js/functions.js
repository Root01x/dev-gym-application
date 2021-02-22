//alert("hola mundo");
$(document).ready(function(){  

    //--------------------- SELECCIONAR FOTO PRODUCTO ---------------------
    $("#foto").on("change",function(){
    	var uploadFoto = document.getElementById("foto").value;
        var foto       = document.getElementById("foto").files;
        var nav = window.URL || window.webkitURL;
        var contactAlert = document.getElementById('form_alert');
        
            if(uploadFoto !='')
            {
                var type = foto[0].type;
                var name = foto[0].name;
                if(type != 'image/jpeg' && type != 'image/jpg' && type != 'image/png')
                {
                    contactAlert.innerHTML = '<p class="errorArchivo">El archivo no es válido.</p>';                        
                    $("#img").remove();
                    $(".delPhoto").addClass('notBlock');
                    $('#foto').val('');
                    return false;
                }else{  
                        contactAlert.innerHTML='';
                        $("#img").remove();
                        $(".delPhoto").removeClass('notBlock');
                        var objeto_url = nav.createObjectURL(this.files[0]);
                        $(".prevPhoto").append("<img id='img' src="+objeto_url+">");
                        $(".upimg label").remove();
                        
                    }
              }else{
              	alert("No selecciono foto");
                $("#img").remove();
              }              
    });

    $('.delPhoto').click(function(){
    	$('#foto').val('');
    	$(".delPhoto").addClass('notBlock');
        $("#img").remove();
        
        if ($("#foto_actual") && $("#foto_remove"))
         {
            $("#foto_remove").val('img_evento.png');
        }

    });


    //modal form delete eventto
    $('.del_event').click(function(e){
        e.preventDefault();
        var evento = $(this).attr('event');
        var action = 'infoEvent';
        //alert(evento);
        
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            async: true,
            data: {action:action,evento:evento},

            success: function(response){
                //console.log(response);
                if(response !='error'){
                    var info = JSON.parse(response);
                    //console.log(info);
                    //$('#evento_id').val(info.codevento);
                    //$('.nameEvento').html(info.descripcion);

                    $('.bodyModal').html('<form action="" method="post" name="form_del_event" id="form_del_event" onsubmit="event.preventDefault(); delEvent();">'+
                                            '<h1><i class="fas fa-calendar-times" style="font-size: 35pt;"></i><br> Eliminar Evento</h1><br>'+
                                            
                                            '<p style="color: red">Estas Seguro de Eliminar el Siguiente Evento? </p><br>'+

                                            '<h2 class="nameEvento">'+info.descripcion+'</h2><br>'+
                                            //'<input type="number" name="cantidad" id="txtCantidad" placeholder="evento" required><br>'+
                                           // '<input type="text" name="precio" id="txtprecio" placeholder="Precio de prueba" required><br>'+
                                            '<input type="hidden" name="evento_id" id="evento_id" value="'+info.codevento+'" required>'+
                                            '<input type="hidden" name="action" value="delEvent" required>'+
                                            '<div class="alert alertDelEvento"></div>'+

                                            '<a href="#" class="btn_cancel" onclick="closeModal();">Cerrar</a>'+
                                            '<button type="submit" class="btn_ok">Confirmar</button>'+

                                           // '<button type="submit" class="btn_new">Eliminar</button>'+
                                           // '<a href="#" class="btn_ok closeModal" onclick="closeModal();">Cerrar</a>'+
                                          '</form>');
                                          

                }
            },
            error: function(error){
                console.log(error);
            }
        });
        $('.modal').fadeIn();

    });

    
    //activa campos para resgistrar clientes
    $('.btn_new_cliente').click(function(e){
        e.preventDefault();
        $('#nom_cliente').removeAttr('disabled');
        $('#tel_cliente').removeAttr('disabled');
        $('#dir_cliente').removeAttr('disabled');
       
        $('#correo_cliente').removeAttr('disabled');
        $('#div_registro_cliente').slideDown();
        $("#btn_refresh").attr('class', 'btn_view');
        $('#cod_tarjeta').removeAttr('disabled');
        $('.alertErrorEvento').html('');
        $('#btn_refresh').removeAttr('disabled');



    });

    //Buscar Cliente
    $('#nit_cliente').keyup(function(e){
        e.preventDefault();

        var cl = $(this).val();
        var action = 'searchCliente';
       
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            async: true,
            data: {action:action,cliente:cl},
    
            success: function(response)
            {
                //console.log(response); 
                if (response == 0) {
                    $('#idcliente').val('');
                    $('#nom_cliente').val('');
                    $('#tel_cliente').val('');
                    $('#correo_cliente').val('');
                    $('#dir_cliente').val('');
                    $('#cod_tarjeta').val('');
                    $("#btn_refresh").attr('class', 'btn_view inactive');
                    $('.alertErrorEvento').html('');
                    $('#btn_refresh').attr('disabled','disabled');


                   // $('#btn_refresh ').html('<button class="btn_view" ><i class="fas fa-sync"></i></button>');
                   
                    
                
                   
                    //mostrar boton agregar
                    $('.btn_new_cliente').slideDown();


                    
                }else{

                    
                    //alert("hasta aqi")
                    var data = $.parseJSON(response);
                    $('#idcliente').val(data.idcliente);
                    serchForDetalleReservas(data.idcliente)
                    $('#nom_cliente').val(data.nombre);
                    $('#tel_cliente').val(data.telefono);
                    $('#correo_cliente').val(data.Correo);
                    $('#dir_cliente').val(data.direccion);
                    $('#cod_tarjeta').val(data.cod_tarjeta);
                    $('.alertErrorEvento').html('');
                    
                    $("#btn_refresh").attr('class', 'btn_view inactive');

                    //oculta boton agregar
                    $('.btn_new_cliente').slideUp();
                    
                    //bloquea campos
                    $('#nom_cliente').attr('disabled','disabled');
                    $('#tel_cliente').attr('disabled','disabled');
                    $('#correo_cliente').attr('disabled','disabled');
                    $('#dir_cliente').attr('disabled','disabled');
                    $('#cod_tarjeta').attr('disabled','disabled');
                    //oculta boton guardar
                    $('#div_registro_cliente').slideUp();


                }
               
    
            },
            error: function(error){

            }
        });


    });
    //crear cliente- transaccion
    $('#form_new_cliente_venta').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            async: true,
            data: $('#form_new_cliente_venta').serialize(),
             
            success: function(response)
            {
                console.log(response);
                if (response != 'error') {

                   
                    //agregar id a input
                    $('#idcliente').val(response);
                    $('#nom_cliente').attr('disabled','disabled');
                    $('#tel_cliente').attr('disabled','disabled');
                    $('#dir_cliente').attr('disabled','disabled');
                    $('#correo_cliente').attr('disabled','disabled');

                    //oculta boton agregar
                    $('.btn_new_cliente').slideUp();
                    //oculta boton guardar
                    $('#div_registro_cliente').slideUp();
                    $('.alertErrorEvento').html('<p style="color:Black;">CLIENTE CREADO CORRECTAMENTE.</p>');



                }else{

                    $('.alertErrorEvento').html('<p style="color:red;">EL CODIGO DE LA TARJETA YA ESTA EN USO.</p>');

                }

    
            },
            error: function(error){

            }
        });
    });

    //buscar evento
    $('#txt_cod_evento').keyup(function(e){
        e.preventDefault();
        var evento = $(this).val();
        var action = 'infoEvent';

        if (evento!='') {
            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                async: true,
                data: {action:action,evento:evento},
        
                success: function(response)
                {
                    
                   if (response != 'ERROR') {
                        var info = JSON.parse(response);
                        $('#txt_descripcion').html(info.descripcion);
                        $('#txt_existencia').html(info.capMax);
                        $('#txt_cant_evento').val('1');
                        $('#txt_precio').html(info.precio);
                        $('#txt_precio_total').html(info.precio);
                        //acctivar cantidad
                        //$('#txt_cant_evento').removeAttr('disabled');

                        //mostrar boton agregar
                        $('#add_evento_venta').slideDown();
                    
                   }else{
                        $('#txt_descripcion').html('-')
                        $('#txt_existencia').html('-');
                        $('#txt_cant_evento').val('0');
                        $('#txt_precio').html('0.00');
                        $('#txt_precio_total').html('0.00');

                        //bloquear cantidad
                        $('#txt_cant_evento').attr('disabled','disabled');
                        //ocultar boton agregar
                        $('#add_evento_venta').slideUp();


                   }
        
        
                },
                error: function(error){
        
                }
            });
        }
            
    });

    //VALIDAR CANTIDAD DEL PRODUCTO
    $('#txt_cant_evento').keyup(function(e){
        e.preventDefault();
        var precio_total = $(this).val() * $('#txt_precio').html();
        var existencia = parseInt($('#txt_existencia').html());
        $('#txt_precio_total').html(precio_total);
        //ocuulta el boton agregar if cant < 1
        if (($(this).val() < 1 || isNaN($(this).val())) || ($(this).val() > existencia)) {
            $('#add_evento_venta').slideUp();
            
        }else{
            $('#add_evento_venta').slideDown();
            
        }
    })

    //agregar evento al dettalle
    $('#add_evento_venta').click(function(e){
        e.preventDefault();
        var cant=1;
    
        if (cant>0) {
            var codevento = $('#txt_cod_evento').val();
            var cantidad = 1;//$('#txt_cant_evento').val();
            var action = 'addEventoDetalle';

            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                async: true,
                data: {action:action,evento:codevento,cantidad:cantidad},
        
                success: function(response)
                {
                    
                    if (response != 'ERROR' && response!= 'ERROR_GARRAFAL') {

                        var info = JSON.parse(response);
                        $('#detalle_venta').html(info.detalle);
                        $('#detalle_totales').html(info.totales);


                        //LIMPIAR DATOS 
                        $('#txt_cod_evento').val('');
                        $('#txt_descripcion').html('-');
                        $('#txt_existencia').html('-');
                        $('#txt_cant_producto').val('0');
                        $('#txt_precio').html('');
                        $('#txt_precio_total').html('');

                        //BLOQUEAR CANTIDAD

                        $('#txt_cant_evento').attr('disabled','disabled');

                        //hide add botton
                        $('#add_evento_venta').slideUp();

                        
                    }else if(response =='ERROR_GARRAFAL'){

                        alert('SOLO UN SEMINARIO POR PERSONA');
                        $('#txt_cod_evento').val('');
                        $('#txt_descripcion').html('-');
                        $('#txt_existencia').html('-');
                        $('#txt_cant_producto').val('0');
                        $('#txt_precio').html('');
                        $('#txt_precio_total').html('');
                        $('#add_evento_venta').slideUp();
                        
                    }else if(response=='ERROR'){
                        console.log('errores re');
                    }
                     viewProcesar();
                    
        
                },
                error: function(error){
        
                }
            });
            
        }
    })

    //agregar evento desde lista de eventos al dettalle
    $('#add_evento_list1').click(function(e){
        alert("EUREKA")
        e.preventDefault();
        
    
        if ($('#txt_cant_evento').val()>0) {
            var codevento = $('#txt_cod_evento').val();
            var cantidad = $('#txt_cant_evento').val();
            var action = 'addEventoDetalle';

            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                async: true,
                data: {action:action,evento:codevento,cantidad:cantidad},
        
                success: function(response)
                {
                    
                    if (response != 'ERROR') {

                        var info = JSON.parse(response);
                        $('#detalle_venta').html(info.detalle);
                        $('#detalle_totales').html(info.totales);


                        //LIMPIAR DATOS 
                        $('#txt_cod_evento').val('');
                        $('#txt_descripcion').html('-');
                        $('#txt_existencia').html('-');
                        $('#txt_cant_producto').val('0');
                        $('#txt_precio').html('0.00');
                        $('#txt_precio_total').html('');

                        //BLOQUEAR CANTIDAD

                        $('#txt_cant_evento').attr('disabled','disabled');

                        //hide add botton
                        $('#add_evento_venta').slideUp();

                        
                    }else{
                        console.log('no hay datos');
                    }
                     viewProcesar();
                    
        
                },
                error: function(error){
        
                }
            });
            
        }
    })

    //anular transaccion
    $('#btn_anular_venta').click(function(e){
        e.preventDefault();

        var rows = $('#detalle_venta tr').length;
        if (rows > 0) {
            var action = 'anularVenta';

            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                async: true,
                data: {action:action},
        
                success: function(response)
                {
                    
                   
                   if (response !='error') {
                       location.reload();
                   }
                    
        
                },
                error: function(error){
        
                }
            });

        }
    });

     //anular transaccion reservsas
     $('#btn_anular_venta_reservas').click(function(e){
        e.preventDefault();

        var rows = $('#detalle_venta2 tr').length;
        if (rows > 0) {
            var action = 'anularVentaReservas';
            var codusuarioR = $('#idusuarioR').val(); 

            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                async: true,
                data: {action:action,codusuarioR:codusuarioR},
        
                success: function(response)
                {
                    
                   
                   if (response !='error') {
                       alert("RESERVA ANULADA")
                       location.reload();
                   }
                    
        
                },
                error: function(error){
        
                }
            });

        }
    });

     //facturar transaccion
    $('#btn_factura_venta').click(function(e){
        e.preventDefault();

        var rows = $('#detalle_venta tr').length;
        if (rows > 0) {

            var action = 'procesarVenta';
            var codcliente = $('#idcliente').val();

            $.ajax({ 
                url: 'ajax.php',
                type: 'POST',
                async: true,
                data: {action:action,codcliente:codcliente},
        
                success: function(response)
                {
                    
                   
                   if (response !='error') {
                       var info = JSON.parse(response);
                       alert("TRANSACCION EXITOSA")
                       //console.log(info);

                       location.reload();
                     }else{
                         console.log('no data');
                         alert("ALGO SALIO MAL")
                     }
                    
        
                },
                error: function(error){
        
                }
            });

        }
    });
        //facturar transaccion reservas
    $('#btn_factura_venta_reservas').click(function(e){
        e.preventDefault();

        var rows = $('#detalle_venta2 tr').length;
        if (rows > 0) {

            var action = 'procesarVentaReservas';
            var codcliente = $('#idcliente').val();
            var codusuarioR = $('#idusuarioR').val();
            //alert(codusuarioR);

            $.ajax({ 
                url: 'ajax.php',
                type: 'POST',
                async: true,
                data: {action:action,codcliente:codcliente,codusuarioR:codusuarioR},
        
                success: function(response)
                {
                    
                   
                   if (response !='error') {
                       var info = JSON.parse(response);
                       alert("TRANSACCION EXITOSA")
                       //console.log(info);

                       location.reload();
                     }else{
                         console.log('no data');
                         alert("ALGO SALIO MAL")
                     }
                    
        
                },
                error: function(error){
        
                }
            });

        }
    });

    //modal CONfirMAR ANULACION TRANSACCION
    $('.anular_factura').click(function(e){
        e.preventDefault();
        var nofactura = $(this).attr('fac');
        var action = 'infoFactura';
       
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            async: true,
            data: {action:action,nofactura:nofactura},

            success: function(response){
               
                if(response !='error'){
                    var info = JSON.parse(response);
                   
                   
                

                   $('.bodyModal').html('<form action="" method="post" name="form_anular_factura" id="form_anular_factura" onsubmit="event.preventDefault(); anularFactura();">'+
                                            '<h1><i class="fas fa-calendar-times" style="font-size: 35pt;"></i><br> Anular Transaccion</h1><br>'+
                                            
                                            '<p style="color: red">Esta seguro de anular esta transaccion? </p><br>'+

                                            '<p><strong>No. '+info.nofactura+'</strong></p>'+
                                            '<p><strong>Monto. $ '+info.totaltFactura+'</strong></p>'+
                                            '<p><strong>Fecha. '+info.fecha+'</strong></p>'+
                                            '<input type="hidden" name="action" value="anularFactura">'+
                                            '<input type="hidden" name="no_factura" id="no_factura" value="'+info.nofactura+'" required></input>'+

                                           
                                            '<div class="alert alertAddEvento"></div>'+

                                            
                                            '<button type="submit" class="btn_ok"> Anular</button>'+
                                            '<a href="#" class="btn_cancel" onclick="closeModal();">Cerrar</a>'+
                                          '</form>');
                                          

                }
            },
            error: function(error){
                console.log(error);
            }
        });
        $('.modal').fadeIn();

    });

    /// refrescar codigo tarjeta
    $('#btn_refresh').click(function(e){
        
        e.preventDefault();

     
            var action = 'obtenerCodigo';

            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                async: true,
                data: {action:action},
        
                success: function(response)
                {
                    

                   if (response !='error') {

                    var info = JSON.parse(response);

                    console.log(info);
                    $('#cod_tarjeta').val(info.uid);

                   }
                    
        
                },
                error: function(error){
        
                }
            });

        
    });

     /// LLENAR DATOS DE LA GESTION DE ACCESO
     $('#btn_refresh_acceso').click(function(e){

        //alert("EUREKA")
        e.preventDefault();
  
            var action = 'obtenerAcceso';
            var codigo = $('#codigo_acceso').val();
           
            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                async: true,
                data: {action:action,codigo:codigo},
        
                success: function(response) 
                {
                    

                   if (response !='error') {

                    var info = JSON.parse(response);

                    //console.log(info);
                    //$('#cod_1').val(info.cod_tarjeta);
                    //$('#cedula').val(info.cedula);
                    $('.cedula' ).html('CEDULA: '+info.cod_tarjeta)
                    $('.nombre' ).html('NOMBRES: '+info.cod_tarjeta+info.apellidos)
                    $('.telefono' ).html('TELEFONO: '+info.telefono)
                    $('.codigo' ).html('CODIGO TARJETA: '+info.cod_tarjeta)
                    //$('.alertErrorEvento').html('<p style="color:Black;">CLIENTE CREADO CORRECTAMENTE.</p>');

                   }
                    
        
                },
                error: function(error){
        
                }
            });

        
    });

     //buscar evento para acceso
     $('#cod_evento_acesso').keyup(function(e){
        e.preventDefault();
        var evento = $(this).val();
        var action = 'infoEvent';

        if (evento!='') {
            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                async: true,
                data: {action:action,evento:evento},
        
                success: function(response)
                {
                    
                   if (response != 'ERROR') {
                        var info = JSON.parse(response);
                        $('#txt_descripcion').html(info.descripcion);
                        
                        $('#seminario').val(info.codevento);
                       
                       
                        //acctivar cantidad
                        //$('#txt_cant_evento').removeAttr('disabled');

                      
                    
                   }else{
                        

                       


                   }
        
        
                },
                error: function(error){
        
                }
            });
        }
            
    });

    



}); //end readdy


function getHora(){
     
    var f = new Date();
    var hora = f.getHours();
    var min = f.getMinutes();
    var sec = f.getSeconds();
    var a = document.getElementById("hora");
    a.innerHTML = hora+":"+min+":"+sec;
    
}

function reload_access(){
    

        //alert("EUREKA")
       // e.preventDefault();
  
            var action = 'obtenerAcceso';
            var codigo = $('#codigo_acceso').val();
            var codigoEvento = $('#seminario').val();
            


            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                async: true,
                data: {action:action,codigo:codigo,codigoEvento:codigoEvento},
        
                success: function(response)
                {
                    
                   if (response !='error3' && response !='error2') {

                    var info = JSON.parse(response);

                    //console.log(info);
                    //$('#cod_1').val(info.cod_tarjeta);
                    //$('#cedula').val(info.cedula);
                    $('.cedula' ).html('CEDULA: '+info.cedula)
                    $('.nombre' ).html('NOMBRES: '+info.nombre+' '+info.apellidos)
                    $('.telefono' ).html('TELEFONO: '+info.op)
                    $('.codigo' ).html('CODIGO TARJETA: '+info.cod_tarjeta)
                    $('.alertErrorAcceso' ).html('')

                    if (info.op==1) {
                        $('.alertErrorAcceso' ).html('<p style="color:Black;  font-size:20pt; width: 100%; background: #66e07d66;border-radius: 6px; margin: 20px auto; padding: 10px">ACCESO AUTORIZADO.</p>')
                    }
                    else if(info.op==2){
                        $('.alertErrorAcceso' ).html('<p style="color:Black;  font-size:20pt; width: 100%; background: #dd797e66;border-radius: 6px; margin: 20px auto; padding: 10px">ACCESO RESTRINGIDO.</p>')
                    }
                    
                    //$('.alertErrorEvento').html('<p style="color:Black;">CLIENTE CREADO CORRECTAMENTE.</p>');

                   }else if (response =='error2') {
                    //sconsole.log("EL CODIGO NO ESTA ASIGNADO A NINGUN CLIENTE")
                    $('.cedula' ).html('')
                    $('.nombre' ).html('')
                    $('.telefono' ).html('')
                    $('.codigo' ).html('')
                    $('.alertErrorAcceso' ).html('<p style="color:Black;  font-size:20pt; width: 100%; background: #F0F8FF;border-radius: 6px; margin: 20px auto; padding: 10px">EL CODIGO O ESTA ASIGNADO A NINGUN CLIENTE.</p>')
                   }
                   
                   else if(response == 'error3'){
                       console.log("codigo vacio")

                     //$('.alertErrorEvento').html('<p style="color:Black;">CLIENTE CREADO CORRECTAMENTE.</p>');

                   }
                   else if(response == 'error4'){
                    $('.cedula' ).html('')
                    $('.nombre' ).html('')
                    $('.telefono' ).html('')
                    $('.codigo' ).html('')
                    $('.alertErrorAcceso' ).html('<p style="color:Black;  font-size:20pt; width: 100%; background: #dd797e66;border-radius: 6px; margin: 20px auto; padding: 10px">ACCESO RESTRINGIDO AL SEMINARIO.</p>')
                  //$('.alertErrorEvento').html('<p style="color:Black;">CLIENTE CREADO CORRECTAMENTE.</p>');

                }
                    
        
                },
                error: function(error){
                    console.log(error);
        
                }
            });
}

function getCodigo(){

    var action = 'obtenerCodigo2';

    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        async: true,
        data: {action:action},

        success: function(response)
        {
            

           if (response !='error') {
            //console.log(response);
            var info = JSON.parse(response);

            console.log(info);
            $('#codigo_acceso').val(info.uid);

           }
           else{
            console.log("no hay datos que mostrar");
           }
            

        },
        error: function(error){
            console.log(error);
        }
    });
    
}

setInterval(reload_access,1000);
setInterval(getCodigo,1000);



function anularFactura(){
    var noFactura = $('#no_factura').val();
    var action = 'anularFactura';
    
    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        async: true,
        data: {action:action,noFactura:noFactura},

        success: function(response)
        {
            if(response == 'error'){
                $('.alertAddEvento').html('<p style="color:red;">Error al anular la transaccion.</p>');
            }else{
                $('#row_'+noFactura+' .estado').html('<span class="anulada">Anulada</span>');
                $('#form_anular_factura .btn_ok').remove();
                $('#row_'+noFactura+' .div_factura').html('<button type="button" class="btn_anular inactive"><i class="fas fa-ban"></i></button>');
                $('.alertAddEvento').html('<p>Transaccion Anulada.</p>');
                

            }
                     
        },
        error: function(error){

        }
    });


}

function del_evento_detalle2(correlativo){

    var action = 'delEventoDetalle';
    var id_detalle = correlativo;

    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        async: true,
        data: {action:action,id_detalle:id_detalle},

        success: function(response)
        {
            
            
            if (response != 'error') 
            {
                
                var info = JSON.parse(response);
                console.log(info);

                $('#detalle_venta').html(info.detalle);
                $('#detalle_totales').html(info.totales);




                //LIMPIAR DATOS 
                $('#txt_cod_evento').val('');
                $('#txt_descripcion').html('-');
                $('#txt_existencia').html('-');
                $('#txt_cant_producto').val('0');
                $('#txt_precio').html('0.00');
                $('#txt_precio_total').html('');
                

                //BLOQUEAR CANTIDAD

                $('#txt_cant_evento').attr('disabled','disabled');

                //hide add botton
                $('#add_evento_venta').slideUp();

            }else{
                $('#detalle_venta').html('');
                $('#detalle_totales').html('');
            }
            viewProcesar();
           
        },
        error: function(error){

        }
    });
}

function del_evento_detalle(correlativo,codevento){

    var action = 'delEventoDetalle';
    var id_detalle = correlativo;
    var cod_evento = codevento;
    
    
    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        async: true,
        data: {action:action,id_detalle:id_detalle},

        success: function(response)
        {
            $('#row_'+cod_evento+' .div_factura').html('<button class="btn_view view_factura " onclick="event.preventDefault(); mostrar('+cod_evento+','+1+')"><i class="far fa-trash-alt"></i> Reservar</button>');

            
            if (response != 'error') 
            {
                //alert(correlativo)
                var info = JSON.parse(response);
                console.log(info);

                $('#detalle_venta').html(info.detalle);
                $('#detalle_totales').html(info.totales);
                



                //LIMPIAR DATOS 
                $('#txt_cod_evento').val('');
                $('#txt_descripcion').html('-');
                $('#txt_existencia').html('-');
                $('#txt_cant_producto').val('0');
                $('#txt_precio').html('0.00');
                $('#txt_precio_total').html('');
                

                //BLOQUEAR CANTIDAD

                $('#txt_cant_evento').attr('disabled','disabled');

                //hide add botton
                $('#add_evento_venta').slideUp();

            }else{
                $('#detalle_venta').html('');
                $('#detalle_totales').html('');
            }
            viewProcesar();
           
        },
        error: function(error){

        }
    });
}
//mostrar /ocltar boton proccesar
function viewProcesar(){
    if ($('#detalle_venta tr').length > 0) {
        $('#btn_factura_venta').show();
    }else{
        $('#btn_factura_venta').hide();


    }
}
function serchForDetalle(id){
    var action = 'serchForDetalle';
    var user = id;

    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        async: true,
        data: {action:action,user:user},

        success: function(response)
        {
            
            
            if (response != 'ERROR') {

                
                var info = JSON.parse(response);
                //console.log("33");
                $('#detalle_venta').html(info.detalle);
                $('#detalle_totales').html(info.totales);


                
            }else{
                console.log('no hay datos');
            }
            viewProcesar();

           
        },
        error: function(error){

        }
    });
}
//buscat datos tabla detalle
function serchForDetalleReservas(id){
    var action = 'serchForDetalleReservas';
    var user = id;
    
    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        async: true,
        data: {action:action,user:user},

        success: function(response)
        {
            
            //alert(user)
            if (response != 'ERROR') {

                
                var info = JSON.parse(response);
                //console.log("33");
                
                $('#detalle_venta2').html(info.detalle);
                $('#detalle_totales2').html(info.totales);


                
            }else{
                alert("NO SE ENCONTRARON RESERVAS")
            }
            viewProcesar();

           
        },
        error: function(error){

        }
    });
}

function delEvent(){
    var pr = $('#evento_id').val();
    $('.alertDelEvent').html('');
    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        async: true,
        data: $('#form_del_event').serialize(),

        success: function(response){
            console.log(response);

            if (response == 'error') {
                $('.alertDelEvent').html('<p style="color: red;">Error al borrar Evento</p>');
                
            }else{
               
                $('.row'+pr).remove();
                $('#form_del_event .btn_ok').remove();
                $('.alertDelEvento').html('<p>Evento Eliiminado Correctamente</p>')
            }

        }
    });
}


function closeModal(){
    $('.modal').fadeOut();
}
//funccion para agregar nuevas reservas
function mostrar(cod_evento, disponible){
    //alert(e)
    if (disponible > 0) {
        var codevento = cod_evento;
        var cantidad = 1;
        var action = 'addEventoDetalle';

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            async: true,
            data: {action:action,evento:codevento,cantidad:cantidad},
    
            success: function(response)
            {
                
                if (response != 'ERROR' && response!= 'ERROR_GARRAFAL') {

                    var info = JSON.parse(response);
                    $('#detalle_venta').html(info.detalle);
                    $('#detalle_totales').html(info.totales);

                    //$('#form_anular_factura .btn_ok').remove();
                   $('#row_'+codevento+' .div_factura').html('<button class="btn_view view_factura inactive" disabled><i class="far fa-trash-alt"></i> Reservar</button>');
                    


                    //LIMPIAR DATOS 
                    $('#txt_cod_evento').val('');
                    $('#txt_descripcion').html('-');
                    $('#txt_existencia').html('-');
                    $('#txt_cant_producto').val('0');
                    $('#txt_precio').html('-');
                    $('#txt_precio_total').html('');

                    //BLOQUEAR CANTIDAD

                    $('#txt_cant_evento').attr('disabled','disabled');

                    //hide add botton
                    $('#add_evento_venta').slideUp();

                    
                }else if(response =='ERROR_GARRAFAL'){

                    alert('SOLO UN SEMINARIO POR PERSONA');
                    $('#add_evento_venta').slideUp();
                    
                }else if(response=='ERROR'){
                    console.log('errores re');
                }
                 viewProcesar();
                
    
            },
            error: function(error){
    
            }
        });
        
    }
    else{

        alert('NO QUEDAN LUGARES DISPONIBLES')

    }
}
function probar(){
    //alert("eureka")
    
}

