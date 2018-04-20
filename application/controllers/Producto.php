<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Producto extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Producto_model',"model");
		$this->load->model('pdf_model'); // modelo para imprimir en pdf
		$this->load->library('html2pdf'); // libreria para imprimir en pdf
		
		$this->sesion = $this->seguridad->get_user_nivel(get_class($this));
		if(!is_int($this->sesion))
		{
			redirect("Login"); // redirige al controlador login si se intenta entrar a este controlador sin tener una session activa
		}
	}

	public function index()
	{
		if($this->sesion == 1 || $this->sesion == 2 || $this->sesion == 3) //valida el nivel de acceso del usuario y permite ejecutar las funciones
		{
			$data["login"] = $this->session->userdata("nombres");//muestra el nombre del usuario
			$data["nav"] = $this->seguridad->get_user_groups();//muestra el menu de los usuarios cuando estan conectados
			$data['titulo'] = "Producto";//titulo de la pagina

			$this->_render_page("producto_view", $data);
		}
		else
		{	
			redirect("Login"); // redirige al controlador login si se intenta entrar a este controlador sin tener una session activa
		}
	}

	public function ajax_list()// carga la lista que sera visualizada en la vista
	{
		if ($this->input->is_ajax_request()) //permite validar si la ejecucion del metodo es por ajax
		{
			if($this->sesion == 1 || $this->sesion == 2 || $this->sesion == 3) //valida el nivel de acceso del usuario y permite ejecutar las funciones
			{
				$data = array();
				$no = $_POST['start'];
				$this->data['users'] = $this->model->get_datatables();// funcion que carga datos en la tabla que se muestra en la vista

				foreach ($this->data['users'] as $rows)
				{
					$no++;
					$row = array();
					$row[] = $no;
					$row[] = $rows->NombreProducto;
					$row[] = $rows->FechaRegistro;
					$row[] = '
						<div class="dropdown">
						  	<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
								Acciones
								<span class="caret"></span>
						  	</button>
						  	<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
								<li><a class="btn btn-sm btn-primary btn-block" style="color: black;" href="javascript:void(0)" title="Editar" onclick="editar_ajax('."'".$rows->id."'".')"><i class="glyphicon glyphicon-pencil"></i> Editar</a></li>
								<li><a class="btn btn-sm btn-info btn-block" style="color: black;" href="javascript:void(0)" title="Detalles" onclick="detalles_ajax('."'".$rows->id."'".')"><i class="glyphicon glyphicon-list-alt"></i> Detalles</a></li>
								<li><a class="btn btn-sm btn-danger btn-block" style="color: black;" href="javascript:void(0)" title="Borrar" onclick="borrar_ajax('."'".$rows->id."'".')"><i class="glyphicon glyphicon-trash"></i> Borrar</a></li>
						  	</ul>
						</div>
						';
					
					$data[] = $row;
				}

				$output = array(
								"draw" => $_POST['draw'],
								"recordsTotal" => $this->model->count_all(), //muestra el total de registros en la tabla
								"recordsFiltered" => $this->model->count_filtered(), // muestra el total de datos que se filtren al buscar
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

	public function ajax_edit($id = FALSE)// carga la informacion de un registro para que sea vista o modificada
	{
		if ($this->input->is_ajax_request()) //permite validar si la ejecucion del metodo es por ajax
		{
			if($this->sesion == 1 || $this->sesion == 2 || $this->sesion == 3) //valida el nivel de acceso del usuario y permite ejecutar las funciones
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

	public function ajax_add()//agrega un nuevo registro
	{
		if ($this->input->is_ajax_request()) //permite validar si la ejecucion del metodo es por ajax
		{
			if($this->sesion == 1 || $this->sesion == 2) //valida el nivel de acceso del usuario y permite ejecutar las funciones
			{
				$this->_validate();

				$data = array(
						'NombreProducto' => $this->input->post('NombreProducto_form', TRUE),
						"FechaRegistro" => date("Y-m-d"),
					);

				$insert = $this->model->save($data, $this->input->post("id_proveedor_form"));
				echo json_encode($insert);
				//registro de acciones
				$this->seguridad->registrar_accion("Se agrega el Producto con el id ".$insert["insert_id"]);
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

	public function ajax_update()// modifica un registro
	{
		if ($this->input->is_ajax_request()) //permite validar si la ejecucion del metodo es por ajax
		{
			if($this->sesion == 1) //valida el nivel de acceso del usuario y permite ejecutar las funciones
			{
				$this->_validate();

				$data = array(
						'NombreProducto' => $this->input->post('NombreProducto_form', TRUE),
					);
												
				//registro de acciones
				$this->seguridad->registrar_accion("Se modifica el Producto con el id ".$this->input->post('id', TRUE));
				$update = $this->model->update(array('id' => $this->input->post('id', TRUE)), $data, $this->input->post("id_proveedor_form"));
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

	public function ajax_delete($id = FALSE)//borrar un registro en base a un id
	{
		if ($this->input->is_ajax_request()) //permite validar si la ejecucion del metodo es por ajax
		{
			if($this->sesion == 1) //valida el nivel de acceso del usuario y permite ejecutar las funciones
			{	
				//registro de acciones
				$this->seguridad->registrar_accion("Se Borro el Producto con el id ".$id);
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

    public function reporte()//realiza un reporte impreso en pdf que se muestra en la pagina
    {
    	if ($this->input->is_ajax_request()) //permite validar si la ejecucion del metodo es por ajax
		{
	    	if($this->sesion == 1 || $this->sesion == 2 || $this->sesion == 3) //valida el nivel de acceso del usuario y permite ejecutar las funciones
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
				$this->seguridad->registrar_accion("Se realizo un reporte de los Productos");

		        $row_empresa = $this->pdf_model->get_empresa($this->input->post("id_empresa", TRUE));

		        $data = array(
		            "logo" => "./files/img/logos_empresas/".$row_empresa->archivo,
		            'title' => "Productos",
		            "header" => utf8_encode($row_empresa->nombre_empresa),
		            'datos' => $this->pdf_model->get_reporte_Producto(),
		            "rif" => "RIF.: ".utf8_encode($row_empresa->razon_social),
		            "contador" => utf8_encode("Nº: $row_empresa->contador"),
		        );
		        
		        //hacemos que coja la vista como datos a imprimir
		        //importante utf8_decode para mostrar bien las tildes, ñ y demás
		        $this->html2pdf->html(utf8_decode($this->load->view('reportes_pdf/Producto_report_pdf', $data, true)));

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

	public function get_list_empresas() // funcion que permite optener el registro de las empresas que se encuentran en la bd para realizar los reportes y siempre se mostrara la predeterminada
	{
		if ($this->input->is_ajax_request()) //permite validar si la ejecucion del metodo es por ajax
		{
			if($this->sesion == 1 || $this->sesion == 2 || $this->sesion == 3) //valida el nivel de acceso del usuario y permite ejecutar las funciones
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

	public function get_list_proveedor()
	{
		if ($this->input->is_ajax_request()) //permite validar si la ejecucion del metodo es por ajax
		{
			if($this->sesion == 1 || $this->sesion == 2 || $this->sesion == 3) //valida el nivel de acceso del usuario y permite ejecutar las funciones
			{
				$data["list"] = $this->model->get_list_filtro_table("proveedor","NombreProveedor");
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

    private function _validate() // valida el envio de los campos si estan vacios o no
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('NombreProducto_form') == '')
		{
			$data['inputerror'][] = 'NombreProducto_form';
			$data['error_string'][] = 'Nombre del Producto es requerido';
			$data['status'] = FALSE;
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

    //funcion que permite realizar la carga de la vista con multiples archivos e informacion
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
