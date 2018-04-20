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
    <div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" >Filtros:</h3>
        </div>
        <div class="panel-body">
        	<form id="form-filter" class="form-horizontal">
                <div class="row col-sm-12">
                   	<div class="form-group">
						<label for="razon_social_filtro" class="col-sm-4 control-label">Razon social de la empresa</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="razon_social_filtro" placeholder="Razon social de la empresa">
						</div>
					</div>
                    <div class="form-group">
                        <label for="nombre_empresa_filtro" class="col-sm-4 control-label">Nombre de la empresa</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="nombre_empresa_filtro" placeholder="Nombre de la empresa">
                        </div>
                    </div>
				</div>
				<div class="form-group">
					<label for="LastName" class="col-sm-2 control-label"></label>
					<div class="col-sm-4">
						<button type="button" id="btn-filter" class="btn btn-primary">Buscar</button>
						<button type="button" id="btn-reset" class="btn btn-default" onclick="recargar_tabla()">Reiniciar campos</button>
                        <button type="button" id="btn-report" class="btn btn-default" onclick="reporte_pdf()">reporte pdf</button>
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
                        <th>Razon social</th>
                        <th>Nombre</th>
                        <th>Predeterminada</th>
                        <th>Logo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                
                </tbody>
                <tfoot>
                    <tr>
                        <th>Nº</th>
                        <th>Razon social</th>
                        <th>Nombre</th>
                        <th>Predeterminada</th>
                        <th>Logo</th>
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
    var base_url = '<?php echo base_url();?>';

    //inicio bloqueo del segundo click
    document.oncontextmenu = function(){return false}
    //fin bloqueo del segundo click

    $(document).ready(function()
    {
        $(".panel-body").toggle();
        //datatables
        table = $('#table').DataTable({ 
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [ 1,'asc'], //[ 9,'asc']], //Initial no order.
    		
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url('Empresas/ajax_list')?>",
                "type": "POST",
                "data": function (data)
    			{
    				data.razon_social_ajaxfiltro = $('#razon_social_filtro').val(); // are send to the server side
                    data.nombre_empresa_ajaxfiltro = $('#nombre_empresa_filtro').val(); // are send to the server side
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
            url: "<?php echo base_url('Empresas/get_list_empresas')?>",
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
        var parametros = { // are send to the server side
            razon_social_ajaxreporte : $('#razon_social_filtro').val(),
            nombre_empresa_ajaxreporte : $('#nombre_empresa_filtro').val(),
            id_empresa : $('[name="id_empresa_form"]').val(),
        };

        $.ajax({
            url: '<?php echo base_url("Empresas/reporte");?>',
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

    function recargar_tabla()
    {
    	//button reset event click
    	$('#form-filter')[0].reset();
        table.ajax.reload(null,false);  //just reload table   
    }

    function mostrar_filtros()
    {
        $(".panel-body").toggle(300);
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

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo base_url('Empresas/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                if(data.status != false)
                {
                    $('[name="id"]').val(data.id);
                    $('[name="razon_social_form"]').val(data.razon_social);
                    $('[name="nombre_empresa_form"]').val(data.nombre_empresa);
                    if (data.predeterminado == 1) 
                    {
                        $('[name="predeterminado_form"]').prop("checked", true);
                    }

                    $('[name="razon_social_form"]').prop("disabled", true);
                    $('[name="nombre_empresa_form"]').prop("disabled", true);
                    $('[name="predeterminado_form"]').prop("disabled", true);

                    $('#logo-preview').show(); // show logo preview modal

                    if(data.archivo)
                    {
                        $('#label-logo').text('Cambiar logo'); // label logo upload
                        $('#logo-preview div').html('<img src="'+base_url+'files/img/logos_empresas/'+data.archivo+'" class="img-responsive">'); // show logo
                        $('#logo-preview div').append('<input type="checkbox" name="remove_logo" value="'+data.archivo+'"/> Removiendo logo cuando se guarda'); // remove logo
                        $('[name="remove_logo"]').prop("disabled", true);
                    }
                    else
                    {
                        $('#label-logo').text('Subir logo'); // label logo upload
                        $('#logo-preview div').text('(No logo)');
                    }

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

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo base_url('Empresas/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                if (data.status != false)
                {
                    $('[name="id"]').val(data.id);
                    $('[name="razon_social_form"]').val(data.razon_social);
                    $('[name="nombre_empresa_form"]').val(data.nombre_empresa);
                    if (data.predeterminado == 1) 
                    {
                        $('[name="predeterminado_form"]').prop("checked", true);
                    }
                    
                    $('[name="razon_social_form"]').prop("disabled", false);
                    $('[name="nombre_empresa_form"]').prop("disabled", false);
                    $('[name="predeterminado_form"]').prop("disabled", false);

                    if(data.archivo)
                    {
                        $('#label-logo').text('Cambiar logo'); // label logo upload
                        $('#logo-preview div').html('<img src="'+base_url+'files/img/logos_empresas/'+data.archivo+'" class="img-responsive">'); // show logo
                        $('#logo-preview div').append('<input type="checkbox" name="remove_logo" value="'+data.archivo+'"/> Removiendo logo cuando se guarda'); // remove logo

                        $('[name="remove_logo"]').prop("disabled", false);
                    }
                    else
                    {
                        $('#label-logo').text('Subir logo'); // label logo upload
                        $('#logo-preview div').text('(No logo)');
                    }

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
            url = "<?php echo base_url('Empresas/ajax_add')?>";
        } else {
            url = "<?php echo base_url('Empresas/ajax_update')?>";
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
                url : "<?php echo base_url('Empresas/ajax_delete')?>/"+id,
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Formulario de <?php echo $titulo ?></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" value="" name="id" readonly/>
                    <select name="id_empresa_form" style="display: none;">
                    </select>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Razon social</label>
                            <div class="col-md-9">
                                <input name="razon_social_form" placeholder="Razon social" class="form-control" type="text" maxlength="20" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Nombre</label>
                            <div class="col-md-9">
                                <textarea name="nombre_empresa_form" placeholder="Nombre" class="form-control" maxlength="250" required></textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Predeterminada</label>
                            <div class="col-md-9">
                                <input name="predeterminado_form" type="checkbox" value="1">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group" id="logo-preview">
                            <label class="control-label col-md-3">Logo</label>
                            <div class="col-md-9">
                                (No logo)
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3" id="label-logo">Subir Logo</label>
                            <div class="col-md-9">
                                <input name="archivo" type="file">
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
</body>
</html>