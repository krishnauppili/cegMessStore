 <?php
   defined('BASEPATH') OR exit('No direct script access allowed');

   class Orders extends CI_Controller {


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


  public function edit_order_form()
   { 

      $t_id = $this->input->post('t_id');
      $itemName= $this->input->post('item_name');
      $split_word=explode("_",$itemName);
      $itemName = $split_word[0];

      for($i=1;$i<count($split_word);$i++)
         $itemName.=' '.$split_word[$i];
      
      echo "<script>console.log('".$itemName."')</script>";
      $quantity = $this->input->post('quantity');
      $rate = $this->input->post('rate');

      $form = "
         <form name = 'edit_row' action = 'edit_order_receival' method = 'post'>
         <div class='input-field'>
         <span class='blue-text text-darken-2'>Item Name</span>
         <input type='hidden' value='".urldecode($t_id)."' id= '".$t_id."' name='modalId'/> 
         <input type='hidden' value='".urldecode($itemName)."' id= '".$itemName."' name='modalItemName'/>  
         <input type='text' value='".urldecode($itemName)."' id= '".$itemName."Disabled' name='itemNameDisabled' readonly/>
         <input type='hidden' value='".urldecode($quantity)."' id= '".$quantity."' name='modalOldQuantity'/> 
         <input type='hidden' value='".urldecode($rate)."' id= '".$rate."' name='modalOldRate'/> 

         </div>
         </div>
         <div class = 'row'>
         <div class='input-field'>
         <span class='blue-text text-darken-2'>Quantity</span>
         <input type='text' value='".urldecode($quantity)."' id='".$quantity."' name='modalQuantity'/>
         </div>
         </div>
         <div class = 'row'>
         <div class='input-field'>
         <span class='blue-text text-darken-2'>Rate</span>
         <input type='text' value='".$rate."'  id='".$rate."' name='modalRate'/>
         
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


   public function edit_order_receival()
   { 

         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else{
            $data = $this->session->flashdata('data');
            $data['title'] = "Edit Order Receival";
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();

            $this->load->view('templates/header');
            $this->load->view('templates/body',$data);
            $this->load->view('edit/edit_order_receival',$data);


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
                  redirect('orders/edit_order_receival');
               }

               $this->session->set_flashdata('data',$data);
               redirect('orders/edit_order_confirmation');
            }




         }
   }



   public function edit_order_confirmation()
   {


         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else {
            $data = $this->session->flashdata('data');

            $this->load->view('templates/header');
            $data['title']= 'Edit Order Confirmation';
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $this->load->view('templates/body',$data); 
            $this->load->view('edit/edit_order_confirmation',$data); 


            if(isset($_POST['cancel']))
            {
               $data['title'] = 'Edit process cancelled';
               redirect('orders/edit_order_receival');

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
                  redirect('orders/edit_order_receival');
               }
               else
               {
                  $data['error'] = $return;
                  console.log($return);
                  $this->session->set_flashdata('data',$data);
                  redirect('orders/edit_order_receival');
               }

            }

            $this->load->view('templates/footer');
         }

      }


   public function edit_vegetable_transactions_form()
   {

      $t_id = $this->input->post('t_id');
      $itemName= $this->input->post('item_name');
      $split_word=explode("_",$itemName);
      $itemName = $split_word[0];

      for($i=1;$i<count($split_word);$i++)
         $itemName.=' '.$split_word[$i];
      $quantity = $this->input->post('quantity');
      $rate = $this->input->post('rate');

      $form = "
         <form name = 'edit_row' action = 'edit_vegetable_transactions' method = 'post'>
         <div class='input-field'>
         <span class='blue-text text-darken-2'>Item Name</span>
         <input type='hidden' value='".urldecode($t_id)."' id= '".$t_id."' name='modalId'/> 
         <input type='hidden' value='".urldecode($itemName)."' id= '".$itemName."' name='modalItemName'/>  
         <input type='text' value='".urldecode($itemName)."' id= '".$itemName."Disabled' name='itemNameDisabled' readonly/>
         <input type='hidden' value='".urldecode($quantity)."' id= '".$quantity."' name='modalOldQuantity'/> 
         <input type='hidden' value='".urldecode($rate)."' id= '".$rate."' name='modalOldRate'/> 


         </div>
         </div>
         <div class = 'row'>
         <div class='input-field'>
         <span class='blue-text text-darken-2'>Quantity</span>
         <input type='text' value='".urldecode($quantity)."' id='".$quantity."' name='modalQuantity'/>
         </div>
         </div>
         <div class = 'row'>
         <div class='input-field'>
         <span class='blue-text text-darken-2'>Rate</span>
         <input type='text' value='".$rate."'  id='".$rate."' name='modalRate'/>
         
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


   public function edit_vegetable_transactions()
   { 

         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else{
            $data = $this->session->flashdata('data');
            $data['title'] = "Edit Vegetable Transactions";
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();

            $this->load->view('templates/header');
            $this->load->view('templates/body',$data);
            $this->load->view('edit/edit_vegetable_transactions',$data);


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
                  redirect('orders/edit_vegetable_transactions');
               }

               $this->session->set_flashdata('data',$data);
               redirect('orders/edit_vegetable_transactions_confirmation');
            }




         }
   }



   public function edit_vegetable_transactions_confirmation()
   {


         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else {
            $data = $this->session->flashdata('data');

            $this->load->view('templates/header');
            $data['title']= 'Edit Vegetable Transactions Confirmation';
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $this->load->view('templates/body',$data); 
            $this->load->view('edit/edit_order_confirmation',$data); 


            if(isset($_POST['cancel']))
            {
               $data['title'] = 'Edit process cancelled';
               redirect('orders/edit_order_receival');

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
                  redirect('orders/edit_vegetable_transactions');
               }
               else
               {
                  $data['error'] = $return;
                  console.log($return);
                  $this->session->set_flashdata('data',$data);
                  redirect('orders/edit_vegetable_transactions');
               }

            }

            $this->load->view('templates/footer');
         }

      }





  
      

      public function delete_order_details()
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else {
            $post_data = $_POST['t_id'];
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $return = $this->orders_model->delete_order_details($post_data);
            if($return == 1)
            echo 'Delete request sent succesfully';
            else
            echo $return;
         }

      }

      public function delete_vegetable_transaction_details()
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else {
            $post_data = $_POST['t_id'];
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $return = $this->orders_model->delete_vegetable_transaction_details($post_data);
            if($return == 1)
            echo 'Delete request sent succesfully';
            else
            echo $return;
         }

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


   public function edit_order_primary_form()
   {

       //$itemName= $this->input->post('t_id');
      $vendorName = $this->input->post('vendor_name');
      
      $date = $this->input->post('t_date');
      echo "<script> console.log($vendorName); </script>";
      $form = "
         <form name = 'edit_row' action = 'update_order_details' method = 'post'>
         <div class = 'row'>
         <div class='input-field'>
         <span class='blue-text text-darken-2'>Vendor Name</span>
         <input type='text' value='".urldecode($vendorName)."' id='".$vendorName."' name='modalQuantity'/>
         </div>
         </div>
         <div class = 'row'>
         <div class='input-field'>
         <span class='blue-text text-darken-2'>Amount</span>
         <input type='text' value='".$date."'  id='".$date."' name='modalAmount'/>
         
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


      public function order_receive($data="")
      {


         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else{
            $this->load->view('templates/header');
            $reload =  $this->session->flashdata('data');
            $data = $reload;
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $data['title'] = 'Order Receival';
            $data['lesser_items'] = $this->items_model->get_lesser_items();
            $tableData = $this->items_model->get_items();
            $data['tableData'] = $tableData;
            $vendorsList = json_decode($this->orders_model->get_vendors(),true);
            $data['vendors'] = $vendorsList['vendorName']; 

            if(isset($_POST['submit'])){
               $quantityAvailable = $this->input->post('quantityAvailable[]');

               $data['billNo'] = $this->input->post('billNo');
               $data['receivedDate'] = date('Y-m-d',strtotime(str_replace(',','',$this->input->post('receivedDate'))));
               $data['selectedVendor'] = $this->input->post('selectedVendor');
               $data['orderNo'] = $this->input->post('orderNo');
               $selectedItems = $this->input->post('selectedItems[]');
               $quantityAvailable = $this->input->post('quantityAvailable[]');
               $latestRate = $this->input->post('latestRate[]');
               $selectedQuantity = ($this->input->post('selectedQuantity[]'));
               
               $data['quantityAvailable'] = array();
               $data['latestRate'] = array();
               $data['selectedQuantity'] = array();
               $data['selectedItems'] = array();
               
               for($i=0;$i<count($selectedItems);$i++)
               {
                  if($selectedQuantity[$i] == '' && $latestRate[$i] == '')
                  continue;
                  array_push($data['selectedItems'],$selectedItems[$i]);
                  array_push($data['selectedQuantity'],$selectedQuantity[$i]);
                  array_push($data['latestRate'],$latestRate[$i]);
                  array_push($data['quantityAvailable'], $quantityAvailable[$i]);
               }
               if(count($data['selectedItems']) == 0)
               {
                  $data['message'] = "No values entered. Empty fields.";
                  $this->session->set_flashdata('data',$data);
                  redirect('orders/order_receive');
               }

               $return = $this->orders_model->validation_for_duplicate_entry($data,'P');
               if($return == 1)
               {
                  $data['error'] = "Duplicate entry. Please check once again";
                  $this->session->set_flashdata('data',$data);
                  redirect('orders/order_receive');


               }

               $this->session->set_flashdata('data',$data);
               redirect('orders/order_confirmation');
            }
            else
            {
               if(isset($reload) && $reload !== null)
               {

                  $this->load->view('templates/body',$data); 
                  $this->load->view('orders/order_receive',$data);

               }
               else{
                  $this->load->view('templates/body',$data); 
                  $this->load->view('orders/order_receive',$data);
               }
            }

            $this->load->view('templates/footer');
         }
      }



      public function order_confirmation()
      {


         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else {
            $data = $this->session->flashdata('data');

            $this->load->view('templates/header');
            $data['title']= 'Order Confirmation';
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $this->load->view('templates/body',$data); 
            $this->load->view('orders/order_confirmation',$data); 
            $data['selectedItems'] = $this->input->post('selectedItems[]');
            $data['selectedQuantity'] = $this->input->post('selectedQuantity[]');
            $data['selectedVendor'] = $this->input->post('selectedVendor');
            $data['latestRate'] = $this->input->post('latestRate[]');
            $data['quantityAvailable'] = $this->input->post('quantityAvailable[]');
            $data['receivedDate'] = $this->input->post('receivedDate');

            if(isset($_POST['cancel']))
            {
               $data['title'] = 'Create a new item';
               $this->session->set_flashdata('data',$data);
               redirect('orders/order_receive');

            }
            else if(isset($_POST['submit']))
            {
               $return = $this->orders_model->order_receive_model($data);
               if($return['status'] == 'Data Inserted Successfully')
               {
                  $data = array();
                  $data['error'] = "Data Inserted Successfully for Bill No :".$return['billNo'];

                  $this->session->set_flashdata('data',$data);
                  redirect('orders/order_receive');
               }
               else
               {
                  $data['error'] = $return['status']."for Bill No :".$return['billNo'];
                  console.log($return);
                  $this->session->set_flashdata('data',$data);
                  redirect('orders/order_receive');
               }

            }

            $this->load->view('templates/footer');
         }

      }

      public function get_order_history($from,$to)
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
            $orderHistory = ($this->orders_model->generate_order_history($from,$to));
             //console.log($orderHistory);
            //echo $orderHistory;
           
            echo json_encode($orderHistory);
         }


      }


      public function get_edit_order_history($from,$to)
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
            $orderHistory = ($this->orders_model->generate_edit_order_history($from,$to));
            
            echo json_encode($orderHistory);
         }


      }


      /*public function vegetable_order_history()
      {

         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else{
            $data = $this->session->flashdata('data');
            $data['title'] = "Vegetable Orders History";
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $vendors = json_decode($this->orders_model->get_vegetable_vendors(),true);
            $data['vendors'] = $vendors['vendorName'];
            $this->load->view('templates/header');
            $this->load->view('templates/body',$data);
            $this->load->view('orders/vegetable_order_history',$data);
         }
      }*/

      public function order_history()
      {

         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else{
            $data = $this->session->flashdata('data');
            $data['title'] = "Orders History";
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();



            $vendors = json_decode($this->orders_model->get_vegetable_vendors(),true);
            $data['vendors'] = $vendors['vendorName'];
            $items = json_decode($this->items_model->get_provision_items(),true);
            $data['items'] = $items['itemName'];
            $this->load->view('templates/header');
            $this->load->view('templates/body',$data);
            $this->load->view('orders/order_history',$data);
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
            $this->load->view('notifications/notification_edit_history',$data);
         }
      }


       public function add_vendor()
      {

         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else {
            $post_data = $_POST['data'];
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $vendor = json_decode($post_data,true);
            $return = $this->orders_model->add_vendor($vendor);
            if($return == 1)
            echo 'Vendor added succesfully';
            else
            echo $return;
         }

      }

      public function edit_vendor_form()
      {


         $vendorName= $this->input->post('vendorName');

         $ownerName = $this->input->post('ownerName');
         $address= $this->input->post('address');

         $contact = $this->input->post('contact');

         $form = "
         <form name = 'edit_row' action = 'update_vendor_details' method = 'post'>
            <div class='input-field'>
               <span class='blue-text text-darken-2'>Vendor Name</span>
               <input type='text' value='".urldecode($vendorName)."' id= '".$vendorName."Disabled' name='modalVendorNameDisabled' disabled/>        
               <input type='hidden' value='".urldecode($vendorName)."' id= '".$vendorName."' name='modalVendorName'/>        

            </div>
         </div>
         <div class = 'row'>
            <div class='input-field'>
               <span class='blue-text text-darken-2'>Owner Name</span>
               <input type='text' value='".urldecode($ownerName)."' id='".$ownerName."' name='modalOwnerName'/>
            </div>
         </div>
         <div class = 'row'>
            <div class='input-field'>
               <span class='blue-text text-darken-2'>Address</span>
               <input type='text' value='".urldecode($address)."' id='".$address."' name='modalAddress'/>
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

               <!--    <a href='javascript:submit_update();' class='btn waves-effect waves-light btn-large' value='submit' type='submit' name='submit'>-->
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


      public function get_vendors_list()
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else
         {
            $data['username'] = $this->ion_auth->user()->row()->username;

            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();

            $vendorsList = ($this->orders_model->get_vendors());
            echo ($vendorsList);
         }

      }


      public function update_vendor_details($data="")
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else {
            $data = $this->session->flashdata('data');
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();

            $data['ownerName'] = urldecode($this->input->post('modalOwnerName'));
            $data['address'] = urldecode($this->input->post('modalAddress'));
            $data['contact'] = $this->input->post('modalContact');
            $data['vendorName'] = urldecode($this->input->post('modalVendorName'));
            $return = $this->orders_model->update_vendor_details($data);
            if($return == 1)

            redirect('orders/vendor_details',$data);
            else
            {
               $data['error'] = $return;
               redirect('orders/vendor_details',$data);
            }

         }

      }

      public function vendor_details()
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else {
            $data['username'] = $this->ion_auth->user()->row()->username;

            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $data['title'] = " Vendors Details";
            $this->load->view('templates/header');
            $this->load->view('templates/body',$data);
            $this->load->view('orders/vendor_details',$data);
         }
      }

       public function vegetable_vendor_details()
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else {
            $data['username'] = $this->ion_auth->user()->row()->username;

            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $data['title'] = " Vendors Details";
            $this->load->view('templates/header');
            $this->load->view('templates/body',$data);
            $this->load->view('orders/vegetable_vendor_details',$data);
         }
      }


      public function get_vegetable_vendors_list()
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else
         {
            $data['username'] = $this->ion_auth->user()->row()->username;

            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();

            $vendorsList = ($this->orders_model->get_vegetable_vendors());
            echo ($vendorsList);
         }

      }

      public function edit_vegetable_vendor_form()
      {


         $vendorName= $this->input->post('vendorName');

         $ownerName = $this->input->post('ownerName');
         $address= $this->input->post('address');

         $contact = $this->input->post('contact');

         $form = "
         <form name = 'edit_row' action = 'update_vegetable_vendor_details' method = 'post'>
            <div class='input-field'>
               <span class='blue-text text-darken-2'>Vendor Name</span>
               <input type='text' value='".urldecode($vendorName)."' id= '".$vendorName."Disabled' name='modalVendorNameDisabled' disabled/>        
               <input type='hidden' value='".urldecode($vendorName)."' id= '".$vendorName."' name='modalVendorName'/>        

            </div>
         </div>
         <div class = 'row'>
            <div class='input-field'>
               <span class='blue-text text-darken-2'>Owner Name</span>
               <input type='text' value='".urldecode($ownerName)."' id='".$ownerName."' name='modalOwnerName'/>
            </div>
         </div>
         <div class = 'row'>
            <div class='input-field'>
               <span class='blue-text text-darken-2'>Address</span>
               <input type='text' value='".urldecode($address)."' id='".$address."' name='modalAddress'/>
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

               <!--    <a href='javascript:submit_update();' class='btn waves-effect waves-light btn-large' value='submit' type='submit' name='submit'>-->
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


      public function update_vegetable_vendor_details($data="")
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else {
            $data = $this->session->flashdata('data');
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();

            $data['ownerName'] = urldecode($this->input->post('modalOwnerName'));
            $data['address'] = urldecode($this->input->post('modalAddress'));
            $data['contact'] = $this->input->post('modalContact');
            $data['vendorName'] = urldecode($this->input->post('modalVendorName'));
            $return = $this->orders_model->update_vegetable_vendor_details($data);
            if($return == 1)

            redirect('orders/vegetable_vendor_details',$data);
            else
            {
               $data['error'] = $return;
               redirect('orders/vegetable_vendor_details',$data);
            }

         }

      }

      public function add_vegetable_vendor()
      {

         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else {
            $post_data = $_POST['data'];
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $vendor = json_decode($post_data,true);
            $return = $this->orders_model->add_vegetable_vendor($vendor);
            if($return == 1)
            echo 'Vendor added succesfully';
            else
            echo $return;
         }

      }

      public function delete_vendor()
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else {
            $post_data = $_POST['data'];
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $return = $this->orders_model->delete_vendor($post_data);
            if($return == 1)
            echo 'Vendor deleted succesfully';
            else
            echo $return;
         }

      }

      public function delete_vegetable_vendor()
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else {
            $post_data = $_POST['data'];
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $return = $this->orders_model->delete_vendor($post_data);
            if($return == 1)
            echo 'Vendor deleted succesfully';
            else
            echo $return;
         }

      }



      public function vegetable_order($data="")
      {


         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else{
            $this->load->view('templates/header');
            $reload =  $this->session->flashdata('data');
            $data = $reload;
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $data['title'] = 'Vegetable Orders';
            $tableData = $this->items_model->get_vegetables();
            $data['tableData'] = $tableData;
            $vendorsList = json_decode($this->orders_model->get_vegetable_vendors(),true);
            $data['vendors'] = $vendorsList['vendorName'];
            $data['messTypes'] = $this->get_mess_types();

            if(isset($_POST['submit'])){

               $quantityAvailable = $this->input->post('quantityAvailable[]');

               //$data['billNo'] = $this->input->post('billNo');
               $data['receivedDate'] = date('Y-m-d',strtotime(str_replace(',','',$this->input->post('receivedDate'))));
               $data['selectedVendor'] = urldecode($this->input->post('selectedVendor'));
               //$data['orderNo'] = $this->input->post('orderNo');
               $data['selectedMess'] = $this->input->post('selectedMess');

               $selectedItems = $this->input->post('selectedItems[]');
               $quantityAvailable = $this->input->post('quantityAvailable[]');
               $latestRate = $this->input->post('latestRate[]');
               $selectedQuantity = ($this->input->post('selectedQuantity[]'));

               $data['quantityAvailable'] = array();
               $data['latestRate'] = array();
               $data['selectedQuantity'] = array();
               $data['selectedItems'] = array();
               for($i=0;$i<count($selectedItems);$i++)
               {
                  if($selectedQuantity[$i] == '' && $latestRate[$i] == '')
                  continue;
                  array_push($data['selectedItems'],$selectedItems[$i]);
                  array_push($data['selectedQuantity'],$selectedQuantity[$i]);
                  array_push($data['latestRate'],$latestRate[$i]);
                  array_push($data['quantityAvailable'], $quantityAvailable[$i]);
               }
               if(count($data['selectedItems']) == 0)
               {
                  $data['message'] = "No values entered. Empty fields.";
                  $this->session->set_flashdata('data',$data);
                  redirect('orders/vegetable_order');
               }

               $return = $this->orders_model->validation_for_duplicate_entry($data,'V');
               if($return == 1)
               {
                   $data['error'] = "Duplicate entry. Please check once again.";
                   $this->session->set_flashdata('data',$data);
                   redirect('orders/vegetable_order');
               }



               $this->session->set_flashdata('data',$data);
               redirect('orders/vegetable_order_confirmation');
            }
            else
            {
               if(isset($reload) && $reload !== null)
               {

                  $this->load->view('templates/body',$data); 
                  $this->load->view('orders/vegetable_order',$data);

               }
               else{
                  $this->load->view('templates/body',$data); 
                  $this->load->view('orders/vegetable_order',$data);
               }
            }

            $this->load->view('templates/footer');
         }
      

      }

      public function vegetable_order_confirmation()
      {


         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else {
            $data = $this->session->flashdata('data');

            $this->load->view('templates/header');
            $data['title']= 'Vegetable Order Confirmation';
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $this->load->view('templates/body',$data); 

            $this->load->view('orders/vegetable_order_confirmation',$data);


            $data['selectedItems'] = $this->input->post('selectedItems[]');
            $data['selectedQuantity'] = $this->input->post('selectedQuantity[]');
            $data['selectedVendor'] = urldecode($this->input->post('selectedVendor'));;
            //$data['orderNo'] = $this->input->post('orderNo');
            $data['latestRate'] = $this->input->post('latestRate[]');
            //$data['billNo'] = $this->input->post('billNo');
            $data['receivedDate'] = $this->input->post('receivedDate');

            $data['selectedMess'] = $this->input->post('selectedMess');
            if(isset($_POST['cancel']))
            {
               $this->session->set_flashdata('data',$data);
               redirect('orders/vegetable_order');

            }
            else if(isset($_POST['submit']))
            {
               $return = $this->orders_model->vegetable_order_receive_model($data);

               if($return['status'] == 'Data Inserted Successfully')
               {
                  $data = array();
                  $data['error'] = "Data Inserted Successfully for Bill No :".$return['billNo'];

                  $this->session->set_flashdata('data',$data);
                  redirect('orders/vegetable_order');
               }
               else
               {
                  $data['error'] = $return['status']."for Bill No :".$return['billNo'];
                  console.log($return);
                  $this->session->set_flashdata('data',$data);
                  redirect('orders/vegetable_order');
               }

            }

            $this->load->view('templates/footer');
         }

      }

      public function vegetable_order_history()
      {

         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else{
            $data = $this->session->flashdata('data');
            $data['title'] = "Vegetable Orders History";
            $data['username'] = $this->ion_auth->user()->row()->username;
            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $vendors = json_decode($this->orders_model->get_vegetable_vendors(),true);
            $data['vendors'] = $vendors['vendorName'];
            $this->load->view('templates/header');
            $this->load->view('templates/body',$data);
            $this->load->view('orders/vegetable_order_history',$data);
         }
      }


      public function get_vegetable_order_history($from,$to, $vendorName = "")
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

            if(empty($vendorName))
            $orderHistory = ($this->orders_model->generate_vegetable_order_history($from,$to));
            else
            {
               $vendorName = urldecode($vendorName);
               $orderHistory = ($this->orders_model->generate_vegetable_order_history($from,$to, $vendorName));
            }
            echo json_encode($orderHistory);
         }


      }


      public function get_vegetable_transactions($from,$to, $vendorName = "")
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

            if(empty($vendorName))
            $vegetableTransactions = ($this->orders_model->generate_vegetable_transactions($from,$to));
            else
            {
               $vendorName = urldecode($vendorName);
               $orderHistory = ($this->orders_model->generate_vegetable_transactions($from,$to, $vendorName));
            }
            echo json_encode($vegetableTransactions);
         }


      }


      public function get_edit_vegetable_transactions($from,$to, $vendorName = "")
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

            if(empty($vendorName))
               $vegetableTransactions = ($this->orders_model->generate_edit_vegetable_transactions($from,$to));
            else
            {
               $vendorName = urldecode($vendorName);
               $orderHistory = ($this->orders_model->generate_edit_vegetable_transactions($from,$to, $vendorName));
            }
            echo json_encode($vegetableTransactions);
         }


      }


      public function check_order_exists($receivedDate,$messName)
      {
         
         $received = date('Y-m-d',strtotime(str_replace(',','',urldecode($receivedDate))));
         $mess = urldecode($messName);
         $output= $this->orders_model->check_order_exists($received,$mess);

            if($output == 0)
               echo "false";
            else
               echo "true";


      }

      public function generate_abstract()
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else{
            $data['username'] = $this->ion_auth->user()->row()->username;

            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $data['title'] = "Generate Abstract";
            $this->load->view('templates/header');
            $reload =  $this->session->flashdata('data');
            if($reload != null && $reload != "")
            $data = $data + $reload;
            $selectedOrders = $this->input->post('selectedOrders[]');

            $billNos = array();
            $vendorNames = array();
            $totalAmount = array();
            $receivedDates = array();
            if(count($selectedOrders) == 0)
            {
               $data['error'] = "Select atleast one items";
               $this->session->set_flashdata('data',$data);
               redirect('orders/order_history');
            }

            $date_to_query = '';
            $vendorId_to_query = '';

            foreach($selectedOrders as $billNo)
            {
                array_push($billNos,$billNo);
               $split_word=explode("_",$billNo);
               $date = substr_replace($split_word[0],'-',2,0); 
               $date = substr_replace($date,'-',5,0);
               $date = date("Y-m-d", strtotime($date)); 
               $vendorId = $split_word[1];
               $date_to_query .="'". $date."'".",";
               $vendorId_to_query .= $vendorId.","; 
            }
            $date_to_query = rtrim($date_to_query,",");
            $vendorId_to_query = rtrim($vendorId_to_query,",");
            $orderDetails = $this->orders_model->get_order_details($date_to_query,$vendorId_to_query);







            $tableTotal = 0;
            for($i=0;$i<count($orderDetails['amount']);$i++)
               $tableTotal += floatval($orderDetails['amount'][$i]);
            $data['billNos'] = $billNos;
            $data['vendorName'] = $orderDetails['vendorName'][0];
            $data['totalAmount'] = $orderDetails['amount'];
            $data['receivedDates'] = $orderDetails['receivedDate'];
            $data['tableTotal'] = $tableTotal;
            $this->load->view('templates/body',$data);


            if(isset($_POST['submit'])){
               if(count(array_unique($orderDetails['vendorName'])) == 1)
               {

                  $this->load->view('orders/generate_abstract',$data);
               }

               else
               {
                  $data['error'] = "You have selected different vendors";
                  $this->session->set_flashdata('data',$data);
                  redirect('orders/order_history');
               }
            }
         }
      }



      public function generate_vegetable_abstract()
      {
         if(!$this->ion_auth->logged_in())
         redirect('auth/login','refresh');
         else{
            $data['username'] = $this->ion_auth->user()->row()->username;

            $data['group'] = $this->ion_auth->get_logged_in_user_group_names();
            $data['title'] = "Generate  Vegetable Bill Abstract";
            $this->load->view('templates/header');


            $reload =  $this->session->flashdata('data');
            if($reload != null && $reload != "")
            $data = $data + $reload;

            $selectedOrders = $this->input->post('selectedOrders[]');
            
            $billNos = array();
            $vendorNames = array();
            $totalAmount = array();
            $receivedDates = array();
            if(count($selectedOrders) == 0)
            {
               $data['error'] = "Select atleast one items";
               $this->session->set_flashdata('data',$data);
               redirect('orders/vegetable_order_history');
            }


            $date_to_query = '';
            $vendorId_to_query = '';
            $messId_to_query ='';

             foreach($selectedOrders as $billNo)
            {
                array_push($billNos,$billNo);
               $split_word=explode("_",$billNo);
               $date = substr_replace($split_word[0],'-',2,0); 
               $date = substr_replace($date,'-',5,0);
               $date = date("Y-m-d", strtotime($date)); 
               $vendorId = $split_word[1];
               $messId = $split_word[2];
               $date_to_query .="'". $date."'".",";
               $vendorId_to_query .= $vendorId.",";
               $messId_to_query .= $messId.",";  
            }
            $date_to_query = rtrim($date_to_query,",");
            $vendorId_to_query = rtrim($vendorId_to_query,",");
            $messId_to_query = rtrim($messId_to_query,",");
            $orderDetails = $this->orders_model->get_vegetable_order_details($date_to_query,$vendorId_to_query,$messId_to_query);







            $tableTotal = 0;
            for($i=0;$i<count($orderDetails['amount']);$i++)
               $tableTotal += floatval($orderDetails['amount'][$i]);
            $data['billNos'] = $billNos;
            $data['vendorName'] = $orderDetails['vendorName'][0];
            $data['totalAmount'] = $orderDetails['amount'];
            $data['receivedDates'] = $orderDetails['receivedDate'];
            $data['tableTotal'] = $tableTotal;
   
            
            /*foreach($selectedOrders as $billNo)
            {
            
               
               $orderDetails = $this->orders_model->get_vegetable_order_details($billNo);
               array_push($billNos,$billNo);
               array_push($vendorNames,$orderDetails['vendorName'][0]);
               $total = 0;
               
               foreach($orderDetails['amount'] as $amount)
               $total += $amount;
               array_push($totalAmount,$total);
               array_push($receivedDates,$orderDetails['receivedDate'][0]);
            }
            

            $data['billNos'] = $billNos;
            $data['vendorName'] = $vendorNames[0];
            $data['totalAmount'] = $totalAmount;
            $data['receivedDates'] = $receivedDates;*/
            $this->load->view('templates/body',$data);


            if(isset($_POST['submit'])){
               if(count(array_unique($orderDetails['vendorName'])) == 1)
               {

                  $this->load->view('orders/generate_vegetable_abstract',$data);
               }

               else
               {
                  $data['error'] = "You have selected different vendors";
                  $this->session->set_flashdata('data',$data);
                  redirect('orders/vegetable_order_history');
               }
            }
         }
      }



      





}

