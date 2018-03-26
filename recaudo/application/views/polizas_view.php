<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style type="text/css">
    th { font-size: 12px; }
    td { font-size: 12px; }
    .lgx
    {
        width:80%;
        margin: auto;
    }
    #table .dropdown-menu
    {
        position: relative;
    }
    #table_siniestros .dropdown-menu
    {
        position: relative;
    }
</style>
<div class="container">
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
                <div class="form-group">
                    <label class="control-label col-md-4">Empresa usada para el reporte</label>
                    <div class="col-md-8">
                        <select name="id_empresa_form" class="form-control"></select>
                    </div>
                </div>
                <div class="form-group">
					<label for="ci_rif_filtro" class="col-md-3 control-label">Doc de identificacion del asegurado</label>
					<div class="col-md-3">
						<input type="text" class="form-control" id="ci_rif_filtro">
					</div>
                    <label for="nombres_filtro" class="col-md-3 control-label">Nombre del asegurado</label>
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="nombres_filtro">
                    </div>
				</div>
               	<div class="form-group">
					<label for="apellidos_filtro" class="col-md-3 control-label">Apellido del asegurado</label>
					<div class="col-md-3">
						<input type="text" class="form-control" id="apellidos_filtro">
					</div>
                    <label for="fecha_contrato_filtro" class="col-md-3 control-label">Fecha de corte de la poliza</label>
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="fecha_corte_filtro">
                    </div>
				</div>  
                <div class="form-group">
                    <label for="tipo_poliza_filtro" class="col-md-2 control-label">Tipo de poliza</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="tipo_poliza_filtro">
                    </div>
                    <label for="nombre_razonsocial_filtro" class="col-md-2 control-label">nombre o razon social del cliente contratante</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="nombre_razonsocial_filtro">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <button type="button" id="btn-filter" class="btn btn-primary">Buscar</button>
                        <button type="button" id="btn-reset" class="btn btn-default" onclick="recargar_tabla()">Reiniciar campos</button>
                        <button type="button" id="btn-report" class="btn btn-default" onclick="reporte_pdf()">reporte pdf</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="table-responsive" >
        <table id="table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>CI o Rif del asegurado</th>
                    <th>Nombres y Apellidos del asegurado</th>
                    <th>Cliente o empresa contratante</th>
                    <th>Fecha de corte</th>
                    <th>Fecha de contrato</th>
                    <th>Tipo de poliza</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
            <tfoot>
                <tr>
                    <th>Nº</th>
                    <th>CI o Rif del asegurado</th>
                    <th>Nombres y Apellidos del asegurado</th>
                    <th>Cliente o empresa contratante</th>
                    <th>Fecha de corte</th>
                    <th>Fecha de contrato</th>
                    <th>Tipo de poliza</th>
                    <th>Acciones</th>
                </tr>
            </tfoot>
        </table>
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
        $(document).on('show.bs.modal', '.modal', function (event)
        {
            var zIndex = 1040 + (10 * $('.modal:visible').length);
            $(this).css('z-index', zIndex);
            setTimeout(function(){
                $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
            }, 0);
        });
    });

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
                "url": "<?php echo base_url('Polizas/ajax_list')?>",
                "type": "POST",
                "data": function (data)
    			{   // are send to the server side
    				data.ci_rif_ajaxfiltro = $('#ci_rif_filtro').val(); 
                    data.nombres_ajaxfiltro = $('#nombres_filtro').val();
    				data.apellidos_ajaxfiltro = $('#apellidos_filtro').val();
                    data.fecha_corte_ajaxfiltro = $("#fecha_corte_filtro").val();
                    data.tipo_poliza_ajaxfiltro = $("#tipo_poliza_filtro").val();
                    data.nombre_razonsocial_ajaxfiltro = $("#nombre_razonsocial_filtro").val();
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

    function listar_siniestros(id)
    {
        //datatables
        table_siniestros = $('#table_siniestros').DataTable({ 
            "retrieve": true, //feature control to overwrite the content of the table
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [ 1,'asc'], //[ 9,'asc']], //Initial no order.
            
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url('Siniestros/ajax_list')?>/"+id,
                "type": "POST",
                "data": function (data)
                {
                    data.tipo_solicitud_ajaxfiltro = $('#tipo_solicitud_filtro').val(); // are send to the server side
                    data.fecha_recepcion_ajaxfiltro = $('#fecha_recepcion_filtro').val(); // are send to the server side
                    data.fecha_entrega_ajaxfiltro = $('#fecha_entrega_filtro').val(); // are send to the server side
                    data.observacion_ajaxfiltro = $("#observacion_filtro").val(); // are send to the server side
                    data.monto_solicitado_ajaxfiltro = $('#monto_solicitado_filtro').val(); // are send to the server side
                    data.monto_aprobado_ajaxfiltro = $("#monto_aprobado_filtro").val(); // are send to the server side
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

        $('#btn-filter_siniestros').click(function(){ //button filter event click
            table_siniestros.ajax.reload(null,false);  //just reload table
        });
    }

    function recargar_tabla()
    {
        //button reset event click
        $('#form-filter')[0].reset();
        table.ajax.reload(null,false);  //just reload table   
    }

    function mostrar_gf()
    {
        if (document.getElementById("gf_form").checked)
        {
            document.getElementById("div_gastos_funerarios").style.display = "block";
        }
        else
        {
            document.getElementById("div_gastos_funerarios").style.display = "none";
        }
    }

    function mostrar_Basica()
    {
        if (document.getElementById("Basica_form").checked)
        {
            document.getElementById("div_Basica").style.display = "block";
        }
        else
        {
            document.getElementById("div_Basica").style.display = "none";
        }
    }

    function mostrar_Exceso()
    {
        if (document.getElementById("Exceso_form").checked)
        {
            document.getElementById("div_Exceso").style.display = "block";
        }
        else
        {
            document.getElementById("div_Exceso").style.display = "none";
        }
    }

    function mostrar_vida()
    {
        if (document.getElementById("vida_form").checked)
        {
            document.getElementById("div_vida_form").style.display = "block";
        }
        else
        {
            document.getElementById("div_vida_form").style.display = "none";
        }
    }

    function mostrar_filtros()
    {
        $(".panel-body").toggle(300);
    }

    function mostrar_filtros_siniestro()
    {
        $("#panel_siniestro").toggle(300);
    }

    function isNumberKey(evt)
    {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode != 46 &&(charCode < 48 || charCode > 57)))
        {
            return false;
        }
        return true;
    }

    var id_max_grupo;
    $(document).ready(function()
    {
        $.ajax({
            url: "<?php echo base_url('Polizas/id_max_grupo')?>",
            dataType:"JSON",
            type: "POST",
            success: function(data)
            {
                if(data.status != false)
                {
                    id_max_grupo = data.id; 
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

    var id_max_enfermedad;
    $(document).ready(function()
    {
        $.ajax({
            url: "<?php echo base_url('Polizas/id_max_enfermedad')?>",
            dataType:"JSON",
            type: "POST",
            success: function(data)
            {
                if(data.status != false)
                {
                    id_max_enfermedad = data.id; 
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

    function cargar_preguntas(id = false)
    {
        $.ajax({
            url: "<?php echo base_url('Polizas/cargar_preguntas')?>",
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

                        $('[name="pregunta_enfermedad_form[]"]').html(output.join(''));//agrega el array y lo muestra en el html
                    }
                    else
                    {
                        var output = [];//array en javascript
                        $.each(data.info, function(key, value)//funcion en jquery que opera igual al foreach de php
                        {
                            output.push('<option value="'+key+'">'+value+'</option>');
                        });

                        $('[name="pregunta_enfermedad_form[]"]').html(output.join(''));//agrega el array y lo muestra en el html
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

    function cargar_afectados(id = false)
    {
        var array = [];//array en javascript
        $('[name="nombres_apellidos_grupo_form[]"]').each(function(index, val)
        {
            var value = $('[name="nombres_apellidos_grupo_form[]"]').eq(index).val();//[index]
            var id_val = $('[name="id_grupo[]"]').eq(index).val();//[index]
            if (id == id_val)
            {
                array.push('<option value="'+id_val+'" selected>'+value+'</option>');
            }
            
            array.push('<option value="'+id_val+'">'+value+'</option>');
            
        });
        
        $('[name="select_grupo_form[]"]').html(array.join(''));//agrega el array y lo muestra en el html
    }

    function listar_beneficiarios(id = false)
    {
        $.ajax({
            url: "<?php echo base_url('Siniestros/listar_beneficiarios')?>/"+id,
            dataType:"JSON",
            type: "POST",
            success: function(data)
            {
                if(data.status != false)
                {
                    var output = [];//array en javascript
                    $.each(data.info, function(key, value)//funcion en jquery que opera igual al foreach de php
                    {
                        output.push('<option value="'+key+'">'+value+'</option>');
                    });

                   $('[name="listar_beneficiarios_form"]').html(output.join(''));//agrega el array y lo muestra en el html
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

    function cargar_beneficiarios()
    {
        parametros = {
            id : $('[name="listar_beneficiarios_form"]').val(),
        };

        $.ajax({
            url: "<?php echo base_url('Siniestros/cargar_beneficiarios')?>",
            dataType:"JSON",
            data: parametros,
            type: "POST",
            success: function(data)
            {
                if(data.status != false)
                {
                    $('[name="id_beneficiario_siniestros"]').val(data.info.id);
                    $('[name="tipo_doc_beneficiario_form"]').val(data.info.tipo_doc_beneficiario);
                    $('[name="doc_identidad_beneficiario_form"]').val(data.info.ci_pasaporte_beneficiarios);
                    $('[name="nombre_beneficiario_form"]').val(data.info.nombres_apellidos_beneficiarios);
                    console.log(data.info.ci_pasaporte_beneficiarios);
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

    var count_grupo = 1;// variable que se usa para generar contador para agregar mas campos de grupos
    function agregar_grupo()//funcion que agrega un grupo de asegurado
    {
        id_max_grupo++;
        count_grupo++;
        var objTogrupo = document.getElementById('grupo_form');
        var divgrupo = document.createElement("div");
        divgrupo.setAttribute("class", "col-md-12 removeclass_grupo"+count_grupo);
        divgrupo.innerHTML = 
            '<div class="form-group">\
                <label class="control-label col-md-2">Nombres y apellidos</label>\
                <div class="col-md-2">\
                    <input type="hidden" value="'+id_max_grupo+'" name="id_grupo[]" readonly/>\
                    <textarea name="nombres_apellidos_grupo_form[]" placeholder="Nombres y apellidos" class="form-control" maxlength="200" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Parentesco</label>\
                <div class="col-md-2">\
                    <textarea name="parentesco_grupo_form[]" placeholder="Parentesco" class="form-control" maxlength="20" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">C.I./ Pasaporte</label>\
                <div class="col-md-2">\
                    <textarea name="ci_pasaporte_grupo_form[]" placeholder="C.I./ Pasaporte" class="form-control" maxlength="20" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Fecha de nacimiento</label>\
                <div class="col-md-2">\
                    <input type="date" name="fecha_nacimiento_grupo_form[]" class="form-control" maxlength="15" required>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Edad</label>\
                <div class="col-md-2">\
                    <input type="number" name="edad_grupo_form[]" class="form-control" onkeypress="return isNumberKey(event)" required>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Sexo</label>\
                <div class="col-md-2">\
                    <select name="sexo_grupo_form[]" class="form-control" required>\
                        <option value="">Seleccione</option>\
                        <option value="Hombre">Hombre</option>\
                        <option value="Mujer">Mujer</option>\
                    </select>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Estado civil</label>\
                <div class="col-md-2">\
                    <select name="estado_civil_grupo_form[]" class="form-control" required>\
                        <option value="">Seleccione</option>\
                        <option value="Soltero">Soltero</option>\
                        <option value="Casado">Casado</option>\
                        <option value="Divorciado">Divorciado</option>\
                        <option value="Viudo">Viudo</option>\
                        <option value="Otro">Otro</option>\
                    </select>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Estatura</label>\
                <div class="col-md-2">\
                    <input name="estatura_grupo_form[]" placeholder="Estatura" class="form-control" type="text" maxlength="6" required>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Peso</label>\
                <div class="col-md-2">\
                    <input name="peso_grupo_form[]" placeholder="Peso" class="form-control" type="text" maxlength="6" required>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Zurdo</label>\
                <div class="col-md-2">\
                    <select name="zurdo_grupo_form[]" class="form-control" required>\
                        <option value="si">si</option>\
                        <option value="no" selected>no</option>\
                    </select>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Profesion</label>\
                <div class="col-md-2">\
                    <textarea name="profesion_grupo_form[]" placeholder="Profesion" class="form-control" maxlength="100" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Ocupacion</label>\
                <div class="col-md-2">\
                    <textarea name="ocupacion_grupo_form[]" placeholder="Ocupacion" class="form-control" maxlength="100" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Pasatiempos</label>\
                <div class="col-md-4">\
                    <textarea name="pasatiempos_grupo_form[]" placeholder="Pasatiempos" class="form-control" maxlength="200" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Deportes</label>\
                <div class="col-md-4">\
                    <textarea name="deportes_grupo_form[]" placeholder="Deportes" class="form-control" maxlength="200" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <button class="btn btn-danger" type="button" onclick="remover_grupo('+ count_grupo +');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> Remover persona</button>\
            <div class="clear"></div>';
        
        objTogrupo.appendChild(divgrupo);
    }

    function modificar_grupo(id,nombres_apellidos_grupo,parentesco_grupo,ci_pasaporte_grupo,fecha_nacimiento_grupo,edad_grupo,sexo_grupo,estado_civil_grupo,peso_grupo,estatura_grupo,zurdo_grupo,profesion_grupo,ocupacion_grupo,pasatiempos_grupo,deportes_grupo)//funcion que agrega un grupo de asegurado
    {
        var htmlgrupo;
        count_grupo++;
        var objTogrupo = document.getElementById('grupo_form')
        var divgrupo = document.createElement("div");
        divgrupo.setAttribute("class", "col-md-12 removeclass_grupo"+count_grupo);
        
        htmlgrupo = '<div class="form-group">\
                <input type="hidden" value="'+id+'" name="id_grupo[]" readonly/>\
                <label class="control-label col-md-2">Nombres y apellidos</label>\
                <div class="col-md-2">\
                    <textarea name="nombres_apellidos_grupo_form[]" placeholder="Nombres y apellidos" class="form-control" maxlength="200" required>'+nombres_apellidos_grupo+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Parentesco</label>\
                <div class="col-md-2">\
                    <textarea name="parentesco_grupo_form[]" placeholder="Parentesco" class="form-control" maxlength="20" required>'+parentesco_grupo+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">C.I./ Pasaporte</label>\
                <div class="col-md-2">\
                    <textarea name="ci_pasaporte_grupo_form[]" placeholder="C.I./ Pasaporte" class="form-control" maxlength="20" required>'+ci_pasaporte_grupo+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Fecha de nacimiento</label>\
                <div class="col-md-2">\
                    <input type="date" value="'+fecha_nacimiento_grupo+'" name="fecha_nacimiento_grupo_form[]" class="form-control" maxlength="15" required>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Edad</label>\
                <div class="col-md-2">\
                    <input type="number" value="'+edad_grupo+'" name="edad_grupo_form[]" class="form-control" onkeypress="return isNumberKey(event)" required>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Sexo</label>\
                <div class="col-md-2">\
                    <select name="sexo_grupo_form[]" class="form-control" required>\
                        <option value="">Seleccione</option>';
                    if (sexo_grupo == "Hombre")
                    {
                        htmlgrupo += '<option value="Hombre" selected>Hombre</option>\
                        <option value="Mujer">Mujer</option>'; 
                    }
                    if (sexo_grupo == "Mujer")
                    {
                        htmlgrupo += '<option value="Hombre">Hombre</option>\
                        <option value="Mujer" selected>Mujer</option>'; 
                    }
                    htmlgrupo += '</select>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Estado civil</label>\
                <div class="col-md-2">\
                    <select name="estado_civil_grupo_form[]" class="form-control" required>\
                        <option value="">Seleccione</option>';
                    if (estado_civil_grupo == "Soltero")
                    {
                        htmlgrupo += '<option value="Soltero" selected>Soltero</option>\
                        <option value="Casado">Casado</option>\
                        <option value="Divorciado">Divorciado</option>\
                        <option value="Viudo">Viudo</option>\
                        <option value="Otro">Otro</option>';
                    }
                    if (estado_civil_grupo == "Casado")
                    {
                        htmlgrupo += '<option value="Soltero">Soltero</option>\
                        <option value="Casado" selected>Casado</option>\
                        <option value="Divorciado">Divorciado</option>\
                        <option value="Viudo">Viudo</option>\
                        <option value="Otro">Otro</option>';
                    }
                    if (estado_civil_grupo == "Divorciado")
                    {
                        htmlgrupo += '<option value="Soltero">Soltero</option>\
                        <option value="Casado">Casado</option>\
                        <option value="Divorciado" selected>Divorciado</option>\
                        <option value="Viudo">Viudo</option>\
                        <option value="Otro">Otro</option>';
                    }
                    if (estado_civil_grupo == "Viudo")
                    {
                        htmlgrupo += '<option value="Soltero">Soltero</option>\
                        <option value="Casado">Casado</option>\
                        <option value="Divorciado">Divorciado</option>\
                        <option value="Viudo" selected>Viudo</option>\
                        <option value="Otro">Otro</option>';
                    }
                    if (estado_civil_grupo == "Otro")
                    {
                        htmlgrupo += '<option value="Soltero">Soltero</option>\
                        <option value="Casado">Casado</option>\
                        <option value="Divorciado">Divorciado</option>\
                        <option value="Viudo">Viudo</option>\
                        <option value="Otro" selected>Otro</option>';
                    }
                    htmlgrupo += '</select>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Estatura</label>\
                <div class="col-md-2">\
                    <input type="text" value="'+estatura_grupo+'" name="estatura_grupo_form[]" placeholder="Estatura" class="form-control"  maxlength="6" required>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Peso</label>\
                <div class="col-md-2">\
                    <input type="text" value="'+peso_grupo+'" name="peso_grupo_form[]" placeholder="Peso" class="form-control"  maxlength="6" required>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Zurdo</label>\
                <div class="col-md-2">\
                    <select name="zurdo_grupo_form[]" class="form-control" required>';
                    if (zurdo_grupo === "si")
                    {
                        htmlgrupo += '<option value="si" selected>si</option>\
                        <option value="no">no</option>';
                    }
                    if (zurdo_grupo === "no")
                    {
                        htmlgrupo += '<option value="si">si</option>\
                        <option value="no" selected>no</option>';
                    }
                    htmlgrupo += '</select>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Profesion</label>\
                <div class="col-md-2">\
                    <textarea name="profesion_grupo_form[]" placeholder="Profesion" class="form-control" maxlength="100" required>'+profesion_grupo+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Ocupacion</label>\
                <div class="col-md-2">\
                    <textarea name="ocupacion_grupo_form[]" placeholder="Ocupacion" class="form-control" maxlength="100" required>'+ocupacion_grupo+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Pasatiempos</label>\
                <div class="col-md-4">\
                    <textarea name="pasatiempos_grupo_form[]" placeholder="Pasa tiempos" class="form-control" maxlength="200" required>'+pasatiempos_grupo+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Deportes</label>\
                <div class="col-md-4">\
                    <textarea name="deportes_grupo_form[]" placeholder="Deportes" class="form-control" maxlength="200" required>'+deportes_grupo+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <button class="btn btn-danger" type="button" onclick="remover_grupo('+count_grupo+','+id+');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> Remover persona</button>\
            <div class="clear"></div>';
        divgrupo.innerHTML = htmlgrupo;
        objTogrupo.appendChild(divgrupo);
    }

    function remover_grupo(rid,id = false)//funcion que remueve un grupo de asegurado
    {
        if (id == false)
        {
            id_max_grupo--;
            $('.removeclass_grupo'+rid).remove();
        }
        else if (id != false)
        {
            if(confirm('Esta seguro de querer borrar esta información?'))
            {
                id_max_grupo--;
                $('.removeclass_grupo'+rid).remove();
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo base_url('Polizas/ajax_delete_grupo')?>/"+id,
                    type: "POST",
                    dataType: "JSON",
                    success: function(data)
                    {
                        if(data.status == true) //if success close modal and reload ajax table
                        {
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
    }

    var count_beneficiario = 1;// variable que se usa para generar contador para agregar mas campos de beneficiarios
    function agregar_beneficiario()//funcion que agrega un grupo de beneficiarios
    {
        count_beneficiario++;
        var objTobeneficiario = document.getElementById('beneficiados_form')
        var divbeneficiario = document.createElement("div");
        divbeneficiario.setAttribute("class", "col-md-12 removeclass_beneficiario"+count_beneficiario);
        divbeneficiario.innerHTML = 
            '<div class="form-group">\
                <input type="hidden" value="" name="id_beneficiario[]" readonly/>\
                <label class="control-label col-md-2">Tipo de documento</label>\
                <div class="col-sm-4">\
                    <select name="tipo_doc_identidad_beneficiario_form[]" class="form-control">\
                        <option value="">Seleccione</option>\
                        <option value="v">Cedula</option>\
                        <option value="j">Rif</option>\
                        <option value="e">Extranjero</option>\
                        <option value="p">Pasaporte</option>\
                    </select>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Doc de indentidad</label>\
                <div class="col-md-4">\
                    <textarea name="ci_pasaporte_beneficiarios_form[]" placeholder="C.I./ Pasaporte" class="form-control" maxlength="20" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-4">Nombres y Apellidos</label>\
                <div class="col-md-8">\
                    <textarea name="nombres_apellidos_beneficiarios_form[]" placeholder="Nombres y Apellidos" class="form-control" maxlength="200" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Parentesco</label>\
                <div class="col-md-4">\
                    <textarea name="parentesco_beneficiarios_form[]" placeholder="Parentesco" class="form-control" maxlength="20" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Distribucion %</label>\
                <div class="col-md-4">\
                    <input type="text" name="distribucion_beneficiarios_form[]" placeholder="Distribucion %" class="form-control" maxlength="11" onkeypress="return isNumberKey(event)" required>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <button class="btn btn-danger" type="button" onclick="remover_beneficiario('+ count_beneficiario +');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> Remover beneficiario</button>\
            <div class="clear"></div>';
        
        objTobeneficiario.appendChild(divbeneficiario);
    }

    function modificar_beneficiario(id,tipo_doc_beneficiario,ci_pasaporte_beneficiarios,nombres_apellidos_beneficiarios,parentesco_beneficiarios,distribucion_beneficiarios)//funcion que agrega un grupo de beneficiarios
    {
        var htmlbeneficiario;
        count_beneficiario++;
        var objTobeneficiario = document.getElementById('beneficiados_form')
        var divbeneficiario = document.createElement("div");
        divbeneficiario.setAttribute("class", "col-md-12 removeclass_beneficiario"+count_beneficiario);
        htmlbeneficiario = 
            '<div class="form-group">\
                <input type="hidden" value="'+id+'" name="id_beneficiario[]" readonly/>\
                <label class="control-label col-md-2">Tipo de documento</label>\
                <div class="col-sm-4">\
                    <select name="tipo_doc_identidad_beneficiario_form[]" class="form-control">';
                    if (tipo_doc_beneficiario == "v")
                    {
                        htmlbeneficiario += '<option value="">Seleccione</option>\
                        <option value="v" selected>Cedula</option>\
                        <option value="j">Rif</option>\
                        <option value="e">Extranjero</option>\
                        <option value="p">Pasaporte</option>';
                    }
                    if (tipo_doc_beneficiario == "j")
                    {
                        htmlbeneficiario += '<option value="">Seleccione</option>\
                        <option value="v">Cedula</option>\
                        <option value="j" selected>Rif</option>\
                        <option value="e">Extranjero</option>\
                        <option value="p">Pasaporte</option>';
                    }
                    if (tipo_doc_beneficiario == "e")
                    {
                        htmlbeneficiario += '<option value="">Seleccione</option>\
                        <option value="v">Cedula</option>\
                        <option value="j">Rif</option>\
                        <option value="e" selected>Extranjero</option>\
                        <option value="p">Pasaporte</option>';
                    }
                    if (tipo_doc_beneficiario == "p")
                    {
                        htmlbeneficiario += '<option value="">Seleccione</option>\
                        <option value="v">Cedula</option>\
                        <option value="j">Rif</option>\
                        <option value="e">Extranjero</option>\
                        <option value="p" selected>Pasaporte</option>';
                    }
                    if (tipo_doc_beneficiario == "")
                    {
                        htmlbeneficiario += '<option value="">Seleccione</option>\
                        <option value="v">Cedula</option>\
                        <option value="j">Rif</option>\
                        <option value="e">Extranjero</option>\
                        <option value="p">Pasaporte</option>';
                    }
                    htmlbeneficiario += '</select>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Doc de identidad</label>\
                <div class="col-md-4">\
                    <textarea name="ci_pasaporte_beneficiarios_form[]" placeholder="Doc de identidad" class="form-control" maxlength="20" required>'+ci_pasaporte_beneficiarios+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-4">Nombres y Apellidos</label>\
                <div class="col-md-8">\
                    <textarea name="nombres_apellidos_beneficiarios_form[]" placeholder="Nombres y Apellidos" class="form-control" maxlength="200" required>'+nombres_apellidos_beneficiarios+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Parentesco</label>\
                <div class="col-md-4">\
                    <textarea name="parentesco_beneficiarios_form[]" placeholder="Parentesco" class="form-control" maxlength="20" required>'+parentesco_beneficiarios+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Distribucion %</label>\
                <div class="col-md-4">\
                    <input type="text" name="distribucion_beneficiarios_form[]" placeholder="Distribucion %" class="form-control" maxlength="11" value="'+distribucion_beneficiarios+'" onkeypress="return isNumberKey(event)" required>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <button class="btn btn-danger" type="button" onclick="remover_beneficiario('+count_beneficiario+','+id+');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> Remover beneficiario</button>\
            <div class="clear"></div>';
        divbeneficiario.innerHTML = htmlbeneficiario;
        objTobeneficiario.appendChild(divbeneficiario);
    }

    function remover_beneficiario(rid,id = false)//funcion que remueve un grupo de asbeneficiariosegurado
    {
        if (id == false)
        {
            $('.removeclass_beneficiario'+rid).remove();
        }
        if (id != false)
        {
            if(confirm('Esta seguro de querer borrar esta información?'))
            {
                $('.removeclass_beneficiario'+rid).remove();
            
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo base_url('Polizas/ajax_delete_beneficiario')?>/"+id,
                    type: "POST",
                    dataType: "JSON",
                    success: function(data)
                    {
                        if(data.status == true) //if success close modal and reload ajax table
                        {
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
    }

    var count_otros_seguros = 1;// variable que se usa para generar contador para agregar mas campos de otros seguros
    function agregar_otros_seguros()//funcion que agrega un grupo de otros seguros
    {
        count_otros_seguros++;
        var objTootros_seguros = document.getElementById('otros_seguros_form')
        var divotros_seguros = document.createElement("div");
        divotros_seguros.setAttribute("class", "col-md-12 removeclass_otros_seguros"+count_otros_seguros);
        divotros_seguros.innerHTML = 
            '<div class="form-group">\
                <input type="hidden" value="" name="id_otros_seguros[]" readonly/>\
                <label class="control-label col-md-2">Nombre de la empresa</label>\
                <div class="col-md-4">\
                    <textarea name="nombre_empresa_form[]" placeholder="Nombre de la empresa" class="form-control" maxlength="200" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Numero de poliza</label>\
                <div class="col-md-4">\
                    <textarea name="numero_poliza_form[]" placeholder="Numero de poliza" class="form-control" maxlength="11" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Monto</label>\
                <div class="col-md-4">\
                    <textarea name="monto_form[]" placeholder="Monto" class="form-control" maxlength="20" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Estatus poliza</label>\
                <div class="col-md-4">\
                    <select name="estado_poliza_form[]" class="form-control" required>\
                        <option value="anulada">Anulada</option>\
                        <option value="vigente">Vigente</option>\
                    </select>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <button class="btn btn-danger" type="button" onclick="remover_otros_seguros('+ count_otros_seguros +');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> Remover seguro</button>\
            <div class="clear"></div>';
        
        objTootros_seguros.appendChild(divotros_seguros);
    }

    function modificar_otros_seguros(id,nombre_empresa,numero_poliza,monto,estado_poliza)//funcion que agrega un grupo de otros seguros
    {
        var htmlotros_seguros;
        count_otros_seguros++;
        var objTootros_seguros = document.getElementById('otros_seguros_form')
        var divotros_seguros = document.createElement("div");
        divotros_seguros.setAttribute("class", "col-md-12 removeclass_otros_seguros"+count_otros_seguros);
            htmlotros_seguros = '<div class="form-group">\
                <input type="hidden" value="'+id+'" name="id_otros_seguros[]" readonly/>\
                <label class="control-label col-md-2">Nombre de la empresa</label>\
                <div class="col-md-4">\
                    <textarea name="nombre_empresa_form[]" placeholder="Nombre de la empresa" class="form-control" maxlength="200" required>'+nombre_empresa+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Numero de poliza</label>\
                <div class="col-md-4">\
                    <textarea name="numero_poliza_form[]" placeholder="Numero de poliza" class="form-control" maxlength="11" required>'+monto+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Monto</label>\
                <div class="col-md-4">\
                    <textarea name="monto_form[]" placeholder="Monto" class="form-control" maxlength="20" required>'+numero_poliza+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Estatus poliza</label>\
                <div class="col-md-4">\
                    <select name="estado_poliza_form[]" class="form-control" required>';
                    if (estado_poliza == "anulada")
                    {
                        htmlotros_seguros += '<option value="anulada" selected>Anulada</option>\
                        <option value="vigente">Vigente</option>';
                    }
                    if (estado_poliza == "vigente")
                    {
                        htmlotros_seguros += '<option value="vigente" selected>Vigente</option>\
                        <option value="anulada">Anulada</option>';
                    }
                    htmlotros_seguros += '</select>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <button class="btn btn-danger" type="button" onclick="remover_otros_seguros('+count_otros_seguros+','+id+');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> Remover seguro</button>\
            <div class="clear"></div>';
        divotros_seguros.innerHTML = htmlotros_seguros;
        objTootros_seguros.appendChild(divotros_seguros);
    }

    function remover_otros_seguros(rid,id = false)//funcion que remueve un grupo de otros seguros
    {
        if (id == false)
        {
            $('.removeclass_otros_seguros'+rid).remove();
        }
        else if (id != false)
        {
            if(confirm('Esta seguro de querer borrar esta información?'))
            {
                $('.removeclass_otros_seguros'+rid).remove();
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo base_url('Polizas/ajax_delete_seguros')?>/"+id,
                    type: "POST",
                    dataType: "JSON",
                    success: function(data)
                    {
                         if(data.status == true) //if success close modal and reload ajax table
                        {
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
    }

    function agregar_enfermedad()//funcion que agrega un grupo de otros seguros
    {
        id_max_enfermedad++;
        var objToenfermedad = document.getElementById('enfermedad_form')
        var divenfermedad = document.createElement("div");
        divenfermedad.setAttribute("class", "col-md-12 removeclass_enfermedad"+id_max_enfermedad);
        divenfermedad.innerHTML = 
            '<div class="form-group">\
                <input type="hidden" value="" name="id_enfermedad[]" readonly/>\
                <label class="control-label col-md-2">Pregunta seleccionada</label>\
                <div class="col-md-10">\
                    <select name="pregunta_enfermedad_form[]" class="form-control">\
                    </select>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Persona afectada</label>\
                <div class="col-md-4">\
                    <select id="select_grupo_form'+id_max_enfermedad+'" name="select_grupo_form[]" class="form-control">\
                    </select>\
                </div>\
                <label class="control-label col-md-2">Diagnostico o intervencion</label>\
                <div class="col-md-4">\
                    <textarea name="diagnostico_intervencion_enfermedad_form[]" placeholder="Diagnostico o intervencion" class="form-control" maxlength="200" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Fecha</label>\
                <div class="col-md-4">\
                    <input type="date" name="fecha_enfermedad_form[]" class="form-control" required>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Nombres y apellidos del medico</label>\
                <div class="col-md-4">\
                    <textarea name="nombres_apellidos_medico_enfermedad_form[]" placeholder="Nombres y apellidos del medico" class="form-control" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Ubicacion del medico</label>\
                <div class="col-md-4">\
                    <textarea name="ubicacion_medico_enfermedad_form[]" placeholder="Ubicacion del medico" class="form-control" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Condicion actual</label>\
                <div class="col-md-4">\
                    <textarea name="condicion_actual_enfermedad_form[]" placeholder="Condicion actual" class="form-control" maxlength="200" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <button class="btn btn-success" type="button" onclick="agregar_documento('+id_max_enfermedad+')"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> Agregar documento</button>\
            <div id="documento_form'+id_max_enfermedad+'" class="form-group">\
            </div>\
            <button class="btn btn-danger" type="button" onclick="remover_enfermedad('+id_max_enfermedad+');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> Remover persona del grupo</button>\
            <div class="clear"></div>';
        
        objToenfermedad.appendChild(divenfermedad);
        cargar_preguntas();
        cargar_afectados();
    }

    function modificar_enfermedad(id,id_pregunta,id_grupo,diagnostico_intervencion_enfermedad,fecha_enfermedad,nombres_apellidos_medico_enfermedad,ubicacion_medico_enfermedad,condicion_actual_enfermedad)//funcion que agrega un grupo de otros seguros
    {
        var htmlenfermedad;
        var objToenfermedad = document.getElementById('enfermedad_form')
        var divenfermedad = document.createElement("div");
        divenfermedad.setAttribute("class", "col-md-12 removeclass_enfermedad"+id);
            htmlenfermedad = 
            '<div class="form-group">\
                <input type="hidden" value="'+id+'" name="id_enfermedad[]" readonly/>\
                <label class="control-label col-md-2">Pregunta seleccionada</label>\
                <div class="col-md-10">\
                    <select name="pregunta_enfermedad_form[]" class="form-control">\
                    </select>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Persona afectada</label>\
                <div class="col-md-4">\
                    <select id="select_grupo_form'+id+'" name="select_grupo_form[]" class="form-control">\
                    </select>\
                </div>\
                <label class="control-label col-md-2">Diagnostico o intervencion</label>\
                <div class="col-md-4">\
                    <textarea name="diagnostico_intervencion_enfermedad_form[]" placeholder="Diagnostico o intervencion" class="form-control" maxlength="200" required>'+diagnostico_intervencion_enfermedad+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Fecha</label>\
                <div class="col-md-4">\
                    <input type="date" value="'+fecha_enfermedad+'" name="fecha_enfermedad_form[]" class="form-control" required>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Nombres y apellidos del medico</label>\
                <div class="col-md-4">\
                    <textarea name="nombres_apellidos_medico_enfermedad_form[]" placeholder="Nombres y apellidos del medico" class="form-control" required>'+nombres_apellidos_medico_enfermedad+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Ubicacion del medico</label>\
                <div class="col-md-4">\
                    <textarea name="ubicacion_medico_enfermedad_form[]" placeholder="Ubicacion del medico" class="form-control" required>'+ubicacion_medico_enfermedad+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Condicion actual</label>\
                <div class="col-md-4">\
                    <textarea name="condicion_actual_enfermedad_form[]" placeholder="Causa" class="form-control" maxlength="200" required>'+condicion_actual_enfermedad+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <button class="btn btn-success" type="button" onclick="agregar_documento('+id+')"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> Agregar documento</button>\
            <div id="documento_form'+id+'" class="form-group">\
            </div>\
            <button class="btn btn-danger" type="button" onclick="remover_enfermedad('+id+','+id+');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> Remover persona del grupo</button>\
            <div class="clear"></div>';
        divenfermedad.innerHTML = htmlenfermedad;
        objToenfermedad.appendChild(divenfermedad);
        cargar_preguntas(id_pregunta);
        cargar_afectados(id_grupo);
    }

    function remover_enfermedad(rid,id = false)//funcion que remueve un grupo de otros seguros
    {
        if(id == false)
        {
            id_max_enfermedad--;
            $('.removeclass_enfermedad'+rid).remove();
        }
        else if (id != false)
        {
            if(confirm('Esta seguro de querer borrar esta información?'))
            {
                id_max_enfermedad--;
                $('.removeclass_enfermedad'+rid).remove();
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo base_url('Polizas/ajax_delete_enfermedad')?>/"+id,
                    type: "POST",
                    dataType: "JSON",
                    success: function(data)
                    {
                        if(data.status == true) //if success close modal and reload ajax table
                        {
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
    }

    var count_documento = 1;
    function agregar_documento(id_enfermedad)//funcion que agrega un documento
    {
        var valor = document.getElementById('select_grupo_form'+id_enfermedad).value;
        count_documento ++;
        var objTodocumento = document.getElementById('documento_form'+id_enfermedad);
        var divdocumento = document.createElement("div");
        divdocumento.setAttribute("class", "col-md-12 removeclass_documento"+count_documento);
        divdocumento.innerHTML = 
            '<div class="form-group">\
                <input type="hidden" name="id_documento[]" value="" readonly>\
                <input type="hidden" name="id_enfermedad_documento[]" value="'+valor+'" readonly>\
                <label class="control-label col-md-2">Documento</label>\
                <div class="col-md-10 input-group">\
                    <textarea name="descripcion_archivo_form[]" placeholder="Descripcion del documento" class="form-control" required></textarea>\
                    <input type="file" name="archivo_form[]" class="form-control" multiple>\
                    <span class="help-block"></span>\
                    <button class="btn btn-danger" type="button" onclick="remover_documento('+count_documento+');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> Remover documento</button>\
                    <div class="clear"></div>\
                </div>\
            </div>';
 
        objTodocumento.appendChild(divdocumento);
    }

    function modificar_documento(id,id_enfermedad,descripcion_archivo)
    {
        var valor = document.getElementById('select_grupo_form'+id_enfermedad).value;
        count_documento ++;
        var objTodocumento = document.getElementById('documento_form'+id_enfermedad)
        var divdocumento = document.createElement("div");
        divdocumento.setAttribute("class", "col-md-12 removeclass_documento"+count_documento);
        divdocumento.innerHTML = 
            '<div class="form-group">\
                <input type="hidden" name="id_documento[]" value="'+id+'" readonly>\
                <input type="hidden" name="id_enfermedad_documento[]" value="'+valor+'" readonly>\
                <label class="control-label col-md-2">Documento</label>\
                <div class="col-md-10 input-group">\
                    <textarea name="descripcion_archivo_form[]" placeholder="Descripcion del documento" class="form-control" required>'+descripcion_archivo+'</textarea>\
                    <input type="file" name="archivo_form[]" class="form-control" multiple>\
                    <span class="help-block"></span>\
                    <button class="btn btn-danger" type="button" onclick="remover_documento('+count_documento+','+id+');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> Remover documento</button>\
                    <div class="clear"></div>\
                </div>\
            </div>';
 
        objTodocumento.appendChild(divdocumento);
    }

    function remover_documento(rid,id = false)//funcion que remueve un grupo de otros seguros
    {
        if (id == false)
        {
            $('.removeclass_documento'+rid).remove();
        }
        else if (id != false)
        {
            if(confirm('Esta seguro de querer borrar esta información?'))
            {
                $('.removeclass_documento'+rid).remove();
                
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo base_url('Polizas/ajax_delete_documento')?>/"+id,
                    type: "POST",
                    dataType: "JSON",
                    success: function(data)
                    {
                        if(data.status == true) //if success close modal and reload ajax table
                        {
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
    }

    var count_padecimiento = 1;// variable que se usa para generar contador para agregar mas campos de otros seguros
    function agregar_padecimiento()//funcion que agrega un grupo de otros seguros
    {
        count_padecimiento++;
        var objTopadecimiento = document.getElementById('padecimiento_form')
        var divpadecimiento = document.createElement("div");
        divpadecimiento.setAttribute("class", "col-md-12 removeclass_padecimiento"+count_padecimiento);
        divpadecimiento.innerHTML = 
            '<div class="form-group">\
                <input type="hidden" value="" name="id_padecimiento[]" readonly/>\
                <label class="control-label col-md-2">Nombre y apellidos</label>\
                <div class="col-md-4">\
                    <textarea name="nombres_apellidos_padecimiento_form[]" placeholder="Nombre y apellidos" class="form-control" maxlength="200" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Parentesco</label>\
                <div class="col-md-4">\
                    <textarea name="parentesco_padecimiento_form[]" placeholder="Parentesco" class="form-control" maxlength="30" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Edad</label>\
                <div class="col-md-4">\
                    <input type="number" value="" name="edad_padecimiento_form[]" placeholder="Edad" class="form-control" onkeypress="return isNumberKey(event)" required>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">¿Fallecio?</label>\
                <div class="col-md-4">\
                <select name="fallecido_padecimiento_form[]" class="form-control" required>\
                        <option value="">Seleccione</option>\
                        <option value="si">si</option>\
                        <option value="no">no</option>\
                    </select>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Causa</label>\
                <div class="col-md-10">\
                    <textarea name="causa_padecimiento_form[]" placeholder="Causa" class="form-control" maxlength="200" required></textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <button class="btn btn-danger" type="button" onclick="remover_padecimiento('+count_padecimiento+');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> Remover persona</button>\
            <div class="clear"></div>';
        
        objTopadecimiento.appendChild(divpadecimiento);
    }

    function modificar_padecimiento(id,nombres_apellidos_padecimiento,parentesco_padecimiento,edad_padecimiento,fallecido_padecimiento,causa_padecimiento)//funcion que agrega un grupo de otros seguros
    {
        var htmlpadecimiento;
        count_padecimiento++;
        var objTopadecimiento = document.getElementById('padecimiento_form')
        var divpadecimiento = document.createElement("div");
        divpadecimiento.setAttribute("class", "col-md-12 removeclass_padecimiento"+count_padecimiento);
            htmlpadecimiento = '<div class="form-group">\
                <input type="hidden" value="'+id+'" name="id_padecimiento[]" readonly/>\
                <label class="control-label col-md-2">Nombre y apellidos</label>\
                <div class="col-md-4">\
                    <textarea name="nombres_apellidos_padecimiento_form[]" placeholder="Nombre y apellidos" class="form-control" maxlength="200" required>'+nombres_apellidos_padecimiento+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">Parentesco</label>\
                <div class="col-md-4">\
                    <textarea name="parentesco_padecimiento_form[]" placeholder="Parentesco" class="form-control" maxlength="30" required>'+parentesco_padecimiento+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Edad</label>\
                <div class="col-md-4">\
                    <input type="number" value="'+edad_padecimiento+'" name="edad_padecimiento_form[]" placeholder="Edad" class="form-control" onkeypress="return isNumberKey(event)" required>\
                    <span class="help-block"></span>\
                </div>\
                <label class="control-label col-md-2">¿Fallecio?</label>\
                <div class="col-md-4">\
                <select name="fallecido_padecimiento_form[]" class="form-control" required>\
                    <option value="">Seleccione</option>';
                    if (fallecido_padecimiento === "si")
                    {
                        htmlpadecimiento += '<option value="si" selected>si</option>\
                        <option value="no">no</option>';
                    }
                    if (fallecido_padecimiento === "no")
                    {
                        htmlpadecimiento += '<option value="si">si</option>\
                        <option value="no" selected>no</option>';
                    }
                    htmlpadecimiento += '</select>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label class="control-label col-md-2">Causa</label>\
                <div class="col-md-10">\
                    <textarea name="causa_padecimiento_form[]" placeholder="Causa" class="form-control" maxlength="200" required>'+causa_padecimiento+'</textarea>\
                    <span class="help-block"></span>\
                </div>\
            </div>\
            <button class="btn btn-danger" type="button" onclick="remover_padecimiento('+count_padecimiento+','+id+');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> Remover persona</button>\
            <div class="clear"></div>';
        divpadecimiento.innerHTML = htmlpadecimiento;
        objTopadecimiento.appendChild(divpadecimiento);
    }

    function remover_padecimiento(rid,id = false)//funcion que remueve un grupo de otros seguros
    {
        if (id == false)
        {
            $('.removeclass_padecimiento'+rid).remove();
        }
        else if (id != false)
        {
            if(confirm('Esta seguro de querer borrar esta información?'))
            {
                $('.removeclass_padecimiento'+rid).remove();
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo base_url('Polizas/ajax_delete_padecimiento')?>/"+id,
                    type: "POST",
                    dataType: "JSON",
                    success: function(data)
                    {
                        if(data.status == true) //if success close modal and reload ajax table
                        {
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
    }

    $(document).ready(function()
    {
        $.ajax({
            url: "<?php echo base_url('Polizas/get_list_empresas')?>",
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

    function cargar_list_contratantes_registrados()
    {
        $.ajax({
            url: "<?php echo base_url('Polizas/get_list_contratante')?>",
            dataType:"JSON",
            type: "POST",
            success: function(data)
            {
                if(data.status != false)
                {
                    var output = [];//array en javascript
                    $.each(data.contratante, function(key, value)//funcion en jquery que opera igual al foreach de php
                    {
                        output.push('<option value="'+key+'">'+value+'</option>');
                    });

                    $('[name="contratante_registrado_form"]').html(output.join(''));//agrega el array y lo muestra en el html
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
    };

    function cargar_contratantes_registrados()
    {
        parametros = {
            id : $('[name="contratante_registrado_form"]').val(),
        };

        $.ajax({
            url: "<?php echo base_url('Polizas/get_contratante')?>",
            dataType:"JSON",
            type: "POST",
            data:  parametros,
            success: function(data)
            {
                if(data.status != false)
                {   //inicio siniestros
                    $('[name="id"]').val(data.id);
                    $('[name="nombre_razonsocial_form"]').val(data.nombre_razonsocial);
                    $('[name="tipo_documento_contratante_form"]').val(data.tipo_documento_contratante);
                    $('[name="ci_rif_form"]').val(data.ci_rif);
                    $('[name="tipo_persona_form"]').val(data.tipo_persona);
                    $('[name="nacionalidad_form"]').val(data.nacionalidad);
                    $('[name="sexo_form"]').val(data.sexo);                 
                    $('[name="estado_civil_form"]').val(data.estado_civil);
                    $('[name="fecha_nacimiento_constitucion_form"]').val(data.fecha_nacimiento_constitucion);
                    $('[name="lugar_nacimiento_constitucion_form"]').val(data.lugar_nacimiento_constitucion);
                    $('[name="nombre_registro_mercantil_form"]').val(data.nombre_registro_mercantil);
                    $('[name="numero_registro_form"]').val(data.numero_registro);
                    $('[name="numero_tomo_form"]').val(data.numero_tomo);
                    $('[name="profesion_actividad_economica_form"]').val(data.profesion_actividad_economica);
                    $('[name="ingreso_prome_anual_form"]').val(data.ingreso_prome_anual);
                    $('[name="representante_legal_form"]').val(data.representante_legal);
                    $('[name="ci_representante_legal_form"]').val(data.ci_representante_legal);
                    $('[name="pais_form"]').val(data.pais);
                    $('[name="estado_form"]').val(data.estado);
                    $('[name="ciudad_form"]').val(data.ciudad);
                    $('[name="municipio_form"]').val(data.municipio);
                    $('[name="parroquia_form"]').val(data.parroquia);
                    $('[name="urbanizacion_form"]').val(data.urbanizacion);
                    $('[name="calle_form"]').val(data.calle);
                    $('[name="centrocomercial_casa_form"]').val(data.centrocomercial_casa);
                    $('[name="piso_form"]').val(data.piso);
                    $('[name="num_apto_form"]').val(data.num_apto);
                    $('[name="telf1_form"]').val(data.telf1);
                    $('[name="telf2_form"]').val(data.telf2);
                    $('[name="telf3_form"]').val(data.telf3);
                    $('[name="telf_cel_form"]').val(data.telf_cel);
                    $('[name="fax_form"]').val(data.fax);
                    $('[name="zona_postal_form"]').val(data.zona_postal);
                    $('[name="correo_form"]').val(data.correo);
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

    function reporte_individual()
    {
        parametros = {
            id : $('[name="id"]').val(),
            id_empresa : $('[name="id_empresa_form"]').val(),
        };
        
        $.ajax({
            url: '<?php echo base_url("Polizas/reporte_individual");?>',
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

    function reporte_pdf()
    {
        var parametros = {// are send to the server side
            ci_rif_ajaxreporte : $('#ci_rif_filtro').val(),
            nombres_ajaxreporte : $('#nombres_filtro').val(),
            apellidos_ajaxreporte : $('#apellidos_filtro').val(),
            fecha_corte_ajaxreporte : $("#fecha_corte_filtro").val(),
            id_empresa : $('[name="id_empresa_form"]').val(),
            tipo_poliza_ajaxreporte : $("#tipo_poliza_filtro").val(),
        };

        $.ajax({
            url: '<?php echo base_url("Polizas/reporte");?>',
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

    function detalles_ajax(id)
    {
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
    	$('#btnSave').hide();
        $("#btnprint").show();
        $("#grupo_form").empty();
        $("#beneficiados_form").empty();
        $("#otros_seguros_form").empty();
        $("#padecimiento_form").empty();
        $("#enfermedad_form").empty();
        
        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo base_url('Polizas/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                if(data.status != false)
                {   //inicio contratante
                    $('[name="id"]').val(data.contratante.id);
                    $('[name="nombre_razonsocial_form"]').val(data.contratante.nombre_razonsocial);
                    $('[name="tipo_documento_contratante_form"]').val(data.contratante.tipo_documento_contratante);
                    $('[name="ci_rif_form"]').val(data.contratante.ci_rif);
        			$('[name="tipo_persona_form"]').val(data.contratante.tipo_persona);
                    $('[name="nacionalidad_form"]').val(data.contratante.nacionalidad);
                    $('[name="sexo_form"]').val(data.contratante.sexo);        			
                    $('[name="estado_civil_form"]').val(data.contratante.estado_civil);
                    $('[name="fecha_nacimiento_constitucion_form"]').val(data.contratante.fecha_nacimiento_constitucion);
                    $('[name="lugar_nacimiento_constitucion_form"]').val(data.contratante.lugar_nacimiento_constitucion);
                    $('[name="nombre_registro_mercantil_form"]').val(data.contratante.nombre_registro_mercantil);
                    $('[name="numero_registro_form"]').val(data.contratante.numero_registro);
                    $('[name="numero_tomo_form"]').val(data.contratante.numero_tomo);
                    $('[name="profesion_actividad_economica_form"]').val(data.contratante.profesion_actividad_economica);
                    $('[name="ingreso_prome_anual_form"]').val(data.contratante.ingreso_prome_anual);
                    $('[name="representante_legal_form"]').val(data.contratante.representante_legal);
                    $('[name="ci_representante_legal_form"]').val(data.contratante.ci_representante_legal);
                    $('[name="pais_form"]').val(data.contratante.pais);
                    $('[name="estado_form"]').val(data.contratante.estado);
                    $('[name="ciudad_form"]').val(data.contratante.ciudad);
                    $('[name="municipio_form"]').val(data.contratante.municipio);
                    $('[name="parroquia_form"]').val(data.contratante.parroquia);
                    $('[name="urbanizacion_form"]').val(data.contratante.urbanizacion);
                    $('[name="calle_form"]').val(data.contratante.calle);
                    $('[name="centrocomercial_casa_form"]').val(data.contratante.centrocomercial_casa);
                    $('[name="piso_form"]').val(data.contratante.piso);
                    $('[name="num_apto_form"]').val(data.contratante.num_apto);
                    $('[name="telf1_form"]').val(data.contratante.telf1);
                    $('[name="telf2_form"]').val(data.contratante.telf2);
                    $('[name="telf3_form"]').val(data.contratante.telf3);
                    $('[name="telf_cel_form"]').val(data.contratante.telf_cel);
                    $('[name="fax_form"]').val(data.contratante.fax);
                    $('[name="zona_postal_form"]').val(data.contratante.zona_postal);
                    $('[name="correo_form"]').val(data.contratante.correo);
                    //inicio asegurado
                    $('[name="id_asegurado"]').val(data.asegurado.id)
                    $('[name="tipo_documento_asegurado_form"]').val(data.asegurado.tipo_documento_asegurado);
                    $('[name="ci_pasaporte_asegurado_form"]').val(data.asegurado.ci_pasaporte_asegurado);
                    $('[name="nacionalidad_asegurado_form"]').val(data.asegurado.nacionalidad_asegurado);
                    $('[name="sexo_asegurado_form"]').val(data.asegurado.sexo_asegurado);
                    $('[name="nombres_asegurado_form"]').val(data.asegurado.nombres_asegurado);
                    $('[name="apellidos_asegurado_form"]').val(data.asegurado.apellidos_asegurado);
                    $('[name="fecha_nacimiento_asegurado_form"]').val(data.asegurado.fecha_nacimiento_asegurado);
                    $('[name="lugar_nacimiento_asegurado_form"]').val(data.asegurado.lugar_nacimiento_asegurado);
                    $('[name="edad_asegurado_form"]').val(data.asegurado.edad_asegurado);
                    $('[name="estado_civil_asegurado_form"]').val(data.asegurado.estado_civil_asegurado);
                    $('[name="profesion_asegurado_form"]').val(data.asegurado.profesion_asegurado);
                    $('[name="ocupacion_asegurado_form"]').val(data.asegurado.ocupacion_asegurado);
                    $('[name="ingreso_anual_asegurado_form"]').val(data.asegurado.ingreso_anual_asegurado);
                    $('[name="fecha_ingreso_empresa_asegurado_form"]').val(data.asegurado.fecha_ingreso_empresa_asegurado);
                    $('[name="fecha_ingreso_poliza_asegurado_form"]').val(data.asegurado.fecha_ingreso_poliza_asegurado);
                    $('[name="estatura_asegurado_form"]').val(data.asegurado.estatura_asegurado);
                    $('[name="peso_asegurado_form"]').val(data.asegurado.peso_asegurado);
                    $('[name="zurdo_asegurado_form"]').val(data.asegurado.zurdo_asegurado);
                    $('[name="deportes_pasatiempo_asegurado_form"]').val(data.asegurado.deportes_pasatiempo);
                    $('[name="pais_asegurado_form"]').val(data.asegurado.pais_asegurado);
                    $('[name="estado_asegurado_form"]').val(data.asegurado.estado_asegurado);
                    $('[name="ciudad_asegurado_form"]').val(data.asegurado.ciudad_asegurado);
                    $('[name="municipio_asegurado_form"]').val(data.asegurado.municipio_asegurado);
                    $('[name="parroquia_asegurado_form"]').val(data.asegurado.parroquia_asegurado);
                    $('[name="urbanizacion_asegurado_form"]').val(data.asegurado.urbanizacion_asegurado);
                    $('[name="calle_asegurado_form"]').val(data.asegurado.calle_asegurado);
                    $('[name="centrocomercial_casa_asegurado_form"]').val(data.asegurado.centrocomercial_casa_asegurado);
                    $('[name="piso_asegurado_form"]').val(data.asegurado.piso_asegurado);
                    $('[name="num_apto_asegurado_form"]').val(data.asegurado.num_apto_asegurado);
                    $('[name="telf1_asegurado_form"]').val(data.asegurado.telf1_asegurado);
                    $('[name="telf2_asegurado_form"]').val(data.asegurado.telf2_asegurado);
                    $('[name="telf3_asegurado_form"]').val(data.asegurado.telf3_asegurado);
                    $('[name="telf_cel_asegurado_form"]').val(data.asegurado.telf_cel_asegurado);
                    $('[name="fax_asegurado_form"]').val(data.asegurado.fax_asegurado);
                    $('[name="zona_postal_asegurado_form"]').val(data.asegurado.zona_postal_asegurado);
                    $('[name="correo_asegurado_form"]').val(data.asegurado.correo_asegurado);
                    // inicio cobertura
                    $('[name="id_cobertura"]').val(data.cobertura.id);
                    $('[name="suma_asegurada_basico_form"]').val(data.cobertura.suma_asegurada_basico);
                    $('[name="deducible_basico_form"]').val(data.cobertura.deducible_basico);
                    $('[name="suma_asegurada_maternidad_form"]').val(data.cobertura.suma_asegurada_maternidad);
                    $('[name="deducible_maternidad_form"]').val(data.cobertura.deducible_maternidad);
                    $('[name="suma_asegurada_exceso_form"]').val(data.cobertura.suma_asegurada_exceso);
                    $('[name="deducible_exceso_form"]').val(data.cobertura.deducible_exceso);
                    $('[name="fecha_corte_form"]').val(data.cobertura.fecha_corte);
                    $('[name="tipo_fecha_corte_form"]').val(data.cobertura.tipo_fecha_corte);
                    $('[name="gastos_funerarios_form"]').val(data.cobertura.gastos_funerarios);
                    $('[name="muerte_accidental_form"]').val(data.cobertura.muerte_accidental);
                    $('[name="invalides_permanente_form"]').val(data.cobertura.invalides_permanente);
                    $('[name="incapacidad_temporal_form"]').val(data.cobertura.incapacidad_temporal);
                    $('[name="gastos_medicos_form"]').val(data.cobertura.gastos_medicos);
                    $('[name="muerte_form"]').val(data.cobertura.muerte);
                    $('[name="ma_it_permanente_form"]').val(data.cobertura.ma_it_permanente);
                    $('[name="pc_it_permanente_form"]').val(data.cobertura.pc_it_permanente);
                    $('[name="pago_muerte_familiar_form"]').val(data.cobertura.pago_muerte_familiar);
                   
                    if (data.tipo_poliza != null)
                    {
                    	data.tipo_poliza.forEach(function(val)
                        {
                        	if (val == "sa")
                        	{
                        		$('[name="salud_form"]').prop("checked", true);
                        	}
                        	if (val == "ap")
                        	{
                        		$('[name="ap_form"]').prop("checked", true);
                        	}
                        	if (val == "vi")
                        	{
                        		$('[name="vida_form"]').prop("checked", true);
                                mostrar_vida();
                            }
                            if (val == "gf")
                            {
                                $('[name="gf_form"]').prop("checked", true);
                                mostrar_gf();
                            }
                            if (val == "Basica")
                            {
                                $('[name="Basica_form"]').prop("checked", true);
                                mostrar_Basica();
                            }
                            if (val == "Exceso")
                            {
                                $('[name="Exceso_form"]').prop("checked", true);
                                mostrar_Exceso();
                            }
                        });
                    }
                                
                    if (data.grupo != null)
                    {
                        data.grupo.forEach(function(elementgru)
                        {
                            modificar_grupo(elementgru.id,elementgru.nombres_apellidos_grupo,elementgru.parentesco_grupo,elementgru.ci_pasaporte_grupo,elementgru.fecha_nacimiento_grupo,elementgru.edad_grupo,elementgru.sexo_grupo,elementgru.estado_civil_grupo,elementgru.peso_grupo,elementgru.estatura_grupo,elementgru.zurdo_grupo,elementgru.profesion_grupo,elementgru.ocupacion_grupo,elementgru.pasatiempos_grupo,elementgru.deportes_grupo);
                        });    
                    }
                    
                    if (data.beneficiarios != null)
                    {
                        data.beneficiarios.forEach(function(elementben)
                        {
                            modificar_beneficiario(elementben.id,elementben.tipo_doc_beneficiario,elementben.ci_pasaporte_beneficiarios,elementben.nombres_apellidos_beneficiarios,elementben.parentesco_beneficiarios,elementben.distribucion_beneficiarios);
                        });
                    }

                    if (data.otros_seguros_asegurado != null)
                    {
                        data.otros_seguros_asegurado.forEach(function(elementseg)
                        {
                            modificar_otros_seguros(elementseg.id,elementseg.nombre_empresa,elementseg.numero_poliza,elementseg.monto,elementseg.estado_poliza);
                        });
                    }

                    if (data.preguntas_asegurado != null)
                    {
                        data.preguntas_asegurado.forEach(function(elementpre)
                        {
                            if (elementpre.respuesta_pregunta == 1)
                            {
                                $('[name="pregunta_form['+elementpre.id_pregunta+']"]').prop("checked", true);
                            }
                        });
                    }

                    if (data.enfermedad != null)
                    {
                        data.enfermedad.forEach(function(elementenf)
                        {
                            modificar_enfermedad(elementenf.id,elementenf.id_pregunta,elementenf.id_grupo,elementenf.diagnostico_intervencion_enfermedad,elementenf.fecha_enfermedad,elementenf.nombres_apellidos_medico_enfermedad,elementenf.ubicacion_medico_enfermedad,elementenf.condicion_actual_enfermedad);
                        }
                        );
                    }

                    if (data.documentos_enfermedad != null)
                    {
                        data.documentos_enfermedad.forEach(function(elementdoc)
                        {
                            elementdoc.forEach(function(doc)
                            {
                                modificar_documento(doc.id,doc.id_enfermedad,doc.descripcion_archivo);
                            });
                        });
                    }

                    if (data.padecimiento != null)
                    {
                        data.padecimiento.forEach(function(elementpad)
                        {
                            modificar_padecimiento(elementpad.id,elementpad.nombres_apellidos_padecimiento,elementpad.parentesco_padecimiento,elementpad.edad_padecimiento,elementpad.fallecido_padecimiento,elementpad.causa_padecimiento);
                        });
                    }
  
                    $("#form :input").prop("disabled", true);
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
        $("#btnprint").show();
        $("#grupo_form").empty();
        $("#beneficiados_form").empty();
        $("#otros_seguros_form").empty();
        $("#enfermedad_form").empty();
        $("#padecimiento_form").empty();
        
        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo base_url('Polizas/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                if (data.status != false)
                {   //inicio contratante
                    $('[name="id"]').val(data.contratante.id);
                    $('[name="nombre_razonsocial_form"]').val(data.contratante.nombre_razonsocial);
                    $('[name="tipo_documento_contratante_form"]').val(data.contratante.tipo_documento_contratante);
                    $('[name="ci_rif_form"]').val(data.contratante.ci_rif);
                    $('[name="tipo_persona_form"]').val(data.contratante.tipo_persona);
                    $('[name="nacionalidad_form"]').val(data.contratante.nacionalidad);
                    $('[name="sexo_form"]').val(data.contratante.sexo);                 
                    $('[name="estado_civil_form"]').val(data.contratante.estado_civil);
                    $('[name="fecha_nacimiento_constitucion_form"]').val(data.contratante.fecha_nacimiento_constitucion);
                    $('[name="lugar_nacimiento_constitucion_form"]').val(data.contratante.lugar_nacimiento_constitucion);
                    $('[name="nombre_registro_mercantil_form"]').val(data.contratante.nombre_registro_mercantil);
                    $('[name="numero_registro_form"]').val(data.contratante.numero_registro);
                    $('[name="numero_tomo_form"]').val(data.contratante.numero_tomo);
                    $('[name="profesion_actividad_economica_form"]').val(data.contratante.profesion_actividad_economica);
                    $('[name="ingreso_prome_anual_form"]').val(data.contratante.ingreso_prome_anual);
                    $('[name="representante_legal_form"]').val(data.contratante.representante_legal);
                    $('[name="ci_representante_legal_form"]').val(data.contratante.ci_representante_legal);
                    $('[name="pais_form"]').val(data.contratante.pais);
                    $('[name="estado_form"]').val(data.contratante.estado);
                    $('[name="ciudad_form"]').val(data.contratante.ciudad);
                    $('[name="municipio_form"]').val(data.contratante.municipio);
                    $('[name="parroquia_form"]').val(data.contratante.parroquia);
                    $('[name="urbanizacion_form"]').val(data.contratante.urbanizacion);
                    $('[name="calle_form"]').val(data.contratante.calle);
                    $('[name="centrocomercial_casa_form"]').val(data.contratante.centrocomercial_casa);
                    $('[name="piso_form"]').val(data.contratante.piso);
                    $('[name="num_apto_form"]').val(data.contratante.num_apto);
                    $('[name="telf1_form"]').val(data.contratante.telf1);
                    $('[name="telf2_form"]').val(data.contratante.telf2);
                    $('[name="telf3_form"]').val(data.contratante.telf3);
                    $('[name="telf_cel_form"]').val(data.contratante.telf_cel);
                    $('[name="fax_form"]').val(data.contratante.fax);
                    $('[name="zona_postal_form"]').val(data.contratante.zona_postal);
                    $('[name="correo_form"]').val(data.contratante.correo);
                    //inicio asegurado
                    $('[name="id_asegurado"]').val(data.asegurado.id)
                    $('[name="tipo_documento_asegurado_form"]').val(data.asegurado.tipo_documento_asegurado);
                    $('[name="ci_pasaporte_asegurado_form"]').val(data.asegurado.ci_pasaporte_asegurado);
                    $('[name="nacionalidad_asegurado_form"]').val(data.asegurado.nacionalidad_asegurado);
                    $('[name="sexo_asegurado_form"]').val(data.asegurado.sexo_asegurado);
                    $('[name="nombres_asegurado_form"]').val(data.asegurado.nombres_asegurado);
                    $('[name="apellidos_asegurado_form"]').val(data.asegurado.apellidos_asegurado);
                    $('[name="fecha_nacimiento_asegurado_form"]').val(data.asegurado.fecha_nacimiento_asegurado);
                    $('[name="lugar_nacimiento_asegurado_form"]').val(data.asegurado.lugar_nacimiento_asegurado);
                    $('[name="edad_asegurado_form"]').val(data.asegurado.edad_asegurado);
                    $('[name="estado_civil_asegurado_form"]').val(data.asegurado.estado_civil_asegurado);
                    $('[name="profesion_asegurado_form"]').val(data.asegurado.profesion_asegurado);
                    $('[name="ocupacion_asegurado_form"]').val(data.asegurado.ocupacion_asegurado);
                    $('[name="ingreso_anual_asegurado_form"]').val(data.asegurado.ingreso_anual_asegurado);
                    $('[name="fecha_ingreso_empresa_asegurado_form"]').val(data.asegurado.fecha_ingreso_empresa_asegurado);
                    $('[name="fecha_ingreso_poliza_asegurado_form"]').val(data.asegurado.fecha_ingreso_poliza_asegurado);
                    $('[name="estatura_asegurado_form"]').val(data.asegurado.estatura_asegurado);
                    $('[name="peso_asegurado_form"]').val(data.asegurado.peso_asegurado);
                    $('[name="zurdo_asegurado_form"]').val(data.asegurado.zurdo_asegurado);
                    $('[name="deportes_pasatiempo_asegurado_form"]').val(data.asegurado.deportes_pasatiempo);
                    $('[name="pais_asegurado_form"]').val(data.asegurado.pais_asegurado);
                    $('[name="estado_asegurado_form"]').val(data.asegurado.estado_asegurado);
                    $('[name="ciudad_asegurado_form"]').val(data.asegurado.ciudad_asegurado);
                    $('[name="municipio_asegurado_form"]').val(data.asegurado.municipio_asegurado);
                    $('[name="parroquia_asegurado_form"]').val(data.asegurado.parroquia_asegurado);
                    $('[name="urbanizacion_asegurado_form"]').val(data.asegurado.urbanizacion_asegurado);
                    $('[name="calle_asegurado_form"]').val(data.asegurado.calle_asegurado);
                    $('[name="centrocomercial_casa_asegurado_form"]').val(data.asegurado.centrocomercial_casa_asegurado);
                    $('[name="piso_asegurado_form"]').val(data.asegurado.piso_asegurado);
                    $('[name="num_apto_asegurado_form"]').val(data.asegurado.num_apto_asegurado);
                    $('[name="telf1_asegurado_form"]').val(data.asegurado.telf1_asegurado);
                    $('[name="telf2_asegurado_form"]').val(data.asegurado.telf2_asegurado);
                    $('[name="telf3_asegurado_form"]').val(data.asegurado.telf3_asegurado);
                    $('[name="telf_cel_asegurado_form"]').val(data.asegurado.telf_cel_asegurado);
                    $('[name="fax_asegurado_form"]').val(data.asegurado.fax_asegurado);
                    $('[name="zona_postal_asegurado_form"]').val(data.asegurado.zona_postal_asegurado);
                    $('[name="correo_asegurado_form"]').val(data.asegurado.correo_asegurado);
                    // inicio cobertura
                    $('[name="id_cobertura"]').val(data.cobertura.id);
                    $('[name="suma_asegurada_basico_form"]').val(data.cobertura.suma_asegurada_basico);
                    $('[name="deducible_basico_form"]').val(data.cobertura.deducible_basico);
                    $('[name="suma_asegurada_maternidad_form"]').val(data.cobertura.suma_asegurada_maternidad);
                    $('[name="deducible_maternidad_form"]').val(data.cobertura.deducible_maternidad);
                    $('[name="suma_asegurada_exceso_form"]').val(data.cobertura.suma_asegurada_exceso);
                    $('[name="deducible_exceso_form"]').val(data.cobertura.deducible_exceso);
                    $('[name="fecha_corte_form"]').val(data.cobertura.fecha_corte);
                    $('[name="tipo_fecha_corte_form"]').val(data.cobertura.tipo_fecha_corte);
                    $('[name="gastos_funerarios_form"]').val(data.cobertura.gastos_funerarios);
                    $('[name="muerte_accidental_form"]').val(data.cobertura.muerte_accidental);
                    $('[name="invalides_permanente_form"]').val(data.cobertura.invalides_permanente);
                    $('[name="incapacidad_temporal_form"]').val(data.cobertura.incapacidad_temporal);
                    $('[name="gastos_medicos_form"]').val(data.cobertura.gastos_medicos);
                    $('[name="muerte_form"]').val(data.cobertura.muerte);
                    $('[name="ma_it_permanente_form"]').val(data.cobertura.ma_it_permanente);
                    $('[name="pc_it_permanente_form"]').val(data.cobertura.pc_it_permanente);
                    $('[name="pago_muerte_familiar_form"]').val(data.cobertura.pago_muerte_familiar);

                    if (data.tipo_poliza != null)
                    {
                        data.tipo_poliza.forEach(function(val)
                        {
                            if (val == "sa")
                            {
                                $('[name="salud_form"]').prop("checked", true);
                            }
                            if (val == "ap")
                            {
                                $('[name="ap_form"]').prop("checked", true);
                            }
                            if (val == "vi")
                            {
                                $('[name="vida_form"]').prop("checked", true);
                                mostrar_vida();
                            }
                            if (val == "gf")
                            {
                                $('[name="gf_form"]').prop("checked", true);
                                mostrar_gf();
                            }
                            if (val == "Basica")
                            {
                                $('[name="Basica_form"]').prop("checked", true);
                                mostrar_Basica();
                            }
                            if (val == "Exceso")
                            {
                                $('[name="Exceso_form"]').prop("checked", true);
                                mostrar_Exceso();
                            }

                        });
                    }

                   	if (data.grupo != null)
                    {
                        data.grupo.forEach(function(elementgru)
                        {
                            modificar_grupo(elementgru.id,elementgru.nombres_apellidos_grupo,elementgru.parentesco_grupo,elementgru.ci_pasaporte_grupo,elementgru.fecha_nacimiento_grupo,elementgru.edad_grupo,elementgru.sexo_grupo,elementgru.estado_civil_grupo,elementgru.peso_grupo,elementgru.estatura_grupo,elementgru.zurdo_grupo,elementgru.profesion_grupo,elementgru.ocupacion_grupo,elementgru.pasatiempos_grupo,elementgru.deportes_grupo);
                        });    
                    }
                    
                    if (data.beneficiarios != null)
                    {
                        data.beneficiarios.forEach(function(elementben)
                        {
                            modificar_beneficiario(elementben.id,elementben.tipo_doc_beneficiario,elementben.ci_pasaporte_beneficiarios,elementben.nombres_apellidos_beneficiarios,elementben.parentesco_beneficiarios,elementben.distribucion_beneficiarios);
                        });
                    }

                    if (data.otros_seguros_asegurado != null)
                    {
                        data.otros_seguros_asegurado.forEach(function(elementseg)
                        {
                            modificar_otros_seguros(elementseg.id,elementseg.nombre_empresa,elementseg.numero_poliza,elementseg.monto,elementseg.estado_poliza);
                        });
                    }

                    if (data.preguntas_asegurado != null)
                    {
                        data.preguntas_asegurado.forEach(function(elementpre)
                        {
                            if (elementpre.respuesta_pregunta == 1)
                            {
                                $('[name="pregunta_form['+elementpre.id_pregunta+']"]').prop("checked", true);
                            }
                        });
                    }

                    if (data.enfermedad != null)
                    {
                        data.enfermedad.forEach(function(elementenf)
                        {
                            modificar_enfermedad(elementenf.id,elementenf.id_pregunta,elementenf.id_grupo,elementenf.diagnostico_intervencion_enfermedad,elementenf.fecha_enfermedad,elementenf.nombres_apellidos_medico_enfermedad,elementenf.ubicacion_medico_enfermedad,elementenf.condicion_actual_enfermedad);
                        }
                        );
                    }

                    if (data.documentos_enfermedad != null)
                    {
                        data.documentos_enfermedad.forEach(function(elementdoc)
                        {
                            elementdoc.forEach(function(doc)
                            {
                                modificar_documento(doc.id,doc.id_enfermedad,doc.descripcion_archivo);
                            });
                        });
                    }

                    if (data.padecimiento != null)
                    {
                        data.padecimiento.forEach(function(elementpad)
                        {
                            modificar_padecimiento(elementpad.id,elementpad.nombres_apellidos_padecimiento,elementpad.parentesco_padecimiento,elementpad.edad_padecimiento,elementpad.fallecido_padecimiento,elementpad.causa_padecimiento);
                        });
                    }

                    $("#form :input").prop("disabled", false);
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

    function agregar_ajax()
    {
        cargar_list_contratantes_registrados();
        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $("#form :input").prop("disabled", false);
        $('.help-block').empty(); // clear error string
        $("#grupo_form").empty();
        $("#beneficiados_form").empty();
        $("#otros_seguros_form").empty();
        $("#enfermedad_form").empty();
        $("#padecimiento_form").empty();
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Agregar'); // Set Title to Bootstrap modal title
        $('#btnSave').show();
        $("#btnprint").hide();
    }

    function borrar_ajax(id)
    {
        if(confirm('Esta seguro de querer borrar esta información?'))
        {
            // ajax delete data to database
            $.ajax({
                url : "<?php echo base_url('Polizas/ajax_delete')?>/"+id,
                type: "POST",
                dataType: "JSON",
                success: function(data)
                {
                    if(data.status == true) //if success close modal and reload ajax table
                    {
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

    function recargar_tabla_siniestros()
    {
        //button reset event click
        $('#form-filter_siniestros')[0].reset();
        table_siniestros.ajax.reload(null,false);  //just reload table   
    }
    
    function reporte_pdf_siniestros()
    {
        var parametros = {// are send to the server side
            tipo_solicitud_ajaxreporte : $('#tipo_solicitud_filtro').val(),
            fecha_recepcion_ajaxreporte : $('#fecha_recepcion_filtro').val(),
            fecha_entrega_ajaxreporte : $('#fecha_entrega_filtro').val(),
            observacion_ajaxreporte : $("#observacion_filtro").val(),
            monto_solicitado_ajaxreporte : $("#monto_solicitado_filtro").val(),
            monto_aprobado_ajaxreporte : $("#monto_aprobado_filtro").val(),
            id_poliza : $('[name="id_poliza"]').val(),
            id_empresa : $('[name="id_empresa_form"]').val(),
        };
        
        $.ajax({
            url: '<?php echo base_url("Siniestros/reporte");?>',
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

    function reporte_individual_siniestros()
    {
        parametros = {
            id : $('[name="id_siniestro"]').val(),
            id_empresa : $('[name="id_empresa_form"]').val(),
        };
        
        $.ajax({
            url: '<?php echo base_url("Siniestros/reporte_individual");?>',
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

    function mostrar_siniestros_ajax(id)
    {
        listar_siniestros(id);
        listar_beneficiarios(id);
        $('[name="id_poliza"]').val(id);
        $('#form-filter_siniestros')[0].reset(); // reset form on modals
        $('#table_siniestros_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Listado de siniestros'); // Set Title to Bootstrap modal title
    }

    function agregar_siniestros_ajax()
    {
        save_method = 'add_siniestros';
        $('#form_siniestros')[0].reset(); // reset form on modals
        $("#form_siniestros :input").prop("disabled", false);
        $('#title_siniestros').text('Agregar siniestros'); // Set Title to Bootstrap modal title
        $('#btnSave').show();
        $("#btnprint").hide();
    }

    function detalles_siniestros_ajax(id)
    {
        $('#form_siniestros')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#btnSave').hide();
        $("#btnprint").show();
        
        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo base_url('Siniestros/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                if(data.status != false)
                {   //inicio siniestros
                    $('[name="id_poliza"]').val(data.id_poliza);
                    $('[name="id_siniestro"]').val(data.id);
                    $('[name="tipo_solicitud_form"]').val(data.tipo_solicitud);
                    $('[name="id_beneficiario_siniestros"]').val(data.id_beneficiario_siniestros);
                    cargar_beneficiarios();
                    $('[name="fecha_recepcion_form"]').val(data.fecha_recepcion);
                    $('[name="fecha_entrega_form"]').val(data.fecha_entrega);
                    $('[name="observacion_form"]').val(data.observacion);
                    $('[name="monto_solicitado_form"]').val(data.monto_solicitado);
                    $('[name="monto_aprobado_form"]').val(data.monto_aprobado);
                    
                    $("#form_siniestros :input").prop("disabled", true);
                    $('#modal_siniestros_form').modal('show'); // show bootstrap modal when complete loaded
                    $('#title_siniestros').text('Detalles'); // Set title to Bootstrap modal title
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

    function editar_siniestros_ajax(id)
    {
        save_method = 'update_siniestros';
        $('#form_siniestros')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#btnSave').show();
        $("#btnprint").show();
        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo base_url('Siniestros/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                if (data.status != false)
                {   //inicio siniestros
                    $('[name="id_poliza"]').val(data.id_poliza);
                    $('[name="id_siniestro"]').val(data.id);
                    $('[name="tipo_solicitud_form"]').val(data.tipo_solicitud);
                    $('[name="id_beneficiario_siniestros"]').val(data.id_beneficiario_siniestros);
                    cargar_beneficiarios();
                    $('[name="fecha_recepcion_form"]').val(data.fecha_recepcion);
                    $('[name="fecha_entrega_form"]').val(data.fecha_entrega);
                    $('[name="observacion_form"]').val(data.observacion);
                    $('[name="monto_solicitado_form"]').val(data.monto_solicitado);
                    $('[name="monto_aprobado_form"]').val(data.monto_aprobado);
                    
                    $('#modal_siniestros_form').modal('show'); // show bootstrap modal when complete loaded
                    $("#form_siniestros :input").prop("disabled", false);
                    $('#title_siniestros').text('Modificar'); // Set title to Bootstrap modal title
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

    function borrar_siniestros_ajax(id)
    {
        if(confirm('Esta seguro de querer borrar esta información?'))
        {
            // ajax delete data to database
            $.ajax({
                url : "<?php echo base_url('Siniestros/ajax_delete')?>/"+id,
                type: "POST",
                dataType: "JSON",
                success: function(data)
                {
                    if(data.status == true) //if success close modal and reload ajax table
                    {
                        recargar_tabla_siniestros();
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

    function pago_corte_poliza_ajax(id)
    {
        save_method = 'pago_poliza';
        $('#form_pago')[0].reset(); // reset form on modals
        $('#title_pago').text('Pago de poliza'); // Set Title to Bootstrap modal title
        $('#btnSave').show();
        $('.form-group').removeClass('has-error'); // clear error class

        $.ajax({
            url : "<?php echo base_url('Polizas/ajax_fecha_corte')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                if (data.status != false)
                {   //inicio siniestros
                    $('[name="id_pago"]').val(data.id);
                    $('#title_tipo_fecha_corte').text("Tipo de fecha de corte: "+data.tipo_fecha_corte);
                    
                    $('#modal_pago_form').modal('show'); // show bootstrap modal when complete loaded
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

        if(save_method == 'add')
        {
            url = "<?php echo base_url('Polizas/ajax_add')?>";
            var formData = new FormData($('#form')[0]);
            var reload_table = 1;
        }
        else if(save_method == "update")
        {
            url = "<?php echo base_url('Polizas/ajax_update')?>";
            var formData = new FormData($('#form')[0]);
            var reload_table = 1;
        }
        else if (save_method == "add_siniestros")
        {
            url = "<?php echo base_url('Siniestros/ajax_add')?>";
            var formData = new FormData($('#form_siniestros')[0]);
            var reload_table = 2;
        }
        else if (save_method == "update_siniestros")
        {
            url = "<?php echo base_url('Siniestros/ajax_update')?>";
            var formData = new FormData($('#form_siniestros')[0]);
            var reload_table = 2;
        }
        else if (save_method == "pago_poliza")
        {
            url = "<?php echo base_url('Polizas/ajax_pago_corte')?>";
            var formData = new FormData($('#form_pago')[0]);
            var reload_table = 3;
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

                    if (reload_table == 1)
                    {
                        $('#modal_form').modal('hide');
                        recargar_tabla();
                    }
                    if (reload_table == 2)
                    {
                        $('#modal_siniestros_form').modal('hide');
                        recargar_tabla_siniestros();
                    }
                    if (reload_table == 3)
                    {
                        $('#modal_pago_form').modal('hide');
                        recargar_tabla();
                    }
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
                    alert("No se estan guardando documentos de las enfermedades. Seleccione los archivos que desea subir");
                    $('#btnSave').text('Guardar'); //change button text
                    $('#btnSave').attr('disabled',false); //set button enable 
                }
                else if (data.status == "file")
                {
                    alert("Error al guardar los documentos de las enfermedades. no es el tipo de archivo o pesa mas de 50 MB");
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
</script>
<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
                <button type="button" id="btnprint" onclick="reporte_individual()" class="btn btn-info"><i class="glyphicon glyphicon-print"></i> Imprimir</button>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal" enctype="multipart/form-data">
                    <input type="hidden" value="" name="id" readonly/> 
                    <div class="form-body">
                        <div class="form-group">
                            <h4>Datos de indentificacion del contratante/tomador</h4>
                            <h4>Persona natural/Juridica</h4>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">Tipo de poliza</label>
                            <div class="col-md-10">
                                <input type="checkbox" name="salud_form" value="salud">
                                <label class="control-label">Salud</label>
                                <input type="checkbox" name="ap_form" value="ap">
                                <label class="control-label">AP</label>
                                <input type="checkbox" name="vida_form" id="vida_form" value="vida" onclick="mostrar_vida()">
                                <label class="control-label">Vida</label>
                                <input type="checkbox" name="gf_form" id="gf_form" value="funerarios" onclick="mostrar_gf()">
                                <label class="control-label">Gastos funerarios</label>
                                <input type="checkbox" name="Basica_form" id="Basica_form" value="Basica" onclick="mostrar_Basica()">
                                <label class="control-label">Basica</label>
                                <input type="checkbox" name="Exceso_form" id="Exceso_form" value="Exceso" onclick="mostrar_Exceso()">
                                <label class="control-label">Exceso</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">Nº de poliza</label>
                            <div class="col-md-4">
                                <input type="text" name="num_poliza_form" placeholder="Nº de poliza" class="form-control" maxlength="20">
                            </div>
                            <label class="control-label col-md-2">Contratande registrado</label>
                            <div class="col-md-4">
                                <select name="contratante_registrado_form" class="form-control" onblur="cargar_contratantes_registrados()"></select>
                            </div>
                        </div>
                        <hr>
                        <!-- inicio del registro de datos del contratante -->
                        <div class="form-group">
                            <label class="control-label col-md-2">Nombres y apellidos/Razon social</label>
                            <div class="col-md-6">
                                <textarea name="nombre_razonsocial_form" placeholder="Nombres y apellidos/Razon social" class="form-control" maxlength="250" required></textarea>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-2">Tipo de documento</label>
                            <div class="col-md-2">
                                <select name="tipo_documento_contratante_form" class="form-control" required>
                                    <option value="">Seleccione</option>
                                    <option value="v">Cedula</option>
                                    <option value="j">Rif</option>
                                    <option value="e">Extranjero</option>
                                    <option value="p">Pasaporte</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">Documento de identidad</label>
                            <div class="col-md-2">
                                <input name="ci_rif_form" placeholder="Documento de identidad" class="form-control" type="text" maxlength="50" required>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-2">Tipo de persona</label>
                            <div class="col-md-2">
                                <select name="tipo_persona_form" class="form-control" required>
                                    <option value="">Seleccione</option>
                                    <option value="natural">Natural</option>
                                    <option value="juridica">Juridica</option>
                                    <option value="gubernamental">Gubernamental</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-2">Nacionalidad</label>
                            <div class="col-md-2">
                                <input name="nacionalidad_form" placeholder="Nacionalidad" class="form-control" type="text" maxlength="20">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-1">Sexo</label>
                            <div class="col-md-2">
                                <select name="sexo_form" class="form-control">
                                    <option value="">Seleccione</option>
                                    <option value="Hombre">Hombre</option>
                                    <option value="Mujer">Mujer</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Estado civil</label>
                            <div class="col-md-2">
                                <select name="estado_civil_form" class="form-control">
                                    <option value="">Seleccione</option>
                                    <option value="Soltero">Soltero</option>
                                    <option value="Casado">Casado</option>
                                    <option value="Divorciado">Divorciado</option>
                                    <option value="Viudo">Viudo</option>
                                    <option value="Otro">Otro</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-3">Fecha de nacimiento/constitucion</label>
                            <div class="col-md-3">
                                <input name="fecha_nacimiento_constitucion_form" placeholder="Fecha de nacimiento/constitucion" class="form-control" type="date" maxlength="15" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Lugar de nacimiento/constitucion</label>
                            <div class="col-md-3">
                                <textarea name="lugar_nacimiento_constitucion_form" placeholder="Lugar de nacimiento/constitucion" class="form-control" maxlength="100" required></textarea>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-3">Nombre de registro mercantil</label>
                            <div class="col-md-3">
                                <textarea name="nombre_registro_mercantil_form" placeholder="Nombre de registro mercantil" class="form-control" maxlength="200"></textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Nº Registro</label>
                            <div class="col-md-3">
                                <input name="numero_registro_form" placeholder="Nº Registro" class="form-control" type="text" maxlength="10" onkeypress="return isNumberKey(event)">
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-3">Nº Tomo</label>
                            <div class="col-md-3">
                                <input name="numero_tomo_form" placeholder="Nº Tomo" class="form-control" type="text" maxlength="10" onkeypress="return isNumberKey(event)">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">Profesion o actividad economica</label>
                            <div class="col-md-6">
                                <textarea name="profesion_actividad_economica_form" placeholder="Profesion o actividad economica" class="form-control" maxlength="200" required></textarea>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-2">Ingreso promedio anual</label>
                            <div class="col-md-2">
                                <input name="ingreso_prome_anual_form" placeholder="bs." class="form-control" type="text" maxlength="100" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Representante legal: Nombre y apellidos</label>
                            <div class="col-md-6">
                                <input name="representante_legal_form" placeholder="Representante legal: Nombre y apellidos" class="form-control" type="text" maxlength="200">
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">C.I.</label>
                            <div class="col-md-2">
                                <input name="ci_representante_legal_form" placeholder="C.I." class="form-control" type="text" maxlength="15">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <!-- fin del registro de datos del contratante -->
                        <!-- inicio del registro de direccion del contratante -->
                        <div class="form-group">
                            <label class="control-label col-md-1">Pais</label>
                            <div class="col-md-5">
                                <input name="pais_form" placeholder="Pais" class="form-control" type="text" maxlength="50" required>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Estado</label>
                            <div class="col-md-5">
                                <input name="estado_form" placeholder="Estado" class="form-control" type="text" maxlength="50" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-1">Ciudad</label>
                            <div class="col-md-5">
                                <input name="ciudad_form" placeholder="Ciudad" class="form-control" type="text" maxlength="50" required>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Municipio</label>
                            <div class="col-md-5">
                                <input name="municipio_form" placeholder="Municipio" class="form-control" type="text" maxlength="50" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-1">Parroquia</label>
                            <div class="col-md-5">
                                <input name="parroquia_form" placeholder="Parroquia" class="form-control" type="text" maxlength="50" required>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Urb.</label>
                            <div class="col-md-5">
                                <input name="urbanizacion_form" placeholder="Urbanizacion" class="form-control" type="text" maxlength="50" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-1">Calle</label>
                            <div class="col-md-5">
                                <input name="calle_form" placeholder="Calle" class="form-control" type="text" maxlength="50" required>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Local/ Casa</label>
                            <div class="col-md-5">
                                <input name="centrocomercial_casa_form" placeholder="Local/Casa" class="form-control" type="text" maxlength="50" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-1">Piso</label>
                            <div class="col-md-2">
                                <input name="piso_form" placeholder="Piso" class="form-control" type="text" maxlength="10">
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Nº apto</label>
                            <div class="col-md-2">
                                <input name="num_apto_form" placeholder="Nº apto" class="form-control" type="text" maxlength="10">
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Telefono 1</label>
                            <div class="col-md-2">
                                <input name="telf1_form" placeholder="Telefono 1" class="form-control" type="text" maxlength="15">
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Telefono 2</label>
                            <div class="col-md-2">
                                <input name="telf2_form" placeholder="Telefono 2" class="form-control" type="text" maxlength="15">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-1">Telefono 3</label>
                            <div class="col-md-2">
                                <input name="telf3_form" placeholder="Telefono 3" class="form-control" type="text" maxlength="15">
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Telefono celular</label>
                            <div class="col-md-2">
                                <input name="telf_cel_form" placeholder="Telefono celular" class="form-control" type="text" maxlength="15">
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Fax</label>
                            <div class="col-md-2">
                                <input name="fax_form" placeholder="Fax" class="form-control" type="text" maxlength="15">
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Zona postal</label>
                            <div class="col-md-2">
                                <input name="zona_postal_form" placeholder="Zona postal" class="form-control" type="text" maxlength="5">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Correo electronico</label>
                            <div class="col-md-9">
                                <input name="correo_form" placeholder="Correo electronico" class="form-control" type="email" maxlength="100">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <!-- fin del registro de direccion del contratante -->
                        <!-- inicio del registro de datos del asegurado -->
                        <hr>
                        <div class="form-group">
                            <h4>Datos del asegurado titular</h4>
                        </div>
                        <hr>
                        <div class="form-group">
                            <input type="hidden" value="" name="id_asegurado" readonly/>
                            <label class="control-label col-md-2">Tipo de documento</label>
                            <div class="col-md-2">
                                <select name="tipo_documento_asegurado_form" class="form-control" required>
                                    <option value="">Seleccione</option>
                                    <option value="v">Cedula</option>
                                    <option value="j">Rif</option>
                                    <option value="e">Extranjero</option>
                                    <option value="p">Pasaporte</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-2">Documento de identidad</label>
                            <div class="col-md-2">
                                <input name="ci_pasaporte_asegurado_form" placeholder="Documento de identidad" class="form-control" type="text" maxlength="20" required>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-2">Nacionalidad</label>
                            <div class="col-md-2">
                                <input name="nacionalidad_asegurado_form" placeholder="Nacionalidad" class="form-control" type="text" maxlength="20" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-1">Nombres</label>
                            <div class="col-md-5">
                                <textarea name="nombres_asegurado_form" placeholder="Nombres" class="form-control" type="text" maxlength="100" required></textarea>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Apellidos</label>
                            <div class="col-md-5">
                                <textarea name="apellidos_asegurado_form" placeholder="Apellidos" class="form-control" type="text" maxlength="100" required></textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">Fecha de nacimiento</label>
                            <div class="col-md-4">
                                <input name="fecha_nacimiento_asegurado_form" class="form-control" type="date" maxlength="15" required>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-2">Lugar de nacimiento</label>
                            <div class="col-md-4">
                                <textarea name="lugar_nacimiento_asegurado_form" placeholder="Lugar de nacimiento" class="form-control" maxlength="100" required></textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">Sexo</label>
                            <div class="col-md-2">
                                <select name="sexo_asegurado_form" class="form-control" required>
                                    <option value="">Seleccione</option>
                                    <option value="Hombre">Hombre</option>
                                    <option value="Mujer">Mujer</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-2">Estado civil</label>
                            <div class="col-md-2">
                                <select name="estado_civil_asegurado_form" class="form-control" required>
                                    <option value="">Seleccione</option>
                                    <option value="1">Soltero</option>
                                    <option value="2">Casado</option>
                                    <option value="3">Divorciado</option>
                                    <option value="4">Viudo</option>
                                    <option value="5">Otro</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-2">Edad</label>
                            <div class="col-md-2">
                                <input type="number" name="edad_asegurado_form" class="form-control" onkeypress="return isNumberKey(event)" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">Profesion</label>
                            <div class="col-md-4">
                                <textarea name="profesion_asegurado_form" placeholder="Profesion" class="form-control" maxlength="100" required></textarea>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-2">Ocupacion</label>
                            <div class="col-md-4">
                                <textarea name="ocupacion_asegurado_form" placeholder="Ocupacion" class="form-control" maxlength="100" required></textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">Ingreso anual</label>
                            <div class="col-md-2">
                                <select name="ingreso_anual_asegurado_form" class="form-control" required>
                                    <option value="">Seleccione</option>
                                    <?php foreach($ingreso_anual_form as $fila => $valor){?>
                                    <option value="<?php echo $fila ?>"><?php echo $valor ?></option>
                                    <?php } ?>
                                </select>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-2">Fecha de ingreso a la empresa</label>
                            <div class="col-md-2">
                                <input name="fecha_ingreso_empresa_asegurado_form" placeholder="Fecha de ingreso a la empresa" class="form-control" type="date" maxlength="15">
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-2">Fecha de ingreso a la poliza</label>
                            <div class="col-md-2">
                                <input name="fecha_ingreso_poliza_asegurado_form" placeholder="Fecha de ingreso a la poliza" class="form-control" type="date" maxlength="15"></textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">Estatura</label>
                            <div class="col-md-2">
                                <input name="estatura_asegurado_form" placeholder="Estatura" class="form-control" type="text" maxlength="6" required>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-2">Peso</label>
                            <div class="col-md-2">
                                <input name="peso_asegurado_form" placeholder="Peso" class="form-control" type="text" maxlength="6" required>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Zurdo</label>
                            <div class="col-md-2">
                                <select name="zurdo_asegurado_form" class="form-control" required>
                                    <option value="si">si</option>
                                    <option value="no" selected>no</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">Deportes/ pasatiempos</label>
                            <div class="col-sm-10">
                                <textarea name="deportes_pasatiempo_asegurado_form" placeholder="Deportes/ pasatiempos" class="form-control" maxlength="200" required></textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <!-- fin del registro de datos del asegurado -->
                        <!-- inicio del registro de direccion del asegurado -->
                        <div class="form-group">
                            <label class="control-label col-md-1">Pais</label>
                            <div class="col-md-5">
                                <input name="pais_asegurado_form" placeholder="Pais" class="form-control" type="text" maxlength="50" required>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Estado</label>
                            <div class="col-md-5">
                                <input name="estado_asegurado_form" placeholder="Estado" class="form-control" type="text" maxlength="50" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-1">Ciudad</label>
                            <div class="col-md-5">
                                <input name="ciudad_asegurado_form" placeholder="Ciudad" class="form-control" type="text" maxlength="50" required>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Municipio</label>
                            <div class="col-md-5">
                                <input name="municipio_asegurado_form" placeholder="Municipio" class="form-control" type="text" maxlength="50" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-1">Parroquia</label>
                            <div class="col-md-5">
                                <input name="parroquia_asegurado_form" placeholder="Parroquia" class="form-control" type="text" maxlength="50" required>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Urb.</label>
                            <div class="col-md-5">
                                <input name="urbanizacion_asegurado_form" placeholder="Urbanizacion" class="form-control" type="text" maxlength="50" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-1">Calle</label>
                            <div class="col-md-5">
                                <input name="calle_asegurado_form" placeholder="Calle" class="form-control" type="text" maxlength="50" required>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Local/ Casa</label>
                            <div class="col-md-5">
                                <input name="centrocomercial_casa_asegurado_form" placeholder="Local/Casa" class="form-control" type="text" maxlength="50" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-1">Piso</label>
                            <div class="col-md-2">
                                <input name="piso_asegurado_form" placeholder="Piso" class="form-control" type="text" maxlength="10">
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Nº apto</label>
                            <div class="col-md-2">
                                <input name="num_apto_asegurado_form" placeholder="Nº apto" class="form-control" type="text" maxlength="10">
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Telefono 1</label>
                            <div class="col-md-2">
                                <input name="telf1_asegurado_form" placeholder="Telefono 1" class="form-control" type="text" maxlength="15">
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Telefono 2</label>
                            <div class="col-md-2">
                                <input name="telf2_asegurado_form" placeholder="Telefono 2" class="form-control" type="text" maxlength="15">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-1">Telefono 3</label>
                            <div class="col-md-2">
                                <input name="telf3_asegurado_form" placeholder="Telefono 3" class="form-control" type="text" maxlength="15">
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Telefono celular</label>
                            <div class="col-md-2">
                                <input name="telf_cel_asegurado_form" placeholder="Telefono celular" class="form-control" type="text" maxlength="15">
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Fax</label>
                            <div class="col-md-2">
                                <input name="fax_asegurado_form" placeholder="Fax" class="form-control" type="text" maxlength="15">
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-1">Zona postal</label>
                            <div class="col-md-2">
                                <input name="zona_postal_asegurado_form" placeholder="Zona postal" class="form-control" type="text" maxlength="5">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Correo electronico</label>
                            <div class="col-md-9">
                                <input name="correo_asegurado_form" placeholder="Correo electronico" class="form-control" type="email" maxlength="100">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <!-- fin del registro de direccion del asegurado -->
                        <hr>
                        <div class="form-group">
                            <h4>Converturas solicitadas</h4>
                        </div>
                        <hr>
                        <!-- inicio del resgistro de la cobertura de la poliza -->
                        <div class="form-group">
                            <label class="control-label col-md-12" style="text-align: left;">Salud</label>
                        </div>
                        <div class="form-group" id="div_Basica" style="display: none;">
                            <input type="hidden" name="id_cobertura" value="" readonly>
                            <label class="control-label col-md-2">Plan</label>
                            <label class="control-label col-md-2"><strong>Basico</strong></label>
                            <label class="control-label col-md-2">Suma asegurada</label>
                            <div class="col-md-2">
                                <input name="suma_asegurada_basico_form" placeholder="Bs" class="form-control" type="number" onkeypress="return isNumberKey(event)">
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-2">Deducible</label>
                            <div class="col-md-2">
                                <input name="deducible_basico_form" placeholder="Bs" class="form-control" type="number" onkeypress="return isNumberKey(event)">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">Plan</label>
                            <label class="control-label col-md-2"><strong>Maternidad</strong></label>
                            <label class="control-label col-md-2">Suma asegurada</label>
                            <div class="col-md-2">
                                <input name="suma_asegurada_maternidad_form" placeholder="Bs" class="form-control" type="number" onkeypress="return isNumberKey(event)">
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-2">Deducible</label>
                            <div class="col-md-2">
                                <input name="deducible_maternidad_form" placeholder="Bs" class="form-control" type="number" onkeypress="return isNumberKey(event)">
                                <span class="help-block"></span>
                            </div>
                        </div> 
                        <div class="form-group" id="div_Exceso" style="display: none;">
                            <label class="control-label col-md-2">Plan</label>
                            <label class="control-label col-md-2"><strong>Exceso</strong></label>
                            <label class="control-label col-md-2">Suma asegurada</label>
                            <div class="col-md-2">
                                <input name="suma_asegurada_exceso_form" placeholder="Bs" class="form-control" type="number" onkeypress="return isNumberKey(event)">
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-2">Deducible</label>
                            <div class="col-md-2">
                                <input name="deducible_exceso_form" placeholder="Bs" class="form-control" type="number" onkeypress="return isNumberKey(event)">
                                <span class="help-block"></span>
                            </div>
                        </div>  
                        <div class="form-group">
                            <label class="control-label col-md-2">Fecha de corte</label>
                            <div class="col-md-4">
                                <input type="date" name="fecha_corte_form" class="form-control" required>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-2">Tipo de fecha de corte</label>
                            <div class="col-md-4">
                                <select name="tipo_fecha_corte_form" class="form-control" required>
                                    <option value="">Seleccione</option>
                                    <option value="Mensual">Mensual</option>
                                    <option value="Trimestral">Trimestral</option>
                                    <option value="Semestral">Semestral</option>
                                    <option value="Anual">Anual</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group"  id="div_gastos_funerarios" style="display: none;">
                            <label class="control-label col-md-4">Gastos funerarios</label>
                            <div class="col-md-8">
                                <input type="number" name="gastos_funerarios_form" class="form-control" placeholder="Bs" onkeypress="return isNumberKey(event)">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div id="div_vida_form" style="display: none;">
                            <div class="form-group"">
                                <label class="control-label col-md-12" style="text-align: left;">Accidentes personales(Solo Asegurado titular)</label>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Muerte accidental</label>
                                <div class="col-md-3">
                                    <input name="muerte_accidental_form" placeholder="Bs" class="form-control" type="number" onkeypress="return isNumberKey(event)">
                                    <span class="help-block"></span>
                                </div>
                                <label class="control-label col-md-3">Invalides permanente</label>
                                <div class="col-md-3">
                                    <input name="invalides_permanente_form" placeholder="Bs" class="form-control" type="number" onkeypress="return isNumberKey(event)">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Incapacidad temporal</label>
                                <div class="col-md-3">
                                    <input name="incapacidad_temporal_form" placeholder="Bs" class="form-control" type="number" onkeypress="return isNumberKey(event)">
                                    <span class="help-block"></span>
                                </div>
                                <label class="control-label col-md-3">Gastos medicos</label>
                                <div class="col-md-3">
                                    <input name="gastos_medicos_form" placeholder="Bs" class="form-control" type="number" onkeypress="return isNumberKey(event)">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-12" style="text-align: left;">Vida(Solo Asegurado titular)</label>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Muerte</label>
                                <div class="col-md-3">
                                    <input name="muerte_form" placeholder="Bs" class="form-control" type="number" onkeypress="return isNumberKey(event)">
                                    <span class="help-block"></span>
                                </div>
                                <label class="control-label col-md-3">Muerte accidental o incapacidad total y permanente</label>
                                <div class="col-md-3">
                                    <input name="ma_it_permanente_form" placeholder="Bs" class="form-control" type="number" onkeypress="return isNumberKey(event)">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Pago del capital por incapacidad total y permanente</label>
                                <div class="col-md-3">
                                    <input name="pc_it_permanente_form" placeholder="Bs" class="form-control" type="number" onkeypress="return isNumberKey(event)">
                                    <span class="help-block"></span>
                                </div>
                                <label class="control-label col-md-3">Pago por muerte de familiares</label>
                                <div class="col-md-3">
                                    <input name="pago_muerte_familiar_form" placeholder="Bs" class="form-control" type="number" onkeypress="return isNumberKey(event)">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <!-- fin del resgistro de la cobertura de la poliza -->
                        <!-- inicio del registro del grupo a asegurar -->
                        <hr>
                        <div class="form-group">
                            <h4>Grupo de personas a asegurar</h4>
                            <button class="btn btn-success" type="button"  onclick="agregar_grupo();"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar persona</button>
                        </div>
                        <hr>
                        <div id="grupo_form"></div>
                        <!-- fin del registro del grupo a asegurar -->
                        
                        <!-- inicio de beneficiarios en caso de muerte del asegurado titular -->
                        <hr>
                        <div class="form-group">
                            <h4>Beneficiarios en caso de fallecimiento del asegurado titular.</h4>
                            <button class="btn btn-success" type="button"  onclick="agregar_beneficiario();"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar beneficiario</button>
                        </div>
                        <hr>
                        <div id="beneficiados_form"></div>
                        <!-- fin de beneficiarios en caso de muerte del asegurado titular -->
                        
                        <!-- inicio de otros seguros de salud en esta u otra compañia aseguradora -->
                        <hr>
                        <div class="form-group">
                            <h4>Otros seguros de salud en esta u otra compañia aseguradora.</h4>
                            <button class="btn btn-success" type="button"  onclick="agregar_otros_seguros();"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar otro seguro</button>
                        </div>
                        <hr>
                        <div id="otros_seguros_form"></div>
                        <!-- fin de otros seguros de salud en esta u otra compañia aseguradora -->
                        <!-- inicio de las preguntas del asegurado -->
                        <hr>
                        <div class="form-group">
                            <h4><strong>Declaracion de salud:</strong> Responda si o no, tomando en cuenta al titular y cada persona del grupo a asegurar.</h4>
                        </div>
                        <hr>
                        <?php foreach($preguntas_form as $fila => $valor){?>
                        <div class="form-group">
                            <label class="control-label col-md-10"><?php echo $valor ?></label>
                            <div class="col-md-2">
                                <select name="pregunta_form[<?php echo $fila ?>]" class="form-control" required>
                                    <option value="no">no</option>
                                    <option value="si">si</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <?php } ?>
                        <!-- fin de las preguntas del asegurado -->
                        <!-- inicio de las personas que son afectados por las preguntas -->
                        <hr>
                        <div class="form-group">
                            <h4>En caso de haber respondido afirmativamente alguna de las preguntas anteriores, indique el numero de la pregunta que corresponda, especifique la persona del grupo a asegurar afectada, e indique detalles tales como: Diagnostico (enfermedad) o intervencion quirurgica, fecha, nombre, y ubicacion del medico tratante, condicion actual. Anexe informe medico, resultados de examenes medicos y cualquier otro documento relacionado.</h4>
                            <button class="btn btn-success" type="button"  onclick="agregar_enfermedad();"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar persona del grupo</button>
                        </div>
                        <hr>
                        <div id="enfermedad_form"></div>
                        <!-- fin de las personas que son afectados por las preguntas --> 
                        <!-- inicio de personas que padecieron alguna de las preguntas -->
                        <hr>
                        <div class="form-group">
                            <h4>Familiar del asegurado o persona del grupo a asegurar que padecio alguna enfermedad mental, del corazon, cancer, diabetes, riñores, tuberculosis, paralisis, apoplejia, hemiplejia, medua, reumatismo o a cometido suicidio.</h4>
                            <button class="btn btn-success" type="button"  onclick="agregar_padecimiento();"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar otra persona</button>
                        </div>
                        <hr>
                        <div id="padecimiento_form"></div>
                        <!-- fin de personas que padecieron alguna de las preguntas -->
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

<div class="modal fade" id="table_siniestros_form" role="dialog">
    <div class="modal-dialog modal-lg lgx">
        <div class="modal-content">
            <div class="modal-header">
                <button class="btn btn-warning" data-toggle="modal" href="#modal_siniestros_form" onclick="agregar_siniestros_ajax()"><i class="glyphicon glyphicon-plus"></i> Agregar siniestro</button>
                <button class="btn btn-default" onclick="recargar_tabla_siniestros()"><i class="glyphicon glyphicon-refresh"></i> Recargar</button>
                <button class="btn btn-default" onclick="mostrar_filtros_siniestro()"><i class="glyphicon glyphicon-open"></i> Mostrar filtros</button>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title" >Filtros:</h3>
                    </div>
                    <div class="panel-body" id="panel_siniestro">
                        <form id="form-filter_siniestros" class="form-horizontal">
                            <div class="row col-md-6">
                                <div class="form-group">
                                    <label for="tipo_solicitud_filtro" class="col-md-4 control-label">Tipo de solicitud</label>
                                    <div class="col-md-8">
                                        <select id="tipo_solicitud_filtro" class="form-control">
                                            <option value="">Seleccione</option>
                                            <option value="carta_aval">Carta aval</option>
                                            <option value="reembolso">Reembolso</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="fecha_recepcion_filtro" class="col-md-4 control-label">Fecha de recepción</label>
                                    <div class="col-md-8">
                                        <input type="date" class="form-control" id="fecha_recepcion_filtro">
                                    </div>
                                </div>
                            </div>
                            <div class="row col-md-6">
                                <div class="form-group">
                                    <label for="fecha_entrega_filtro" class="col-md-4 control-label">Fecha de entrega</label>
                                    <div class="col-md-8">
                                        <input type="date" class="form-control" id="fecha_entrega_filtro">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="observacion_filtro" class="col-md-4 control-label">Observación</label>
                                    <div class="col-md-8">
                                        <textarea id="observacion_filtro" placeholder="Observacion" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-md-6">
                                <div class="form-group">
                                    <label for="monto_solicitado_filtro" class="col-md-4 control-label">Monto solicitado</label>
                                    <div class="col-md-8">
                                        <input type="number" class="form-control" id="monto_solicitado_filtro" placeholder="Monto solicitado" onkeypress="return isNumberKey(event)">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="monto_aprobado_filtro" class="col-md-4 control-label">Monto aprobado</label>
                                    <div class="col-md-8">
                                        <input type="number" class="form-control" id="monto_aprobado_filtro" placeholder="Monto aprobado" onkeypress="return isNumberKey(event)">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <button type="button" id="btn-filter" class="btn btn-primary">Filtrar</button>
                                    <button type="button" id="btn-reset" class="btn btn-default" onclick="recargar_tabla_siniestros()">Reiniciar campos</button>
                                    <button type="button" id="btn-report" class="btn btn-default" onclick="reporte_pdf()">reporte pdf</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="table-responsive"><!--class="table table-striped table-hover responsive"-->
                        <table id="table_siniestros" class="table table-striped table-hover responsive" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Nº</th>
                                    <th>Tipo de solicitud</th>
                                    <th>Nombre del beneficiario</th>
                                    <th>Doc de identidad del beneficiario</th>
                                    <th>Fecha de recepcion</th>
                                    <th>Tiempo transcurrido</th>
                                    <th>Fecha de entrega</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Nº</th>
                                    <th>Tipo de solicitud</th>
                                    <th>Nombre del beneficiario</th>
                                    <th>Doc de identidad del beneficiario</th>
                                    <th>Fecha de recepcion</th>
                                    <th>Tiempo transcurrido</th>
                                    <th>Fecha de entrega</th>
                                    <th>Acciones</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="modal_siniestros_form" role="dialog">
    <div class="modal-dialog modal-lg lgx">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 class="modal-title" id="title_siniestros"></h3>
                <button type="button" id="btnprint" onclick="reporte_individual_siniestros()" class="btn btn-info"><i class="glyphicon glyphicon-print"></i> Imprimir</button>
            </div>
            <div class="modal-body">
                <form action="#" id="form_siniestros" class="form-horizontal" enctype="multipart/form-data">
                    <input type="hidden" value="" name="id_siniestro" readonly/>
                    <input type="hidden" value="" name="id_poliza" readonly/>
                    <div class="form-body">
                        <div class="form-group">
                            <h4>Datos del siniestro</h4>
                        </div>
                        <hr>
                        <!-- inicio del registro de datos del contratante -->
                        <div class="form-group">
                            <label class="control-label col-md-4">Tipo de solicitud</label>
                            <div class="col-md-8">
                                <select name="tipo_solicitud_form" class="form-control" required>
                                    <option value="">Seleccione</option>
                                    <option value="carta_aval">Carta aval</option>
                                    <option value="reembolso">Reembolso</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">Beneficiario seleccionado</label>
                            <div class="col-md-8">
                                <select name="listar_beneficiarios_form" id="listar_beneficiarios_form" class="form-control" onblur="cargar_beneficiarios()">
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="id_beneficiario_siniestros" value="" readonly>
                            <label class="control-label col-md-3">documento de identidad</label>
                            <div class="col-md-3">
                                <select name="tipo_doc_beneficiario_form" class="form-control" readonly>
                                    <option value="">Seleccione</option>
                                    <option value="v">Cedula</option>
                                    <option value="j">Rif</option>
                                    <option value="e">Extranjero</option>
                                    <option value="p">Pasaporte</option>
                                </select>
                            </div>
                            <label class="control-label col-md-3">Doc de identidad</label>
                            <div class="col-md-3">
                                <input type="text" name="doc_identidad_beneficiario_form" placeholder="Doc de identidad" class="form-control" maxlength="15" readonly>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">Nombre y apellido del beneficiario</label>
                            <div class="col-md-8">
                                <textarea name="nombre_beneficiario_form" placeholder="Nombre y apellido del beneficiario" class="form-control" maxlength="200" readonly></textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Fecha de recepcion</label>
                            <div class="col-md-3">
                                <input type="date" name="fecha_recepcion_form" class="form-control" required>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-3">Fecha de entrega</label>
                            <div class="col-md-3">
                                <input type="date" name="fecha_entrega_form" class="form-control">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">Observacion</label>
                            <div class="col-md-8">
                                <textarea name="observacion_form" placeholder="Observacion" class="form-control"></textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Monto solicitado</label>
                            <div class="col-md-3">
                                <input type="number" name="monto_solicitado_form" class="form-control" onkeypress="return isNumberKey(event)" required> 
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label col-md-3">Monto aprobado</label>
                            <div class="col-md-3">
                                <input type="number" name="monto_aprobado_form" class="form-control" onkeypress="return isNumberKey(event)"> 
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

<div class="modal fade" id="modal_pago_form" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 class="modal-title" id="title_pago"></h3>
            </div>
            <div class="modal-body">
                <form action="#" id="form_pago" class="form-horizontal" enctype="multipart/form-data">
                    <input type="hidden" value="" name="id_pago" readonly/>
                    <div class="form-body">
                        <div class="form-group">
                            <h4 id="title_tipo_fecha_corte"></h4>
                        </div>
                        <hr>                
                        <div class="form-group">
                            <label class="control-label col-md-4">Cancelacion de corte</label>
                            <div class="col-md-8">
                                <input type="number" name="nuevo_corte_form" class="form-control" placeholder="Cancelacion de corte">
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