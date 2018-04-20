<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('usuarios_model', "model");
		$this->load->model('pdf_model'); // modelo para imprimir en pdf
		$this->load->library('html2pdf'); // libreria para imprimir en pdf
		$this->load->helper('array');
		
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
			$data["login"] = $this->session->userdata("nombres");//muestra el nombre del usuario
			$data["nav"] = $this->seguridad->get_user_groups();//muestra el menu de los usuarios cuando estan conectados
			$data['titulo'] = "Usuarios";//titulo de la pagina
			$data['modulo_filtro'] = form_dropdown('',$this->_array_controllers(),'','id="modulo_filtro" class="form-control"');/*envia el array de controladores para mostrar a los usuarios*/
			$data["nivel"] = $nivel = array("1", "2", "3"); //nivel de seguridad usado al momento de agregar un modulo 
			$data["modulo_form"] = $this->_array_controllers("controllers");

			$this->_render_page("usuarios_view", $data);
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
				$this->data['users'] = $this->model->get_datatables();

				foreach ($this->data['users'] as $rows) {
					$no++;
					$row = array();
					$row[] = $no;
					$row[] = $rows->nombres;
					$row[] = $rows->apellidos;
					$row[] = $rows->correo;
					$row[] = $rows->telefono;
					$row[] = $rows->ultimo_inicio;
					$row[] = $rows->modulos;
					$row[] = $rows->activo;
					if ($rows->activo == 1)
					{
						$row[] = '
						<div class="dropdown">
						  	<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
								Acciones
								<span class="caret"></span>
						  	</button>
						  	<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
								<li><a class="btn btn-sm btn-primary btn-block" style="color: black;" href="javascript:void(0)" title="Editar" onclick="editar_ajax('."'".$rows->id."'".')"><i class="glyphicon glyphicon-pencil"></i> Editar</a></li>
								<li><a class="btn btn-sm btn-info btn-block" style="color: black;" href="javascript:void(0)" title="Editar" onclick="detalles_ajax('."'".$rows->id."'".')"><i class="glyphicon glyphicon-list-alt"></i> Detalles</a></li>
								<li><a class="btn btn-sm btn-primary btn-block" style="color: black;" href="javascript:void(0)" title="Cambiar clave" onclick="cambiar_clave_ajax('."'".$rows->id."'".')"><i class="glyphicon glyphicon-pencil"></i> Cambiar clave</a></li>
								<li><a class="btn btn-sm btn-warning btn-block" style="color: black;" href="javascript:void(0)" title="" onclick="Estado_ajax('."'".$rows->id."'".')"><i class="glyphicon glyphicon-trash"></i> Desactivar</a></li>
								<li><a class="btn btn-sm btn-danger btn-block" style="color: black;" href="javascript:void(0)" title="Borrar" onclick="borrar_ajax('."'".$rows->id."'".')"><i class="glyphicon glyphicon-trash"></i> Borrar</a></li>
						  	</ul>
						</div>
						';
					}
					else
					{
						$row[] = '
						<div class="dropdown">
						  	<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
								Acciones
								<span class="caret"></span>
						  	</button>
						  	<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">
								<li><a class="btn btn-sm btn-primary btn-block" href="javascript:void(0)" title="Editar" onclick="editar_ajax('."'".$rows->id."'".')"><i class="glyphicon glyphicon-pencil"></i> Editar</a></li>
								<li><a class="btn btn-sm btn-info btn-block" href="javascript:void(0)" title="Editar" onclick="detalles_ajax('."'".$rows->id."'".')"><i class="glyphicon glyphicon-list-alt"></i> Detalles</a></li>
								<li><a class="btn btn-sm btn-primary btn-block" style="color: black;" href="javascript:void(0)" title="Cambiar clave" onclick="cambiar_clave_ajax('."'".$rows->id."'".')"><i class="glyphicon glyphicon-pencil"></i> Cambiar clave</a></li>
								<li><a class="btn btn-sm btn-danger btn-block" href="javascript:void(0)" title="" onclick="Estado_ajax('."'".$rows->id."'".')"><i class="glyphicon glyphicon-trash"></i> Activar</a></li>
								<li><a class="btn btn-sm btn-danger btn-block" href="javascript:void(0)" title="Borrar" onclick="borrar_ajax('."'".$rows->id."'".')"><i class="glyphicon glyphicon-trash"></i> Borrar</a></li>
						  	</ul>
						</div>
						';
					}
					
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

	public function ajax_edit($id = FALSE)
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1 || $this->sesion == 2 || $this->sesion == 3)
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

	public function ajax_add()
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1 || $this->sesion == 2)
			{
				$this->_validate(TRUE);

				$string_array = $this->_val_controllers(); // funcion que compara los controladores que se enviaron desde la vista y la carpeta controllers
				
				$pass = password_hash($this->input->post('contrasena_form', TRUE), PASSWORD_DEFAULT);//realiza un hash a la clave del usuario para q no se pueda ver desde la bd

				$data = array(
						'nombres' => $this->input->post('nombres_form', TRUE),
						'apellidos' => $this->input->post('apellidos_form', TRUE),
						'correo' => $this->input->post('email_form', TRUE),
						'clave' => $pass,
						'telefono' => $this->input->post('telefono_form', TRUE),
						'create_on' => date("d-m-Y H:i:s"),
						'activo' => 1,
						"modulos" => $string_array,
					);
				
				$insert = $this->model->save($data);
				echo json_encode($insert);
				//registro de acciones
				$this->seguridad->registrar_accion("Se agrega el usuario con el id ".$insert["insert_id"]);
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

				$string_array = $this->_val_controllers(); // funcion que compara los controladores que se enviaron desde la vista y la carpeta controllers

				$data = array(
						'nombres' => $this->input->post('nombres_form', TRUE),
						'apellidos' => $this->input->post('apellidos_form', TRUE),
						'correo' => $this->input->post('email_form', TRUE),
						'telefono' => $this->input->post('telefono_form', TRUE),
						"modulos" => $string_array,
					);
				//registro de acciones
				$this->seguridad->registrar_accion("Se modifica el usuario con el id ".$this->input->post('id'));
				$update = $this->model->update(array('id' => $this->input->post('id', TRUE)), $data);
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

	public function ajax_change_password()
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1)
			{
				if ($this->input->post('clave_nueva_form', TRUE) == $this->input->post('repetir_clave_form', TRUE))
				{
					$pass = password_hash($this->input->post('repetir_clave_form'), PASSWORD_DEFAULT);//realiza un hash a la clave del usuario para q no se pueda ver desde la bd
					$data = array(
							'clave' => $pass,
						);

					//registro de acciones
					$this->seguridad->registrar_accion("Se modifica la clave del usuario con el id ".$this->input->post('id', TRUE));
					$update = $this->model->update(array('id' => $this->input->post('id', TRUE)), $data);
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
				$this->seguridad->registrar_accion("Se Borro el usuario con el id ".$id);
				$this->model->delete_by_id($id);
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

	public function ajax_status_change($id = FALSE)
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1)
			{	
				$row = $this->model->get_by_id($id);
				if ($row->active === 0)
				{
					$this->model->status_change(array('id' => $id), array('active' => 1));
					//registro de acciones
					$this->seguridad->registrar_accion("Se activo de la cuenta del usuario con el id ".$id);
				}
				elseif ($row->active === 1)
				{
					$this->model->status_change(array('id' => $id), array('active' => 0));
					//registro de acciones
					$this->seguridad->registrar_accion("Se desactivo de la cuenta del usuario con el id ".$id);
				}
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
				$id = $this->input->post("id", TRUE);
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
				$this->seguridad->registrar_accion("Se realizo un reporte individual del usuario con el id: $id");

				$row_empresa = $this->pdf_model->get_empresa($this->input->post("id_empresa", TRUE));
				
		        $data = array(
		        	"logo" => "./files/img/logos_empresas/".$row_empresa->archivo,
		            'title' => "USUARIOS",
		            "header" => utf8_encode($row_empresa->nombre_empresa),
		            "rif" => "RIF.: ".$row_empresa->razon_social,
		            "contador" => utf8_encode("Nº: $row_empresa->contador"),
		            'datos' => $this->pdf_model->get_datoespecifico("usuarios","id",$id),
		        );

		        //hacemos que coja la vista como datos a imprimir
		        //importante utf8_decode para mostrar bien las tildes, ñ y demás
		        $this->html2pdf->html(utf8_decode($this->load->view('reportes_pdf/usuarios_report_individual_pdf', $data, true)));

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
				$this->seguridad->registrar_accion("Se realizo un reporte de los usuarios");

		        $row_empresa = $this->pdf_model->get_empresa($this->input->post("id_empresa", TRUE));
				
		        $data = array(
		        	"logo" => "./files/img/logos_empresas/".$row_empresa->archivo,
		            'title' => "USUARIOS",
		            "header" => utf8_encode($row_empresa->nombre_empresa),
		            "rif" => "RIF.: ".$row_empresa->razon_social,
		            "contador" => utf8_encode("Nº: $row_empresa->contador"),
		            'datos' => $this->pdf_model->get_reporte_usuarios(),
		        );

		        //hacemos que coja la vista como datos a imprimir
		        //importante utf8_decode para mostrar bien las tildes, ñ y demás
		        $this->html2pdf->html(utf8_decode($this->load->view('reportes_pdf/usuarios_report_pdf', $data, true)));

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
	
	public function csrf()
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1 || $this->sesion == 2 || $this->sesion == 3)
			{	
				$data = array(
					"csrf_name" => $this->security->get_csrf_token_name(),
					"csrf_hash" => $this->security->get_csrf_hash(),
				);

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

    private function _val_controllers()//convierte toda la info de los controladores que se muestran en la vista en un string
    {
   		$controller_list = $this->_array_controllers("controllers"); //obtiene los controladores
		$nivel_form_list = $this->_array_controllers("nivel");
		$data_post = $this->input->post(NULL); // obtiene todos los post que son enviados
		
		$array_with_null_controllers = elements($controller_list, $data_post); //funcion de codeigniter que obtiene valores de un array de campos especificos en este caso son los controladores
		$array_with_null_nivel = elements($nivel_form_list, $data_post);
		
		$array_controllers = array();
		foreach($array_with_null_controllers as $row => $value) // elimina cada valor nulo que venga del array
		{
			if($value != NULL)
			{
				$array_controllers[$row] = $value;
			}
		}

		$array_nivel = array();
		foreach($array_with_null_nivel as $row => $value) // elimina cada valor vacio que venga del array
		{
			if($value != "") // se evalua asi xq son enviados los campos como string en vez de vacio
			{
				$array_nivel[$row] = $value;
			}
		}

		$array_nivel_controllers = array();
		foreach ($array_controllers as $key_controllers => $value_controllers)
		{
			foreach ($array_nivel as $key_nivel => $value_nivel)
			{
				$cleankey_nivel = str_replace("_nivel_form", "", $key_nivel);
				if ($key_controllers == $cleankey_nivel)
				{
					$array_nivel_controllers[] = $value_controllers."|".$value_nivel;
				}
			}
		}

		return implode(",", $array_nivel_controllers); //convierte el array en string
    }

	private function _array_controllers($param = FALSE)//muestra un array con el contenido de la carpeta controladores para usarlo en listas o para agregarlos a la base de datos
    {
    	$controllers_array = directory_map(APPPATH.'controllers/', FALSE, FALSE); //optiene el listado de controladores por una funcion de codeigniter
		//funciones para limpiar y enlistar los controladores para los usuarios
		$deletedata = array("index.html", "Login.php","inicio.php","Tipo_transaccion.php"); //enlista los archivos que no se deben mostrar
		$array = array_diff($controllers_array, $deletedata); // elimina los archivos que no se deben mostrar
    	
    	if($param == "controllers")// se utiliza al momento de comparar la existencia de los modulos que estaran en el grupo al momento de registrarse en la BD
    	{
			$controllers_opt = array();
			foreach ($array as $controllers_fetch) {
				$controllers_opt[$controllers_fetch] = $controllers_fetch;
			}

			$cotrollers_string = implode(",", $controllers_opt); //convierte en cadena de texto el array para quitarle la extension
			$controllers_pre_array = str_replace(".php", "", $cotrollers_string); //quita la extension del texto
			return $controllers = explode(",", $controllers_pre_array); //convierte en array otra vez el texto sin la extension

    	}
    	elseif ($param == "nivel")// se usa para obtener y comparar los controladores que ingresan del form con los niveles de seguridad
    	{
			$controllers_opt = array();
			foreach ($array as $key => $val) {
				$controllers_opt[$key] = $val;
			}

			$cotrollers_string = implode(",", $controllers_opt); //convierte en cadena de texto el array para quitarle la extension
			$controllers_pre_array = str_replace(".php", "_nivel_form", $cotrollers_string); //quita la extension del texto
			return $controllers = explode(",", $controllers_pre_array); //convierte en array otra vez el texto sin la extension
    	}
    	else
    	{
			$controllers_opt = array('' => '');
			foreach ($array as $controllers_fetch) {
				$controllers_opt[$controllers_fetch] = $controllers_fetch;
			}
			
			$cotrollers_string = implode(",", $controllers_opt); //convierte en cadena de texto el array para quitarle la extension
			$controllers_pre_array = str_replace(".php", "", $cotrollers_string); //quita la extension del texto
			return $controllers = explode(",", $controllers_pre_array); //convierte en array otra vez el texto sin la extension

    	}
    }

	private function _validate($val = FALSE)// si se envia TRUE como valor en la funcion se utiliza para agregar un usuario de lo contrario es para modificar
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if ($val == TRUE)
		{
			if($this->input->post('nombres_form') == '')
			{
				$data['inputerror'][] = 'nombres_form';
				$data['error_string'][] = 'nombres del usuario son requeridos';
				$data['status'] = FALSE;
			}
			if($this->input->post('apellidos_form') == '')
			{
				$data['inputerror'][] = 'apellidos_form';
				$data['error_string'][] = 'apellidos del usuario son requeridos';
				$data['status'] = FALSE;
			}
			if($this->input->post('email_form') == '')
			{
				$data['inputerror'][] = 'email_form';
				$data['error_string'][] = 'Correo electronico es requerido';
				$data['status'] = FALSE;
			}
			if($this->input->post('contrasena_form') == '')
			{
				$data['inputerror'][] = 'contrasena_form';
				$data['error_string'][] = 'Clave es requerida';
				$data['status'] = FALSE;
			}
			if($this->input->post('telefono_form') == '')
			{
				$data['inputerror'][] = 'telefono_form';
				$data['error_string'][] = 'Numero telefonico es requerido';
				$data['status'] = FALSE;
			}
		}
		if ($val == FALSE)
		{
			if($this->input->post('nombres_form') == '')
			{
				$data['inputerror'][] = 'nombres_form';
				$data['error_string'][] = 'nombres del usuario son requeridos';
				$data['status'] = FALSE;
			}
			if($this->input->post('apellidos_form') == '')
			{
				$data['inputerror'][] = 'apellidos_form';
				$data['error_string'][] = 'apellidos del usuario son requeridos';
				$data['status'] = FALSE;
			}
			if($this->input->post('email_form') == '')
			{
				$data['inputerror'][] = 'email_form';
				$data['error_string'][] = 'Correo electronico es requerido';
				$data['status'] = FALSE;
			}
			if($this->input->post('telefono_form') == '')
			{
				$data['inputerror'][] = 'telefono_form';
				$data['error_string'][] = 'Numero telefonico es requerido';
				$data['status'] = FALSE;
			}
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
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
		$view_html .= $this->load->view('plantilla/footer', $this->viewdata, $returnhtml);
		if ($returnhtml) return $this->output->set_output($view_html);//esto retornara HTML en el 3 argumento ya que se envia como TRUE
	}
}
