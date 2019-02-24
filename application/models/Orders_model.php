<?php
class Orders_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->db->db_debug = TRUE;
		$this->load->model('items_model');
	}

	public function get_vendors()
	{

		$return['vendorName'] = array();
		$return['vendorId'] = array();
		$return['ownerName'] = array();
		$return['address'] = array();
		$return['contact'] = array();
		$this->db->where('category','Provision');
		$this->db->where('functioning','1');
		$vendors = $this->db->get('vendor_details');
		foreach($vendors->result() as $row){

			array_push($return['vendorName'],$row->vendor_name);
			array_push($return['vendorId'],$row->vendor_id);
			array_push($return['ownerName'],$row->owner_name);
			array_push($return['address'],$row->address);
			array_push($return['contact'],$row->contact);
		}
		return json_encode(array("vendorName" => $return['vendorName'],
					"vendorId" => $return['vendorId'],
					"ownerName" => $return['ownerName'],
					"address" => $return['address'],
					"contact" => $return['contact']));

	}


	public function get_vegetable_vendors()
	{

		$return['vendorName'] = array();
		$return['ownerName'] = array();
		$return['address'] = array();
		$return['contact'] = array();
		$this->db->where('functioning','1');
		$this->db->where('category','Vegetable');
		$vendors = $this->db->get('vendor_details');
		foreach($vendors->result() as $row){

			array_push($return['vendorName'],$row->vendor_name);
			array_push($return['ownerName'],$row->owner_name);
			array_push($return['address'],$row->address);
			array_push($return['contact'],$row->contact);
		}
		return json_encode(array("vendorName" => $return['vendorName'],
					"ownerName" => $return['ownerName'],
					"address" => $return['address'],
					"contact" => $return['contact']));

	}



	public function get_max_vendor_id()
	{
		$query = "SELECT max(vendor_id) as max_vendor_id from vendor_details";
		$vendors = $this->db->query($query);
		foreach($vendors->result() as $row)
    		$max_vendor_id = $row->max_vendor_id;
    	return $max_vendor_id;

	}

	public function add_vegetable_vendor($data)
	{	$max_vendor_id = $this->orders_model->get_max_vendor_id();
		$max_vendor_id = $max_vendor_id +1;
		$data['vendorName']=urldecode($data['vendorName']);
		if(preg_match('/[\'^£$%&*()}.{@#~?><>,|=_+¬-]/', $data['vendorName'])||(strpos($data['vendorName'], '/')!==false)||(strpos($data['vendorName'], '\\')!==false))
          {
              return "No special characters allowed for Vendor Name";
          }
          if(strlen($data['vendorName'])>=200)
          {
              return "Vendor name should not be greater than 200";
                        
          }
        $data['vendorName']=trim($data['vendorName']);
		$insert = array( "vendor_id" => $max_vendor_id,
				"vendor_name" => strtoupper($data['vendorName']),
				"category" =>  'Vegetable',
				"owner_name" => strtoupper(urldecode($data['ownerName'])),
				"address" => strtoupper(urldecode($data['address'])),
				"contact" => strtoupper(urldecode($data['contact'])),
				"functioning" => '1'
				);
		$this->db->trans_start();
		if(!$this->db->insert('vendor_details',$insert))
		{
			$error=$this->db->error();
			$this->db->trans_complete();
			return $error['message'];
		}
		$this->db->trans_complete();
		return 1;

	}


	public function add_vendor($data)
	{	$max_vendor_id = $this->orders_model->get_max_vendor_id();
		$max_vendor_id = $max_vendor_id +1;
		$data['vendorName']=urldecode($data['vendorName']);
		if(preg_match('/[\'^£$%&*()}.{@#~?><>,|=_+¬-]/', $data['vendorName'])||(strpos($data['vendorName'], '/')!==false)||(strpos($data['vendorName'], '\\')!==false))
          {
              return "No special characters allowed for Vendor Name";
          }
          if(strlen($data['vendorName'])>=200)
          {
              return "Vendor name should not be greater than 200";
                        
          }
        $data['vendorName']=trim($data['vendorName']);

		$insert = array( "vendor_id" => $max_vendor_id,
				"vendor_name" => strtoupper($data['vendorName']),
				"category" =>  'Provision',
				"owner_name" => strtoupper(urldecode($data['ownerName'])),
				"address" => strtoupper(urldecode($data['address'])),
				"contact" => strtoupper(urldecode($data['contact'])),
				"functioning" => '1'
				);
		$this->db->trans_start();
		if(!$this->db->insert('vendor_details',$insert))
		{
			$error=$this->db->error();
			$this->db->trans_complete();
			return $error['message'];
		}
		$this->db->trans_complete();
		return 1;

	}

	

	public function delete_vendor($vendorName)
	{
		$this->db->where('vendor_name',$vendorName);
		$this->db->set('functioning','0');
		$this->db->trans_start();
		if(!$this->db->update('vendor_details'))
		{
			$error=$this->db->error();
			$this->db->trans_complete();
			return $error['message'];
		}
		$this->db->trans_complete();
		return 1;

	}

	

	public function update_vendor_details($data)
	{	
		$this->db->trans_start();
		$this->db->where('vendor_name',$data['vendorName']);
		$this->db->set('contact',strtoupper(urldecode($data['contact'])));

		$this->db->set('owner_name',strtoupper(urldecode($data['ownerName'])));
		$this->db->set('address',strtoupper(urldecode($data['address'])));


		if(!$this->db->update('vendor_details'))
		{
			$error=$this->db->error();

			$this->db->trans_complete();
			return $error['message'];
		}
		else
		{

			$this->db->trans_complete();
			return 1;
		}

	}

	public function update_vegetable_vendor_details($data)
	{	
		$this->db->trans_start();
		$this->db->where('vendor_name',$data['vendorName']);
		$this->db->set('contact',strtoupper(urldecode($data['contact'])));

		$this->db->set('owner_name',strtoupper(urldecode($data['ownerName'])));
		$this->db->set('address',strtoupper(urldecode($data['address'])));


		if(!$this->db->update('vendor_details'))
		{
			$error=$this->db->error();

			$this->db->trans_complete();
			return $error['message'];
		}
		else
		{

			$this->db->trans_complete();
			return 1;
		}

	}



	public function get_order_details($date_to_query,$vendorId_to_query)
	{

		$query = "SELECT ROUND(sum(t.amount),2) as 'total_amount',t.vendor_name as 'vendor_name',CAST(t.t_date AS DATE) as 'received_date' from transactions t 
					inner join vendor_details v on t.vendor_name = v.vendor_name
		where t.t_type='O' and t.t_date IN (".$date_to_query.") and v.vendor_id IN (".$vendorId_to_query.") group by received_date,vendor_name";
		$this->db->trans_start();
		$orderDetails = $this->db->query($query);
		$this->db->trans_complete();

		$output['vendorName'] = array();
		$output['amount'] = array();
		$output['receivedDate'] = array();

		foreach($orderDetails->result() as $row)
		{
			array_push($output['vendorName'],$row->vendor_name);
            array_push($output['receivedDate'],date('d-m-Y',strtotime($row->received_date)));
			array_push($output['amount'],$row->total_amount);
		}
		return $output;
	}


	public function get_vegetable_order_details($date_to_query,$vendorId_to_query,$messId_to_query)
	{
		$query = "select a.vendorName as 'vName',a.receivedDate as 'rDate', ROUND(sum(a.amount),2) as 'tAmount' from (SELECT t2.t_date as 'receivedDate', t2.vendor_name as 'vendorName',t2.mess_name as 'messName', t1.min_rate * t2.quantity as 'amount' from
			(SELECT t_id,t_date, item_name,vendor_name,mess_name,amount, quantity from transactions where t_date IN (".$date_to_query.") and t_type='VT') t2
 			INNER JOIN
 			mess_details m 
 			ON m.mess_name = t2.mess_name
 			AND m.mess_id IN (".$messId_to_query.")
 			INNER JOIN
 			vendor_details v
 			ON v.vendor_name = t2.vendor_name
 			AND v.vendor_id IN(".$vendorId_to_query.")
 			INNER JOIN
 			(select t_date,min(amount/quantity) as min_rate,item_name from transactions where t_date IN (".$date_to_query.") and t_type='VT' group by item_name, t_date) t1 
			ON t1.t_date = t2.t_date AND t1.item_name = t2.item_name) a 
 			group by vName,rDate,a.messName order by rDate desc"   ;
		$this->db->trans_start();
		$orderDetails = $this->db->query($query);
		$this->db->trans_complete();

		//$output['t_id'] = array();
		$output['vendorName'] = array();
		//$output['itemName'] = array();
		//$output['quantityReceived'] = array();
		//$output['rate'] = array();
		$output['amount'] = array();
		$output['receivedDate'] = array();

		foreach($orderDetails->result() as $row)
		{
			//array_push($output['t_id'], $row->t_id);
			array_push($output['vendorName'],$row->vName);
			//array_push($output['itemName'],$row->item_name);
			//array_push($output['quantityReceived'],$row->quantity);
            array_push($output['receivedDate'],date('d-m-Y',strtotime($row->rDate)));
			//array_push($output['rate'],$row->rate);
			array_push($output['amount'],$row->tAmount);
		}
		return $output;
	}



	/*public function generate_order_history($from,$to)
	{
		$query="SELECT t.t_id as 't_id',t.item_name as 'itemName',t.quantity as 'quantityReceived',t.amount as 'amount',t.vendor_name as 'vendorName', t.t_date as 'receivedDate' from transactions t 
             where t.t_type='O' and (t.t_date>= '".$from."' and t.t_date <='".$to."') group by t.vendor_name, t.t_date order by t.t_date,vendor_name;";

        $output = array();
			 
		$resultant = $this->db->query($query);

		$resultantRow = $resultant->first_row();
		
		if(isset($resultantRow))
		{
			
			do
			{
				
				$temp['t_id'] = $resultantRow->t_id;
				$temp['vendor_name'] = $resultantRow->vendorName;
 				$temp['t_date'] = date('d-m-Y',strtotime($resultantRow->receivedDate));
			
				$temp['items'] = array();

			
					do
					{
						$items = array();
						$items['t_id'] = $resultantRow->t_id;
						$items['item_name'] = $resultantRow->itemName;
						$items['quantity'] = $resultantRow->quantityReceived;
						$items['rate'] = $resultantRow->amount / $resultantRow->quantityReceived;
						$items['amount'] = $resultantRow->amount;
						array_push($temp['items'],$items);
						$receivedDate = date('d-m-Y',strtotime($resultantRow->receivedDate));
						$vendor_name = $resultantRow->vendorName;
				
					}while (($resultantRow = $resultant->next_row()) and ($receivedDate == date('d-m-Y',strtotime($resultantRow->receivedDate))) and ($vendor_name == $resultantRow->vendorName));

				array_push($output,$temp);

			}while ($resultantRow);
		}

		return ($output);

	}*/


	public function generate_order_history($from,$to)
	{
		$query="SELECT t.t_id as 't_id',t.item_name as 'itemName',t.quantity as 'quantityReceived',ROUND(t.amount,2) as 'amount',t.vendor_name as 'vendorName', t.t_date as 'receivedDate' from transactions t 
             where t.t_type='O' and (t.t_date>= '".$from."' and t.t_date <='".$to."') order by t.t_date,vendor_name;";

        $output = array();
			 
		$resultant = $this->db->query($query);

		$resultantRow = $resultant->first_row();
		
		if(isset($resultantRow))
		{
			
			do
			{
				
				$temp['t_id'] = $resultantRow->t_id;
				$temp['vendor_name'] = $resultantRow->vendorName;
 				$temp['t_date'] = date('d-m-Y',strtotime($resultantRow->receivedDate));
			
				$temp['items'] = array();

			
					do
					{
						$items = array();
						$items['t_id'] = $resultantRow->t_id;
						$items['item_name'] = $resultantRow->itemName;
						$items['quantity'] = $resultantRow->quantityReceived;
						if($items['quantity'] > 0)
							$items['rate'] = $resultantRow->amount / $resultantRow->quantityReceived;
						else
							$items['rate'] = 0;
						$items['amount'] = $resultantRow->amount;
						array_push($temp['items'],$items);
						$receivedDate = date('d-m-Y',strtotime($resultantRow->receivedDate));
						$vendor_name = $resultantRow->vendorName;
				
					}while (($resultantRow = $resultant->next_row()) and ($receivedDate == date('d-m-Y',strtotime($resultantRow->receivedDate))) and ($vendor_name == $resultantRow->vendorName));

				array_push($output,$temp);

			}while ($resultantRow);
		}

		return ($output);
	
	}


	public function generate_edit_order_history($from,$to)
	{

		$query="SELECT t.t_id as 't_id',t.item_name as 'itemName',t.quantity as 'quantityReceived',t.amount as 'amount',t.vendor_name as 'vendorName', t.t_date as 'receivedDate' from transactions t  
			left join 
			(select t_id from temp_transactions where seen = 0) temp 
			on t.t_id = temp.t_id 
			where t.t_type='O' and (t.t_date>= '".$from."' and t.t_date <='".$to."') and temp.t_id 
			is null order by t.t_date,t.vendor_name";

        $output = array();
			 
		$resultant = $this->db->query($query);

		$resultantRow = $resultant->first_row();
		
		if(isset($resultantRow))
		{
			
			do
			{
				
				$temp['t_id'] = $resultantRow->t_id;
				$temp['vendor_name'] = $resultantRow->vendorName;
 				$temp['t_date'] = date('d-m-Y',strtotime($resultantRow->receivedDate));
			
				$temp['items'] = array();

			
					do
					{
						$items = array();
						$items['t_id'] = $resultantRow->t_id;
						$items['item_name'] = $resultantRow->itemName;
						$items['quantity'] = $resultantRow->quantityReceived;
						if($items['quantity'] > 0)
							$items['rate'] = $resultantRow->amount / $resultantRow->quantityReceived;
						else
							$items['rate'] = 0;
						$items['amount'] = $resultantRow->amount;
						array_push($temp['items'],$items);
						$receivedDate = date('d-m-Y',strtotime($resultantRow->receivedDate));
						$vendor_name = $resultantRow->vendorName;
				
					}while (($resultantRow = $resultant->next_row()) and ($receivedDate == date('d-m-Y',strtotime($resultantRow->receivedDate))) and ($vendor_name == $resultantRow->vendorName));

				array_push($output,$temp);

			}while ($resultantRow);
		}

		return ($output);
	}

	public function validation_for_duplicate_entry($data,$type)
	{
		$return = 0;
		$itemNames = "'".$data['selectedItems'][0]."'";
		$temp=array();
		for($i=1;$i<count($data['selectedItems']);$i++)
			$itemNames.=",'".$data['selectedItems'][$i]."'";
		if($type = 'P')
		$query = "SELECT vendor_name,t_date,item_name from transactions where t_type = 'O' and mess_name = 'HOSTEL STORES' and vendor_name = '".$data['selectedVendor']."' and t_date = '".$data['receivedDate']."' and item_name in (".$itemNames.")";
		if($type = 'V')
		$query = "SELECT vendor_name,t_date,item_name from transactions where t_type = 'VT' and mess_name = '".$data['selectedMess']."'and vendor_name = '".$data['selectedVendor']."' and t_date = '".$data['receivedDate']."' and item_name in (".$itemNames.")";

		$result =$this->db->query($query);
		foreach($result->result() as $row)
			$temp = $row->item_name;
		if(count($temp)>0)
			return 1;
		else
			return 0;	

	}

	public function order_receive_model($data)
	{

		$selectedItems = $data['selectedItems'][0];
		$selectedQuantity = $data['selectedQuantity'][0];
		$latestRate = $data['latestRate'][0];
		$receivedDate = $data['receivedDate'];
		$selectedVendor = $data['selectedVendor'];

		for ($i = 1 ; $i < count($data['selectedItems']) ; $i++)
		{
			$selectedItems .= ','.$data['selectedItems'][$i];
			$selectedQuantity .= ','.$data['selectedQuantity'][$i];
			$latestRate .= ','.$data['latestRate'][$i];
		}

		$query = "CALL insert_order ( '".$selectedVendor."','".$selectedItems."','".$receivedDate."','".$selectedQuantity."','".$latestRate."','O',@query,@status,@billNo);";
		
		$query .= "SELECT @status,@query,@billNo;";
		
		$mysqli = new mysqli("localhost","root","HostelStore12345","ceg_mess_store");

		$temp = array();


		$mysqli->begin_transaction();

		if ($mysqli->multi_query($query)) {
    	
    		do {
   	
   	        	if ($result = $mysqli->store_result()) {
           
           		 	while ($row = $result->fetch_row()) {
            			
            			$temp['status'] = $row[0];
            			
            			if (isset($row[1]))
            				$temp['query'] = $row[1];

            			if (isset($row[2]))
            				$temp['billNo'] = $row[2];


            		}
            		$result->free();
        		}

        		if($mysqli->more_results())
        		{

        		}

        		
    		} while ($mysqli->more_results() && $mysqli->next_result());
		}

  
		if($temp['status'] == 'success')
		{
			$mysqli->commit();
			$temp['status'] = 'Data Inserted Successfully';

		}

			
		
		else
		{
			$mysqli->rollback();
		}

		$query1 = "CALL write_log(\"".$temp['query']."\");";

		if($mysqli->multi_query($query1)){
			do
			{
				if($result = $mysqli->store_result()){
					while ($row = $result->fetch_row()) {

					}
					$result->free();
				}

         		if ($mysqli->more_results()) {
        
            	}

			}while ($mysqli->more_results() && $mysqli->next_result());

		}
		//if($mysqli->query($query))
			//$test_variable1 .= '\ndetails logged';

		$mysqli->close();

		return $temp;	

	}

	


	public function vegetable_order_receive_model($data)
	{
		$itemNames = $data['selectedItems'][0];
		$selectedQuantity = $data['selectedQuantity'][0];
		$latestRate = $data['latestRate'][0];
		$selectedVendor = $data['selectedVendor'];
		$selectedMess = $data['selectedMess'];
		$receivedDate = $data['receivedDate'];
		for($i = 1 ; $i < count($data['selectedItems']); $i++)
		{
			$itemNames .= ','.$data['selectedItems'][$i];
			$selectedQuantity .= ','.$data['selectedQuantity'][$i];
			$latestRate .= ','.$data['latestRate'][$i];
			
		}

		$query = "CALL vegetable_transactions('".$selectedMess."','".$selectedVendor."','".$receivedDate."','".$itemNames."','".$selectedQuantity."','".$latestRate."','insert',@query,@status,@billNo);";

		$query .= "SELECT @status,@query,@billNo;";
		
		$mysqli = new mysqli("localhost","root","HostelStore12345","ceg_mess_store");

		$temp = array();


		$mysqli->begin_transaction();

		if ($mysqli->multi_query($query)) {
    	
    		do {
   	
   	        	if ($result = $mysqli->store_result()) {
           
           		 	while ($row = $result->fetch_row()) {
            			
            			$temp['status'] = $row[0];
            			
            			if (isset($row[1]))
            				$temp['query'] = $row[1];

            			if (isset($row[2]))
            				$temp['billNo'] = $row[2];


            		}
            		$result->free();
        		}

        		if($mysqli->more_results())
        		{

        		}

        		
    		} while ($mysqli->more_results() && $mysqli->next_result());
		}

  
		if($temp['status'] == 'success')
		{
			$mysqli->commit();
			$temp['status'] = 'Data Inserted Successfully';

		}

			
		
		else
		{
			$mysqli->rollback();
		}

		$query1 = "CALL write_log(\"".$temp['query']."\");";

		if($mysqli->multi_query($query1)){
			do
			{
				if($result = $mysqli->store_result()){
					while ($row = $result->fetch_row()) {

					}
					$result->free();
				}

         		if ($mysqli->more_results()) {
        
            	}

			}while ($mysqli->more_results() && $mysqli->next_result());

		}
		//if($mysqli->query($query))
			//$test_variable1 .= '\ndetails logged';

		$mysqli->close();

		return $temp;	
	
	}


	/*public function generate_vegetable_order_history($from,$to, $vendorName = "")
	{

		
		$output = array();


		$query = "SELECT t_id,vendor_name as 'vendorName',CAST(t_date as DATE) as 'suppliedDate' from transactions where vendor_name = '".$vendorName."' and t_date >= '".$from."' and t_date <= '".$to."' and t_type='VT' group by vendor_name, t_date";

		$orders = $this->db->query($query);

	
		foreach($orders->result() as $row)
		{

			$query1 = "SELECT t2.t_id,t2.item_name as 'itemName', t2.quantity as 'quantityReceived',(t2.amount/t2.quantity) 
			as 'proposedRate' ,t1.min_rate as 'actualRate',(t1.min_rate * t2.quantity) as 'amount' from
			(select t_date,min(amount/quantity) as min_rate,item_name from transactions group by item_name, t_date) t1 
			INNER JOIN 
			(SELECT t_id,t_date, item_name,vendor_name,mess_name,amount, quantity from transactions where  t_date ='".$row->suppliedDate."' and vendor_name = '".$vendorName."') t2 
			ON t1.t_date = t2.t_date AND t1.item_name = t2.item_name
			INNER JOIN
			(SELECT item_name,category from items) i1
			ON i1.item_name = t1.item_name AND
			i1.category = 'Vegetable'";
			$temp['t_id'] = $row->t_id;
			$temp['vendorName'] = $vendorName;
			$temp['receivedDate'] = date('d-m-Y',strtotime($row->suppliedDate));
			
			$temp['items'] = array();
			
			$itemsObj = $this->db->query($query1);
			foreach($itemsObj->result() as $itemsRow){
				$items = array();
				$items['t_id'] = $itemsRow->t_id;
				$items['itemName'] = $itemsRow->itemName;
				$items['quantityReceived'] = $itemsRow->quantityReceived;
				$items['rate'] = $itemsRow->actualRate;
				$items['proposedRate'] = $itemsRow->proposedRate;
				$items['amount'] = $itemsRow->amount;
				array_push($temp['items'],$items);
			}
			array_push($output,$temp);
		}

		return ($output);
	}*/


	public function generate_vegetable_order_history($from,$to, $vendorName = "")
	{


		$output = array();
		
		$query1 = "SELECT t2.t_id as 't_id',t2.mess_name as 'messName',t2.vendor_name as 'vendorName',t2.item_name as 'itemName', t2.quantity as 'quantityReceived',t2.t_date as 'receivedDate',ROUND((t2.amount/t2.quantity),2) 
			as 'proposedRate' ,ROUND(((t1.min_rate * t2.quantity)/t2.quantity),2) as 'actualRate',
			ROUND((t1.min_rate * t2.quantity),2) as 'amount' 
			from
			(select t_date,min(amount/quantity) as min_rate,item_name from transactions where t_date >= '".$from."' and t_date <= '".$to."' and t_type='VT' group by item_name, t_date) t1 
			INNER JOIN 
			(SELECT t_id,t_date, item_name,vendor_name,mess_name,amount, quantity from transactions where t_date >= '".$from."' and t_date <= '".$to."' and vendor_name = '".$vendorName."' and t_type='VT') t2 
			ON t1.t_date = t2.t_date AND t1.item_name = t2.item_name
            order by receivedDate,messName,itemName";

			$itemsObj = $this->db->query($query1);

			$itemsRow = $itemsObj->first_row();

		

			if(isset($itemsRow))
			{
			
				do
				{
			
					$temp['t_id'] = $itemsRow->t_id;
					$temp['vendorName'] = $itemsRow->vendorName;
					$temp['messName'] = $itemsRow->messName;
 					$temp['receivedDate'] = date('d-m-Y',strtotime($itemsRow->receivedDate));
			
					$temp['items'] = array();

			
					do
					{
						$items = array();
						$items['t_id'] = $itemsRow->t_id;
						$items['itemName'] = $itemsRow->itemName;
						$items['quantityReceived'] = $itemsRow->quantityReceived;
						$items['rate'] = $itemsRow->actualRate;
						$items['proposedRate'] = $itemsRow->proposedRate;
						$items['amount'] = $itemsRow->amount;
						array_push($temp['items'],$items);
						$receivedDate = date('d-m-Y',strtotime($itemsRow->receivedDate));
						$vendor_name = $itemsRow->vendorName;
						$mess_name = $itemsRow->messName;
						
				
					}while (($itemsRow = $itemsObj->next_row()) and ($receivedDate == date('d-m-Y',strtotime($itemsRow->receivedDate))) and ($vendor_name == $itemsRow->vendorName) and ($mess_name == $itemsRow->messName) );

				array_push($output,$temp);

			}while ($itemsRow);
		}

		return ($output);
	}


	public function generate_edit_vegetable_transactions($from,$to, $vendorName = "")
	{

		$output = array();
		
		$query1 = "SELECT t2.t_id as 't_id',t2.mess_name as 'messName',t2.vendor_name as 'vendorName',t2.item_name as 'itemName', t2.quantity as 'quantityReceived',t2.t_date as 'receivedDate',(t2.amount/t2.quantity) 
			as 'proposedRate' ,t2.amount as 'amount'
			from transactions t2 
			LEFT JOIN 
			(select t_id from temp_transactions where seen = 0)temp
			on t2.t_id = temp.t_id
			where t2.t_date >= '".$from."' and t2.t_date <= '".$to."' and t2.t_type='VT' 
			and temp.t_id is null order by receivedDate,vendorName,messName";

			$itemsObj = $this->db->query($query1);

			$itemsRow = $itemsObj->first_row();

		

			if(isset($itemsRow))
			{
			
				do
				{
			
					$temp['t_id'] = $itemsRow->t_id;
					$temp['vendorName'] = $itemsRow->vendorName;
					$temp['messName'] = $itemsRow->messName;
 					$temp['receivedDate'] = date('d-m-Y',strtotime($itemsRow->receivedDate));
			
					$temp['items'] = array();

			
					do
					{
						$items = array();
						$items['t_id'] = $itemsRow->t_id;
						$items['itemName'] = $itemsRow->itemName;
						$items['quantityReceived'] = $itemsRow->quantityReceived;
						$items['proposedRate'] = $itemsRow->proposedRate;
						$items['amount'] = $itemsRow->amount;
						array_push($temp['items'],$items);
						$receivedDate = date('d-m-Y',strtotime($itemsRow->receivedDate));
						$vendor_name = $itemsRow->vendorName;
						$mess_name = $itemsRow->messName;
				
					}while (($itemsRow =$itemsObj->next_row()) and ($receivedDate == date('d-m-Y',strtotime($itemsRow->receivedDate))) and ($vendor_name == $itemsRow->vendorName) and ($mess_name == $itemsRow->messName));


				array_push($output,$temp);

			}while ($itemsRow);
		}

		return ($output);

		/*$output = array();

		$query = "select vendor_name as 'vendorName',CAST(t_date as DATE) as 'suppliedDate' , mess_name from transactions where t_date >= '".$from."' and t_date <= '".$to."' and t_type = 'VT' group by vendor_name, t_date,mess_name";

		$orders = $this->db->query($query);

		foreach($orders->result() as $row)
		{

			$query1 = "SELECT t.t_id,t.item_name,t.quantity,t.amount/t.quantity as 'rate', t.amount from transactions t 
						LEFT JOIN 
						(select t_id from temp_transactions where seen = 0)temp
						on t.t_id = temp.t_id
						where t.t_type ='VT' and t.vendor_name = '".$row->vendorName."' and t.t_date = '".$row->suppliedDate."' and t.mess_name = '".$row->mess_name."' and temp.t_id is null";

			$temp['vendorName'] = $row->vendorName;
			$temp['receivedDate'] = date('d-m-Y',strtotime($row->suppliedDate));
			$temp['messName'] = $row->mess_name;
			$temp['items'] = array();
			
			$itemsObj = $this->db->query($query1);
			foreach($itemsObj->result() as $itemsRow){
				$items = array();
				$items['id'] = $itemsRow->t_id;
				$items['itemName'] = $itemsRow->item_name;
				$items['quantityReceived'] = $itemsRow->quantity;
				$items['proposedRate'] = $itemsRow->rate;
				$items['amount'] = $itemsRow->amount;
				array_push($temp['items'],$items);
			}
			array_push($output,$temp);
		}

		return ($output);*/
	}


	public function check_order_exists($receivedDate,$messName)
	{
		$this->db->select('*');
		$this->db->where('date',$receivedDate);
		$this->db->where('messName',$messName);
		$this->db->trans_start();
		$output = $this->db->get('messVegetableBill');
		$this->db->trans_complete();

		
			if($output->num_rows() > 0)
				return 1;
			else
				return 0;
		

	}

	public function update_edit_details($data)
	{
		$data['editedAmount'] = $data['editedQuantity'] * $data['editedRate'];
		$query = "INSERT into temp_transactions(notification_type,t_id,t_type,item_name,quantity,new_quantity,amount,new_amount,t_date,mess_name,vendor_name,entry_time)
			select 'Edit',t_id,t_type,item_name,quantity,".$data['editedQuantity'].",amount,".$data['editedAmount'].",t_date,mess_name,vendor_name,NOW() from transactions where t_id = '".$data['t_id']."'";

		$this->db->trans_start();
		if(!($this->db->query($query)))
		{
			$error=$this->db->error();
			$this->db->trans_complete();
			return $error['message'];
		}
		$this->db->trans_complete();
		return 1;


	}

	public function update_vegetable_transaction_details($data)
	{
		$query = "insert into temp_transactions(notification_type,t_id,t_type,item_name,quantity,new_quantity,amount,new_amount,t_date,mess_name,vendor_name,entry_time)
			select 'Edit',t_id,t_type,item_name,quantity,".$data['quantity'].",amount,".$data['amount'].",t_date,mess_name,vendor_name,NOW() from transactions where t_id = '".$data['t_id']."'";

		$this->db->trans_start();
		$this->db->query($query);
		$this->db->trans_complete();
		return 1;


	}


	public function delete_order_details($t_id)
	{
		$query = "insert into temp_transactions(notification_type,t_id,t_type,item_name,quantity,new_quantity,amount,new_amount,t_date,mess_name,vendor_name,entry_time)
			select 'Delete',t_id,'O',item_name,quantity,NULL,amount,NULL,t_date,mess_name,vendor_name,NOW() from transactions where t_id = '".$t_id."'";

		$this->db->trans_start();
		$this->db->query($query);
		$this->db->trans_complete();
		return 1;


	}

	public function delete_vegetable_transaction_details($t_id)
	{
		$query = "insert into temp_transactions(notification_type,t_id,t_type,item_name,quantity,new_quantity,amount,new_amount,t_date,mess_name,vendor_name,entry_time)
			select 'Delete',t_id,'VT',item_name,quantity,NULL,amount,NULL,t_date,mess_name,vendor_name,NOW() from transactions where t_id = '".$t_id."'";

		$this->db->trans_start();
		$this->db->query($query);
		$this->db->trans_complete();
		return 1;


	}


	public function approve_order_edit($data)
	{

		$selectedId = $data['t_id'];
		$date = date('Y-m-d',strtotime($data['date']));
		$type = $data['type'];
		$notification_type = $data['notification_type'];
		$vendor_name = $data['vendor_name'];
		$mess_name = $data['mess_name'];
		$item_name = $data['item_name'];
		$new_quantity = $data['new_quantity'];
		$new_rate = $data['new_rate'];

		$query = "CALL edit_order ( '".$vendor_name."','".$item_name."','".$date."','".$new_quantity."','".$new_rate."','".$selectedId."','".$type."','".$notification_type."',@query,@status,@billNo);";
		
		
		$query .= "SELECT @status,@query,@billNo;";
		
		$mysqli = new mysqli("localhost","root","HostelStore12345","ceg_mess_store");

		$temp = array();


		$mysqli->begin_transaction();

		if ($mysqli->multi_query($query)) {
    	
    		do {
   	
   	        	if ($result = $mysqli->store_result()) {
           
           		 	while ($row = $result->fetch_row()) {
            			
            			$temp['status'] = $row[0];
            			
            			if (isset($row[1]))
            				$temp['query'] = $row[1];

            			if (isset($row[2]))
            				$temp['billNo'] = $row[2];


            		}
            		$result->free();
        		}

        		if($mysqli->more_results())
        		{

        		}

        		
    		} while ($mysqli->more_results() && $mysqli->next_result());
		}

  
		if($temp['status'] == 'success')
		{	
			
			
			
				$mysqli->commit();
				$temp['status'] = 'Data Inserted Successfully';
			

		}

		else
		{
			$mysqli->rollback();
		}

		$query1 = "CALL write_log(\"".$temp['query']."\");";

		if($mysqli->multi_query($query1)){
			do
			{
				if($result = $mysqli->store_result()){
					while ($row = $result->fetch_row()) {

					}
					$result->free();
				}

         		if ($mysqli->more_results()) {
        
            	}

			}while ($mysqli->more_results() && $mysqli->next_result());

		}


		$mysqli->close();

		return $temp;

	}



	public function approve_vegetable_transaction_edit($data)
	{

		$selectedId = $data['t_id'];
		$date = date('Y-m-d',strtotime($data['date']));
		$type = $data['type'];
		$notification_type = $data['notification_type'];
		$vendor_name = $data['vendor_name'];
		$mess_name = $data['mess_name'];
		$item_name = $data['item_name'];
		$new_quantity = $data['new_quantity'];
		$new_rate = $data['new_rate'];

		$query = "CALL vegetable_transactions('".$mess_name."','".$vendor_name."','".$date."','".$item_name."','".$new_quantity."','".$new_rate."','".$notification_type."',@query,@status,@billNo);";
		
		
		$query .= "SELECT @status,@query,@billNo;";
		
		$mysqli = new mysqli("localhost","root","HostelStore12345","ceg_mess_store");

		$temp = array();


		$mysqli->begin_transaction();

		if ($mysqli->multi_query($query)) {
    	
    		do {
   	
   	        	if ($result = $mysqli->store_result()) {
           
           		 	while ($row = $result->fetch_row()) {
            			
            			$temp['status'] = $row[0];
            			
            			if (isset($row[1]))
            				$temp['query'] = $row[1];

            			if (isset($row[2]))
            				$temp['billNo'] = $row[2];


            		}
            		$result->free();
        		}

        		if($mysqli->more_results())
        		{

        		}

        		
    		} while ($mysqli->more_results() && $mysqli->next_result());
		}

  
		if($temp['status'] == 'success')
		{	
			$seen = 1;
			$approved = 1;
			$return1 = $this->orders_model->update_temp_transactions($seen,$approved,$selectedId);
			if($return1 == 1) 
			{
				$mysqli->commit();
				$temp['status'] = 'Data Inserted Successfully';
			}

		}	
		
		else
		{
			$mysqli->rollback();
		}

		$query1 = "CALL write_log(\"".$temp['query']."\");";

		if($mysqli->multi_query($query1)){
			do
			{
				if($result = $mysqli->store_result()){
					while ($row = $result->fetch_row()) {

					}
					$result->free();
				}

         		if ($mysqli->more_results()) {
        
            	}

			}while ($mysqli->more_results() && $mysqli->next_result());

		}


		$mysqli->close();

		return $temp;

	}

	public function update_temp_transactions($seen,$approved,$selectedId)
	{
		$query = "UPDATE temp_transactions set seen = ".$seen.", approved = ".$approved." where t_id = '".$selectedId."'";
		$this->db->trans_start();
		$this->db->query($query);
		$this->db->trans_complete();
		if(!($this->db->query($query)))
		{
			$error=$this->db->error();
			$this->db->trans_complete();
			return $error['message'];
		}
		$this->db->trans_complete();
		return 1;

	}

	
    

}		
