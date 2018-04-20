<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Polizas extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Polizas_model', "model");
		$this->load->model('pdf_model'); // modelo para imprimir en pdf
		$this->load->library('html2pdf'); // libreria para imprimir en pdf
		
		$this->sesion = $this->seguridad->get_user_nivel(get_class($this));
		if(!is_int($this->sesion))
		{
			redirect("Login");
		}
	}

	public function index()
	{
		if($this->sesion == 1 || $this->sesion == 2 || $this->sesion == 3)
		{
			$ano_array = array();
			$edad_array = array();
			for($ano = 1; $ano<=90; $ano++) {//funcion para generar un select que muestra los años en el buscador
				$ano_array[] = $ano;
			}
			foreach ($ano_array as $key => $value) {
				$edad_array[$value] = $value;
			}
			$data["edad_form"] = $edad_array;
			$data["login"] = $this->session->userdata("nombres");//muestra el nombre del usuario
			$data["nav"] = $this->seguridad->get_user_groups();//muestra el menu de los usuarios cuando estan conectados
			$data['titulo'] = "Polizas";//titulo de la pagina
			//$data['modulo_filtro'] = form_dropdown('',$this->_array_controllers(),'','id="modulo_filtro" class="form-control"');/*envia el array de controladores para mostrar a los usuarios*/
			$data["nivel"] = $nivel = array("1", "2", "3"); //nivel de seguridad usado al momento de agregar un modulo 
			$data["ingreso_anual_form"] = $this->model->get_list_filtro("monto", "ingreso_anual_asegurado"); // lista que muestra los montos anuales que gana el asegurado
			$data["preguntas_form"] = $this->model->get_list_filtro("pregunta", "preguntas_declaracion_salud"); //muestra las preguntas que se realizan al asegurado
			$this->_render_page("polizas_view", $data);
		}
		else
		{	
			redirect("Login");
		}
	}

	public function ajax_list()
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1 || $this->sesion == 2 || $this->sesion == 3)
			{
				$data = array();
				$no = $_POST['start'];
				$this->data['array'] = $this->model->get_datatables();

				foreach ($this->data['array'] as $rows) {
					$no++;
					$row = array();
					$row[] = $no;
					$row[] = $rows->ci_rif;
					$row[] = $rows->nombres." ".$rows->apellidos;
					$row[] = $rows->nombre_razonsocial;
					$row[] = $this->_calculo_pago_poliza($rows->fecha_corte,$rows->tipo_fecha_corte);
					$row[] = $rows->tipo_fecha_corte;
					$row[] = $rows->tipo_poliza;
					$row[] = '<td>
					<div class="dropdown">
						<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
								Acciones
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
							<li><a data-toggle="modal" class="btn btn-sm btn-success" style="color: black;" href="javascript:void(0)" title="Pago de poliza" onclick="pago_corte_poliza_ajax('."'".$rows->id."'".')" data-toggle="modal"><i class="glyphicon glyphicon-plus"></i> Pago de poliza</a></li>
							<li><a data-toggle="modal" class="btn btn-sm btn-warning" style="color: black;" href="javascript:void(0)" title="Siniestros" onclick="mostrar_siniestros_ajax('."'".$rows->id."'".')" data-toggle="modal"><i class="glyphicon glyphicon-plus"></i> Siniestros</a></li>
							<li><a class="btn btn-sm btn-primary" style="color: black;" href="javascript:void(0)" title="Editar" onclick="editar_ajax('."'".$rows->id."'".')"><i class="glyphicon glyphicon-pencil"></i> Editar</a></li>
							<li><a class="btn btn-sm btn-info" style="color: black;" href="javascript:void(0)" title="Detalles" onclick="detalles_ajax('."'".$rows->id."'".')"><i class="glyphicon glyphicon-list-alt"></i> Detalles</a></li>
							<li><a class="btn btn-sm btn-danger" style="color: black;" href="javascript:void(0)" title="Borrar" onclick="borrar_ajax('."'".$rows->id."'".')"><i class="glyphicon glyphicon-trash"></i> Borrar</a></li>
						</ul>
					</div></td>
					';
					
					$data[] = $row;
				}

				$output = array(
								"draw" => $_POST['draw'],
								"recordsTotal" => $this->model->count_all(),
								"recordsFiltered" => $this->model->count_filtered(),
								"data" => $data,
						);
				//output to json format
				echo json_encode($output);
				exit();
			}
			else
			{	
				redirect("Login");
			}
		}
		else
		{
			redirect("Login");
		}
	}

	public function cargar_preguntas()
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1 || $this->sesion == 2 || $this->sesion == 3)
			{
				$data = array();
				$data["info"] = $this->model->get_list_filtro("pregunta","preguntas_declaracion_salud");
				
				echo json_encode($data);
				exit();
			}
			else
			{	
				echo json_encode(array("status" => FALSE));
				exit();
			}		
		}
		else
		{
			redirect("Login");
		}
	}

	public function id_max_grupo()
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1 || $this->sesion == 2 || $this->sesion == 3)
			{
				$data = $this->model->get_id_max_by_table("grupo_asegurado");
				if($data->id == NULL)
				{
					$data->id = 0;
				}
				echo json_encode($data);
				exit();
			}
			else
			{	
				echo json_encode(array("status" => FALSE));
				exit();
			}		
		}
		else
		{
			redirect("Login");
		}
	}

	public function id_max_enfermedad()
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1 || $this->sesion == 2 || $this->sesion == 3)
			{
				$data = $this->model->get_id_max_by_table("enfermedad_preguntas");
				if($data->id == NULL)
				{
					$data->id = 0;
				}
				echo json_encode($data);
				exit();
			}
			else
			{	
				echo json_encode(array("status" => FALSE));
				exit();
			}		
		}
		else
		{
			redirect("Login");
		}
	}

	public function ajax_edit($id = FALSE)
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1 || $this->sesion == 2 || $this->sesion == 3)
			{
				$row = $this->model->get_by_id($id);

				$data = array();
				$data["tipo_poliza"] = $this->_tipo_poliza($row->tipo_poliza);

				$data["contratante"] = $this->model->get_by_id_and_table(array("id" => $row->id_contratante),"contratante");
				
				$data["asegurado"] = $this->model->get_by_id_and_table(array("id" => $row->id_asegurado),"asegurado");

				$data["cobertura"] = $this->model->get_by_id_and_table(array("id_asegurado" => $row->id_asegurado),"cobertura_solicitada");

				$data["grupo"] = $this->model->get_list_by_id_and_table(array("id_asegurado" => $row->id_asegurado),"grupo_asegurado");
				
				if (empty($data["grupo"]))
				{
					$data["grupo"] = NULL;
				}
				
				$data["beneficiarios"] = $this->model->get_list_by_id_and_table(array("id_asegurado" => $row->id_asegurado),"beneficiarios");
				
				if (empty($data["beneficiarios"]))
				{
					$data["beneficiarios"] = NULL;
				}

				$data["otros_seguros_asegurado"] = $this->model->get_list_by_id_and_table(array("id_asegurado" => $row->id_asegurado),"otros_seguros_asegurado");
				
				if (empty($data["otros_seguros_asegurado"]))
				{
					$data["otros_seguros_asegurado"] = NULL;
				}

				$data["preguntas_asegurado"] = $this->model->get_list_by_id_and_table(array("id_asegurado" => $row->id_asegurado),"preguntas_asegurado");
				
				if (empty($data["preguntas_asegurado"]))
				{
					$data["preguntas_asegurado"] = NULL;
				}

				$data["enfermedad"] = $this->model->get_list_by_id_and_table(array("id_asegurado" => $row->id_asegurado),"enfermedad_preguntas");
				
				if (empty($data["enfermedad"]))
				{
					$data["enfermedad"] = NULL;
				}
				else
				{
					foreach ($data["enfermedad"] as $key => $value)
					{
						$data["documentos_enfermedad"][] = $this->model->get_list_by_id_and_table(array("id_enfermedad" => $value->id),"documentos_enfermedad");
					}
				}
								
				$data["padecimiento"] = $this->model->get_list_by_id_and_table(array("id_asegurado_padecimiento" => $row->id_asegurado),"padecimiento");
				
				if (empty($data["padecimiento"]))
				{
					$data["padecimiento"] = NULL;
				}

				echo json_encode($data);
				exit();
			}
			else
			{	
				echo json_encode(array("status" => FALSE));
				exit();
			}		
		}
		else
		{
			redirect("Login");
		}
	}

	public function ajax_add()
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1 || $this->sesion == 2)
			{
				$this->_validate();

				$data["tipo_poliza"] = $this->_tipo_poliza();
				$data["num_poliza"] = $this->input->post("num_poliza_form", TRUE);
				
				$data["contratante"] = array(
					'nombre_razonsocial' => $this->input->post('nombre_razonsocial_form', TRUE),
					"tipo_documento_contratante" => $this->input->post("tipo_documento_contratante_form", TRUE),
					'ci_rif' => $this->input->post('ci_rif_form', TRUE),
					'tipo_persona' => $this->input->post('tipo_persona_form', TRUE),
					'nacionalidad' => $this->input->post('nacionalidad_form', TRUE),
					'sexo' => $this->input->post('sexo_form', TRUE),
					'estado_civil' =>$this->input->post('estado_civil_form', TRUE),
					'fecha_nacimiento_constitucion' => $this->input->post('fecha_nacimiento_constitucion_form', TRUE),
					"lugar_nacimiento_constitucion" => $this->input->post('lugar_nacimiento_constitucion_form', TRUE),
					"nombre_registro_mercantil" => $this->input->post('nombre_registro_mercantil_form', TRUE),
					"numero_registro" => $this->input->post('numero_registro_form', TRUE),
					"numero_tomo" => $this->input->post('numero_tomo_form', TRUE),
					"profesion_actividad_economica" => $this->input->post('profesion_actividad_economica_form', TRUE),
					"ingreso_prome_anual" => $this->input->post('ingreso_prome_anual_form', TRUE),
					"representante_legal" => $this->input->post('representante_legal_form', TRUE),
					"ci_representante_legal" => $this->input->post('ci_representante_legal_form', TRUE),
					"pais" => $this->input->post('pais_form', TRUE),
					"estado" => $this->input->post('estado_form', TRUE),
					"ciudad" => $this->input->post('ciudad_form', TRUE),
					"municipio" => $this->input->post('municipio_form', TRUE),
					"parroquia" => $this->input->post('parroquia_form', TRUE),
					"urbanizacion" => $this->input->post('urbanizacion_form', TRUE),
					"calle" => $this->input->post('calle_form', TRUE),
					"centrocomercial_casa" => $this->input->post('centrocomercial_casa_form', TRUE),
					"piso" => $this->input->post('piso_form', TRUE),
					"num_apto" => $this->input->post('num_apto_form', TRUE),
					"telf1" => $this->input->post('telf1_form', TRUE),
					"telf2" => $this->input->post('telf2_form', TRUE),
					"telf3" => $this->input->post('telf3_form', TRUE),
					"telf_cel" => $this->input->post('telf_cel_form', TRUE),
					"fax" => $this->input->post('fax_form', TRUE),
					"zona_postal" => $this->input->post('zona_postal_form', TRUE),
					"correo" => $this->input->post('correo_form', TRUE),
				);

				$data["asegurado"] = array(
					"tipo_documento_asegurado" => $this->input->post("tipo_documento_asegurado_form", TRUE),
					"ci_pasaporte_asegurado" => $this->input->post("ci_pasaporte_asegurado_form", TRUE),
					"nacionalidad_asegurado" => $this->input->post("nacionalidad_asegurado_form", TRUE),
					"sexo_asegurado" => $this->input->post("sexo_asegurado_form", TRUE),
					"nombres_asegurado" => $this->input->post("nombres_asegurado_form", TRUE),
					"apellidos_asegurado" => $this->input->post("apellidos_asegurado_form", TRUE),
					"fecha_nacimiento_asegurado" => $this->input->post("fecha_nacimiento_asegurado_form", TRUE),
					"lugar_nacimiento_asegurado" => $this->input->post("lugar_nacimiento_asegurado_form", TRUE),
					"edad_asegurado" => $this->input->post("edad_asegurado_form", TRUE),
					"estado_civil_asegurado" => $this->input->post("estado_civil_asegurado_form", TRUE),
					"profesion_asegurado" => $this->input->post("profesion_asegurado_form", TRUE),
					"ocupacion_asegurado" => $this->input->post("ocupacion_asegurado_form", TRUE),
					"ingreso_anual_asegurado" => $this->input->post("ingreso_anual_asegurado_form", TRUE),
					"fecha_ingreso_empresa_asegurado" => $this->input->post("fecha_ingreso_empresa_asegurado_form", TRUE),
					"fecha_ingreso_poliza_asegurado" => $this->input->post("fecha_ingreso_poliza_asegurado_form", TRUE),
					"estatura_asegurado" => $this->input->post("estatura_asegurado_form", TRUE),
					"peso_asegurado" => $this->input->post("peso_asegurado_form", TRUE),
					"zurdo_asegurado" => $this->input->post("zurdo_asegurado_form", TRUE),
					"deportes_pasatiempo" => $this->input->post("deportes_pasatiempo_asegurado_form", TRUE),
					"pais_asegurado" => $this->input->post("pais_asegurado_form", TRUE),
					"estado_asegurado" => $this->input->post("estado_asegurado_form", TRUE),
					"ciudad_asegurado" => $this->input->post("ciudad_asegurado_form", TRUE),
					"municipio_asegurado" => $this->input->post("municipio_asegurado_form", TRUE),
					"parroquia_asegurado" => $this->input->post("parroquia_asegurado_form", TRUE),
					"urbanizacion_asegurado" => $this->input->post("urbanizacion_asegurado_form", TRUE),
					"calle_asegurado" => $this->input->post("calle_asegurado_form", TRUE),
					"centrocomercial_casa_asegurado" => $this->input->post("centrocomercial_casa_asegurado_form", TRUE),
					"piso_asegurado" => $this->input->post("piso_asegurado_form", TRUE),
					"num_apto_asegurado" => $this->input->post("num_apto_asegurado_form", TRUE),
					"telf1_asegurado" => $this->input->post("telf1_asegurado_form", TRUE),
					"telf2_asegurado" => $this->input->post("telf2_asegurado_form", TRUE),
					"telf3_asegurado" => $this->input->post("telf3_asegurado_form", TRUE),
					"telf_cel_asegurado" => $this->input->post("telf_cel_asegurado_form", TRUE),
					"fax_asegurado" => $this->input->post("fax_asegurado_form", TRUE),
					"zona_postal_asegurado" => $this->input->post("zona_postal_asegurado_form", TRUE),
					"correo_asegurado" => $this->input->post("correo_asegurado_form", TRUE),
				);

				$data["cobertura"] = array(
					"suma_asegurada_basico" => $this->input->post("suma_asegurada_basico_form", TRUE),
					"deducible_basico" => $this->input->post("deducible_basico_form", TRUE),
					"suma_asegurada_maternidad" => $this->input->post("suma_asegurada_maternidad_form", TRUE),
					"deducible_maternidad" => $this->input->post("deducible_maternidad_form", TRUE),
					"suma_asegurada_exceso" => $this->input->post("suma_asegurada_exceso_form", TRUE),
					"deducible_exceso" => $this->input->post("deducible_exceso_form", TRUE),
					"fecha_corte" => $this->input->post("fecha_corte_form", TRUE),
					"tipo_fecha_corte" => $this->input->post("tipo_fecha_corte_form", TRUE),
					"gastos_funerarios" => $this->input->post("gastos_funerarios_form", TRUE),
					"muerte_accidental" => $this->input->post("muerte_accidental_form", TRUE),
					"invalides_permanente" => $this->input->post("invalides_permanente_form", TRUE),
					"incapacidad_temporal" => $this->input->post("incapacidad_temporal_form", TRUE),
					"gastos_medicos" => $this->input->post("gastos_medicos_form", TRUE),
					"muerte" => $this->input->post("muerte_form", TRUE),
					"ma_it_permanente" => $this->input->post("ma_it_permanente_form", TRUE),
					"pc_it_permanente" => $this->input->post("pc_it_permanente_form", TRUE),
					"pago_muerte_familiar" => $this->input->post("pago_muerte_familiar_form", TRUE),
				);

				$data["grupo"] = array(
					"nombres_apellidos_grupo" => $this->input->post("nombres_apellidos_grupo_form", TRUE),
					"parentesco_grupo" => $this->input->post("parentesco_grupo_form", TRUE),
					"ci_pasaporte_grupo" => $this->input->post("ci_pasaporte_grupo_form", TRUE),
					"fecha_nacimiento_grupo" => $this->input->post("fecha_nacimiento_grupo_form", TRUE),
					"edad_grupo" => $this->input->post("edad_grupo_form", TRUE),
					"sexo_grupo" => $this->input->post("sexo_grupo_form", TRUE),
					"estado_civil_grupo" => $this->input->post("estado_civil_grupo_form", TRUE),
					"peso_grupo" => $this->input->post("peso_grupo_form", TRUE),
					"estatura_grupo" => $this->input->post("estatura_grupo_form", TRUE),
					"zurdo_grupo" => $this->input->post("zurdo_grupo_form", TRUE),
					"profesion_grupo" => $this->input->post("profesion_grupo_form", TRUE),
					"ocupacion_grupo" => $this->input->post("ocupacion_grupo_form", TRUE),
					"pasatiempos_grupo" => $this->input->post("pasatiempos_grupo_form", TRUE),
					"deportes_grupo" => $this->input->post("deportes_grupo_form", TRUE),
				);
				
				$data["beneficiario"] = array(
					"tipo_doc_beneficiario" => $this->input->post("tipo_doc_identidad_beneficiario_form", TRUE),
					"ci_pasaporte_beneficiarios" => $this->input->post("ci_pasaporte_beneficiarios_form", TRUE),
					"nombres_apellidos_beneficiarios" => $this->input->post("nombres_apellidos_beneficiarios_form", TRUE),
					"parentesco_beneficiarios" => $this->input->post("parentesco_beneficiarios_form", TRUE),
					"distribucion_beneficiarios" => $this->input->post("distribucion_beneficiarios_form", TRUE),
				);
				
				$data["otros_seguros"] = array(
					"nombre_empresa" => $this->input->post("nombre_empresa_form", TRUE),
					"numero_poliza" => $this->input->post("numero_poliza_form", TRUE),
					"monto" => $this->input->post("monto_form", TRUE),
					"estado_poliza" => $this->input->post("estado_poliza_form", TRUE),
				);
				
				$data["preguntas"] = array(
					"respuesta_pregunta" => $this->input->post("pregunta_form", TRUE),
				);
				
				$data["enfermedad"] = array(
					"id_pregunta" => $this->input->post("pregunta_enfermedad_form", TRUE),
					"id_grupo" => $this->input->post("select_grupo_form", TRUE),
					"diagnostico_intervencion_enfermedad" => $this->input->post("diagnostico_intervencion_enfermedad_form", TRUE),
					"fecha_enfermedad" => $this->input->post("fecha_enfermedad_form", TRUE),
					"nombres_apellidos_medico_enfermedad" => $this->input->post("nombres_apellidos_medico_enfermedad_form", TRUE),
					"ubicacion_medico_enfermedad" => $this->input->post("ubicacion_medico_enfermedad_form", TRUE),
					"condicion_actual_enfermedad" => $this->input->post("condicion_actual_enfermedad_form", TRUE),
				);
				
				if ($this->input->post("archivo_form") != NULL && $this->input->post("descripcion_archivo_form") != NULL)// verifica si se envia datos de documentos relacionados a alguna enfermedad
				{
					$doc = $this->_do_upload();//funcion para subir los documentos y obtener su informacion para enviarla a la base de datos
					if(is_array($doc))
					{
						if (!isset($doc["client_name"]))
						{
							foreach ($doc as $key => $value)
							{
								$archivo_cliente[] = $value["client_name"];
								$archivo[] = $value["file_name"];
								$destino[] = $value["file_path"];
								$tipo[] = $value["file_type"];
								$tamanio[] = $value["file_size"];
							}
						}
						else
						{
							$archivo_cliente[] = $doc["client_name"];
							$archivo[] = $doc["file_name"];
							$destino[] = $doc["file_path"];
							$tipo[] = $doc["file_type"];
							$tamanio[] = $doc["file_size"];
						}
					
						$data["documento"] = array(
							"id_enfermedad" => $this->input->post("id_enfermedad_documento", TRUE),
							"descripcion_archivo" => $this->input->post("descripcion_archivo_form", TRUE),
							"archivo_cliente" => $archivo_cliente,
							"archivo" => $archivo,
							"destino" => $destino,
							"tipo" => $tipo,
							"tamanio" => $tamanio,
						);
					}
				}
								
				$data["padecimiento"] = array(
					"nombres_apellidos_padecimiento" => $this->input->post("nombres_apellidos_padecimiento_form", TRUE),
					"parentesco_padecimiento" => $this->input->post("parentesco_padecimiento_form", TRUE),
					"edad_padecimiento" => $this->input->post("edad_padecimiento_form", TRUE),
					"fallecido_padecimiento" => $this->input->post("fallecido_padecimiento_form", TRUE),
					"causa_padecimiento" => $this->input->post("causa_padecimiento_form", TRUE),
				);

				$insert = $this->model->save($data);
				echo json_encode($insert);
				
				//registro de acciones
				$this->seguridad->registrar_accion("Se agrega la poliza con el id ".$insert["insert_id"]);
				exit();
			}
			else
			{	
				echo json_encode(array("status" => "nivel"));
				exit();
			}		
		}
		else
		{
			redirect("Login");
		}
	}

	public function ajax_update()
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1)
			{
				$this->_validate();
				
				$data["tipo_poliza"] = $this->_tipo_poliza();
				$data["num_poliza"] = $this->input->post("num_poliza_form", TRUE);
				
				$where["contratante"] = array('id' => $this->input->post('id', TRUE));
				$data["contratante"] = array(
					'nombre_razonsocial' => $this->input->post('nombre_razonsocial_form', TRUE),
					"tipo_documento_contratante" => $this->input->post("tipo_documento_contratante_form", TRUE),
					'ci_rif' => $this->input->post('ci_rif_form', TRUE),
					'tipo_persona' => $this->input->post('tipo_persona_form', TRUE),
					'nacionalidad' => $this->input->post('nacionalidad_form', TRUE),
					'sexo' => $this->input->post('sexo_form', TRUE),
					'estado_civil' =>$this->input->post('estado_civil_form', TRUE),
					'fecha_nacimiento_constitucion' => $this->input->post('fecha_nacimiento_constitucion_form', TRUE),
					"lugar_nacimiento_constitucion" => $this->input->post('lugar_nacimiento_constitucion_form', TRUE),
					"nombre_registro_mercantil" => $this->input->post('nombre_registro_mercantil_form', TRUE),
					"numero_registro" => $this->input->post('numero_registro_form', TRUE),
					"numero_tomo" => $this->input->post('numero_tomo_form', TRUE),
					"profesion_actividad_economica" => $this->input->post('profesion_actividad_economica_form', TRUE),
					"ingreso_prome_anual" => $this->input->post('ingreso_prome_anual_form', TRUE),
					"representante_legal" => $this->input->post('representante_legal_form', TRUE),
					"ci_representante_legal" => $this->input->post('ci_representante_legal_form', TRUE),
					"pais" => $this->input->post('pais_form', TRUE),
					"estado" => $this->input->post('estado_form', TRUE),
					"ciudad" => $this->input->post('ciudad_form', TRUE),
					"municipio" => $this->input->post('municipio_form', TRUE),
					"parroquia" => $this->input->post('parroquia_form', TRUE),
					"urbanizacion" => $this->input->post('urbanizacion_form', TRUE),
					"calle" => $this->input->post('calle_form', TRUE),
					"centrocomercial_casa" => $this->input->post('centrocomercial_casa_form', TRUE),
					"piso" => $this->input->post('piso_form', TRUE),
					"num_apto" => $this->input->post('num_apto_form', TRUE),
					"telf1" => $this->input->post('telf1_form', TRUE),
					"telf2" => $this->input->post('telf2_form', TRUE),
					"telf3" => $this->input->post('telf3_form', TRUE),
					"telf_cel" => $this->input->post('telf_cel_form', TRUE),
					"fax" => $this->input->post('fax_form', TRUE),
					"zona_postal" => $this->input->post('zona_postal_form', TRUE),
					"correo" => $this->input->post('correo_form', TRUE),
				);
				
				$where["asegurado"] = array("id" => $this->input->post("id_asegurado", TRUE));
				$data["asegurado"] = array(
					"id_contratante" => $this->input->post("id", TRUE),
					"tipo_documento_asegurado" => $this->input->post("tipo_documento_asegurado_form", TRUE),
					"ci_pasaporte_asegurado" => $this->input->post("ci_pasaporte_asegurado_form", TRUE),
					"nacionalidad_asegurado" => $this->input->post("nacionalidad_asegurado_form", TRUE),
					"sexo_asegurado" => $this->input->post("sexo_asegurado_form", TRUE),
					"nombres_asegurado" => $this->input->post("nombres_asegurado_form", TRUE),
					"apellidos_asegurado" => $this->input->post("apellidos_asegurado_form", TRUE),
					"fecha_nacimiento_asegurado" => $this->input->post("fecha_nacimiento_asegurado_form", TRUE),
					"lugar_nacimiento_asegurado" => $this->input->post("lugar_nacimiento_asegurado_form", TRUE),
					"edad_asegurado" => $this->input->post("edad_asegurado_form", TRUE),
					"estado_civil_asegurado" => $this->input->post("estado_civil_asegurado_form", TRUE),
					"profesion_asegurado" => $this->input->post("profesion_asegurado_form", TRUE),
					"ocupacion_asegurado" => $this->input->post("ocupacion_asegurado_form", TRUE),
					"ingreso_anual_asegurado" => $this->input->post("ingreso_anual_asegurado_form", TRUE),
					"fecha_ingreso_empresa_asegurado" => $this->input->post("fecha_ingreso_empresa_asegurado_form", TRUE),
					"fecha_ingreso_poliza_asegurado" => $this->input->post("fecha_ingreso_poliza_asegurado_form", TRUE),
					"estatura_asegurado" => $this->input->post("estatura_asegurado_form", TRUE),
					"peso_asegurado" => $this->input->post("peso_asegurado_form", TRUE),
					"zurdo_asegurado" => $this->input->post("zurdo_asegurado_form", TRUE),
					"deportes_pasatiempo" => $this->input->post("deportes_pasatiempo_asegurado_form", TRUE),
					"pais_asegurado" => $this->input->post("pais_asegurado_form", TRUE),
					"estado_asegurado" => $this->input->post("estado_asegurado_form", TRUE),
					"ciudad_asegurado" => $this->input->post("ciudad_asegurado_form", TRUE),
					"municipio_asegurado" => $this->input->post("municipio_asegurado_form", TRUE),
					"parroquia_asegurado" => $this->input->post("parroquia_asegurado_form", TRUE),
					"urbanizacion_asegurado" => $this->input->post("urbanizacion_asegurado_form", TRUE),
					"calle_asegurado" => $this->input->post("calle_asegurado_form", TRUE),
					"centrocomercial_casa_asegurado" => $this->input->post("centrocomercial_casa_asegurado_form", TRUE),
					"piso_asegurado" => $this->input->post("piso_asegurado_form", TRUE),
					"num_apto_asegurado" => $this->input->post("num_apto_asegurado_form", TRUE),
					"telf1_asegurado" => $this->input->post("telf1_asegurado_form", TRUE),
					"telf2_asegurado" => $this->input->post("telf2_asegurado_form", TRUE),
					"telf3_asegurado" => $this->input->post("telf3_asegurado_form", TRUE),
					"telf_cel_asegurado" => $this->input->post("telf_cel_asegurado_form", TRUE),
					"fax_asegurado" => $this->input->post("fax_asegurado_form", TRUE),
					"zona_postal_asegurado" => $this->input->post("zona_postal_asegurado_form", TRUE),
					"correo_asegurado" => $this->input->post("correo_asegurado_form", TRUE),
				);

				$where["cobertura"] = array("id" => $this->input->post("id_cobertura", TRUE));
				$data["cobertura"] = array(
					"suma_asegurada_basico" => $this->input->post("suma_asegurada_basico_form", TRUE),
					"deducible_basico" => $this->input->post("deducible_basico_form", TRUE),
					"suma_asegurada_maternidad" => $this->input->post("suma_asegurada_maternidad_form", TRUE),
					"deducible_maternidad" => $this->input->post("deducible_maternidad_form", TRUE),
					"suma_asegurada_exceso" => $this->input->post("suma_asegurada_exceso_form", TRUE),
					"deducible_exceso" => $this->input->post("deducible_exceso_form", TRUE),
					"fecha_corte" => $this->input->post("fecha_corte_form", TRUE),
					"tipo_fecha_corte" => $this->input->post("tipo_fecha_corte_form", TRUE),
					"gastos_funerarios" => $this->input->post("gastos_funerarios_form", TRUE),
					"muerte_accidental" => $this->input->post("muerte_accidental_form", TRUE),
					"invalides_permanente" => $this->input->post("invalides_permanente_form", TRUE),
					"incapacidad_temporal" => $this->input->post("incapacidad_temporal_form", TRUE),
					"gastos_medicos" => $this->input->post("gastos_medicos_form", TRUE),
					"muerte" => $this->input->post("muerte_form", TRUE),
					"ma_it_permanente" => $this->input->post("ma_it_permanente_form", TRUE),
					"pc_it_permanente" => $this->input->post("pc_it_permanente_form", TRUE),
					"pago_muerte_familiar" => $this->input->post("pago_muerte_familiar_form", TRUE),
				);

				$where["grupo"] = array("id" => $this->input->post("id_grupo", TRUE));
				$data["grupo"] = array(
					"id_asegurado" => $this->input->post("id_asegurado", TRUE),
					"nombres_apellidos_grupo" => $this->input->post("nombres_apellidos_grupo_form", TRUE),
					"parentesco_grupo" => $this->input->post("parentesco_grupo_form", TRUE),
					"ci_pasaporte_grupo" => $this->input->post("ci_pasaporte_grupo_form", TRUE),
					"fecha_nacimiento_grupo" => $this->input->post("fecha_nacimiento_grupo_form", TRUE),
					"edad_grupo" => $this->input->post("edad_grupo_form", TRUE),
					"sexo_grupo" => $this->input->post("sexo_grupo_form", TRUE),
					"estado_civil_grupo" => $this->input->post("estado_civil_grupo_form", TRUE),
					"peso_grupo" => $this->input->post("peso_grupo_form", TRUE),
					"estatura_grupo" => $this->input->post("estatura_grupo_form", TRUE),
					"zurdo_grupo" => $this->input->post("zurdo_grupo_form", TRUE),
					"profesion_grupo" => $this->input->post("profesion_grupo_form", TRUE),
					"ocupacion_grupo" => $this->input->post("ocupacion_grupo_form", TRUE),
					"pasatiempos_grupo" => $this->input->post("pasatiempos_grupo_form", TRUE),
					"deportes_grupo" => $this->input->post("deportes_grupo_form", TRUE),
				);
				
				$where["beneficiario"] = array("id" => $this->input->post("id_beneficiario", TRUE));
				$data["beneficiario"] = array(
					"id_asegurado" => $this->input->post("id_asegurado", TRUE),
					"tipo_doc_beneficiario" => $this->input->post("tipo_doc_identidad_beneficiario_form", TRUE),
					"ci_pasaporte_beneficiarios" => $this->input->post("ci_pasaporte_beneficiarios_form", TRUE),
					"nombres_apellidos_beneficiarios" => $this->input->post("nombres_apellidos_beneficiarios_form", TRUE),
					"parentesco_beneficiarios" => $this->input->post("parentesco_beneficiarios_form", TRUE),
					"distribucion_beneficiarios" => $this->input->post("distribucion_beneficiarios_form", TRUE),
				);

				$where["otros_seguros"] = array("id" => $this->input->post("id_otros_seguros", TRUE));
				$data["otros_seguros"] = array(
					"id_asegurado" => $this->input->post("id_asegurado", TRUE),
					"nombre_empresa" => $this->input->post("nombre_empresa_form", TRUE),
					"numero_poliza" => $this->input->post("numero_poliza_form", TRUE),
					"monto" => $this->input->post("monto_form", TRUE),
					"estado_poliza" => $this->input->post("estado_poliza_form", TRUE),
				);
				
				$data["preguntas"] = array(
					"id_asegurado" => $this->input->post("id_asegurado", TRUE),
					"respuesta_pregunta" => $this->input->post("pregunta_form", TRUE),
				);
				
				$where["enfermedad"] = array("id" => $this->input->post("id_enfermedad", TRUE));
				$data["enfermedad"] = array(
					"id_asegurado" => $this->input->post("id_asegurado", TRUE),
					"id_pregunta" => $this->input->post("pregunta_enfermedad_form", TRUE),
					"id_grupo" => $this->input->post("select_grupo_form", TRUE),
					"diagnostico_intervencion_enfermedad" => $this->input->post("diagnostico_intervencion_enfermedad_form", TRUE),
					"fecha_enfermedad" => $this->input->post("fecha_enfermedad_form", TRUE),
					"nombres_apellidos_medico_enfermedad" => $this->input->post("nombres_apellidos_medico_enfermedad_form", TRUE),
					"ubicacion_medico_enfermedad" => $this->input->post("ubicacion_medico_enfermedad_form", TRUE),
					"condicion_actual_enfermedad" => $this->input->post("condicion_actual_enfermedad_form", TRUE),
				);
				
				$doc = $this->_do_upload(TRUE);//funcion para subir los documentos y obtener su informacion para enviarla a la base de datos
				if(is_array($doc))
				{
					//borra el archivo
					foreach ($this->input->post('id_documento') as $key => $value)
					{
						$rows = $this->model->get_by_id_and_table(array("id" => $value),"documentos_enfermedad");
						if(file_exists('files/docs/asegurados/'.$rows->archivo))
						{
							unlink('files/docs/asegurados/'.$rows->archivo);	
						}
					}
					
					if (!isset($doc["client_name"]))
					{
						foreach ($doc as $key => $value)
						{
							$archivo_cliente[] = $value["client_name"];
							$archivo[] = $value["file_name"];
							$destino[] = $value["file_path"];
							$tipo[] = $value["file_type"];
							$tamanio[] = $value["file_size"];
						}
					}
					else
					{
						$archivo_cliente[] = $doc["client_name"];
						$archivo[] = $doc["file_name"];
						$destino[] = $doc["file_path"];
						$tipo[] = $doc["file_type"];
						$tamanio[] = $doc["file_size"];
					}
					
					$where["documento"] = array("id" => $this->input->post('id_documento', TRUE));
					$data["documento"] = array(
						"id_enfermedad" => $this->input->post("id_enfermedad_documento", TRUE),
						"descripcion_archivo" => $this->input->post("descripcion_archivo_form", TRUE),
						"archivo_cliente" => $archivo_cliente,
						"archivo" => $archivo,
						"destino" => $destino,
						"tipo" => $tipo,
						"tamanio" => $tamanio,
					);	
				}
				else
				{
					$where["documento"] = array("id" => $this->input->post('id_documento', TRUE));
					$data["documento"] = array(
						"id_enfermedad" => $this->input->post("id_enfermedad_documento", TRUE),
						"descripcion_archivo" => $this->input->post("descripcion_archivo_form", TRUE),
					);
				}
				
				$where["padecimiento"] = array("id" => $this->input->post("id_padecimiento", TRUE));
				$data["padecimiento"] = array(
					"id_asegurado" => $this->input->post("id_asegurado", TRUE),
					"nombres_apellidos_padecimiento" => $this->input->post("nombres_apellidos_padecimiento_form", TRUE),
					"parentesco_padecimiento" => $this->input->post("parentesco_padecimiento_form", TRUE),
					"edad_padecimiento" => $this->input->post("edad_padecimiento_form", TRUE),
					"fallecido_padecimiento" => $this->input->post("fallecido_padecimiento_form", TRUE),
					"causa_padecimiento" => $this->input->post("causa_padecimiento_form", TRUE),
				);
				
				//registro de acciones
				$this->seguridad->registrar_accion("Se modifica la poliza con el id ".$this->input->post('id', TRUE));
				$update = $this->model->update($data,$where);
				echo json_encode($update);
				exit();
			}
			else
			{	
				echo json_encode(array("status" => "nivel"));
				exit();
			}		
		}
		else
		{
			redirect("Login");
		}
	}

	public function ajax_pago_corte()
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1)
			{
				$nuevo_corte = $this->input->post("nuevo_corte_form", TRUE);

				$row = $this->model->get_by_id_and_table(array("id" => $this->input->post("id_pago", TRUE)),"poliza_comprada");

				$fecha = new DateTime($row->fecha_corte);
				
			    if ($row->tipo_fecha_corte == "Mensual")//Mensual
				{
					$tiempo = ($nuevo_corte * 1);
					$fecha->add(new DateInterval('P'.$tiempo.'M'));
					$nueva_fecha = $fecha->format('Y-m-d');
				}
				if ($row->tipo_fecha_corte == "Trimestral")//Trimestral
				{
					$tiempo = ($nuevo_corte * 3);
					$fecha->add(new DateInterval('P'.$tiempo.'M'));
					$nueva_fecha = $fecha->format('Y-m-d');
				}
				if ($row->tipo_fecha_corte == "Semestral")//Semestral
				{
					$tiempo = ($nuevo_corte * 6);
					$fecha->add(new DateInterval('P'.$tiempo.'M'));
					$nueva_fecha = $fecha->format('Y-m-d');
				}
				if ($row->tipo_fecha_corte == "Anual")//Anual
				{
					$tiempo = ($nuevo_corte * 1);
					$fecha->add(new DateInterval('P'.$tiempo.'Y'));
					$nueva_fecha = $fecha->format('Y-m-d');
				}
				
				$data = array(
					"fecha_corte" => $nueva_fecha,
				);
				
				$update_poliza = $this->model->update_table("poliza_comprada",$data,array("id" => $row->id));
				$update_cobertura = $this->model->update_table("cobertura_solicitada",$data,array("id_asegurado" => $row->id_asegurado));

				if($update_cobertura["status"] == TRUE && $update_poliza["status"] == TRUE)
				{
					$update = array("status" => TRUE);
				}
				else
				{
					$update = array("status" => "error");
				}

				echo json_encode($update);
				exit();
			}
			else
			{	
				echo json_encode(array("status" => "nivel"));
				exit();
			}		
		}
		else
		{
			redirect("Login");
		}
	}

	public function ajax_fecha_corte($id = FALSE)
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1)
			{
				$data = $this->model->get_by_id($id);
				
				echo json_encode($data);
				exit();
			}
			else
			{	
				echo json_encode(array("status" => FALSE));
				exit();
			}		
		}
		else
		{
			redirect("Login");
		}
	}

	public function ajax_delete($id = FALSE)
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1)
			{	
				//registro de acciones
				$this->seguridad->registrar_accion("Se Borro la poliza con el id ".$id);
				//se ubican los archivos que correspondan con la poliza y se borran
				$row = $this->model->get_by_id($id);
				$data["enfermedad"] = $this->model->get_list_by_id_and_table(array("id_asegurado" => $row->id_asegurado),"enfermedad_preguntas");//se ubican las enfermedades que esten registradas
				foreach ($data["enfermedad"] as $key => $value)
				{
					$array = $this->model->get_list_by_id_and_table(array("id_enfermedad" => $value->id),"documentos_enfermedad");//se ubican los documentos que esten registrados
					foreach ($array as $llave => $valor)
					{
						if(file_exists('files/docs/asegurados/'.$valor->archivo))
						{
							unlink('files/docs/asegurados/'.$valor->archivo);//se borran los archivos registrados
						}
					}
				}
				
				$this->model->delete_by_id($id);//se borra la poliza de la bd
				echo json_encode(array("status" => TRUE));
				exit();
			}
			else
			{	
				echo json_encode(array("status" => FALSE));
				exit();
			}		
		}
		else
		{
			redirect("Login");
		}
	}

	public function ajax_delete_grupo($id = FALSE)
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1)
			{	
				//registro de acciones
				$this->seguridad->registrar_accion("Se Borro un asegurado del grupo con el id ".$id);
				$this->model->delete_by_id_and_table($id,"grupo_asegurado");
				echo json_encode(array("status" => TRUE));
				exit();
			}
			else
			{	
				echo json_encode(array("status" => FALSE));
				exit();
			}		
		}
		else
		{
			redirect("Login");
		}
	}

	public function ajax_delete_beneficiario($id = FALSE)
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1)
			{	
				//registro de acciones
				$this->seguridad->registrar_accion("Se Borro la el beneficiario con el id ".$id);
				$this->model->delete_by_id_and_table($id,"beneficiarios");
				echo json_encode(array("status" => TRUE));
				exit();
			}
			else
			{	
				echo json_encode(array("status" => FALSE));
				exit();
			}		
		}
		else
		{
			redirect("Login");
		}
	}

	public function ajax_delete_seguros($id = FALSE)
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1)
			{	
				//registro de acciones
				$this->seguridad->registrar_accion("Se Borro una sup-poliza con el id ".$id);
				$this->model->delete_by_id_and_table($id,"otros_seguros_asegurado");
				echo json_encode(array("status" => TRUE));
				exit();
			}
			else
			{	
				echo json_encode(array("status" => FALSE));
				exit();
			}		
		}
		else
		{
			redirect("Login");
		}
	}

	public function ajax_delete_enfermedad($id = FALSE)
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1)
			{	
				//registro de acciones
				$this->seguridad->registrar_accion("Se Borro un asegurado del grupo que sufrio alguna enfermedad marcada en las preguntas con el id ".$id);
				//se ubican los archivos que esten registrados con la enfermedad
				$data["documentos"] = $this->model->get_list_by_id_and_table(array("id_enfermedad" => $id),"documentos_enfermedad");//se ubican los archivos que esten registradas
				foreach ($data["documentos"] as $key => $value)
				{
					if(file_exists('files/docs/asegurados/'.$value->archivo))
					{
						unlink('files/docs/asegurados/'.$value->archivo);//se borran los archivos registrados
					}
				}

				$this->model->delete_by_id_and_table($id,"enfermedad_preguntas");//se borra la enfermedad de la bd
				echo json_encode(array("status" => TRUE));
				exit();
			}
			else
			{	
				echo json_encode(array("status" => FALSE));
				exit();
			}		
		}
		else
		{
			redirect("Login");
		}
	}

	public function ajax_delete_padecimiento($id = FALSE)
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1)
			{	
				//registro de acciones
				$this->seguridad->registrar_accion("Se Borro un familiar del asegurado titular o del grupo con algun padecimiento con el id ".$id);
				$this->model->delete_by_id_and_table($id,"padecimiento");
				echo json_encode(array("status" => TRUE));
				exit();
			}
			else
			{	
				echo json_encode(array("status" => FALSE));
				exit();
			}		
		}
		else
		{
			redirect("Login");
		}
	}

	public function ajax_delete_documento($id = FALSE)
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1)
			{	
				//registro de acciones
				$this->seguridad->registrar_accion("Se Borro un familiar del asegurado titular o del grupo con el id ".$id);
				//ubica el archivo para eliminarlo
				$rows = $this->model->get_by_id_and_table(array("id" => $id),"documentos_enfermedad");
				if(file_exists('files/docs/asegurados/'.$rows->archivo))
				{
					unlink('files/docs/asegurados/'.$rows->archivo);	
				}
				//elimina el documento de la bd
				$this->model->delete_by_id_and_table($id,"padecimiento");
				echo json_encode(array("status" => TRUE));
				exit();
			}
			else
			{	
				echo json_encode(array("status" => FALSE));
				exit();
			}		
		}
		else
		{
			redirect("Login");
		}
	}

    public function reporte_individual()
	{
		if ($this->input->is_ajax_request())
		{
	    	if($this->sesion == 1 || $this->sesion == 2 || $this->sesion == 3)
			{
				//establecemos la carpeta en la que queremos guardar los pdfs,
		        //si no existen las creamos y damos permisos
		        $this->_createFolder();

		        //importante el slash del final o no funcionará correctamente
		        $this->html2pdf->folder('./files/pdfs/');

		        $reporte = "reporte_individual.pdf";
		        //establecemos el nombre del archivo
		        $this->html2pdf->filename($reporte);

		        //establecemos el tipo de papel
		        $this->html2pdf->paper('a4', 'portrait');
				
				//registro de acciones
				$this->seguridad->registrar_accion("Se realizo un reporte individual de la poliza con el id: ".$this->input->post("id"));

				$row = $this->model->get_by_id($this->input->post("id", TRUE));
				$row_empresa = $this->pdf_model->get_empresa($this->input->post("id_empresa", TRUE));

				$data_array["contratante"] = $this->model->get_by_id_and_table(array("id" => $row->id_contratante),"contratante");
				$data_array["asegurado"] = $this->model->get_by_id_and_table(array("id" => $row->id_asegurado),"asegurado");
				$data_array["cobertura"] = $this->model->get_by_id_and_table(array("id_asegurado" => $row->id_asegurado),"cobertura_solicitada");
				$data_array["grupo"] = $this->model->get_list_by_id_and_table(array("id_asegurado" => $row->id_asegurado),"grupo_asegurado");
				$data_array["beneficiarios"] = $this->model->get_list_by_id_and_table(array("id_asegurado" => $row->id_asegurado),"beneficiarios");
				$data_array["otros_seguros_asegurado"] = $this->model->get_list_by_id_and_table(array("id_asegurado" => $row->id_asegurado),"otros_seguros_asegurado");
				$data_array["preguntas_asegurado"] = $this->model->get_list_by_id_and_table(array("id_asegurado" => $row->id_asegurado),"preguntas_asegurado");
				$data_array["padecimiento"] = $this->model->get_list_by_id_and_table(array("id_asegurado_padecimiento" => $row->id_asegurado),"padecimiento");
				
		        $data = array(
		            "logo" => "./files/img/logos_empresas/".$row_empresa->archivo,
		            'title' => "POLIZAS",
		            "header" => utf8_encode($row_empresa->nombre_empresa),
		            'datos' => $data_array,
		            "rif" => "RIF.: ".$row_empresa->razon_social,
		            "contador" => utf8_encode("Nº: $row_empresa->contador"),
		        );

		        //hacemos que coja la vista como datos a imprimir
		        //importante utf8_decode para mostrar bien las tildes, ñ y demás
		        $this->html2pdf->html(utf8_decode($this->load->view('reportes_pdf/polizas_report_individual_pdf', $data, true)));

		        //si el pdf se guarda correctamente lo mostramos en pantalla
		        if($this->html2pdf->create('save'))
		        {
		            $this->_show($reporte);
		        }
		    }
			else
			{	
				echo json_encode(array("status" => FALSE));
				exit();
			}
		}
		else
		{
			redirect("Login");
		}
    }

    public function reporte()
    {
    	if ($this->input->is_ajax_request())
		{
	    	if($this->sesion == 1 || $this->sesion == 2 || $this->sesion == 3)
			{
		        //establecemos la carpeta en la que queremos guardar los pdfs,
		        //si no existen las creamos y damos permisos
		        $this->_createFolder();

		        //importante el slash del final o no funcionará correctamente
		        $this->html2pdf->folder('./files/pdfs/');

		        $reporte = "reporte.pdf";
		        //establecemos el nombre del archivo
		        $this->html2pdf->filename($reporte);

		        //establecemos el tipo de papel
		        $this->html2pdf->paper('a4', 'landscape');

		        //registro de acciones
				$this->seguridad->registrar_accion("Se realizo un reporte de las polizas");

				$row_empresa = $this->pdf_model->get_empresa($this->input->post("id_empresa", TRUE));

				$data = array(
					"logo" => "./files/img/logos_empresas/".$row_empresa->archivo,
		            'title' => "POLIZAS",
		            "header" => utf8_encode($row_empresa->nombre_empresa),
		            'datos' => $this->pdf_model->get_reporte_polizas(),
		            "contador" => utf8_encode("Nº: $row_empresa->contador"),
		            "rif" => "RIF.: ".$row_empresa->razon_social,
		        );

		        //hacemos que coja la vista como datos a imprimir
		        //importante utf8_decode para mostrar bien las tildes, ñ y demás
		        $this->html2pdf->html(utf8_decode($this->load->view('reportes_pdf/polizas_report_pdf', $data, true)));

		        //si el pdf se guarda correctamente lo mostramos en pantalla
		        if($this->html2pdf->create('save'))
		        {
		            $this->_show($reporte);
		        }
		    }
			else
			{	
				echo json_encode(array("status" => FALSE));
				exit();
			}
		}
		else
		{
			redirect("Login");
		}
    }

    public function get_list_empresas()
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1 || $this->sesion == 2 || $this->sesion == 3)
			{
				$data = $this->pdf_model->list_empresas();
				echo json_encode($data);
				exit();
			}
			else
			{	
				echo json_encode(array("status" => FALSE));
				exit();
			}		
		}
		else
		{
			redirect("Login");
		}
	}

	public function get_list_contratante()
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1 || $this->sesion == 2 || $this->sesion == 3)
			{
				$data["contratante"] = $this->model->get_list_filtro("nombre_razonsocial","contratante");
				
				echo json_encode($data);
				exit();
			}
			else
			{	
				echo json_encode(array("status" => FALSE));
				exit();
			}		
		}
		else
		{
			redirect("Login");
		}
	}
	
	public function get_contratante()
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1 || $this->sesion == 2 || $this->sesion == 3)
			{
				$data = $this->model->get_by_id_and_table(array("id" => $this->input->post("id", TRUE)),"contratante");
				echo json_encode($data);
				exit();
			}
			else
			{	
				echo json_encode(array("status" => FALSE));
				exit();
			}		
		}
		else
		{
			redirect("Login");
		}
	}

	private function _calculo_pago_poliza($fecha_corte,$tipo_fecha_corte)
	{
		$datetimei = new DateTime($fecha_corte);
		$datetimef = new DateTime(); //fecha actual
		$intervalo = $datetimef->diff($datetimei);
			
		if ($tipo_fecha_corte == "Mensual")//Mensual
		{
			if ($intervalo->m >= 1 && ($datetimei < $datetimef))
			{
				$data = '<span style="color:red;font-weight:bold">'.$fecha_corte.'</span>';
			}
			else
			{
				$data = $fecha_corte;
			}
		}
		if ($tipo_fecha_corte == "Trimestral")//Trimestral
		{
			if ($intervalo->m >= 3 && ($datetimei < $datetimef))
			{
				$data = '<span style="color:red;font-weight:bold">'.$fecha_corte.'</span>';
			}
			else
			{
				$data = $fecha_corte;
			}
		}
		if ($tipo_fecha_corte == "Semestral")//Semestral
		{
			if ($intervalo->m >= 6 && ($datetimei < $datetimef))
			{
				$data = '<span style="color:red;font-weight:bold">'.$fecha_corte.'</span>';
			}
			else
			{
				$data = $fecha_corte;
			}
		}
		if ($tipo_fecha_corte == "Anual")//Anual
		{
			if ($intervalo->y >= 1 && ($datetimei < $datetimef))
			{
				$data = '<span style="color:red;font-weight:bold">'.$fecha_corte.'</span>';
			}
			else
			{
				$data = $fecha_corte;
			}
		}
		return $data;
	}

    private function _tipo_poliza($param = FALSE)//funcion utilizada para verificar que tipo de poliza es
    {
    	// $param se envia desde la bd en string para que se convierta en un array
    	$array = array();

    	if ($param == FALSE)
    	{
    		if ($this->input->post("salud_form") != NULL)
	    	{
				$array[] = $this->input->post("salud_form", TRUE);
			}
			if ($this->input->post("ap_form") != NULL)
			{
				$array[] = $this->input->post("ap_form", TRUE);
			}
			if ($this->input->post("vida_form") != NULL)
			{
				$array[] = $this->input->post("vida_form", TRUE);
			}
			if ($this->input->post("gf_form") != NULL)
			{
				$array[] = $this->input->post("gf_form", TRUE);
			}
			if ($this->input->post("Basica_form") != NULL)
			{
				$array[] = $this->input->post("Basica_form", TRUE);
			}
			if ($this->input->post("Exceso_form") != NULL)
			{
				$array[] = $this->input->post("Exceso_form", TRUE);
			}

			if ($this->input->post("salud_form") == NULL && $this->input->post("ap_form") == NULL && $this->input->post("vida_form") == NULL && $this->input->post("gf_form") == NULL && $this->input->post("Basica_form") == NULL && $this->input->post("Exceso_form") == NULL)
			{
				return "";
			}

			$lastKey = array_keys($array);
			$key_final = end($lastKey);
			$var = null;
			foreach ($array as $key => $value)
			{
				if ($key_final == $key)
				{
					$var .= $value;
				}
				else
				{
					$var .= $value." ";
				}
			}
    	}
    	else
    	{
    		$var = explode(" ",$param); 
    	}
    	
		return $var;
    }

	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		//inicio del registro de datos del contratante	
		if($this->input->post('tipo_documento_contratante_form') == '')
		{
			$data['inputerror'][] = 'tipo_documento_contratante_form';
			$data['error_string'][] = 'Tipo de documento del contratante es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('nombre_razonsocial_form') == '')
		{
			$data['inputerror'][] = 'nombre_razonsocial_form';
			$data['error_string'][] = 'Nombres y apellidos/Razon social es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('ci_rif_form') == '')
		{
			$data['inputerror'][] = 'ci_rif_form';
			$data['error_string'][] = 'C.I./Pasaporte/Rif es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('tipo_persona_form') == '')
		{
			$data['inputerror'][] = 'tipo_persona_form';
			$data['error_string'][] = 'tipo de persona es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('fecha_nacimiento_constitucion_form') == '')
		{
			$data['inputerror'][] = 'fecha_nacimiento_constitucion_form';
			$data['error_string'][] = 'Fecha de nacimiento/constitucion es requerida';
			$data['status'] = FALSE;
		}
		if($this->input->post('lugar_nacimiento_constitucion_form') == '')
		{
			$data['inputerror'][] = 'lugar_nacimiento_constitucion_form';
			$data['error_string'][] = 'Lugar de nacimiento/constitucion es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('profesion_actividad_economica_form') == '')
		{
			$data['inputerror'][] = 'profesion_actividad_economica_form';
			$data['error_string'][] = 'Profesion o actividad economica es requerida';
			$data['status'] = FALSE;
		}
		if($this->input->post('ingreso_prome_anual_form') == '')
		{
			$data['inputerror'][] = 'ingreso_prome_anual_form';
			$data['error_string'][] = 'Ingreso promedio anual es requerido';
			$data['status'] = FALSE;
		}
		//fin del registro de datos del contratante
		//inicio del registro de datos de direccion del contrante
		if($this->input->post('pais_form') == '')
		{
			$data['inputerror'][] = 'pais_form';
			$data['error_string'][] = 'Pais es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('estado_form') == '')
		{
			$data['inputerror'][] = 'estado_form';
			$data['error_string'][] = 'Estado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('ciudad_form') == '')
		{
			$data['inputerror'][] = 'ciudad_form';
			$data['error_string'][] = 'Ciudad es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('municipio_form') == '')
		{
			$data['inputerror'][] = 'municipio_form';
			$data['error_string'][] = 'Municipio es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('parroquia_form') == '')
		{
			$data['inputerror'][] = 'parroquia_form';
			$data['error_string'][] = 'Parroquia es requerida';
			$data['status'] = FALSE;
		}
		if($this->input->post('urbanizacion_form') == '')
		{
			$data['inputerror'][] = 'urbanizacion_form';
			$data['error_string'][] = 'Urbanizacion es requerida';
			$data['status'] = FALSE;
		}
		if($this->input->post('calle_form') == '')
		{
			$data['inputerror'][] = 'calle_form';
			$data['error_string'][] = 'Calle es requerida';
			$data['status'] = FALSE;
		}
		if($this->input->post('centrocomercial_casa_form') == '')
		{
			$data['inputerror'][] = 'centrocomercial_casa_form';
			$data['error_string'][] = 'Local/ Casa es requerido';
			$data['status'] = FALSE;
		}
		//fin del registro de datos de direccion del contratante
        
        // inicio del registro de datos del asegurado
        if($this->input->post('tipo_documento_asegurado_form') == '')
		{
			$data['inputerror'][] = 'tipo_documento_asegurado_form';
			$data['error_string'][] = 'Tipo de documento del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('ci_pasaporte_asegurado_form') == '')
		{
			$data['inputerror'][] = 'ci_pasaporte_asegurado_form';
			$data['error_string'][] = 'C.I./Pasaporte del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('nacionalidad_asegurado_form') == '')
		{
			$data['inputerror'][] = 'nacionalidad_asegurado_form';
			$data['error_string'][] = 'Nacionalidad del asegurado es requerida';
			$data['status'] = FALSE;
		}
		if($this->input->post('nombres_asegurado_form') == '')
		{
			$data['inputerror'][] = 'nombres_asegurado_form';
			$data['error_string'][] = 'Nombre del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('apellidos_asegurado_form') == '')
		{
			$data['inputerror'][] = 'apellidos_asegurado_form';
			$data['error_string'][] = 'Apellido del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('fecha_nacimiento_asegurado_form') == '')
		{
			$data['inputerror'][] = 'fecha_nacimiento_asegurado_form';
			$data['error_string'][] = 'Fecha nacimiento del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('lugar_nacimiento_asegurado_form') == '')
		{
			$data['inputerror'][] = 'lugar_nacimiento_asegurado_form';
			$data['error_string'][] = 'Lugar nacimiento del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('sexo_asegurado_form') == '')
		{
			$data['inputerror'][] = 'sexo_asegurado_form';
			$data['error_string'][] = 'Sexo del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('estado_civil_asegurado_form') == '')
		{
			$data['inputerror'][] = 'estado_civil_asegurado_form';
			$data['error_string'][] = 'Estado civil del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('edad_asegurado_form') == '')
		{
			$data['inputerror'][] = 'edad_asegurado_form';
			$data['error_string'][] = 'Edad del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('profesion_asegurado_form') == '')
		{
			$data['inputerror'][] = 'profesion_asegurado_form';
			$data['error_string'][] = 'Profesion asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('ocupacion_asegurado_form') == '')
		{
			$data['inputerror'][] = 'ocupacion_asegurado_form';
			$data['error_string'][] = 'Ocupacion del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('ingreso_anual_asegurado_form') == '')
		{
			$data['inputerror'][] = 'ingreso_anual_asegurado_form';
			$data['error_string'][] = 'Ingreso anual del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('fecha_ingreso_empresa_asegurado_form') == '')
		{
			$data['inputerror'][] = 'fecha_ingreso_empresa_asegurado_form';
			$data['error_string'][] = 'Fecha de ingreso de la empresa del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('fecha_ingreso_poliza_asegurado_form') == '')
		{
			$data['inputerror'][] = 'fecha_ingreso_poliza_asegurado_form';
			$data['error_string'][] = 'Fecha de ingreso de la poliza del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('estatura_asegurado_form') == '')
		{
			$data['inputerror'][] = 'estatura_asegurado_form';
			$data['error_string'][] = 'Estatura del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('peso_asegurado_form') == '')
		{
			$data['inputerror'][] = 'peso_asegurado_form';
			$data['error_string'][] = 'Peso del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('zurdo_asegurado_form') == '')
		{
			$data['inputerror'][] = 'zurdo_asegurado_form';
			$data['error_string'][] = 'si es Zurdo el asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('deportes_pasatiempo_asegurado_form') == '')
		{
			$data['inputerror'][] = 'deportes_pasatiempo_asegurado_form';
			$data['error_string'][] = 'Deportes/ pasatiempos del asegurado es requerido';
			$data['status'] = FALSE;
		}
		// fin del registro de datos del asegurado 
        // inicio del registro de direccion del asegurado 
        if($this->input->post('pais_asegurado_form') == '')
		{
			$data['inputerror'][] = 'pais_asegurado_form';
			$data['error_string'][] = 'pais del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('estado_asegurado_form') == '')
		{
			$data['inputerror'][] = 'estado_asegurado_form';
			$data['error_string'][] = 'Estado del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('ciudad_asegurado_form') == '')
		{
			$data['inputerror'][] = 'ciudad_asegurado_form';
			$data['error_string'][] = 'Ciudad del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('municipio_asegurado_form') == '')
		{
			$data['inputerror'][] = 'municipio_asegurado_form';
			$data['error_string'][] = 'Municipio del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('parroquia_asegurado_form') == '')
		{
			$data['inputerror'][] = 'parroquia_asegurado_form';
			$data['error_string'][] = 'Parroquia del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('urbanizacion_asegurado_form') == '')
		{
			$data['inputerror'][] = 'urbanizacion_asegurado_form';
			$data['error_string'][] = 'Urbanizacion del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('calle_asegurado_form') == '')
		{
			$data['inputerror'][] = 'calle_asegurado_form';
			$data['error_string'][] = 'Calle del asegurado es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('centrocomercial_casa_asegurado_form') == '')
		{
			$data['inputerror'][] = 'centrocomercial_casa_asegurado_form';
			$data['error_string'][] = 'Local o casa del asegurado es requerido';
			$data['status'] = FALSE;
		}
		// fin del registro de direccion del asegurado

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	//variable mod usada para verificar si se agrega archivo o se modifica archivo
	private function _do_upload($mod = FALSE)
	{
		$config["remove_spaces"] 	= TRUE;
		$config['upload_path']      = 'files/docs/asegurados/';
        $config['allowed_types']    = 'pdf|gif|jpg|png|bmp';
        $config['max_size']         = 50000; //set max size allowed in Kilobyte
        $config['file_name']        = round(microtime(true) * 1000); //just milisecond timestamp fot unique name
        $config["max_filename"]		= 0;
        $config["multi"]			= 'all';
        $this->load->library('upload', $config);
        
        $files = array();//donde se guarda la info de los archivos que se estan subiendo
        if ($mod == FALSE) //si es FALSE indica que se esta agregando
        {
        	foreach($_FILES as $key => $value)
	        {
	            if( ! empty($value['name']))
	            {
	                if( ! $this->upload->do_upload($key))
	                {
						$data['status'][] = "file";
	                }
	                else
	                {
	                    // Build a file array from all uploaded files
	                    $files = $this->upload->data();
	                }
	            }
	        }

	        // There was errors, we have to delete the uploaded files
	        if(isset($data['status']))
	        {
	            foreach($files as $key => $file)
	            {
	                @unlink($file['full_path']);
	            }
	            return NULL;//retorno FALSE indica que no se subieron archivos al momento de agregar
	        }
	        else
	        {
				return $files;
		    }
        }
        if ($mod == TRUE)//si es TRUE indica que se esta modificando
        {
        	foreach($_FILES as $key => $value)
	        {
	            if( ! empty($value['name']))
	            {
	                if($this->upload->do_upload($key))
	                {
	                    // Build a file array from all uploaded files
	                    $files = $this->upload->data();
	                }
	            }
	        }
   
	        // There was errors, we have to delete the uploaded files
	        if(empty($files))
	        {
	            return NULL;//retorno FALSE indica que no se subieron archivos al momento de agregar
	        }
	        else
	        {
				return $files;
		    }
        }   
	}

    private function _createFolder()//esta funcion permite crear la carpeta si no existe en el servidor. es solo por verificacion
    {
        if(!is_dir("./files"))
        {
            mkdir("./files", 0777);
            mkdir("./files/pdfs", 0777);
        }
    }

    //esta función muestra el pdf en el navegador siempre que existan
    //tanto la carpeta como el archivo pdf
    private function _show($filename)
    {
        if(is_dir("./files/pdfs"))
        {
            $route = json_encode(base_url("files/pdfs/".$filename));
            if(file_exists("./files/pdfs/".$filename))
            {
                //usado para mostrar el pdf si es utilizado la libreria sin ajax
                //header('Content-type: application/pdf'); 
                //readfile($route);

                //usado para mostrar el pdf si es utulizado la libraria con ajax
                echo $route;
            }
        }
    }

	private function _render_page($view, $data=null, $returnhtml=TRUE)
	{
		$this->viewdata = (empty($data)) ? $this->data: $data;
		$view_html = $this->load->view('plantilla/head', $this->viewdata, $returnhtml);
		$view_html .= $this->load->view('plantilla/nav', $this->viewdata, $returnhtml);
		$view_html .= $this->load->view($view, $this->viewdata, $returnhtml);

		if ($returnhtml) return $this->output->set_output($view_html);//esto retornara HTML en el 3 argumento ya que se envia como TRUE
	}
}