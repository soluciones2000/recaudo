<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Empresas extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('empresas_model',"model");
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
			$data["login"] = $this->session->userdata("nombres");//muestra el nombre del usuario
			$data["nav"] = $this->seguridad->get_user_groups();//muestra el menu de los usuarios cuando estan conectados
			$data['titulo'] = "Empresas";//titulo de la pagina

			$this->_render_page("empresas_view", $data);
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

				foreach ($this->data['users'] as $rows)
				{
					$no++;
					$row = array();
					$row[] = $no;
					$row[] = $rows->razon_social;
					$row[] = $rows->nombre_empresa;
					if ($rows->predeterminado == 1)
					{
						$row[] = "si";
					}
					else
					{
						$row[] = "no";
					}
					if($rows->archivo)
					{
						$row[] = '<a href="'.base_url('files/img/logos_empresas/'.$rows->archivo).'" target="_blank"><img src="'.base_url('files/img/logos_empresas/'.$rows->archivo).'" class="img-responsive" /></a>';
					}
					else
					{
						$row[] = '(No Logo)';
					}
					$row[] = '
						<div class="dropdown">
						  	<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
								Acciones
								<span class="caret"></span>
						  	</button>
						  	<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
								<li><a class="btn btn-sm btn-primary" style="color: black;" href="javascript:void(0)" title="Editar" onclick="editar_ajax('."'".$rows->id."'".')"><i class="glyphicon glyphicon-pencil"></i> Editar</a></li>
								<li><a class="btn btn-sm btn-info" style="color: black;" href="javascript:void(0)" title="Detalles" onclick="detalles_ajax('."'".$rows->id."'".')"><i class="glyphicon glyphicon-list-alt"></i> Detalles</a></li>
								<li><a class="btn btn-sm btn-danger" style="color: black;" href="javascript:void(0)" title="Borrar" onclick="borrar_ajax('."'".$rows->id."'".')"><i class="glyphicon glyphicon-trash"></i> Borrar</a></li>
						  	</ul>
						</div>
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
				$this->_validate();

				$data = array(
						'razon_social' => $this->input->post('razon_social_form', TRUE),
						"nombre_empresa" => $this->input->post("nombre_empresa_form", TRUE),
						"predeterminado" => $this->input->post("predeterminado_form", TRUE),
						"contador" => 0,
					);
				
				if(!empty($_FILES['archivo']['name']))
				{
					$upload = $this->_do_upload();
					$data['archivo'] = $upload;
				}
				else
				{
					$data['archivo'] = NULL;
				}

				$insert = $this->model->save($data);
				echo json_encode($insert);
				//registro de acciones
				$this->seguridad->registrar_accion("Se agrega la empresa con el id ".$insert["insert_id"]);
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

				$data = array(
						'razon_social' => $this->input->post('razon_social_form', TRUE),
						"nombre_empresa" => $this->input->post("nombre_empresa_form", TRUE),
						"predeterminado" => $this->input->post("predeterminado_form", TRUE),
					);
				$model = $this->model->get_by_id($this->input->post('id', TRUE));
				if($this->input->post('remove_logo')) // if remove logo checked
				{
					if(file_exists('files/img/logos_empresas/'.$model->archivo) && $model->archivo)
					{
						unlink('files/img/logos_empresas/'.$model->archivo);
					}
					$data['archivo'] = NULL;
				}

				if(!empty($_FILES['archivo']['name']))
				{
					$upload = $this->_do_upload();
					
					//delete file
					if(file_exists('files/img/logos_empresas/'.$model->archivo) && $model->archivo)
					{
						unlink('files/img/logos_empresas/'.$model->archivo);
					}

					$data['archivo'] = $upload;
				}
				
				//registro de acciones
				$this->seguridad->registrar_accion("Se modifica la Empresa con el id ".$this->input->post('id', TRUE));
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

	public function ajax_delete($id = FALSE)
	{
		if ($this->input->is_ajax_request())
		{
			if($this->sesion == 1)
			{	
				//registro de acciones
				$this->seguridad->registrar_accion("Se Borro la empresa con el id ".$id);

				$model = $this->model->get_by_id($id);
				$this->model->delete_by_id($id);

				if(file_exists('files/img/logos_empresas/'.$model->archivo) && $model->archivo)
				{
					unlink('files/img/logos_empresas/'.$model->archivo);
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
				$this->seguridad->registrar_accion("Se realizo un reporte de las empresas");

		        $row_empresa = $this->pdf_model->get_empresa($this->input->post("id_empresa", TRUE));

		        $data = array(
		            "logo" => "./files/img/logos_empresas/".$row_empresa->archivo,
		            'title' => "EMPRESAS",
		            "header" => utf8_encode($row_empresa->nombre_empresa),
		            'datos' => $this->pdf_model->get_reporte_empresas(),
		            "rif" => "RIF.: ".utf8_encode($row_empresa->razon_social),
		            "contador" => utf8_encode("Nº: $row_empresa->contador"),
		        );
		        
		        //hacemos que coja la vista como datos a imprimir
		        //importante utf8_decode para mostrar bien las tildes, ñ y demás
		        $this->html2pdf->html(utf8_decode($this->load->view('reportes_pdf/Empresas_report_pdf', $data, true)));

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

    private function _do_upload()
	{
		$config['upload_path']          = 'files/img/logos_empresas/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['max_size']             = 5000; //set max size allowed in Kilobyte
        $config['max_width']            = 100; // set max width image allowed
        $config['max_height']           = 100; // set max height allowed
        $config['file_name']            = round(microtime(true) * 1000); //just milisecond timestamp fot unique name

        $this->load->library('upload', $config);

        if(!$this->upload->do_upload('archivo')) //upload and validate
        {
            $data['inputerror'][] = 'archivo';
			$data['error_string'][] = 'Upload error: '.$this->upload->display_errors('',''); //show ajax error
			$data['status'] = FALSE;
			echo json_encode($data);
			exit();
		}
		return $this->upload->data('file_name');
	}

 	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('razon_social_form') == '')
		{
			$data['inputerror'][] = 'razon_social_form';
			$data['error_string'][] = 'Razon social es requerido';
			$data['status'] = FALSE;
		}
		if($this->input->post('nombre_empresa_form') == '')
		{
			$data['inputerror'][] = 'nombre_empresa_form';
			$data['error_string'][] = 'Nombre es requerido';
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

	private function _render_page($view, $data=null, $returnhtml=TRUE)
	{
		$this->viewdata = (empty($data)) ? $this->data: $data;
		$view_html = $this->load->view('plantilla/head', $this->viewdata, $returnhtml);
		$view_html .= $this->load->view('plantilla/nav', $this->viewdata, $returnhtml);
		$view_html .= $this->load->view($view, $this->viewdata, $returnhtml);

		if ($returnhtml) return $this->output->set_output($view_html);//esto retornara HTML en el 3 argumento ya que se envia como TRUE
	}
}
