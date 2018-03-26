<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style type="text/css">
    th { font-size: 12px; }
    td { font-size: 12px; }
</style>
<div class="py-5">
    <div class="container">
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
                            <p class="lead">Nombres del usuario</p>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="nombres_filtro">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="lead">Apellidos del usuario</p>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="apellidos_filtro">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="lead">Correo del usuario</p>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="correo_filtro">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="lead">modulos</p>
                        </div>
                        <div class="col-md-6">
                            <?php echo $modulo_filtro; ?>
                        </div>
                    </div>          
                    <div class="row">
                        <div class="col-md-6 text-center">
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
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Correo</th>
                            <th>Telefono</th>
                            <th>Ultimo ingreso</th>
                            <th>Modulos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Nº</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Correo</th>
                            <th>Telefono</th>
                            <th>Ultimo ingreso</th>
                            <th>Modulos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>    
<script type="text/javascript">
    var save_method; //for save method string
    var table;
    var base_url = '<?php echo base_url();?>';
    
    $(document).ready(function()
    {
        $(".card-body").toggle();
        //datatables
        table = $('#table').DataTable({ 
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [ 1,'asc'], //[ 9,'asc']], //Initial no order.
    		
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url('Usuarios/ajax_list')?>",
                "type": "POST",
                "data": function (data)
    			{
    				data.first_name_ajaxfiltro = $('#nombres_filtro').val(); // are send to the server side
                    data.last_name_ajaxfiltro = $('#apellidos_filtro').val(); // are send to the server side
    				data.email_ajaxfiltro = $('#correo_filtro').val(); // are send to the server side
                    data.modulo_ajaxfiltro = $("#modulo_filtro option:selected").text(); // are send to the server side
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
            url: "<?php echo base_url('Usuarios/get_list_empresas')?>",
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
            nombres_ajaxreporte : $('#nombres_filtro').val(), 
            apellidos_ajaxreporte : $('#apellidos_filtro').val(), 
            correo_ajaxreporte : $('#correo_filtro').val(),
            modulo_ajaxreporte : $("#modulo_filtro option:selected").text(),
            id_empresa : $('[name="id_empresa_form"]').val(),
        };

        $.ajax({
            url: '<?php echo base_url("Usuarios/reporte");?>',
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

    function reporte_individual()
    {
        parametros = {
            id : $('[name="id"]').val(),
            id_empresa : $('[name="id_empresa_form"]').val(),
        };
        
        $.ajax({
            url: '<?php echo base_url("Usuarios/reporte_individual");?>',
            type: "POST",
            dataType: 'JSON',
            data:  parametros,
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
        $(".card-body").toggle(300);
    }
    	
    function agregar_ajax()
    {
        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('[name="nombres_form"]').prop("disabled", false);
        $('[name="apellidos_form"]').prop("disabled", false);
        $('[name="email_form"]').prop("disabled", false);
        $('[name="contrasena_form"]').prop("disabled", false);
        $('[name="telefono_form"]').prop("disabled", false);
        $("#div_clave").show();
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
            url : "<?php echo base_url('Usuarios/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                if(data.status != false)
                {
                    $('[name="id"]').val(data.id);
                    $('[name="nombres_form"]').val(data.nombres);
                    $('[name="apellidos_form"]').val(data.apellidos);
                    $('[name="email_form"]').val(data.correo);
                    $("#div_clave").hide();
                    $('[name="telefono_form"]').val(data.telefono);

                    $('[name="nombres_form"]').prop("disabled", true);
                    $('[name="apellidos_form"]').prop("disabled", true);
                    $('[name="email_form"]').prop("disabled", true);
                    $('[name="contrasena_form"]').prop("disabled", true);
                    $('[name="telefono_form"]').prop("disabled", true);
                    
                    var array = data.modulos.split(",");// convierte un string a array donde este la coma
                    for (var i = 0; i < array.length; i++) 
                    {
                        var subarray = array[i].split("|");// tras realizar la primera separacion se hace otra mas con el simbolo |
                        for(var j = 0; j < subarray.length; j++)
                        {
                            $('[name="'+subarray[0]+'"]').prop("checked", true);
                            $('[name="'+subarray[0]+'_nivel_form'+'"]').val(subarray[1]);
                        }
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
            url : "<?php echo base_url('Usuarios/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                if (data.status != false)
                {
                    $('[name="id"]').val(data.id);
                    $('[name="nombres_form"]').val(data.nombres);
                    $('[name="apellidos_form"]').val(data.apellidos);
        			$('[name="email_form"]').val(data.correo);
                    $("#div_clave").hide();
                    $('[name="telefono_form"]').val(data.telefono);

                    $('[name="nombres_form"]').prop("disabled", false);
                    $('[name="apellidos_form"]').prop("disabled", false);
                    $('[name="email_form"]').prop("disabled", false);
                    $('[name="contrasena_form"]').prop("disabled", false);
                    $('[name="telefono_form"]').prop("disabled", false);
                    
                    var array = data.modulos.split(",");// convierte un string a array donde este la coma
                    for (var i = 0; i < array.length; i++) 
                    {
                        var subarray = array[i].split("|");// tras realizar la primera separacion se hace otra mas con el simbolo |
                        for(var j = 0; j < subarray.length; j++)
                        {
                            $('[name="'+subarray[0]+'"]').prop("checked", true);
                            $('[name="'+subarray[0]+'_nivel_form'+'"]').val(subarray[1]);
                        }
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

    function cambiar_clave_ajax(id)
    {
        save_method = 'cambiar';
        $('[name="id"]').val(id);
        $('#form_clave')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modal_clave_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Cambiar clave'); // Set Title to Bootstrap modal title
        $('#btnSave').show();
    }

    function save()
    {
        $('#btnSave').text('Guardando...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        var url;

        if(save_method == 'add')
        {
            url = "<?php echo base_url('Usuarios/ajax_add')?>";
            // ajax adding data to database
            var formData = new FormData($('#form')[0]);
        }
        else 
        {
            url = "<?php echo base_url('Usuarios/ajax_update')?>";
            // ajax adding data to database
            var formData = new FormData($('#form')[0]);
        }
        if (save_method == "cambiar")
        {
            url = "<?php echo base_url('Usuarios/ajax_change_password')?>";
            // ajax adding data to database
            var formData = new FormData($('#form_clave')[0]);
        }

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
                    $('#modal_clave_form').modal('hide');
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
                else if (data.status == null)
                {
                    alert("Las claves no coinciden");
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
                url : "<?php echo base_url('Usuarios/ajax_delete')?>/"+id,
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

    function Estado_ajax(id)
    {
        if(confirm('Esta seguro de querer cambiar el estado del usuario?'))
        {
            // ajax delete data to database
            $.ajax({
                url : "<?php echo base_url('Usuarios/ajax_status_change')?>/"+id,
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
                <button type="button" id="btnprint" onclick="reporte_individual()" class="btn btn-info"><i class="glyphicon glyphicon-print"></i> Imprimir</button>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" value="" name="id" readonly/>
                    <select name="id_empresa_form" style="display: none;">
                    </select>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Nombres del usuario</label>
                            <div class="col-md-9">
                                <input name="nombres_form" placeholder="Nombres del usuario" class="form-control" type="text" maxlength="50">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Apellidos del usuario</label>
                            <div class="col-md-9">
                                <input name="apellidos_form" placeholder="Apellidos del usuario" class="form-control" type="text" maxlength="50">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Correo</label>
                            <div class="col-md-9">
                                <input name="email_form" placeholder="Correo" class="form-control" type="email" maxlength="100">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group" id="div_clave">
                            <label class="control-label col-md-3">Contraseña</label>
                            <div class="col-md-9">
                                <input name="contrasena_form" placeholder="Contraseña" class="form-control" type="password" maxlength="255">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Telefono</label>
                            <div class="col-md-9">
                                <input name="telefono_form" placeholder="Telefono" class="form-control" type="text" maxlength="20">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3"></label>
                            <div class="col-md-9">
                                <label class="control-label col-md-6" style="text-align: center;">Modulos</label>
                                <label class="control-label col-md-6" style="text-align: center;">Nivel de seguridad</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Modulos del usuario</label>
                            <div class="col-md-9">
                                <?php foreach($modulo_form as $var){ ?>
                                <div class="col-md-6" style="text-align: center;">
                                    <input name="<?php echo $var ?>" type="checkbox" value="<?php echo $var ?>">
                                    <label class="control-label"><?php echo $var ?></label>
                                </div>
                                <div class="col-md-6">
                                    <select name="<?php echo $var ?>_nivel_form" class="form-control">
                                    <option value="">Seleccione</option>
                                    <?php foreach($nivel as $fila){?>
                                        <option value="<?php echo $fila ?>"><?php echo $fila ?></option>
                                    <?php } ?>
                                    </select>
                                </div>
                                <?php } ?>
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
<div class="modal fade" id="modal_clave_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Formulario de <?php echo $titulo ?></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form_clave" class="form-horizontal">
                    <input type="hidden" value="" name="id" readonly/> 
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Nueva clave</label>
                            <div class="col-md-9">
                                <input name="clave_nueva_form" placeholder="Nueva clave" class="form-control" type="password" maxlength="255">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Repetir clave</label>
                            <div class="col-md-9">
                                <input name="repetir_clave_form" placeholder="Repetir clave" class="form-control" type="password" maxlength="255">
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