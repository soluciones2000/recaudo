<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="col-md-9 col-xs-11 p-l-2 p-t-2">
    <a href="#sidebar" data-toggle="collapse"><i class="fa fa-navicon fa-lg"></i></a>
    <hr>
    	<h1>Listado de <?php echo $titulo ?></h1>
    	<h3><?php echo $titulo ?></h3>
    	<br />
    	<button class="btn btn-success" onclick="agregar_ajax()"><i class="glyphicon glyphicon-plus"></i> Agregar <?php echo $titulo ?></button>
    	<button class="btn btn-default" onclick="recargar_tabla()"><i class="glyphicon glyphicon-refresh"></i> Recargar</button>
    	<button class="btn btn-default" onclick="mostrar_filtros()"><i class="glyphicon glyphicon-open"></i> Mostrar filtros</button>
        <div class="card">
            <div class="card-heading">
    			<h3 class="card-title" >Filtros:</h3>
            </div>
            <div class="card-body">
            	<form id="form-filter" class="form-horizontal">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="lead">Nombre de la cuenta</p>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="Nombre_Cuenta_filtro">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="lead">Saldo en la cuenta</p>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="SaldoCuenta_filtro">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="button" id="btn-filter" class="btn btn-primary">Buscar</button>
                            <button type="button" id="btn-reset" class="btn btn-default" onclick="recargar_tabla()">Reiniciar campos</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table id="table" class="table table-striped table-bordered" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Nombre de la cuenta</th>
                            <th>Saldo en la cuenta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Nº</th>
                            <th>Nombre de la cuenta</th>
                            <th>Saldo en la cuenta</th>
                            <th>Acciones</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    
</div>
<script type="text/javascript">
    var save_method; //for save method string
    var table;

    $(document).ready(function()
    {
        cargar_gestor();// carga el gestor al cargar la pagina por primera vez
        cargar_cliente();// carga el cliente al cargar la pagina por primera vez

        $(".card-body").toggle();
        //datatables
        table = $('#table').DataTable({ 
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [ 1,'asc'], //[ 9,'asc']], //Initial no order.
    		
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url('Cuenta_gestor/ajax_list')?>",
                "type": "POST",
                "data": function (data)
    			{    // are send to the server side
    				data.SaldoCuenta_ajaxfiltro = $('#SaldoCuenta_filtro').val();
                    data.Nombre_Cuenta_ajaxfiltro = $("#Nombre_Cuenta_filtro").val();
                }
            },

            //Set column definition initialisation properties.
            "columnDefs": [
    			{ 
    				"targets": [ 0 ], //first column / numbering column
    				"orderable": false, //set not orderable
    			},
                { 
                    "targets": [ -1 ], //last column
                    "orderable": false, //set not orderable
                },
            ],
    		// cambio de idioma de ingles a español
    		"language": {
                "lengthMenu": "mostrando _MENU_ registros por pagina",
                "zeroRecords": "registro no encontrado",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "No existen registros",
                "infoFiltered": "(flitrados de _MAX_ el total de registros)",
    			"decimal":        "",
    			"emptyTable":     "No existe información en la tabla",
    			"infoPostFix":    "",
    			"thousands":      ",",
    			"loadingRecords": "Cargando...",
    			"processing":     "Procesando...",
    			"search":         "Buscar:",
    			"paginate": {
    				"first":      "Primero",
    				"last":       "Ultimo",
    				"next":       "Siguiente",
    				"previous":   "Anterior"
    			},
    			"aria": {
    				"sortAscending":  ": Activar para ordenar la columna ascendente",
    				"sortDescending": ": Activar para ordenar la columna descendente"
    			}
            },
        });

        $('#btn-filter').click(function(){ //button filter event click
            table.ajax.reload(null,false);  //just reload table
        });
    });

    $(document).ready(function()
    {
        $.ajax({
            url: "<?php echo base_url('Cuenta_gestor/get_list_empresas')?>",
            dataType:"JSON",
            type: "POST",
            success: function(data)
            {
                if(data.status != false)
                {
                    var predeterminado = data.predeterminado;
                    var output = [];//array en javascript
                    $.each(data.list, function(key, value)//funcion en jquery que opera igual al foreach de php
                    {
                        if (key == predeterminado)
                        {
                            output.push('<option value="'+key+'" selected>'+value+'</option>');
                        }
                        else
                        {
                            output.push('<option value="'+key+'">'+value+'</option>');
                        }
                    });

                    $('[name="id_empresa_form"]').html(output.join(''));//agrega el array y lo muestra en el html
                }
                else
                {
                    alert("No posee el nivel de seguridad requerido");
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error al cargar los datos');
            }
        });
    });

    function reporte_pdf()
    {
        var parametros = {// are send to the server side
            id_empresa : $('[name="id_empresa_form"]').val(),
            SaldoCuenta_ajaxreporte : $('#SaldoCuenta_filtro').val(),
            Nombre_Cuenta_ajaxreporte : $("#Nombre_Cuenta_filtro").val(),
        };

        $.ajax({
            url: '<?php echo base_url("Cuenta_gestor/reporte");?>',
            type: "POST",
            dataType: 'JSON',
            data: parametros,
            success: function(data)
            {
                if(data.status != false)// si es falso dara error de seguridad
                {
                    window.open(data, "_blank"); //muestra el resultado optenido
                }
                else
                {
                    alert("No posee el nivel de seguridad requerido");
                }
            }
        });
    }

    function cargar_gestor(id = false)
    {
        $.ajax({
            url: "<?php echo base_url('Cuenta_gestor/cargar_gestor')?>",
            dataType:"JSON",
            type: "POST",
            success: function(data)
            {
                if(data.status != false)
                {
                    if (id != false)
                    {
                        var output = [];//array en javascript
                        $.each(data.info, function(key, value)//funcion en jquery que opera igual al foreach de php
                        {
                            if (id === key)
                            {
                                output.push('<option value="'+key+'" selected>'+value+'</option>');
                            }
                            else
                            {
                                output.push('<option value="'+key+'">'+value+'</option>');
                            }
                        });

                        $('[name="idGestor_form"]').html(output.join(''));//agrega el array y lo muestra en el html
                    }
                    else
                    {
                        var output = [];//array en javascript
                        $.each(data.info, function(key, value)//funcion en jquery que opera igual al foreach de php
                        {
                            output.push('<option value="'+key+'">'+value+'</option>');
                        });

                        $('[name="idGestor_form"]').html(output.join(''));//agrega el array y lo muestra en el html
                    }
                }
                else
                {
                    alert("No posee el nivel de seguridad requerido");
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error al cargar los datos');
            }
        });
    }

    function cargar_cliente(id = false)
    {
        $.ajax({
            url: "<?php echo base_url('Cuenta_gestor/cargar_cliente')?>",
            dataType:"JSON",
            type: "POST",
            success: function(data)
            {
                if(data.status != false)
                {
                    if (id != false)
                    {
                        var output = [];//array en javascript
                        $.each(data.info, function(key, value)//funcion en jquery que opera igual al foreach de php
                        {
                            if (id === key)
                            {
                                output.push('<option value="'+key+'" selected>'+value+'</option>');
                            }
                            else
                            {
                                output.push('<option value="'+key+'">'+value+'</option>');
                            }
                        });

                        $('[name="idCliente_form"]').html(output.join(''));//agrega el array y lo muestra en el html
                    }
                    else
                    {
                        var output = [];//array en javascript
                        $.each(data.info, function(key, value)//funcion en jquery que opera igual al foreach de php
                        {
                            output.push('<option value="'+key+'">'+value+'</option>');
                        });

                        $('[name="idCliente_form"]').html(output.join(''));//agrega el array y lo muestra en el html
                    }
                }
                else
                {
                    alert("No posee el nivel de seguridad requerido");
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error al cargar los datos');
            }
        });
    }

    function recargar_tabla()
    {
    	//button reset event click
    	$('#form-filter')[0].reset();
        table.ajax.reload(null,false);  //just reload table   
    }

    function mostrar_filtros()
    {
        $(".card-body").toggle(300);
    }
    	
    function agregar_ajax()
    {
        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $("#form :input").prop("disabled", false);
        $('.help-block').empty(); // clear error string
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Agregar'); // Set Title to Bootstrap modal title
    	$('#btnSave').show();
    }

    function detalles_ajax(id)
    {
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#btnSave').hide();
        $("#form :input").prop("disabled", true);
        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo base_url('Cuenta_gestor/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                if(data.status != false)
                {
                    $('[name="id"]').val(data.id);
                    $('[name="idCliente_form"]').val(data.idCliente);
                    $('[name="idGestor_form"]').val(data.idGestor);
                    $('[name="Nombre_Cuenta_form"]').val(data.Nombre_Cuenta);
                    $('[name="SaldoCuenta_form"]').val(data.SaldoCuenta);

                    cargar_gestor(data.idGestor);
                    cargar_cliente(data.idCliente);

                    $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                    $('.modal-title').text('Detalles'); // Set title to Bootstrap modal title
                }
                else
                {
                    alert("No posee el nivel de seguridad requerido");
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error al cargar los datos');
            }
        });
    }

    function editar_ajax(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
    	$('#btnSave').show();
        $("#form :input").prop("disabled", false);
        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo base_url('Cuenta_gestor/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                if (data.status != false)
                {
                    $('[name="id"]').val(data.id);
                    $('[name="idCliente_form"]').val(data.idCliente);
                    $('[name="idGestor_form"]').val(data.idGestor);
                    $('[name="Nombre_Cuenta_form"]').val(data.Nombre_Cuenta);
                    $('[name="SaldoCuenta_form"]').val(data.SaldoCuenta);

                    cargar_gestor(data.idGestor);
                    cargar_cliente(data.idCliente);

                    $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                    $('.modal-title').text('Modificar'); // Set title to Bootstrap modal title
                }
                else
                {
                    alert("No posee el nivel de seguridad requerido");
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error al cargar los datos');
            }
        });
    }

    function save()
    {
        $('#btnSave').text('Guardando...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        var url;

        if(save_method == 'add') {
            url = "<?php echo base_url('Cuenta_gestor/ajax_add')?>";
        } else {
            url = "<?php echo base_url('Cuenta_gestor/ajax_update')?>";
        }

        // ajax adding data to database
        var formData = new FormData($('#form')[0]);
        $.ajax({
            url : url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function(data)
            {
                if(data.status == true) //if success close modal and reload ajax table
                {
                    $('#modal_form').modal('hide');
                    recargar_tabla();
                }
                else if(data.status == "error")
                {
                    alert("Ya existe un registro igual");
                    $('#btnSave').text('Guardar'); //change button text
                    $('#btnSave').attr('disabled',false); //set button enable 
                }
                else if (data.status == "nivel")
                {
                    alert("No tiene el nivel de seguridad necesario");
                    $('#btnSave').text('Guardar'); //change button text
                    $('#btnSave').attr('disabled',false); //set button enable 
                }
                else if (data.status == false)
                {
                    for (var i = 0; i < data.inputerror.length; i++) 
                    {
                        $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                        $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
                    }
                }
                $('#btnSave').text('Guardar'); //change button text
                $('#btnSave').attr('disabled',false); //set button enable 
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert("Error al agregar o modficar un registro");
                $('#btnSave').text('Guardar'); //change button text
                $('#btnSave').attr('disabled',false); //set button enable 
            }
        });
    }

    function borrar_ajax(id)
    {
        if(confirm('Esta seguro de querer borrar esta información?'))
        {
            // ajax delete data to database
            $.ajax({
                url : "<?php echo base_url('Cuenta_gestor/ajax_delete')?>/"+id,
                type: "POST",
                dataType: "JSON",
                success: function(data)
                {
                    if(data.status == true) //if success close modal and reload ajax table
                    {
                        $('#modal_form').modal('hide');
                        recargar_tabla();
                    }
                    else if (data.status == false)
                    {
                        alert("No tiene el nivel de seguridad necesario");
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error al borrar la información');
                }
            });
        }
    }
</script>
<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-md-6">
                    <h3 class="modal-title" style="text-align: left;">Formulario de <?php echo $titulo ?></h3>
                </div>
                <div class="col-md-6">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>                
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" value="" name="id" readonly/>
                    <select name="id_empresa_form" style="display: none;">
                    </select>
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="lead">Nombre del cliente</p>
                            </div>
                            <div class="col-md-6">
                                <select name="idCliente_form" class="form-control">
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="lead">Nombre del gestor</p>
                            </div>
                            <div class="col-md-6">
                                <select name="idGestor_form" class="form-control">
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="lead">Nombre de la cuenta</p>
                            </div>
                            <div class="col-md-6">
                                <input name="Nombre_Cuenta_form" placeholder="Nombre de la cuenta" class="form-control" type="text" maxlength="100">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="lead">Saldo de la cuenta</p>
                            </div>
                            <div class="col-md-6">
                                <input name="SaldoCuenta_form" placeholder="Saldo de la cuenta" class="form-control" type="text" maxlength="18">
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->