<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mess extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */

	private $messTypes= array("JUNIOR MESS","SENIOR VEG MESS","SENIOR NON VEG MESS","GIRLS MESS");

	public function __construct()
	{
		parent::__construct();
		$this->load->model('items_model');
		$this->load->model('mess_model');
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->helper('date');
		$this->load->library('ion_auth');

	}

	public function getMessTypes()
	{
		return $this->messTypes;
	}

	public function get_mess_types()
	{
		
		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else
		{
			$data['username'] = $this->ion_auth->user()->row()->username;

			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();

			$jsonMessTypes = ($this->mess_model->get_mess_types_model());

			$messTypes = json_decode($jsonMessTypes,true);

			return $messTypes['messName'];
		}

	}

	public function check_for_lesser_items()
	{
		$items = $this->items_model->get_lesser_items();
		return $items;
	}



	public function mess_details()
	{

		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else {
			$data['username'] = $this->ion_auth->user()->row()->username;

			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();
			$data['title'] = "Mess Details";
			$this->load->view('templates/header');
			$this->load->view('templates/body',$data);
			$data['messCategory'] = $this->get_mess_categories();
			$this->load->view('mess/mess_details',$data);
		}
	}

	public function get_mess_categories()
	{
		
		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else
		{
			$data['username'] = $this->ion_auth->user()->row()->username;

			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();

			$jsonMessCategories = ($this->mess_model->get_mess_categories_model());

			$messCategories = json_decode($jsonMessCategories,true);

			return $messCategories['messCategory'];
		}

	}

	public function get_mess_details()
	{
		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else
		{
			$data['username'] = $this->ion_auth->user()->row()->username;

			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();

			$messList = ($this->mess_model->get_mess_types_model());
			echo ($messList);
		}

	}


	public function edit_mess_form()
	{

		
		$messName= $this->input->post('messName');
		$messIncharge = $this->input->post('messIncharge');
		
		$contact = $this->input->post('contact');

		$form = "
			<form name = 'edit_row' action = 'update_mess_details' method = 'post'>
			<div class='input-field'>
			<span class='blue-text text-darken-2'>Mess Name</span>
			<input type='hidden' value='".urldecode($messName)."' id= '".$messName."' name='modalMessName'/>	
			<input type='text' value='".urldecode($messName)."' id= '".$messName."Disabled' name='messNameDisabled' disabled/>

			</div>
			</div>
			<div class = 'row'>
			<div class='input-field'>
			<span class='blue-text text-darken-2'>Mess Incharge</span>
			<input type='text' value='".urldecode($messIncharge)."' id='".$messIncharge."' name='modalMessIncharge'/>
			</div>
			</div>
			<div class = 'row'>
			<div class='input-field'>
			<span class='blue-text text-darken-2'>Contact</span>
			<input type='text' value='".$contact."'  id='".$contact."' name='modalContact'/>
			
			</div>
			</div>
			<div class='row'>
			<div class='col s8 offset-s3'>

		<!--	<a href='javascript:submit_update();' class='btn waves-effect waves-light btn-large' value='submit' type='submit' name='submit'>-->
			<button class='btn waves-effect waves-light btn-large' value='submit' type='submit' name='submit'>
			Submit
			<i class='glyphicon glyphicon-chevron-right'></i>	
			</button>

			<button class='btn waves-effect waves-light red darken-1 btn-large' value='reset' type='reset' name='cancel'>
			Cancel
			<i class='glyphicon glyphicon-remove'></i>
			</button>
			</div>
			</div>

			</form>";
		echo $form;
	}


	



	
	
	public function add_mess()
	{

		 if(!$this->ion_auth->logged_in())
                        redirect('auth/login','refresh');
         else {
			$post_data = $_POST['data'];
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
			$mess = json_decode($post_data,true);
			
			$mess['messName'] = urldecode($mess['messName']);
			$mess['messIncharge'] = urldecode($mess['messIncharge']);
			$mess['messCategory'] = urldecode($mess['category']);
			$return = $this->mess_model->add_mess($mess);
			if($return == 1)
			echo 'Mess added succesfully';
			else
			echo $return;
                }

	}

	public function delete_mess()
	{
		if(!$this->ion_auth->logged_in())
                        redirect('auth/login','refresh');
	         else {
			$post_data = $_POST['data'];
                        $data['username'] = $this->ion_auth->user()->row()->username;
                        $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
			$return = $this->mess_model->delete_mess($post_data);
			if($return == 1)
			echo 'Mess deleted succesfully';
			else
			echo $return;
                }

	}

	public function update_mess_details($data="")
	{
		if(!$this->ion_auth->logged_in())
                        redirect('auth/login','refresh');
                else {
                        $data = $this->session->flashdata('data');
			$data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
			$data['messIncharge'] = $this->input->post('modalMessIncharge');
			$data['contact'] = $this->input->post('modalContact');
			$data['messName'] = urldecode($this->input->post('modalMessName'));
			$return = $this->mess_model->update_mess_details($data);
			if($return == 1)

				redirect('mess/mess_details',$data);
			else
			{
				$data['error'] = $return;
				redirect('mess/mess_details',$data);
			}

		}

	}

	public function get_mess_bill_report($messName,$from,$to)
	{


		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else{
			$data['username'] = $this->ion_auth->user()->row()->username;

			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();

			$messName = urldecode($messName);
			$from = urldecode($from);
			$to= urldecode($to);
			$from = date('Y-m-d',strtotime(str_replace(',','',$from)));
			$to = date('Y-m-d',strtotime(str_replace(',','',$to)));
			$messBill = ($this->mess_model->generate_mess_bill($messName,$from,$to));
			echo json_encode($messBill);
		}

	}

	public function get_mess_vegetable_bill_report($messName,$from,$to)
	{


		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else{
			$data['username'] = $this->ion_auth->user()->row()->username;

			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();

			$messName = urldecode($messName);
			$from = urldecode($from);
			$to= urldecode($to);
			$from = date('Y-m-d',strtotime(str_replace(',','',$from)));
			$to = date('Y-m-d',strtotime(str_replace(',','',$to)));
			$messBill = ($this->mess_model->generate_mess_vegetable_bill($messName,$from,$to));
			echo json_encode($messBill);
		}

	}

	public function get_mess_consumption_report($messName,$from,$to)
	{
		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else
		{
			$data['username'] = $this->ion_auth->user()->row()->username;

			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();

			$messName = urldecode($messName);
			$from = urldecode($from);
			$to= urldecode($to);
			$from = date('Y-m-d',strtotime(str_replace(',','',$from)));
			$to = date('Y-m-d',strtotime(str_replace(',','',$to)));
			$messConsumption = ($this->mess_model->generate_mess_consumption($messName,$from,$to));
			echo json_encode($messConsumption);
		}

	}


	public function get_edit_mess_consumption_report($messName,$from,$to)
	{
		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else
		{
			$data['username'] = $this->ion_auth->user()->row()->username;

			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();

			$messName = urldecode($messName);
			$from = urldecode($from);
			$to= urldecode($to);
			$from = date('Y-m-d',strtotime(str_replace(',','',$from)));
			$to = date('Y-m-d',strtotime(str_replace(',','',$to)));
			$messConsumption = ($this->mess_model->generate_edit_mess_consumption($messName,$from,$to));
			echo json_encode($messConsumption);
		}

	}




	public function get_mess_vegetable_consumption_report($messName,$from,$to)
	{
		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else
		{
			$data['username'] = $this->ion_auth->user()->row()->username;

			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();

			$messName = urldecode($messName);
			$from = urldecode($from);
			$to= urldecode($to);
			$from = date('Y-m-d',strtotime(str_replace(',','',$from)));
			$to = date('Y-m-d',strtotime(str_replace(',','',$to)));
			$messConsumption = ($this->mess_model->generate_mess_vegetable_consumption($messName,$from,$to));
			echo json_encode($messConsumption);
		}

	}

	
	public function get_mess_vegetable_average_report( $from,$to,$messName="")
	{
		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else
		{
			$data['username'] = $this->ion_auth->user()->row()->username;

			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();

			//$messName = urldecode($messName);
			$from = urldecode($from);
			$to= urldecode($to);
			

			$from = date('Y-m-d',strtotime(str_replace(',','',$from)));
			$to = date('Y-m-d',strtotime(str_replace(',','',$to)));

			if(empty($messName))
				$messConsumption = ($this->mess_model->generate_mess_vegetable_average($from,$to));
			else{
				$messName = urldecode($messName);
				$messConsumption = ($this->mess_model->generate_mess_vegetable_average($from,$to,$messName));
			}

			
			echo json_encode($messConsumption);
		}

	}

	 public function generate_mess_vegetable_average_bill()
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else{
            $data['username'] = $this->ion_auth->user()->row()->username;

            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $data['title'] = "Generate Mess Average";
            $this->load->view('templates/header');


            $reload =  $this->session->flashdata('data');
            if($reload != null && $reload != "")
            $data = $data + $reload;

            $from = $this->input->post('from');
            $to = $this->input->post('to');
            $from = date('Y-m-d',strtotime(str_replace(',','',$from)));
			$to = date('Y-m-d',strtotime(str_replace(',','',$to)));
            

            $data['vegetableName'] = $this->input->post('vegetableNames[]');
            $data['totalQuantity'] = $this->input->post('totalQuantity');
            $data['totalAmount'] = $this->input->post('totalAmount');
            $data['averagePrice'] = $this->input->post('averagePrice');
            
            
            $this->load->view('templates/body',$data);


            if(isset($_POST['submit'])){
               
                  $this->load->view('mess/generate_mess_vegetable_average_bill',$data);
               
            }
         }
      }


      public function mess_vegetable_average()
	{
		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else {
			$data['title'] = "Mess Vegetable Average";
			$data['username'] = $this->ion_auth->user()->row()->username;

			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();

			$this->load->view('templates/header');
			$this->load->view('templates/body',$data);
			$data['messTypes'] = $this->get_mess_types();
			$this->load->view('mess/mess_vegetable_average',$data);
		}
	}


	 public function generate_mess_vegetable_average_abstract()
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else{
            $data['username'] = $this->ion_auth->user()->row()->username;

            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $data['title'] = "Generate Mess Vegetable Average Abstract";
            $this->load->view('templates/header');


            $reload =  $this->session->flashdata('data');
            if($reload != null && $reload != "")
            $data = $data + $reload;

            $selectedOrders = $this->input->post('selectedOrders[]');
            $vegetableNames = $this->input->post('vegetableNames[]');
            $totalQuantity = $this->input->post('totalQuantity[]');
            $totalAmount = $this->input->post('totalAmount[]');
            $averagePrice = $this->input->post('averagePrice[]');

            $from = urldecode($this->input->post('from'));
            $to = urldecode($this->input->post('to'));
            $messName = urldecode($this->input->post('messName'));

            $data['vegetableNames'] = array();
            $data['totalAmount'] = array();
            $data['totalQuantity'] = array();
            $data['averagePrice'] = array();

            if(count($selectedOrders) == 0)
            {
               $data['error'] = "Select atleast one items";
               $this->session->set_flashdata('data',$data);
               redirect('mess/mess_vegetable_average');
            }
            
            $data['messName'] = $messName;
            $data['orders'] = $selectedOrders;
            $data['from'] = $from;
            $data['to'] = $to;

            $index = 0;
            for($i=0;$i<count($selectedOrders);$i++)
            {
            	$index = array_search($selectedOrders[$i], $vegetableNames);
            	array_push($data['vegetableNames'], $vegetableNames[$index]);
            	array_push($data['totalAmount'], $totalAmount[$index]);
            	array_push($data['totalQuantity'], $totalQuantity[$index]);
            	array_push($data['averagePrice'], $averagePrice[$index]);
            }
            
            $this->load->view('templates/body',$data);


            if(isset($_POST['submit'])){
                  $this->load->view('mess/generate_mess_vegetable_average_abstract',$data);
            }
         }
      }



	public function get_mess_average_report($from,$to,$messName="")
	{
		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else
		{
			$data['username'] = $this->ion_auth->user()->row()->username;

			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();

			
			$from = urldecode($from);
			$to= urldecode($to);

			$from = date('Y-m-d',strtotime(str_replace(',','',$from)));
			$to = date('Y-m-d',strtotime(str_replace(',','',$to)));

			if(empty($messName))
				$messConsumption = ($this->mess_model->generate_mess_average($from,$to));
			else{
				$messName = urldecode($messName);
				$messConsumption = ($this->mess_model->generate_mess_average($from,$to,$messName));
			}
			echo json_encode($messConsumption);
		}

	}

	 public function generate_mess_average_bill()
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else{
            $data['username'] = $this->ion_auth->user()->row()->username;

            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $data['title'] = "Generate Average";
            $this->load->view('templates/header');


            $reload =  $this->session->flashdata('data');
            if($reload != null && $reload != "")
            $data = $data + $reload;

            $from = $this->input->post('from');
            $to = $this->input->post('to');
            $from = date('Y-m-d',strtotime(str_replace(',','',$from)));
			$to = date('Y-m-d',strtotime(str_replace(',','',$to)));
            

            $data['vegetableName'] = $this->input->post('vegetableNames[]');
            $data['totalQuantity'] = $this->input->post('totalQuantity');
            $data['totalAmount'] = $this->input->post('totalAmount');
            $data['averagePrice'] = $this->input->post('averagePrice');
            
            
            $this->load->view('templates/body',$data);


            if(isset($_POST['submit'])){
               
                  $this->load->view('mess/generate_mess_average_bill',$data);
               
            }
         }
      }


      public function mess_average()
	{
		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else {
			$data['title'] = "Mess Average";
			$data['username'] = $this->ion_auth->user()->row()->username;

			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();

			$this->load->view('templates/header');
			$this->load->view('templates/body',$data);
			$data['messTypes'] = $this->get_mess_types();
			$this->load->view('mess/mess_average',$data);
		}
	}

	 public function generate_mess_average_abstract()
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else{
            $data['username'] = $this->ion_auth->user()->row()->username;

            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $data['title'] = "Generate Mess Average Abstract";
            $this->load->view('templates/header');


            $reload =  $this->session->flashdata('data');
            if($reload != null && $reload != "")
            $data = $data + $reload;

            $selectedOrders = $this->input->post('selectedOrders[]');
            $itemNames = $this->input->post('itemNames[]');
            $totalQuantity = $this->input->post('totalQuantity[]');
            $totalAmount = $this->input->post('totalAmount[]');
            $averagePrice = $this->input->post('averagePrice[]');

              $from = urldecode($this->input->post('from'));
            $to = urldecode($this->input->post('to'));
            $messName = urldecode($this->input->post('messName'));



            $data['itemNames'] = array();
            $data['totalAmount'] = array();
            $data['totalQuantity'] = array();
            $data['averagePrice'] = array();

            if(count($selectedOrders) == 0)
            {
               $data['error'] = "Select atleast one items";
               $this->session->set_flashdata('data',$data);
               redirect('mess/mess_average');
            }
            
            $data['messName'] = $messName;
            $data['orders'] = $selectedOrders;
               $data['from'] = $from;
            $data['to'] = $to;
         

            for($i=0;$i<count($selectedOrders);$i++)
            {
            	$index = array_search($selectedOrders[$i], $itemNames);
            	array_push($data['itemNames'], $itemNames[$index]);
            	array_push($data['totalAmount'], $totalAmount[$index]);
            	array_push($data['totalQuantity'], $totalQuantity[$index]);
            	array_push($data['averagePrice'], $averagePrice[$index]);
            }




            $this->load->view('templates/body',$data);


            if(isset($_POST['submit'])){
                  $this->load->view('mess/generate_mess_average_abstract',$data);
            }
         }
      }

      




	public function get_mess_return_report($messName,$from,$to)
	{
		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else
		{
			$data['username'] = $this->ion_auth->user()->row()->username;

			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();

			$messName = urldecode($messName);
			$from = urldecode($from);
			$to= urldecode($to);
			$from = date('Y-m-d',strtotime(str_replace(',','',$from)));
			$to = date('Y-m-d',strtotime(str_replace(',','',$to)));
			$messReturn = ($this->mess_model->generate_mess_return($messName,$from,$to));
			echo json_encode($messReturn);
		}

	}


	public function get_edit_mess_return_report($messName,$from,$to)
	{
		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else
		{
			$data['username'] = $this->ion_auth->user()->row()->username;

			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();

			$messName = urldecode($messName);
			$from = urldecode($from);
			$to= urldecode($to);
			$from = date('Y-m-d',strtotime(str_replace(',','',$from)));
			$to = date('Y-m-d',strtotime(str_replace(',','',$to)));
			$messReturn = ($this->mess_model->generate_edit_mess_return($messName,$from,$to));
			echo json_encode($messReturn);
		}

	}

	


	public function mess_bill()
	{

		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else{
			$data['title'] = "Mess Bill";
			$data['username'] = $this->ion_auth->user()->row()->username;


			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();

			$this->load->view('templates/header');
			$this->load->view('templates/body',$data);
			$data['messTypes'] = $this->get_mess_types();

			$this->load->view('mess/mess_bill',$data);
		}
	}

	public function mess_vegetable_bill()
	{

		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else{
			$data['title'] = "Mess Vegetable Bill";
			$data['username'] = $this->ion_auth->user()->row()->username;


			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();

			$this->load->view('templates/header');
			$this->load->view('templates/body',$data);
			$data['messTypes'] = $this->get_mess_types();

			$this->load->view('mess/mess_vegetable_bill',$data);
		}
	}

	public function mess_consumption()
	{
		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else {
			$data['title'] = "Mess Consumption";
			$data['username'] = $this->ion_auth->user()->row()->username;

			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();

			$this->load->view('templates/header');
			$this->load->view('templates/body',$data);
			$data['messTypes'] = $this->get_mess_types();
			$this->load->view('mess/mess_consumption',$data);
		}
	}


	public function edit_mess_consumption()
   { 

         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else{
            $data = $this->session->flashdata('data');
            $data['title'] = "Edit Item Issue";
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();

            $this->load->view('templates/header');
            $this->load->view('templates/body',$data);
            $data['messTypes'] = $this->get_mess_types();
            $this->load->view('edit/edit_mess_consumption',$data);


            if(isset($_POST['submit']))
            {
                $data['t_id'] = urldecode($this->input->post('modalId'));
                $data['itemName'] = urldecode($this->input->post('modalItemName'));
                $data['editedQuantity'] = $this->input->post('modalQuantity');
                $data['editedRate'] = $this->input->post('modalRate');
                $data['oldQuantity'] = $this->input->post('modalOldQuantity');
                $data['oldRate'] = $this->input->post('modalOldRate');



               if(count($data['itemName']) == 0)
               {
                  $data['message'] = "No values entered. Empty fields.";
                  $this->session->set_flashdata('data',$data);
                  redirect('mess/edit_mess_consumption');
               }

               $this->session->set_flashdata('data',$data);
               redirect('mess/edit_mess_consumption_confirmation');
            }




         }
   }



   public function edit_mess_consumption_confirmation()
   {


         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else {
            $data = $this->session->flashdata('data');

            $this->load->view('templates/header');
            $data['title']= 'Edit Issue Confirmation';
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $this->load->view('templates/body',$data); 
            $this->load->view('edit/edit_order_confirmation',$data); 


            if(isset($_POST['cancel']))
            {
               $data['title'] = 'Edit process cancelled';
               redirect('mess/edit_mess_consumption');

            }
            else if(isset($_POST['submit']))
            {
               $temp = array();
               $temp['t_id'] = $this->input->post('t_id');
               $temp['itemName'] = $this->input->post('itemName');
               $temp['editedQuantity'] = $this->input->post('editedQuantity');
               $temp['editedRate'] = $this->input->post('editedRate');
               $return = $this->orders_model->update_edit_details($temp);
               if($return ==1)
               {
                  $data = array();
                  $data['error'] = "Edit request sent for approval";

                  $this->session->set_flashdata('data',$data);
                  redirect('mess/edit_mess_consumption');
               }
               else
               {
                  $data['error'] = $return;
                  console.log($return);
                  $this->session->set_flashdata('data',$data);
                  redirect('mess/edit_mess_consumption');
               }

            }

            $this->load->view('templates/footer');
         }

      }

    public function edit_mess_consumption_form()
   {

      $t_id = $this->input->post('t_id');
      $itemName= $this->input->post('itemName');
      $split_word=explode("_",$itemName);
      $itemName = $split_word[0];

      for($i=1;$i<count($split_word);$i++)
         $itemName.=' '.$split_word[$i];
      $quantitySupplied = $this->input->post('quantitySupplied');
      
      $rate = $this->input->post('latestRate');

      $form = "
         <form name = 'edit_row' action = 'edit_mess_consumption' method = 'post'>
         <div class='input-field'>
         <span class='blue-text text-darken-2'>Item Name</span>
         <input type='hidden' value='".urldecode($itemName)."' id= '".$itemName."' name='modalItemName'/>  
         <input type='text' value='".urldecode($itemName)."' id= '".$itemName."Disabled' name='itemNameDisabled' readonly/>
         <input type='hidden' value='".urldecode($t_id)."' id= '".$t_id."' name='modalId'/> 
         <input type='hidden' value='".urldecode($quantitySupplied)."' id= '".$quantitySupplied."' name='modalOldQuantity'/> 
         <input type='hidden' value='".urldecode($rate)."' id= '".$rate."' name='modalOldRate'/> 

         </div>
         </div>
         <div class = 'row'>
         <div class='input-field'>
         <span class='blue-text text-darken-2'>Quantity</span>
         <input type='text' value='".urldecode($quantitySupplied)."' id='".$quantitySupplied."' name='modalQuantity'/>
         </div>
         </div>
         <div class = 'row'>
         <div class='input-field'>
         <span class='blue-text text-darken-2'>Rate</span>
         <input type='text' value='".$rate."'  id='".$rate."' name='modalRate' readonly/>
         
         </div>
         </div>
         <div class='row'>
         <div class='col s8 offset-s3'>
         <button class='btn waves-effect waves-light btn-large' value='submit' type='submit' name='submit'>
         Submit
         <i class='glyphicon glyphicon-chevron-right'></i>  
         </button>

         <button class='btn waves-effect waves-light red darken-1 btn-large' value='reset' type='reset' name='cancel'>
         Cancel
         <i class='glyphicon glyphicon-remove'></i>
         </button>
         </div>
         </div>

         </form>";
      echo $form;
   }



   public function delete_mess_consumption()
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else {
            $post_data = $_POST['t_id'];
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $return = $this->mess_model->delete_mess_consumption($post_data);
            if($return == 1)
            echo 'Delete request sent succesfully';
            else
            echo $return;
         }

      }

      public function delete_mess_return()
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else {
            $post_data = $_POST['t_id'];
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $return = $this->mess_model->delete_mess_return($post_data);
            if($return == 1)
            echo 'Delete request sent succesfully';
            else
            echo $return;
         }

      }



   public function edit_mess_return_form()
   {

      $t_id= $this->input->post('t_id');
      $itemName= $this->input->post('itemName');
      $split_word=explode("_",$itemName);
      $itemName = $split_word[0];

      for($i=1;$i<count($split_word);$i++)
         $itemName.=' '.$split_word[$i];
      $quantityReturned = $this->input->post('quantityReturned');
      
      $rate = $this->input->post('rate');

      $form = "
         <form name = 'edit_row' action = 'edit_mess_return' method = 'post'>
         <div class='input-field'>
         <span class='blue-text text-darken-2'>Item Name</span>
            
         <input type='hidden' value='".urldecode($t_id)."' id= '".$t_id."' name='modalId'/> 
         <input type='text' value='".urldecode($itemName)."' id= '".$itemName."Disabled' name='modalItemName' readonly/>
         <input type='hidden' value='".urldecode($quantityReturned)."' id= '".$quantityReturned."' name='modalOldQuantity'/> 
         <input type='hidden' value='".urldecode($rate)."' id= '".$rate."' name='modalOldRate'/> 

         </div>
         </div>
         <div class = 'row'>
         <div class='input-field'>
         <span class='blue-text text-darken-2'>Quantity</span>
         <input type='text' value='".urldecode($quantityReturned)."' id='".$quantityReturned."' name='modalQuantity'/>
         </div>
         </div>
         <div class = 'row'>
         <div class='input-field'>
         <span class='blue-text text-darken-2'>Rate</span>
         <input type='text' value='".$rate."'  id='".$rate."' name='modalRate' readonly/>
         
         </div>
         </div>
         <div class='row'>
         <div class='col s8 offset-s3'>
         <button class='btn waves-effect waves-light btn-large' value='submit' type='submit' name='submit'>
         Submit
         <i class='glyphicon glyphicon-chevron-right'></i>  
         </button>

         <button class='btn waves-effect waves-light red darken-1 btn-large' value='reset' type='reset' name='cancel'>
         Cancel
         <i class='glyphicon glyphicon-remove'></i>
         </button>
         </div>
         </div>

         </form>";
      echo $form;
   }

   public function edit_mess_return()
   { 

         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else{
            $data = $this->session->flashdata('data');
            $data['title'] = "Edit Item Return";
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();

            $this->load->view('templates/header');
            $this->load->view('templates/body',$data);
            $data['messTypes'] = $this->get_mess_types();
            $this->load->view('edit/edit_mess_return',$data);


            if(isset($_POST['submit']))
            {
                $data['t_id'] = urldecode($this->input->post('modalId'));
                $data['itemName'] = urldecode($this->input->post('modalItemName'));
                $data['editedQuantity'] = $this->input->post('modalQuantity');
                $data['editedRate'] = $this->input->post('modalRate');
                $data['oldQuantity'] = $this->input->post('modalOldQuantity');
                $data['oldRate'] = $this->input->post('modalOldRate');



               if(count($data['itemName']) == 0)
               {
                  $data['message'] = "No values entered. Empty fields.";
                  $this->session->set_flashdata('data',$data);
                  redirect('mess/edit_mess_return');
               }

               $this->session->set_flashdata('data',$data);
               redirect('mess/edit_mess_return_confirmation');
            }




         }
   }



   public function edit_mess_return_confirmation()
   {


         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else {
            $data = $this->session->flashdata('data');

            $this->load->view('templates/header');
            $data['title']= 'Edit Issue Confirmation';
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $this->load->view('templates/body',$data); 
            $this->load->view('edit/edit_order_confirmation',$data); 


            if(isset($_POST['cancel']))
            {
               $data['title'] = 'Edit process cancelled';
               redirect('mess/edit_mess_return');

            }
            else if(isset($_POST['submit']))
            {
               $temp = array();
               $temp['t_id'] = $this->input->post('t_id');
               $temp['itemName'] = $this->input->post('itemName');
               $temp['editedQuantity'] = $this->input->post('editedQuantity');
               $temp['editedRate'] = $this->input->post('editedRate');
               $return = $this->orders_model->update_edit_details($temp);
               if($return ==1)
               {
                  $data = array();
                  $data['error'] = "Edit request sent for approval";

                  $this->session->set_flashdata('data',$data);
                  redirect('mess/edit_mess_return');
               }
               else
               {
                  $data['error'] = $return;
                  console.log($return);
                  $this->session->set_flashdata('data',$data);
                  redirect('mess/edit_mess_return');
               }

            }

            $this->load->view('templates/footer');
         }

      }

	public function mess_vegetable_consumption()
	{
		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else {
			$data['title'] = "Mess Vegetable Consumption";
			$data['username'] = $this->ion_auth->user()->row()->username;

			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();

			$this->load->view('templates/header');
			$this->load->view('templates/body',$data);
			$data['messTypes'] = $this->get_mess_types();
			$this->load->view('mess/mess_vegetable_consumption',$data);
		}
	}

	public function mess_return()
	{
		if(!$this->ion_auth->logged_in())
			redirect('auth/login','refresh');
		else {
			$data['title'] = "Mess Returns";
			$data['username'] = $this->ion_auth->user()->row()->username;

			$data['group'] = $this->ion_auth->get_logged_in_user_group_names();

			$this->load->view('templates/header');
			$this->load->view('templates/body',$data);
			$data['messTypes'] = $this->get_mess_types();

			$this->load->view('mess/mess_return',$data);
		}
	}

	

	




}
