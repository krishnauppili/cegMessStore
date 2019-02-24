<?php
class Items_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->db->db_debug = TRUE;
	}

	public function get_lesser_items()
	{
		$query = "SELECT item_name, quantity_remaining FROM provision_stock WHERE quantity_remaining < minimum_quantity";
		$return['itemNames'] = array();
		$return['quantityAvailable'] = array();
		$result = $this->db->query($query);
		foreach($result->result() as $row)
		{

			array_push($return['itemNames'],$row->item_name);
			array_push($return['quantityAvailable'],$row->quantity_remaining);
		}
		return $return;
	}

	public function get_vegetables($names=null)
    {
        $itemName = array();
        $latestRate = array();
        $quantityAvailable = array();
        if(isset($names) && $names != null)
        {
            $this->db->where_in('items_name',$names);
            $this->db->where('category','Vegetable');
            $this->db->where('functioning',1);
            $this->db->order_by('precedence','ASC');
            $items = $this->db->get('items');
        }
        else
        {
        	$this->db->where('category','Vegetable');
        	$this->db->where('functioning',1);
            $this->db->order_by('precedence','ASC');

            $items = $this->db->get('items');
        }
        foreach($items->result() as $row)
        {
            array_push($itemName,$row->item_name);
        }
        return json_encode(array("itemNames" => $itemName));
    }

    public function get_max_id()
    {
    	$query = "SELECT max(item_id) as max_item_id from items;";
    	$id = $this->db->query($query);
    	foreach($id->result() as $row)
    		$max_item_id = $row->max_item_id;
    	return $max_item_id;

    }


	public function add_item_model($data)
	{

		for($i=0;$i<count($data['itemName']);$i++)
		{
			$item = strtoupper($data['itemName'][$i]);
			$item=trim($item);
			$max_item_id = $this->items_model->get_max_id();
			$max_item_id = $max_item_id+1;
			if($data['selectedType'][$i] =='Processed')
				$query = "INSERT into items(item_id,category,item_name,precedence,processed) VALUES (".$max_item_id.",'Provision','".$item."',".$data['precedence'][$i].",1)";
			else
				$query = "INSERT into items(item_id,category,item_name,precedence,processed) VALUES (".$max_item_id.",'Provision','".$item."',".$data['precedence'][$i].",0)";


			$this->db->trans_start();
			
			if(!($this->db->query($query)))
			{
				$error=$this->db->error();
				$this->db->trans_complete();
				return $error['message'];
			}
			else
			{
				$query1 = "INSERT into provision_stock(item_name,quantity_remaining,minimum_quantity,clearance_stock,latest_rate) VALUES('".$item."',".$data['quantityAvailable'][$i].",".$data['minimumQuantity'][$i].",".$data['quantityAvailable'][$i].",".$data['itemRate'][$i].")";
				
				if(!($this->db->query($query1)))
				{
				$error1=$this->db->error();
				$this->db->trans_complete();
				return $error1['message'];
				}
				else
					$this->db->trans_complete();
			}
		}


		return 1;
	}

	public function add_vegetable_model($data)
	{

		for($i=0;$i<count($data['itemName']);$i++)
		{
			$item = strtoupper($data['itemName'][$i]);
			$max_item_id = $this->items_model->get_max_id();
			$max_item_id = $max_item_id+1;
			if($data['selectedType'][$i] =='Processed')
				$query = "INSERT into items(item_id,category,item_name,precedence,processed) VALUES (".$max_item_id.",'Vegetable','".$item."',".$data['precedence'][$i].",1)";
			else
				$query = "INSERT into items(item_id,category,item_name,precedence,processed) VALUES (".$max_item_id.",'Vegetable','".$item."',".$data['precedence'][$i].",0)";


			$this->db->trans_start();
			
			if(!($this->db->query($query)))
			{
				$error=$this->db->error();
				$this->db->trans_complete();
				return $error['message'];
			}
			else
			{
				$this->db->trans_complete();
				
			}
		}


		return 1;
	}


	public function get_item_types_model()
	{

		$return['itemType'] = array();
		$query="SELECT item_type from item_types";
		$vendors = $this->db->query($query);
		foreach($vendors->result() as $row){

			array_push($return['itemType'],$row->item_type);
		}
		return json_encode(array(
					"itemType" => $return['itemType']));

	}

	public function get_provision_items()
	{

		$return['itemName'] = array();
		$query="SELECT item_name from items where category='Provision' order by precedence asc";
		$items = $this->db->query($query);
		foreach($items->result() as $row){

			array_push($return['itemName'],$row->item_name);
		}
		return json_encode(array(
					"itemName" => $return['itemName']));

	}



	public function get_quantity_available($itemName)
	{
		$query = "SELECT quantity_remaining from provision_stock where item_name = '".$itemName."'";
		$quantity = $this->db->query($query);
		foreach($quantity->result() as $row)
    		$quantity_available = $row->quantity_remaining;
    	return $quantity_available;
	}


	public function stock_approximation_model($data)
	{
		
		$approximatedDate = $data['approximatedDate'];
		for($i=0;$i<count($data['selectedItems']);$i++)
		{
			$itemNames = $data['selectedItems'][$i];
			$selectedQuantity = $data['selectedQuantity'][$i];

			
			$quantityAvailable = $this->items_model->get_quantity_available($itemNames);

			if(floatval($quantityAvailable) == 0.0 )
				$quantityAvailable = 0.001;

			$differencePercentage = ((abs(floatval($data['selectedQuantity'][$i]) - floatval($quantityAvailable)))/floatval($quantityAvailable))*100;
			

			$query = "INSERT into stock_approximation(item_name,actual_stock,system_stock,t_date,difference_percent) VALUES ('".$itemNames."',".$selectedQuantity.",".$quantityAvailable.",'".$approximatedDate."',".$differencePercentage.")";
			$this->db->trans_start();
			if(!($this->db->query($query)))
			{
				$error=$this->db->error();
				$this->db->trans_complete();
				return $error['message'];
			}
			else
				$this->db->trans_complete();



		}
		
		//return (string)(((abs(floatval($data['selectedQuantity'][$i]) - floatval($data['quantityAvailable'][$i])))/floatval($data['quantityAvailable'][$i]))*100);
		return 1;

		
		
	}


	public function validation_for_duplicate_entry($data)
	{
		$return = 0;
		$itemNames = "'".$data['selectedItems'][0]."'";
		$temp=array();
		for($i=1;$i<count($data['selectedItems']);$i++)
			$itemNames.=",'".$data['selectedItems'][$i]."'";

		$query = "SELECT mess_name,t_date,item_name from transactions where t_type = 'I' and vendor_name = 'HOSTEL STORES' and mess_name = '".$data['selectedMess']."' and t_date = '".$data['issuedDate']."' and item_name in (".$itemNames.")";
		$result =$this->db->query($query);
		foreach($result->result() as $row)
			$temp = $row->item_name;
		if(count($temp)>0)
			return 1;
		else
			return 0;
	}

	public function issue_item_model($data)
	{
		$itemNames = $data['selectedItems'];
		$selectedQuantity = $data['selectedQuantity'];
		$selectedMess = $data['selectedMess'];
		$dateIssued = $data['issuedDate'];
		$item = $itemNames[0];
		$eachSelected = $selectedQuantity[0];
		for($i=1;$i<count($itemNames);$i++)
	 	{
			$item = $item.','.$itemNames[$i];
			$eachSelected = $eachSelected.','.$selectedQuantity[$i];
		}


		$query = "CALL insert_issue('".$selectedMess."','".$dateIssued."','".$item."','".$eachSelected."','I',@query,@status,@billNo);";
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

		/*$query1 = "CALL write_log(\"".$temp['query']."\");";
		$query1.="insert into log_query(query_1,billNo) values (\"".$temp['query']."\",\"".$temp['billNo']."\");";

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

		}*/
		//if($mysqli->query($query))
			//$test_variable1 .= '\ndetails logged';

		$mysqli->close();

		return $temp;


	}



	public function validation_for_issue($data)
	{
		$return = 0;
		for($i=0;$i<count($data['selectedItems']);$i++)
		{
			if($data['selectedQuantity'][$i] > $data['quantityAvailable'][$i])
				$return = 1;
		}
		return $return;
	}




	

	public function get_items($names=null)
	{
		$itemName = array();
		$latestRate = array();
		$quantityAvailable = array();
        $clearanceStock = array();
		
		$query = 'SELECT ps.item_name , ps.quantity_remaining, ps.clearance_stock, ps.latest_rate FROM items it INNER JOIN provision_stock ps ON ps.item_name = it.item_name ORDER BY it.precedence';
		$result = $this->db->query($query);
		foreach($result->result() as $row)
		{
			array_push($itemName,$row->item_name);
			array_push($latestRate,$row->latest_rate);
			array_push($quantityAvailable,$row->quantity_remaining);
            array_push($clearanceStock,$row->clearance_stock);
		}
		return json_encode(array("itemNames" => $itemName, "latestRate" => $latestRate, "quantityAvailable" => $quantityAvailable,"clearanceStock" => $clearanceStock));
	}


	public function get_consumed_items_till_today($messName,$returnDate)
	{
		$last_day = date('Y-m-d', strtotime($returnDate));
		$first_day =date('Y-m-01', strtotime($returnDate));
		
		$issueditems = $this->db->query("select t1.item_name as 'item_name', t1.quantity_issued - IFNULL(t2.quantity_returned,0) as 'quantity',t1.rate as 'rate' from
			(select t.item_name as 'item_name', sum(t.quantity) as 'quantity_issued' , t.amount/t.quantity as 'rate' from transactions t INNER JOIN items i ON i.item_name = t.item_name where t.t_type = 'I' AND t.mess_name = '".$messName."' AND t.t_date >= '".$first_day."' AND t.t_date <='".$last_day."' AND i.category = 'Provision'
			GROUP BY t.item_name) t1 
			LEFT JOIN
			(select t.item_name as 'item_name', sum(t.quantity) as 'quantity_returned'  from transactions t where t.t_type = 'R' AND t.vendor_name = '".$messName."' AND t.t_date >= '".$first_day."' AND t.t_date <='".$last_day."'
			GROUP BY t.item_name) t2
			ON
			t1.item_name = t2.item_name");
		$return['itemNames'] = array();
		$return['quantitySupplied'] = array();
		$return['latestRate'] = array();

     

		foreach($issueditems->result() as $row)
		{
			
			array_push($return['itemNames'],$row->item_name);
            array_push($return['latestRate'],$row->rate);
			//for return quantity deduction
		    $this->db->select_sum('quantity')->from('transactions');
		    $this->db->where('t_type','R');
			$this->db->where('mess_name',$messName);
			$this->db->where('item_name',$row->item_name);
			$this->db->where('t_date >=',$first_day);
			$this->db->where('t_date <=',$last_day);
			$returneditems = $this->db->get();
			foreach($returneditems->result() as $deductionrow)
			{
				$quantitytosubtract = $deductionrow->quantity;
				break;
			}
			$actualquantitysupplied = $row->quantity - $quantitytosubtract;
			//end of return quantity deduction

			
			array_push($return['quantitySupplied'],$actualquantitysupplied);
		}

		return $return;
	}

	public function validation_for_return($data)
	{
		$return = 0;
		for($i=0;$i<count($data['selectedItems']);$i++)
		{
			if($data['selectedQuantity'][$i] > $data['quantitySupplied'][$i])
				$return = 1;
		}
		return $return;
	}




	public function return_item_model($data)
	{

		$selectedItems = $data['selectedItems'][0];
		$selectedQuantity = $data['selectedQuantity'][0];
		$latestRate = $data['latestRate'][0];
		$issuedDate = $data['issuedDate'];
		$selectedMess = $data['selectedMess'];

		for ($i = 1 ; $i < count($data['selectedItems']) ; $i++)
		{
			$selectedItems .= ','.$data['selectedItems'][$i];
			$selectedQuantity .= ','.$data['selectedQuantity'][$i];
			$latestRate .= ','.$data['latestRate'][$i];
		}

		$query = "CALL insert_order ( '".$selectedMess."','".$selectedItems."','".$issuedDate."','".$selectedQuantity."','".$latestRate."','R',@query,@status,@billNo);";
		
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


	public function generate_notification_details()
	{
		$query="SELECT t_date,notification_type,t_type,mess_name,vendor_name from temp_transactions where seen = 0 group by notification_type,mess_name,vendor_name,t_date,t_type";
			 
		$resultant = $this->db->query($query);
		$output = array();
		foreach($resultant->result() as $row)
		{
			$temp['t_date'] = date('d-m-Y',strtotime($row->t_date));
			$temp['t_type'] = $row->t_type;
			$temp['notification_type'] = $row->notification_type;
			$temp['mess_name'] = $row->mess_name;
			$temp['vendor_name'] = $row->vendor_name;
			$temp['items'] = array();
			$this->db->select('t_id,item_name,quantity,new_quantity,amount,new_amount');
			$this->db->where('t_date',$row->t_date);
			$this->db->where('t_type',$row->t_type);
			$this->db->where('notification_type',$row->notification_type);
			$this->db->where('mess_name',$row->mess_name);
			$this->db->where('vendor_name',$row->vendor_name);
			$this->db->where('seen',0);
			$this->db->trans_start();
			$itemsObj = $this->db->get('temp_transactions');
			$this->db->trans_complete();
			foreach($itemsObj->result() as $itemsRow){
				$items = array();
				$items['t_id'] = $itemsRow->t_id;
				$items['item_name'] = $itemsRow->item_name;
				$items['quantity'] = $itemsRow->quantity;
				if(($itemsRow->new_quantity)!= NULL) 
						$items['new_quantity'] = $itemsRow->new_quantity;
				else
						$items['new_quantity'] = $items['quantity'];
				$items['rate'] = $itemsRow->amount / $itemsRow->quantity;
				if(($itemsRow->new_amount)!= NULL) 
					$items['new_rate'] = $itemsRow->new_amount / $itemsRow->new_quantity;
				else
					$items['new_rate'] = $items['rate'];

				array_push($temp['items'],$items);
			}
			array_push($output,$temp);
		}
		return ($output);
	}


	public function generate_stock_approximation_details()
	{
		$return['sid'] = array();
		$return['date'] = array();
		$return['itemNames'] = array();
		$return['systemStock'] = array();
		$return['actualStock'] = array();
		$return['differencePercentage'] = array();

		$query ="SELECT s_id,t_date,item_name,system_stock,actual_stock,difference_percent from stock_approximation where seen = 0";
		$items=$this->db->query($query);
		foreach($items->result() as $row)
		{
			array_push($return['sid'],$row->s_id);
			array_push($return['date'],date('d-m-Y',strtotime($row->t_date)));
			array_push($return['itemNames'],$row->item_name);
			array_push($return['systemStock'],$row->system_stock);
           
			array_push($return['actualStock'],$row->actual_stock);
			array_push($return['differencePercentage'],$row->difference_percent);
		}
		return $return;
	}



	public function generate_items_stock($itemName="")
	{
		$return['itemNames'] = array();
		$return['quantityAvailable'] = array();
		$return['rate'] = array();
		$return['clearanceStock'] = array();
		if($itemName !="")
		  $query ="SELECT item_name,quantity_remaining,latest_rate,clearance_stock from provision_stock where item_name like '".$itemName."%'";
		else
			 $query ="SELECT item_name,quantity_remaining,latest_rate,clearance_stock from provision_stock";

		$items=$this->db->query($query);
		foreach($items->result() as $row)
		{	
			array_push($return['itemNames'],$row->item_name);
			array_push($return['quantityAvailable'],$row->quantity_remaining);
			array_push($return['rate'],$row->latest_rate);
			array_push($return['clearanceStock'],$row->clearance_stock);
		}
		return $return;
	}

	public function generate_items_total_stock($itemName)
	{
		$return['itemNames'] = array();
		$return['quantityAvailable'] = array();
		$return['rate'] = array();
		$return['clearanceStock'] = array();
		$query ="SELECT item_name,quantity_remaining,latest_rate,clearance_stock from provision_stock";
		$items=$this->db->query($query);
		foreach($items->result() as $row)
		{	
			array_push($return['itemNames'],$row->item_name);
			array_push($return['quantityAvailable'],$row->quantity_remaining);
			array_push($return['rate'],$row->latest_rate);
			array_push($return['clearanceStock'],$row->clearance_stock);
		}
		return $return;
	}

	public function approve_issue_edit($data)
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

		$query = "CALL edit_issue ( '".$mess_name."','".$date."','".$item_name."','".$new_quantity."','".$selectedId."','".$type."','".$notification_type."',@query,@status,@billNo);";
		
		
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

	public function approve_return_edit($data)
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


	public function disapprove_stock_approximation($data)
	{
		$query = "update stock_approximation set seen = 1 ,approved = 0 where s_id = ".$data['sid']."";
		$this->db->trans_start();
		$this->db->query($query);
		$this->db->trans_complete();
		return 1;

	}


	public function approve_stock_approximation($data)
	{

		$approximatedDate = date('Y-m-d',strtotime($data['approximatedDate']));
		$systemStock = $data['systemStock'];
		$actualStock = $data['actualStock'];
		$itemName = $data['itemName'];
		$difference_percent = $data['differencePercentage'];

		$query = "CALL stock_approximation ( '".$approximatedDate."','".$itemName."',".$actualStock.",@query,@status,@billNo);";
		
		
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


	






  }
