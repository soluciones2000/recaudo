<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Polizas_model extends CI_Model {

	var $table = 'poliza_comprada';
	var $column_order = array('ci_rif','nombres','apellidos',"nombre_razonsocial",'fecha_corte',"tipo_poliza", null); //set column field database for datatable orderable
	var $column_search = array('ci_rif','nombres','apellidos',"nombre_razonsocial",'fecha_corte',"tipo_poliza"); //set column field database for datatable searchable 
	var $order = array('ci_rif' => 'asc'); // default order 
	var $id = "id";

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		//add custom filter here
		if($this->input->post('ci_rif_ajaxfiltro'))
		{
			$this->db->like('ci_rif', $this->input->post('ci_rif_ajaxfiltro', TRUE));
		}
		if($this->input->post('nombres_ajaxfiltro'))
		{
			$this->db->like('nombres', $this->input->post('nombres_ajaxfiltro', TRUE));
		}
		if($this->input->post('apellidos_ajaxfiltro'))
		{
			$this->db->like('apellidos', $this->input->post('apellidos_ajaxfiltro', TRUE));
		}
		if($this->input->post('nombre_razonsocial_ajaxfiltro'))
		{
			$this->db->like('nombre_razonsocial', $this->input->post('nombre_razonsocial_ajaxfiltro', TRUE));
		}
		if($this->input->post('fecha_corte_ajaxfiltro'))
		{
			$this->db->like('fecha_corte', $this->input->post('fecha_corte_ajaxfiltro', TRUE));
		}
		if($this->input->post('tipo_poliza_ajaxfiltro'))
		{
			$this->db->like('tipo_poliza', $this->input->post('tipo_poliza_ajaxfiltro', TRUE));
		}

		
		$this->db->from($this->table);
		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	public function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		{
			$this->db->limit($_POST['length'], $_POST['start']);
		}
		$this->db->order_by("id", "asc");
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	public function get_id_max_by_table($tabla)
	{
		$this->db->select_max("id");
		$query = $this->db->get($tabla);
		return $query->row();
	}

	public function get_list_by_id_and_table($id,$tabla)
	{
		$this->db->from($tabla);
		$this->db->where($id);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where($this->id,$id);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_by_id_and_table($id,$tabla)
	{
		$this->db->from($tabla);
		$this->db->where($id);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_list_filtro($filtro,$tabla)
	{
		$this->db->from($tabla);
		$this->db->order_by($filtro,'asc');
		$query = $this->db->get();
		$result = $query->result();
		
		$array = array();
		foreach ($result as $row) 
		{
			$array[$row->id] = $row->$filtro;
		}
		return $array;
	}

	public function get_list_by_id_preguntas_asegurado($id)
	{
		$this->db->from("preguntas_asegurado");
		$this->db->where($id);
		$this->db->order_by("id", "asc");
		$query = $this->db->get();
		$result = $query->result();

		$array = array();
		foreach ($result as $row) 
		{
			$array[$row->id_pregunta] = $row->id;
		}
		return $array;
	}

	public function save($data)
	{
		$this->db->insert("contratante",$data["contratante"]);// agrega los datos a la tabla contratante

		$data["asegurado"]["id_contratante"] = $this->db->insert_id(); // obtiene el id del contratante que se genera al momento de guardar su informacion
		$this->db->insert("asegurado",$data["asegurado"]); //agrega los datos a la tabla asegurado
		
		//informacion que se guarda en la tabla que se muestra al usuario
		$data_poliza["id_asegurado"] = $this->db->insert_id();
		$data_poliza["id_contratante"] = $data["asegurado"]["id_contratante"];
		$data_poliza["nombre_razonsocial"] = $data["contratante"]["nombre_razonsocial"];
		$data_poliza["ci_rif"] = $data["asegurado"]["ci_pasaporte_asegurado"];
		$data_poliza["nombres"] = $data["asegurado"]["nombres_asegurado"];
		$data_poliza["apellidos"] = $data["asegurado"]["apellidos_asegurado"];
		$data_poliza["fecha_corte"] = $data["cobertura"]["fecha_corte"];
		$data_poliza["tipo_fecha_corte"] = $data["cobertura"]["tipo_fecha_corte"];
		$data_poliza["fecha_contrato"] = date("d-m-Y H:i:s");
		$data_poliza["tipo_poliza"] = $data["tipo_poliza"];
		$data_poliza["num_poliza"] = $data["num_poliza"];
		
		$this->db->insert($this->table, $data_poliza);// agrega los datos a la tabla principal
		$data_poliza["insert_id"] = $this->db->insert_id();
		
		//ingreso de datos de la cobertura de la poliza
		$data["cobertura"]["id_asegurado"] = $data_poliza["id_asegurado"];
		$this->db->insert("cobertura_solicitada",$data["cobertura"]);

		//inicio del registro de datos en la tabla grupo_asegurado
		if ($data["grupo"]["nombres_apellidos_grupo"] != NULL)
		{
			foreach ($data["grupo"]["nombres_apellidos_grupo"] as $key_grupo => $value_grupo)
			{
				$info_grupo = array(
					"id_asegurado" => $data_poliza["id_asegurado"],
					"nombres_apellidos_grupo" => $value_grupo,
					"parentesco_grupo" => $data["grupo"]["parentesco_grupo"][$key_grupo],
					"ci_pasaporte_grupo" => $data["grupo"]["ci_pasaporte_grupo"][$key_grupo],
					"fecha_nacimiento_grupo" => $data["grupo"]["fecha_nacimiento_grupo"][$key_grupo],
					"edad_grupo" => $data["grupo"]["edad_grupo"][$key_grupo],
					"sexo_grupo" => $data["grupo"]["sexo_grupo"][$key_grupo],
					"estado_civil_grupo" => $data["grupo"]["estado_civil_grupo"][$key_grupo],
					"peso_grupo" => $data["grupo"]["peso_grupo"][$key_grupo],
					"estatura_grupo" => $data["grupo"]["estatura_grupo"][$key_grupo],
					"zurdo_grupo" => $data["grupo"]["zurdo_grupo"][$key_grupo],
					"profesion_grupo" => $data["grupo"]["profesion_grupo"][$key_grupo],
					"ocupacion_grupo" => $data["grupo"]["ocupacion_grupo"][$key_grupo],
					"pasatiempos_grupo" => $data["grupo"]["pasatiempos_grupo"][$key_grupo],
					"deportes_grupo" => $data["grupo"]["deportes_grupo"][$key_grupo],
				);

				$this->db->insert("grupo_asegurado", $info_grupo);
			}
		}
		//fin del registro de datos en la tabla grupo_asegurado

		//inicio del registro de datos en la tabla beneficiarios
		if ($data["beneficiario"]["ci_pasaporte_beneficiarios"] != NULL)
		{
			foreach ($data["beneficiario"]["ci_pasaporte_beneficiarios"] as $key_beneficiario => $value_beneficiario)
			{
				$info_beneficiario = array(
					"id_asegurado" => $data_poliza["id_asegurado"],
					"tipo_doc_beneficiario" => $data["beneficiario"]["tipo_doc_beneficiario"][$key_beneficiario],
					"ci_pasaporte_beneficiarios" => $value_beneficiario,
					"nombres_apellidos_beneficiarios" => $data["beneficiario"]["nombres_apellidos_beneficiarios"][$key_beneficiario],
					"parentesco_beneficiarios" => $data["beneficiario"]["parentesco_beneficiarios"][$key_beneficiario],
					"distribucion_beneficiarios" => $data["beneficiario"]["distribucion_beneficiarios"][$key_beneficiario],
				);
				
				$this->db->insert("beneficiarios", $info_beneficiario);
			}
		}
		//fin del registro de datos en la tabla beneficiarios

		//inicio del registro de datos en la tabla otros_seguros_asegurado
		if ($data["otros_seguros"]["nombre_empresa"] != NULL)
		{
			foreach ($data["otros_seguros"]["nombre_empresa"] as $key_otros_seguros => $value_otros_seguros)
			{
				$info_otros_seguros = array(
					"id_asegurado" => $data_poliza["id_asegurado"],
					"nombre_empresa" => $value_otros_seguros,
					"numero_poliza" => $data["otros_seguros"]["numero_poliza"][$key_otros_seguros],
					"monto" => $data["otros_seguros"]["monto"][$key_otros_seguros],
					"estado_poliza" => $data["otros_seguros"]["estado_poliza"][$key_otros_seguros],
				);

				$this->db->insert("otros_seguros_asegurado", $info_otros_seguros);
			}
		}
		//fin del registro de datos en la tabla otros_seguros_asegurado

		//inicio del registro de las preguntas del asegurado
		if ($data["preguntas"]["respuesta_pregunta"] != NULL)
		{
			foreach ($data["preguntas"]["respuesta_pregunta"] as $key_preguntas => $value_preguntas)
			{
				if ($value_preguntas == "si")
				{
					$info_preguntas = array(
						"id_asegurado" => $data_poliza["id_asegurado"],
						"id_pregunta" => $key_preguntas,
						"respuesta_pregunta" => $data["preguntas"]["respuesta_pregunta"][$key_preguntas],
					);

					$this->db->insert("preguntas_asegurado", $info_preguntas);
				}
			}
		}
		//fin del registro de las preguntas del asegurado

		//inicio del registro en la tabla enfermedad_preguntas de las personas q se aseguran en grupo
		if ($data["enfermedad"]["id_pregunta"] != NULL)
		{
			foreach ($data["enfermedad"]["id_pregunta"] as $key_enfermedad => $value_enfermedad)
			{				
				$info_enfermedad = array(
					"id_asegurado" => $data_poliza["id_asegurado"],
					"id_pregunta" => $value_enfermedad,
					"id_grupo" => $data["enfermedad"]["id_grupo"][$key_enfermedad],
					"diagnostico_intervencion_enfermedad" => $data["enfermedad"]["diagnostico_intervencion_enfermedad"][$key_enfermedad],
					"fecha_enfermedad" => $data["enfermedad"]["fecha_enfermedad"][$key_enfermedad],
					"nombres_apellidos_medico_enfermedad" => $data["enfermedad"]["nombres_apellidos_medico_enfermedad"][$key_enfermedad],
					"ubicacion_medico_enfermedad" => $data["enfermedad"]["ubicacion_medico_enfermedad"][$key_enfermedad],
					"condicion_actual_enfermedad" => $data["enfermedad"]["condicion_actual_enfermedad"][$key_enfermedad],
				);

				$this->db->insert("enfermedad_preguntas",$info_enfermedad);
			}
		}
		//fin del registro en la tabla enfermedad_preguntas de las personas q se aseguran en grupo

		//inicio del registro en la tabla documentos_enfermedad de las personas q se aseguran en grupo
		if (isset($data["documento"])) 
		{
			if ($data["documento"]["descripcion_archivo"] != NULL)
			{
				foreach ($data["documento"]["descripcion_archivo"] as $key_doc => $value_doc)
				{				
					$info_documento = array(
						"id_enfermedad" => $data["documento"]["id_enfermedad"][$key_doc],
						"descripcion_archivo" => $value_doc,
						"archivo_cliente" => $data["documento"]["archivo_cliente"][$key_doc],
						"archivo" => $data["documento"]["archivo"][$key_doc],
						"destino" => $data["documento"]["destino"][$key_doc],
						"tipo" => $data["documento"]["tipo"][$key_doc],
						"tamanio" => $data["documento"]["tamanio"][$key_doc],
					);

					$this->db->insert("documentos_enfermedad",$info_documento);
				}
			}
		}
		//fin del registro en la tabla documentos_enfermedad de las personas q se aseguran en grupo

		//inicio del registro de datos en la tabla padecimiento
		if ($data["padecimiento"]["nombres_apellidos_padecimiento"] != NULL)
		{
			foreach ($data["padecimiento"]["nombres_apellidos_padecimiento"] as $key_padecimiento => $value_padecimiento)
			{
				$info_padecimiento = array(
					"id_asegurado_padecimiento" => $data_poliza["id_asegurado"],
					"nombres_apellidos_padecimiento" => $value_padecimiento,
					"parentesco_padecimiento" => $data["padecimiento"]["parentesco_padecimiento"][$key_padecimiento],
					"edad_padecimiento" => $data["padecimiento"]["edad_padecimiento"][$key_padecimiento],
					"fallecido_padecimiento" => $data["padecimiento"]["fallecido_padecimiento"][$key_padecimiento],
					"causa_padecimiento" => $data["padecimiento"]["causa_padecimiento"][$key_padecimiento],
				);

				$this->db->insert("padecimiento", $info_padecimiento);
			}
		}
		//fin del registro de datos en la tabla padecimiento
		return array("status" => TRUE, "insert_id" => $data_poliza["insert_id"]);
	}

	public function update($data,$where)
	{	
		$this->db->update("contratante",$data["contratante"], $where["contratante"]);// modifica los datos a la tabla contratante
		
		$this->db->update("asegurado",$data["asegurado"], $where["asegurado"]); //modifica los datos a la tabla asegurado

		//modificacion de los datos de la cobertura solicitada
		$this->db->update("cobertura_solicitada",$data["cobertura"],$where["cobertura"]);
		
		//informacion que se guarda en la tabla que se muestra al usuario
		$data_poliza["nombre_razonsocial"] = $data["contratante"]["nombre_razonsocial"];
		$data_poliza["ci_rif"] = $data["asegurado"]["ci_pasaporte_asegurado"];
		$data_poliza["nombres"] = $data["asegurado"]["nombres_asegurado"];
		$data_poliza["apellidos"] = $data["asegurado"]["apellidos_asegurado"];
		$data_poliza["fecha_corte"] = $data["cobertura"]["fecha_corte"];
		$data_poliza["tipo_fecha_corte"] = $data["cobertura"]["tipo_fecha_corte"];
		$data_poliza["tipo_poliza"] = $data["tipo_poliza"];
		$data_poliza["num_poliza"] = $data["num_poliza"];
		
		$this->db->update($this->table, $data_poliza, array("id_asegurado"=> $where["asegurado"]["id"],"id_contratante" => $data["asegurado"]["id_contratante"]));// agrega los datos a la tabla principal
		
		//inicio del registro de datos en la tabla grupo_asegurado
		if ($data["grupo"]["nombres_apellidos_grupo"] != NULL)
		{
			foreach ($data["grupo"]["nombres_apellidos_grupo"] as $key_grupo => $value_grupo)
			{
				$info_grupo = array(
					"id_asegurado" => $data["grupo"]["id_asegurado"],
					"nombres_apellidos_grupo" => $value_grupo,
					"parentesco_grupo" => $data["grupo"]["parentesco_grupo"][$key_grupo],
					"ci_pasaporte_grupo" => $data["grupo"]["ci_pasaporte_grupo"][$key_grupo],
					"fecha_nacimiento_grupo" => $data["grupo"]["fecha_nacimiento_grupo"][$key_grupo],
					"edad_grupo" => $data["grupo"]["edad_grupo"][$key_grupo],
					"sexo_grupo" => $data["grupo"]["sexo_grupo"][$key_grupo],
					"estado_civil_grupo" => $data["grupo"]["estado_civil_grupo"][$key_grupo],
					"peso_grupo" => $data["grupo"]["peso_grupo"][$key_grupo],
					"estatura_grupo" => $data["grupo"]["estatura_grupo"][$key_grupo],
					"zurdo_grupo" => $data["grupo"]["zurdo_grupo"][$key_grupo],
					"profesion_grupo" => $data["grupo"]["profesion_grupo"][$key_grupo],
					"ocupacion_grupo" => $data["grupo"]["ocupacion_grupo"][$key_grupo],
					"pasatiempos_grupo" => $data["grupo"]["pasatiempos_grupo"][$key_grupo],
					"deportes_grupo" => $data["grupo"]["deportes_grupo"][$key_grupo]
				);

				$this->db->from("grupo_asegurado");
				$this->db->where("id", $where["grupo"]["id"][$key_grupo]);
				$query = $this->db->get();
				if ($query->num_rows() > 0)
				{
					$this->db->update('grupo_asegurado', $info_grupo, array("id" => $where["grupo"]["id"][$key_grupo]));
				}
				else
				{
					$this->db->insert("grupo_asegurado",$info_grupo);
				}
				$query = NULL;
			}
		}
		//fin del registro de datos en la tabla grupo_asegurado
		
		//inicio del registro de datos en la tabla beneficiarios
		if ($data["beneficiario"]["ci_pasaporte_beneficiarios"] != NULL)
		{
			foreach ($data["beneficiario"]["ci_pasaporte_beneficiarios"] as $key_beneficiario => $value_beneficiario)
			{
				$info_beneficiario = array(
					"id_asegurado" => $data["beneficiario"]["id_asegurado"],
					"tipo_doc_beneficiario" => $data["beneficiario"]["tipo_doc_beneficiario"][$key_beneficiario],
					"ci_pasaporte_beneficiarios" => $value_beneficiario,
					"nombres_apellidos_beneficiarios" => $data["beneficiario"]["nombres_apellidos_beneficiarios"][$key_beneficiario],
					"parentesco_beneficiarios" => $data["beneficiario"]["parentesco_beneficiarios"][$key_beneficiario],
					"distribucion_beneficiarios" => $data["beneficiario"]["distribucion_beneficiarios"][$key_beneficiario],
				);
				
				$this->db->from("beneficiarios");
				$this->db->where("id", $where["beneficiario"]["id"][$key_beneficiario]);
				$query = $this->db->get();
				if ($query->num_rows() > 0)
				{
					$this->db->update('beneficiarios', $info_beneficiario, array("id" => $where["beneficiario"]["id"][$key_beneficiario]));
				}
				else
				{
					$this->db->insert("beneficiarios",$info_beneficiario);
				}
				$query = NULL;
			}
		}
		//fin del registro de datos en la tabla beneficiarios

		//inicio del registro de datos en la tabla otros_seguros_asegurado
		if ($data["otros_seguros"]["nombre_empresa"] != NULL)
		{
			foreach ($data["otros_seguros"]["nombre_empresa"] as $key_otros_seguros => $value_otros_seguros)
			{
				$info_otros_seguros = array(
					"id_asegurado" => $data["otros_seguros"]["id_asegurado"],
					"nombre_empresa" => $value_otros_seguros,
					"numero_poliza" => $data["otros_seguros"]["numero_poliza"][$key_otros_seguros],
					"monto" => $data["otros_seguros"]["monto"][$key_otros_seguros],
					"estado_poliza" => $data["otros_seguros"]["estado_poliza"][$key_otros_seguros],
				);
				
				$this->db->from("otros_seguros_asegurado");
				$this->db->where("id", $where["otros_seguros"]["id"][$key_otros_seguros]);
				$query = $this->db->get();
				if ($query->num_rows() > 0)
				{
					$this->db->update('otros_seguros_asegurado', $info_otros_seguros, array("id" => $where["otros_seguros"]["id"][$key_otros_seguros]));
				}
				else
				{
					$this->db->insert("beneficiarios",$info_otros_seguros);
				}
				$query = NULL;
			}
		}
		//fin del registro de datos en la tabla otros_seguros_asegurado
		
		//inicio del registro de las preguntas del asegurad
		if ($data["preguntas"]["respuesta_pregunta"] != NULL)
		{
			foreach ($data["preguntas"]["respuesta_pregunta"] as $key_preguntas => $value_preguntas)//se busca el valor de $key_preguntas debido a que es el id del la pregunta
			{
				if ($value_preguntas == "no")
				{
					$this->db->from("preguntas_asegurado");
					$this->db->where(array("id_asegurado"=> $data["preguntas"]["id_asegurado"],"id_pregunta" => $key_preguntas));
					$query = $this->db->get();
					if ($query->num_rows() > 0)
					{
						$this->db->where(array("id_asegurado"=> $data["preguntas"]["id_asegurado"],"id_pregunta" => $key_preguntas));
						$this->db->delete("preguntas_asegurado");
					}
				}
				elseif ($value_preguntas == "si")
				{
					$this->db->from("preguntas_asegurado");
					$this->db->where(array("id_asegurado"=> $data["preguntas"]["id_asegurado"],"id_pregunta" => $key_preguntas));
					$query = $this->db->get();
					if ($query->num_rows() == 0)
					{
						$info_preguntas = array(
							"id_asegurado" => $data["preguntas"]["id_asegurado"],
							"id_pregunta" => $key_preguntas,
							"respuesta_pregunta" => $value_preguntas,
						);
						$this->db->insert("preguntas_asegurado",$info_preguntas);
					}
				}
			}
		}
		//fin del registro de las preguntas del asegurado
		
		//inicio del registro de datos en la tabla enfermedad_preguntas
		if ($data["enfermedad"]["id_pregunta"] != NULL)
		{
			foreach ($data["enfermedad"]["id_pregunta"] as $key_enfermedad => $value_enfermedad)
			{		
				$info_enfermedad = array(
					"id_asegurado" => $data["enfermedad"]["id_asegurado"],
					"id_pregunta" => $value_enfermedad,
					"id_grupo" => $data["enfermedad"]["id_grupo"][$key_enfermedad],
					"diagnostico_intervencion_enfermedad" => $data["enfermedad"]["diagnostico_intervencion_enfermedad"][$key_enfermedad],
					"fecha_enfermedad" => $data["enfermedad"]["fecha_enfermedad"][$key_enfermedad],
					"nombres_apellidos_medico_enfermedad" => $data["enfermedad"]["nombres_apellidos_medico_enfermedad"][$key_enfermedad],
					"ubicacion_medico_enfermedad" => $data["enfermedad"]["ubicacion_medico_enfermedad"][$key_enfermedad],
					"condicion_actual_enfermedad" => $data["enfermedad"]["condicion_actual_enfermedad"][$key_enfermedad],
				);

				$this->db->from("enfermedad_preguntas");
				$this->db->where("id", $where["enfermedad"]["id"][$key_enfermedad]);
				$query = $this->db->get();
				if ($query->num_rows() > 0)
				{
					$this->db->update('enfermedad_preguntas', $info_enfermedad, array("id" => $where["enfermedad"]["id"][$key_enfermedad]));
				}
				else
				{
					$this->db->insert("enfermedad_preguntas",$info_enfermedad);
				}
				$query = NULL;
			}
		}		
		//fin del registro de datos en la tabla enfermedad_preguntas

		//inicio del registro en la tabla documentos_enfermedad de las personas q se aseguran en grupo
		if (isset($data["documento"]))
		{
			if ($data["documento"]["descripcion_archivo"] != NULL)
			{
				foreach ($data["documento"]["descripcion_archivo"] as $key_doc => $value_doc)
				{
					if (!isset($data["documento"]["archivo_cliente"]))
					{
						$info_documento = array(
							"id_enfermedad" => $data["documento"]["id_enfermedad"][$key_doc],
							"descripcion_archivo" => $value_doc,
						);
					}
					else
					{
						$info_documento = array(
							"id_enfermedad" => $data["documento"]["id_enfermedad"][$key_doc],
							"descripcion_archivo" => $value_doc,
							"archivo_cliente" => $data["documento"]["archivo_cliente"][$key_doc],
							"archivo" => $data["documento"]["archivo"][$key_doc],
							"destino" => $data["documento"]["destino"][$key_doc],
							"tipo" => $data["documento"]["tipo"][$key_doc],
							"tamanio" => $data["documento"]["tamanio"][$key_doc],
						);
					}
					
					$this->db->from("documentos_enfermedad");
					$this->db->where("id", $where["documento"]["id"][$key_doc]);
					$query = $this->db->get();
					if ($query->num_rows() > 0)
					{
						$this->db->update('documentos_enfermedad', $info_documento, array("id" => $where["documento"]["id"][$key_doc]));
					}
					else
					{
						$this->db->insert("documentos_enfermedad",$info_documento);
					}
					$query = NULL;
				}
			}
		}
		//fin del registro en la tabla documentos_enfermedad de las personas q se aseguran en grupo

		//inicio del registro de datos en la tabla padecimiento
		if ($data["padecimiento"]["nombres_apellidos_padecimiento"] != NULL)
		{
			foreach ($data["padecimiento"]["nombres_apellidos_padecimiento"] as $key_padecimiento => $value_padecimiento)
			{
				$info_padecimiento = array(
					"id_asegurado_padecimiento" => $data["padecimiento"]["id_asegurado"],
					"nombres_apellidos_padecimiento" => $value_padecimiento,
					"parentesco_padecimiento" => $data["padecimiento"]["parentesco_padecimiento"][$key_padecimiento],
					"edad_padecimiento" => $data["padecimiento"]["edad_padecimiento"][$key_padecimiento],
					"fallecido_padecimiento" => $data["padecimiento"]["fallecido_padecimiento"][$key_padecimiento],
					"causa_padecimiento" => $data["padecimiento"]["causa_padecimiento"][$key_padecimiento],
				);

				$this->db->from("padecimiento");
				$this->db->where("id", $where["padecimiento"]["id"][$key_padecimiento]);
				$query = $this->db->get();
				if ($query->num_rows() > 0)
				{
					$this->db->update('padecimiento', $info_padecimiento, array("id" => $where["padecimiento"]["id"][$key_padecimiento]));
				}
				else
				{
					$this->db->insert("padecimiento",$key_padecimiento);
				}
				$query = NULL;
			}
		}	
		//fin del registro de datos en la tabla padecimiento
		return array("status" => TRUE);
	}
	
	public function update_table($tabla,$data,$where)
	{
		$this->db->update($tabla,$data,$where);// modifica los datos a la tabla contratante
		return array("status" => TRUE);
	}

	public function delete_by_id($id)
	{
		$this->db->where($this->id, $id);
		$this->db->delete($this->table);
	}

	public function delete_by_id_and_table($id,$tabla)
	{
		$this->db->where($this->id, $id);
		$this->db->delete($tabla);
	}
}