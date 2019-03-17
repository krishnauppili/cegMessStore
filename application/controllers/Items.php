<?php
   defined('BASEPATH') OR exit('No direct script access allowed');

   class Items extends CI_Controller {

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
         $this->load->model('orders_model');

         $this->load->helper('form');
         $this->load->helper('url');
         $this->load->library('session');
         $this->load->library('form_validation'); 
         $this->load->helper('date');
         $this->load->library('ion_auth');

      }

      public function check_for_lesser_items()
      {
         $items = $this->items_model->get_lesser_items();
         return $items;
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

         public function issue_item($data="")
         {
            if(!$this->ion_auth->logged_in())
            redirect('auth/login','refresh');
            else{
               $this->load->view('templates/header');
               $reload =  $this->session->flashdata('data');
               $data = $reload;
               $data['username'] = $this->ion_auth->user()->row()->username;
               $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
               $data['title'] = 'Issue items to mess';
               $data['lesser_items'] = $this->check_for_lesser_items();
               $tableData = $this->items_model->get_items();
               $data['tableData'] = $tableData;
               $data['messTypes'] = $this->get_mess_types();

               if(isset($_POST['submit'])){
                  $selectedItems = $this->input->post('selectedItems[]');

                  $data['selectedMess'] = $this->input->post('selectedMess');
                  $quantityAvailable = $this->input->post('quantityAvailable[]');
                  $latestRate = $this->input->post('latestRate[]');
                  $selectedQuantity = ($this->input->post('selectedQuantity[]'));
                  $data['issuedDate'] = date('Y-m-d',strtotime(str_replace(',','',$this->input->post('issuedDate'))));
                  $data['quantityAvailable'] = array();
                  $data['latestRate'] = array();
                  $data['selectedQuantity'] = array();
                  $data['selectedItems'] = array();
                  for($i=0;$i<count($selectedItems);$i++)
                  {
                     if($selectedQuantity[$i] == '')
                     continue;
                     array_push($data['selectedItems'],$selectedItems[$i]);
                     array_push($data['selectedQuantity'],$selectedQuantity[$i]);
                     array_push($data['latestRate'],$latestRate[$i]);
                     array_push($data['quantityAvailable'], $quantityAvailable[$i]);
                  }
                  if(count($data['selectedItems']) == 0)
                  {
                     $data['message'] = "Enter atleast one item\'s quantity";
                     $this->session->set_flashdata('data',$data);
                     redirect('items/issue_item');

                  }
                  $return = $this->items_model->validation_for_issue($data);
                  if($return == 1)
                  {
                     $data['message'] = "Entered quantity is greater than the available quantity. Please check once again.";
                     $this->session->set_flashdata('data',$data);
                     redirect('items/issue_item');

                  }
                  $return1 = $this->items_model->validation_for_duplicate_entry($data);
                  if($return1 == 1)
                  {
                     $data['error'] = "Duplicate entry. Please check once again.";
                     $this->session->set_flashdata('data',$data);
                     redirect('items/issue_item');
                  }

                  $this->session->set_flashdata('data',$data);
                  redirect('items/issue_confirmation');
               }
               else
               {
                  if(isset($reload) && $reload !== null)
                  {

                     $this->load->view('templates/body',$data); 
                     $this->load->view('items/issue_item',$data);

                  }
                  else{
                     $this->load->view('templates/body',$data); 
                     $this->load->view('items/issue_item',$data);
                  }
               }

               $this->load->view('templates/footer');
            }
         }

         public function issue_confirmation()
         {


            if(!$this->ion_auth->logged_in())
            redirect('auth/login','refresh');
            else {
               $data = $this->session->flashdata('data');

               $this->load->view('templates/header');
               $data['title']= 'Issue Confirmation';
               $data['username'] = $this->ion_auth->user()->row()->username;
               $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
               $this->load->view('templates/body',$data); 

               $this->load->view('items/issue_confirmation',$data);


               $data['selectedItems'] = $this->input->post('selectedItems[]');
               $data['selectedQuantity'] = $this->input->post('selectedQuantity[]');
               $data['quantityAvailable'] = $this->input->post('quantityAvailable[]');
               $data['selectedMess'] = $this->input->post('selectedMess');
               $data['issuedDate'] = $this->input->post('issuedDate');


               if(isset($_POST['cancel']))
               {
                  $data['title'] = 'Create a new item';
                  $this->session->set_flashdata('data',$data);
                  redirect('items/issue_item');

               }
               else if(isset($_POST['submit']))
               {
                  $return = $this->items_model->issue_item_model($data);
                  if($return['status'] == 'Data Inserted Successfully')
                  {
                     $data = array();
                     $data['error'] = "Data Inserted Successfully for Bill No :".$return['billNo'];

                     $this->session->set_flashdata('data',$data);
                     redirect('items/issue_item');
                  }
                  else
                  {
                     $data['error'] = $return['query']." for Bill No :".$return['billNo'];
                     console.log($return);
                     $this->session->set_flashdata('data',$data);
                     redirect('items/issue_item');
                   }




               }

               $this->load->view('templates/footer');
            }

         }

         public function items_in_stock()
         {
            if(!$this->ion_auth->logged_in())
            redirect('auth/login','refresh');
            else {

               $this->load->view('templates/header');
               $data = $this->session->flashdata('data');
               $data['username'] = $this->ion_auth->user()->row()->username;
               $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
               $data['title'] = "Items in Stock";
               $this->load->view('templates/body',$data);
               $return = $this->items_model->get_items();
               $return = json_decode($return,true);
               if($return != null && $return != '')
               $data = $data + $return;
               $this->load->view('items/items_in_stock',$data);
               $this->load->view('templates/footer',$data);

            }


         }

         public function items_search()
         {
               if(!$this->ion_auth->logged_in())
               redirect('auth/login','refresh');
               else {
               $data['title'] = "Items in Stock";
               $data['username'] = $this->ion_auth->user()->row()->username;

               $data['group'] = $this->ion_auth->get_logged_in_user_group_names();

                $this->load->view('templates/header');
                $this->load->view('templates/body',$data);
                //$data['messTypes'] = $this->get_mess_types();
                $this->load->view('items/items_search',$data);
                }   
         }

         public function get_items_stock_report($itemName="")
         {
            if(!$this->ion_auth->logged_in())
            redirect('auth/login','refresh');
            else
            {
               $data['username'] = $this->ion_auth->user()->row()->username;
               $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
               $itemName = urldecode($itemName);
               if($itemName!="")
                  $itemsStock = ($this->items_model->generate_items_stock($itemName));
               else
                  $itemsStock = ($this->items_model->generate_items_stock());
               echo json_encode($itemsStock);
            }

         }

          public function get_items_stock_total_report($itemName)
         {
            if(!$this->ion_auth->logged_in())
            redirect('auth/login','refresh');
            else
            {
               $data['username'] = $this->ion_auth->user()->row()->username;
               $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
               $itemName = urldecode($itemName);
               $itemsStock = ($this->items_model->generate_items_total_stock($itemName));
               echo json_encode($itemsStock);
            }

         }

         public function stock_approximation($data="")
         {


            if(!$this->ion_auth->logged_in())
            redirect('auth/login','refresh');
            else{
               $this->load->view('templates/header');
               $reload =  $this->session->flashdata('data');
               $data = $reload;
               $data['username'] = $this->ion_auth->user()->row()->username;
               $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
               $data['title'] = 'Update Stock';
               //$data['lesser_items'] = $this->check_for_lesser_items();
               $tableData = $this->items_model->get_items();
               $data['tableData'] = $tableData;
               //$data['messTypes'] = $this->get_mess_types();

               if(isset($_POST['submit'])){
                  $selectedItems = $this->input->post('selectedItems[]');

                  //$data['selectedMess'] = $this->input->post('selectedMess');
                  $quantityAvailable = $this->input->post('quantityAvailable[]');

                  $latestRate = $this->input->post('latestRate[]');
                  $selectedQuantity = ($this->input->post('selectedQuantity[]')); 
                  $data['approximatedDate'] = date('Y-m-d',strtotime(str_replace(',','',$this->input->post('approximatedDate'))));
                  $data['quantityAvailable'] = array();
                  $data['latestRate'] = array();
                  $data['selectedQuantity'] = array();
                  $data['selectedItems'] = array();
                  for($i=0;$i<count($selectedItems);$i++)
                  {
                     if($selectedQuantity[$i] == '')
                     continue;
                     array_push($data['selectedItems'],$selectedItems[$i]);
                     array_push($data['selectedQuantity'],$selectedQuantity[$i]);
                     array_push($data['latestRate'],$latestRate[$i]);
                     array_push($data['quantityAvailable'], $quantityAvailable[$i]);
                  }
                  if(count($data['selectedItems']) == 0)
                  {
                     $data['message'] = "Enter atleast one item\'s quantity";
                     $this->session->set_flashdata('data',$data);
                     redirect('items/stock_approximation');

                  }
                  $this->session->set_flashdata('data',$data);
                  redirect('items/stock_approximation_confirmation');
               }
               else
               {
                  if(isset($reload) && $reload !== null)
                  {

                     $this->load->view('templates/body',$data); 
                     $this->load->view('items/stock_approximation',$data);

                  }
                  else{
                     $this->load->view('templates/body',$data); 
                     $this->load->view('items/stock_approximation',$data);
                  }
               }

               $this->load->view('templates/footer');
            }
         }


         public function stock_approximation_confirmation()
         {


            if(!$this->ion_auth->logged_in())
            redirect('auth/login','refresh');
            else {
               $data = $this->session->flashdata('data');

               $this->load->view('templates/header');
               $data['title']= 'Stock Approximation Confirmation';
               $data['username'] = $this->ion_auth->user()->row()->username;
               $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
               $this->load->view('templates/body',$data); 

               $this->load->view('items/stock_approximation_confirmation',$data);


               $data['selectedItems'] = $this->input->post('selectedItems[]');
               $data['selectedQuantity'] = $this->input->post('selectedQuantity[]');
               $data['quantityAvailable'] = $this->input->post('quantityAvailable[]');
               //$data['selectedMess'] = $this->input->post('selectedMess');
               $data['approximatedDate'] = $this->input->post('approximatedDate');


               if(isset($_POST['cancel']))
               {
                  $data['title'] = 'Create a new item';
                  $this->session->set_flashdata('data',$data);
                  redirect('items/stock_approximation');

               }
               else if(isset($_POST['submit']))
               {
                  $return = $this->items_model->stock_approximation_model($data);
                  if($return == 1)
                  {

                     $data['error'] = "Data sent for Approval";
                     $data['title']="Sent for Approval";

                     $this->session->set_flashdata('data',$data);
                     
                     redirect('items/stock_approximation');
                  }
                  else
                  {
                     $data['error'] = $return;
                     $this->session->set_flashdata('data',$data);
                     redirect('items/stock_approximation');
                  }

               }

               $this->load->view('templates/footer');
            }

         }

         public function get_item_types()
         {
      
             if(!$this->ion_auth->logged_in())
             redirect('auth/login','refresh');
             else
             {
                  $data['username'] = $this->ion_auth->user()->row()->username;

                  $data['group'] = $this->ion_auth->get_logged_in_user_group_names();

                  $jsonItemTypes = ($this->items_model->get_item_types_model());

                  $itemTypes = json_decode($jsonItemTypes,true);

                  return $itemTypes['itemType'];
             }

          }



         public function add_item($data="")
         {


            if(!$this->ion_auth->logged_in())
            redirect('auth/login','refresh');
            else{
               $this->form_validation->set_rules('itemName[]', 'Item Name', 'required');
               $this->form_validation->set_rules('itemRate[]', 'Item Rate', 'required');
               $this->form_validation->set_rules('quantityAvailable[]', 'Quantity Available', 'required');
               $this->form_validation->set_rules('minimumQuantity[]', 'Quantity Available', 'required');
               $this->form_validation->set_rules('precedence[]', 'Precedence', 'required');
               $this->form_validation->set_rules('selectedType[]', 'Type', 'required');
               $this->load->view('templates/header');

               $reload =  $this->session->flashdata('data');
               $data =$reload;
               $data['username'] = $this->ion_auth->user()->row()->username;
               $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
               $data['title'] ='Add Items to Store';
               $data['itemType'] = $this->get_item_types();



               if(isset($_POST['cancel']))
               {
                  redirect('items/add_item');
               }

               else if(isset($_POST['submit'])){
                  $data['itemName'] = $this->input->post('itemName[]');
                  $data['itemRate'] = $this->input->post('itemRate[]');
                  $data['quantityAvailable'] = $this->input->post('quantityAvailable[]');
                  $data['minimumQuantity'] = $this->input->post('minimumQuantity[]');
                  $data['selectedType'] = $this->input->post('selectedType[]');
                 // $data['vat'] = $this->input->post('vat[]');
                  $data['precedence'] = $this->input->post('precedence[]');

                  $this->session->set_flashdata('data',$data);

                  if ($this->form_validation->run() === FALSE){

                     $this->load->view('templates/body',$data);
                     $this->load->view('items/add_item',$data);

                  }
                  else
                  {
                     $flag = 1;
                     for( $i =0; $i < count($data['itemName']); $i++)
                     {

                        if(preg_match('/[\'^£$%&*()}.{@#~?><>,|=_+¬-]/', $data['itemName'][$i])||(strpos($data['itemName'][$i], '/')!==false)||(strpos($data['itemName'][$i], '\\')!==false))
                        {
                           $flag=0;
                           $data['error'] = "No special characters are allowed";
                           break;
                        }
                        if(strlen($data['itemName'][$i])>=100)
                        {
                           $flag=0;
                           $data['error'] = "Item name should not be greater than 100";
                           break;
                        }

                     }
                     if($flag==0)
                     {
                        
                        $this->load->view('templates/body',$data);
                        $this->load->view('items/add_item',$data);

                     }
                     else
                     {
                        redirect('items/add_confirmation');

                     }
                     
                  }
                  
               }
               else
               {
                  if(isset($reload) && $reload !== null)
                  {

                     $this->load->view('templates/body',$data);
                     $this->load->view('items/add_item',$reload);
                  }
                  else{

                     $this->load->view('templates/body',$data);
                     $this->load->view('items/add_item',$data);
                  }
               }

               $this->load->view('templates/footer');
            }
         }






         public function add_confirmation()
         {  

            if(!$this->ion_auth->logged_in())
            redirect('auth/login','refresh');
            else {
               $data = $this->session->flashdata('data');
               $this->load->view('templates/header');
               $data['username'] = $this->ion_auth->user()->row()->username;
               $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
               $data['title'] = 'Add Confirmation';

               $this->load->view('templates/body',$data);


               $this->load->view('items/add_confirmation',$data);

               $data['itemName'] = $this->input->post('itemName[]');
               $data['itemRate'] = $this->input->post('itemRate[]');
               $data['quantityAvailable'] = $this->input->post('quantityAvailable[]');
               $data['minimumQuantity'] = $this->input->post('minimumQuantity[]');
               $data['selectedType'] = $this->input->post('selectedType[]');
               $data['precedence'] = $this->input->post('precedence[]');
               if(isset($_POST['cancel']))
               {
                  $data['title'] = 'Create a new item';
                  $this->session->set_flashdata('data',$data);
                  redirect('items/add_item');   

               }
               else if(isset($_POST['submit']))
               {
                  $return = $this->items_model->add_item_model($data);
                  unset($data['itemName']);
                  unset($data['itemRate']);
                  unset($data['quantityAvailable']);
                 // unset($data['vat']);
                  unset($data['precedence']);
                  if($return == 1)
                  {
                     $data['error'] = "Data Inserted Successfully";

                     $this->session->set_flashdata('data',$data);
                     redirect('items/add_item');
                  }
                  else
                  {
                     $data['error'] = $return;
                     $this->session->set_flashdata('data',$data);
                     redirect('items/add_item');
                  }

               }
            }

         }



         public function add_vegetable($data="")
         {


            if(!$this->ion_auth->logged_in())
            redirect('auth/login','refresh');
            else{
               $this->form_validation->set_rules('itemName[]', 'Item Name', 'required');
               $this->form_validation->set_rules('precedence[]', 'Precedence', 'required');
               $this->form_validation->set_rules('selectedType[]', 'Type', 'required');

               $this->load->view('templates/header');

               $reload =  $this->session->flashdata('data');
               $data =$reload;
               $data['username'] = $this->ion_auth->user()->row()->username;
               $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
               $data['title'] ='Add Vegetables to Store';
               $data['itemType'] = $this->get_item_types();


               if(isset($_POST['cancel']))
               {
                  redirect('items/add_vegetable');
               }

               else if(isset($_POST['submit'])){
                  $data['itemName'] = $this->input->post('itemName[]');
                  $data['precedence'] = $this->input->post('precedence[]');
                  $data['selectedType'] = $this->input->post('selectedType[]');
                  $this->session->set_flashdata('data',$data);

                  if ($this->form_validation->run() === FALSE){

                     $this->load->view('templates/body',$data);
                     $this->load->view('items/add_vegetable',$data);

                  }
                  else
                  redirect('items/add_vegetable_confirmation');
               }
               else
               {
                  if(isset($reload) && $reload !== null)
                  {

                     $this->load->view('templates/body',$data);
                     $this->load->view('items/add_vegetable',$reload);
                  }
                  else{

                     $this->load->view('templates/body',$data);
                     $this->load->view('items/add_vegetable',$data);
                  }
               }

               $this->load->view('templates/footer');
            }
         }


         public function add_vegetable_confirmation()
         {  

            if(!$this->ion_auth->logged_in())
            redirect('auth/login','refresh');
            else {
               $data = $this->session->flashdata('data');
               $this->load->view('templates/header');
               $data['username'] = $this->ion_auth->user()->row()->username;
               $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
               $data['title'] = 'Add Vegetable Confirmation';

               $this->load->view('templates/body',$data);


               $this->load->view('items/add_vegetable_confirmation',$data);

               $data['itemName'] = $this->input->post('itemName[]');
               $data['precedence'] = $this->input->post('precedence[]');
               $data['selectedType'] = $this->input->post('selectedType[]');
               if(isset($_POST['cancel']))
               {
                  $this->session->set_flashdata('data',$data);
                  redirect('items/add_vegetable'); 

               }
               else if(isset($_POST['submit']))
               {
                  $return = $this->items_model->add_vegetable_model($data);
                  unset($data['itemName']);
                  unset($data['precedence']);
                  unset($data['selectedType']);
                  if($return == 1)
                  {
                     $data['error'] = "Data Inserted Successfully";

                     $this->session->set_flashdata('data',$data);
                     redirect('items/add_vegetable');
                  }
                  else
                  {
                     $data['error'] = $return;
                     $this->session->set_flashdata('data',$data);
                     redirect('items/add_vegetable');
                  }

               }
            }

         }


         public function return_item($data="")
      {

         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else {
            $this->load->view('templates/header');
            $reload =  $this->session->flashdata('data');
            $data = $reload;
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $data['messTypes'] = $this->get_mess_types();
            $data['title'] = 'Return Items to Store';
            if(isset($_POST['submit'])){

               $selectedItems = $this->input->post('selectedItems[]');
               $data['selectedMess'] = $this->input->post('selectedMess');

               $quantitySupplied = $this->input->post('quantitySupplied[]');

               $latestRate = $this->input->post('latestRate[]');
               $selectedQuantity = ($this->input->post('selectedQuantity[]'));
               $data['issuedDate'] = date('Y-m-d',strtotime(str_replace(',','',$this->input->post('issuedDate'))));

               $data['quantitySupplied'] = array();
               $data['latestRate'] = array();
               $data['selectedQuantity'] = array();
               $data['selectedItems'] = array();
               for($i=0;$i<count($selectedItems);$i++)
               {
                  if($selectedQuantity[$i] == '')
                  continue;
                  array_push($data['selectedItems'],$selectedItems[$i]);
                  array_push($data['selectedQuantity'],$selectedQuantity[$i]);
                  array_push($data['latestRate'],$latestRate[$i]);
                  array_push($data['quantitySupplied'], $quantitySupplied[$i]);
               }
               if(count($data['selectedItems']) == 0)
               {
                  $data['message'] = "Enter atleast one item\'s quantity";
                  $this->session->set_flashdata('data',$data);
                  redirect('items/return_item');

               }
               $return = $this->items_model->validation_for_return($data);
               if($return == 1)
               {
                  $data['message'] = "Entered quantity is greater than the supplied quantity. Please check once again.";
                  $this->session->set_flashdata('data',$data);
                  redirect('items/return_item');

               }


               $quantityAvailable = json_decode($this->items_model->get_items($data['selectedItems']),true);
               $data['quantityAvailable'] = $quantityAvailable['quantityAvailable'];

               $this->session->set_flashdata('data',$data);
               redirect('items/return_confirmation');
            }
            else
            {
               if(isset($reload) && $reload !== null)
               {

                  $this->load->view('templates/body',$data); 
                  $this->load->view('items/return_item',$data);
               }

               else
               {

                  $this->load->view('templates/body',$data); 
                  $this->load->view('items/return_item',$data);
               }
            }
            $this->load->view('templates/footer');
         }
      }

      public function return_confirmation()
         {


            if(!$this->ion_auth->logged_in())
            redirect('auth/login','refresh');
            else{ 
               $data = $this->session->flashdata('data');

               $this->load->view('templates/header');

               $data['title'] = 'Return Confirmation';
               $data['username'] = $this->ion_auth->user()->row()->username;
               $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
               $this->load->view('templates/body',$data); 

               $this->load->view('items/return_confirmation',$data);


               $data['selectedItems'] = $this->input->post('selectedItems[]');
               $data['quantitySupplied'] = $this->input->post('quantitySupplied[]');
               $data['quantityAvailable'] = $this->input->post('quantityAvailable[]');
               $data['selectedQuantity'] = $this->input->post('selectedQuantity[]');
               $data['latestRate'] = $this->input->post('latestRate[]');
               $data['selectedMess'] = $this->input->post('selectedMess');
               $data['issuedDate'] = $this->input->post('issuedDate');

               if(isset($_POST['cancel']))
               {
                  $data['title'] = 'Create a news item';
                  $this->session->set_flashdata('data',$data);
                  redirect('items/return_item');

               }
               else if(isset($_POST['submit']))
               {
                  $return = $this->items_model->return_item_model($data);
                  if($return['status'] == 'Data Inserted Successfully')
                  {
                     $data = array();
                     $data['error'] = "Data Inserted Successfully for Bill No :".$return['billNo'];

                     $this->session->set_flashdata('data',$data);
                     redirect('items/return_item');
                  }
                  else
                  {
                     $data['error'] = $return['status']."for Bill No :".$return['billNo'];
                     console.log($return);
                     $this->session->set_flashdata('data',$data);
                     redirect('items/return_item');
                   }

               }
               $this->load->view('templates/footer');
            }

         }



      public function getMessConsumptionTillToday($messName = null,$date=null)
      {

        $date = date('Y-m-d',strtotime(str_replace(',','',urldecode($date))));
         $messName = urldecode($messName);
         $consumedItems = ($this->items_model->get_consumed_items_till_today($messName,$date));
         echo json_encode($consumedItems);
      }

      public function edit_issue_item()
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
            $this->load->view('edit/edit_issue_item',$data);
         }
      }


      public function notification_edit_history()
      {

         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else{
            $data = $this->session->flashdata('data');
            $data['title'] = "Edit notification";
            $data['username'] = $this->ion_auth->user()->row()->username;


            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();


            $this->load->view('templates/header');
            $this->load->view('templates/body',$data);
            $this->load->view('items/notification_edit_history',$data);
         }
      }

      public function notification_stock_approximation()
      {

         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else{
            $data = $this->session->flashdata('data');
            $data['title'] = "Stock approximation notifications";
            $data['username'] = $this->ion_auth->user()->row()->username;


            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();


            $this->load->view('templates/header');
            $this->load->view('templates/body',$data);
            $this->load->view('items/notification_stock_approximation',$data);
         }
      }

      public function get_notification_details()
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else
         {
            $data['username'] = $this->ion_auth->user()->row()->username;

            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();

            $notificationDetails = ($this->items_model->generate_notification_details());
           
            echo json_encode($notificationDetails);
         }


      }


      public function get_stock_approximation_details()
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else
         {
            $data['username'] = $this->ion_auth->user()->row()->username;

            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();

            $stockApproximationDetails = ($this->items_model->generate_stock_approximation_details());
            echo json_encode($stockApproximationDetails);
         }
      }

   public function notification_approval(){
      if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
      else{
         $data['username'] = $this->ion_auth->user()->row()->username;
         $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
         $notificationDetails = ($this->items_model->generate_notification_details());
         echo json_encode($notificationDetails);
      }
   }

   public function approve_stock_approximation(){
      if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
      else {
         $temp=array();
         $temp['approximatedDate'] = $_POST['approximatedDate'];
         $temp['itemNameParams'] = $_POST['itemNameParams'];
         $temp['actualStockParams'] = $_POST['actualStockParams'];
         $data['username'] = $this->ion_auth->user()->row()->username;
         $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
         $return = $this->items_model->approve_stock_approximation($temp);
         if($return['status'] == "Data Inserted Successfully")
            echo 'Approval done succesfully for Edit in '.$return['billNo'].'';
         else
            echo 'Error : '.$return['status'].' for Bill No :'.$return['billNo'].'';
      }
   }

   public function disapprove_stock_approximation()
   {
      if(!$this->ion_auth->logged_in())
                        redirect('auth/login','refresh');
            else {
               $temp=array();

         $temp['sid'] = $_POST['sid'];
         $data['username'] = $this->ion_auth->user()->row()->username;
         $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
         $return = $this->items_model->disapprove_stock_approximation($temp);
         if($return == 1)
         echo 'Disapproval done succesfully';
         else
         echo $return;
                }

   }

   public function approve_edit()
   {
      if(!$this->ion_auth->logged_in())
                        redirect('auth/login','refresh');
            else {
               $temp=array();

         $temp['t_id'] = $_POST['t_id'];
         $temp['date'] = $_POST['date'];
         $temp['type'] = $_POST['type'];
         $temp['notification_type'] = $_POST['notification_type'];
         $temp['vendor_name'] = $_POST['vendor_name'];
         $temp['mess_name'] = $_POST['mess_name'];
         $temp['item_name'] = $_POST['item_name'];
         $temp['quantity'] = $_POST['quantity'];
         $temp['new_quantity'] = $_POST['new_quantity'];
         $temp['rate'] = $_POST['rate'];
         $temp['new_rate'] = $_POST['new_rate'];
         $data['username'] = $this->ion_auth->user()->row()->username;
         $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
         $return = array();
         if($temp['type']=="O")
         {
            $return = $this->orders_model->approve_order_edit($temp);
            if($return['status'] == "Data Inserted Successfully")
               echo 'Approval done succesfully for Edit in '.$return['billNo'].'';
            else
            echo 'Error : '.$return['status'].' for Bill No :'.$return['billNo'].'';
         }
         if($temp['type']=="I")
         {
            $return = $this->items_model->approve_issue_edit($temp);
            if($return['status'] == "Data Inserted Successfully")
               echo 'Approval done succesfully for Edit in '.$return['billNo'].'';
            else
            echo 'Error : '.$return['status'].' for Bill No :'.$return['billNo'].'';
            
       


         }

         if($temp['type']=="R")
         {
            $return = $this->items_model->approve_return_edit($temp);
            if($return['status'] == "Data Inserted Successfully")
               echo 'Approval done succesfully for Edit in '.$return['billNo'].'';
            else
            echo 'Error : '.$return['status'].' for Bill No :'.$return['billNo'].'';

         }

         if($temp['type']=="VT")
         {
            $return = $this->orders_model->approve_vegetable_transaction_edit($temp);
            if($return['status'] == "Data Inserted Successfully")
               echo 'Approval done succesfully for Edit in '.$return['billNo'].'';
            else
            echo 'Error : '.$return['status'].' for Bill No :'.$return['billNo'].'';
         }

      }

   }

   public function disapprove_edit()
   {
      if(!$this->ion_auth->logged_in())
                        redirect('auth/login','refresh');
            else {
               $temp=array();

         $selectedId = $_POST['t_id'];
         $data['username'] = $this->ion_auth->user()->row()->username;
         $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
         $seen = 1;
         $approved = 0;
      
         $return = $this->orders_model->update_temp_transactions($seen,$approved,$selectedId);
         
         if($return == 1)
         echo 'Disapproval done succesfully';
         else
         echo $return;
                }

   }



      


      }

