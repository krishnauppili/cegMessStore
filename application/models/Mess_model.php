
<?php
class Mess_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->db->db_debug = FALSE;
		$this->load->model('orders_model');
	}

	public function get_mess_types_model()
	{
		$return['messName'] = array();
		$return['contact'] = array();
		$return['messIncharge'] = array();
		$return['messCategory'] = array();
		$query="SELECT mess_name,mess_incharge,category,contact from mess_details where functioning = 1";
		$vendors = $this->db->query($query);
		foreach($vendors->result() as $row){

			array_push($return['messName'],$row->mess_name);
			array_push($return['messIncharge'],$row->mess_incharge);
			array_push($return['messCategory'],$row->category);
			array_push($return['contact'],$row->contact);
		}
		return json_encode(array("messName" => $return['messName'],
					"messIncharge" => $return['messIncharge'],
					"messCategory" => $return['messCategory'],
					"contact" => $return['contact']));

	}


	

	public function get_mess_categories_model()
	{

		$return['messCategory'] = array();
		$query="SELECT mess_type from mess_types";
		$vendors = $this->db->query($query);
		foreach($vendors->result() as $row){

			array_push($return['messCategory'],$row->mess_type);
		}
		return json_encode(array(
					"messCategory" => $return['messCategory']));

	}


	public function add_mess($data)
	{
		$max_mess_id = $this->mess_model->get_max_mess_id();
		$max_mess_id = $max_mess_id+1;
		 if(preg_match('/[\'^£$%&*()}.{@#~?><>,|=_+¬-]/', $data['messName'])||(strpos($data['messName'], '/')!==false)||(strpos($data['messName'], '\\')!==false))
          {
              return "No special characters allowed for Mess Name";
          }
          if(strlen($data['messName'])>=200)
          {
              return "Mess name should not be greater than 200";
                        
          }
        $data['messName']=trim($data['messName']);
		$query = "INSERT into mess_details(mess_id,mess_name,category,mess_incharge,contact,functioning) VALUES (".$max_mess_id.",'".$data['messName']."','".$data['category']."','".$data['messIncharge']."','".$data['contact']."',1);";
		$this->db->trans_start();
		if(!($this->db->query($query)))
		{
			$error=$this->db->error();
			$this->db->trans_complete();
			return $error['message'];
		}
		else
		{
			$max_vendor_id = $this->orders_model->get_max_vendor_id();
			$max_vendor_id = $max_vendor_id + 1;
			$query1 = "INSERT into vendor_details(vendor_id,vendor_name,category,owner_name,address,contact,functioning)
						SELECT ".$max_vendor_id.",'".$data['messName']."','Mess','".$data['messIncharge']."','CEG Anna University','".$data['contact']."',0";
			if(!($this->db->query($query1)))
			{
			$error=$this->db->error();
			$this->db->trans_complete();
			return $error['message'];
			}
			else
				$this->db->trans_complete();
		}
		return 1;
	}


	public function update_mess_details($data)
	{
		$this->db->trans_start();
		$this->db->where('mess_name',$data['messName']);
		$this->db->set('contact',$data['contact']);
		$this->db->set('mess_incharge',$data['messIncharge']);
		
		
		if(!$this->db->update('mess_details'))
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

	public function get_max_mess_id()
    {
    	$query = "SELECT max(mess_id) as max_mess_id from mess_details;";
    	$id = $this->db->query($query);
    	foreach($id->result() as $row)
    		$max_mess_id = $row->max_mess_id;
    	return $max_mess_id;

    }

    public function delete_mess($messName)
	{
		$this->db->where('mess_name',$messName);
		$this->db->set('functioning','0');
		$this->db->trans_start();
                if(!$this->db->update('mess_details'))
                {
                        $error=$this->db->error();
                        $this->db->trans_complete();
                        return $error['message'];
                }
                $this->db->trans_complete();
                return 1;

	}



	public function generate_mess_bill($messName,$from,$to)
	{
		
		$return['suppliedDate'] = array();
		$return['totalAmount'] = array();
		$query = "SELECT bill_date, sum(t_amount) as 'totalAmount' FROM (SELECT 
				t.t_date AS 'bill_date',
				(CASE
				WHEN i.processed = 1 and m.category = 'Party' THEN (t.amount * 0.30) +t.amount
				WHEN i.processed = 0 and m.category = 'Party' THEN (t.amount * 0.15) + t.amount
				ELSE t.amount
				END) as 't_amount' 
				from transactions t
				INNER JOIN 
				mess_details m
				ON
				t.mess_name = m.mess_name
				INNER JOIN
				items i ON
				i.item_name = t.item_name AND
				i.category = 'Provision'
				where t.mess_name='".$messName."' and t.t_date>='".$from."' and t.t_date<='".$to."' 
				and (t_type = 'I' OR t_type = 'GI')) t1 group by bill_date order by bill_date";
		
		$items = $this->db->query($query);
		foreach($items->result() as $row)
		{
			array_push($return['suppliedDate'],date('d-m-Y',strtotime($row->bill_date)));
			array_push($return['totalAmount'],$row->totalAmount);
		}
		$retAmount = $this->get_returns_amount($messName,$from,$to);
		if($retAmount == null)
			$return['returnTotal'] = 0;
		else
			$return['returnTotal'] = $retAmount;
		return $return;
	}
	
	public function get_returns_amount($messName, $from, $to)
	{

		$query="SELECT sum(amount) as amount from transactions where t_type='R' and vendor_name='".$messName."' and t_date>='".$from."' and t_date<='".$to."'";
		$items = $this->db->query($query);
		foreach($items->result() as $row)
		{
			$returnTotal = $row->amount;
		}
		return $returnTotal;
		
	}



	public function generate_mess_consumption($messName,$from,$to)
	{
		$return['t_id'] = array();
		$return['itemNames'] = array();
		$return['quantitySupplied'] = array();
		$return['suppliedDate'] = array();
		$return['rate'] = array();
		$return['amount'] = array();
		$query ="SELECT t.t_id as 't_id',t.item_name as 'item_name',t.quantity as 'quantity',
				CAST(t.t_date AS DATE) as 't_date',
								(SELECT
                                CASE
								WHEN i.processed = 1 and m.category = 'Party' THEN (t.amount * 0.30) +t.amount
                                WHEN i.processed = 0 and m.category = 'Party' THEN (t.amount * 0.15) + t.amount
                                ELSE t.amount
                                END)/t.quantity as 'rate',
                                (SELECT
                                CASE
								WHEN i.processed = 1 and m.category = 'Party' THEN (t.amount * 0.30) +t.amount
                                WHEN i.processed = 0 and m.category = 'Party' THEN (t.amount * 0.15) + t.amount
                                ELSE t.amount
                                END) as 'amount' from transactions t 
                                INNER JOIN
                                items i 
                                ON i.item_name = t.item_name AND
                                i.category = 'Provision'
                                INNER JOIN
                                mess_details m 
                                ON 
                                m.mess_name = t.mess_name 
                                where (t.t_type='I' or t.t_type = 'GI') and t.mess_name = '".$messName."' and t.t_date >= '".$from."' and t.t_date <='".$to."' order by t.t_date";
		$items=$this->db->query($query);
		foreach($items->result() as $row)
		{	array_push($return['t_id'],$row->t_id);
			array_push($return['itemNames'],$row->item_name);
			array_push($return['quantitySupplied'],$row->quantity);
            array_push($return['suppliedDate'],date('d-m-Y',strtotime($row->t_date)));
			array_push($return['rate'],$row->rate);
			array_push($return['amount'],$row->amount);
		}
		return $return;
	}




	public function generate_edit_mess_consumption($messName,$from,$to)
	{
		$return['t_id'] = array();
		$return['itemNames'] = array();
		$return['quantitySupplied'] = array();
		$return['suppliedDate'] = array();
		$return['rate'] = array();
		$return['amount'] = array();
		$query ="SELECT t.t_id as 't_id',t.item_name as 'item_name',t.quantity as 'quantity',
				CAST(t.t_date AS DATE) as 't_date',
								(SELECT
                                CASE
								WHEN i.processed = 1 and m.category = 'Party' THEN (t.amount * 0.30) +t.amount
                                WHEN i.processed = 0 and m.category = 'Party' THEN (t.amount * 0.15) + t.amount
                                ELSE t.amount
                                END)/t.quantity as 'rate',
                                (SELECT
                                CASE
								WHEN i.processed = 1 and m.category = 'Party' THEN (t.amount * 0.30) +t.amount
                                WHEN i.processed = 0 and m.category = 'Party' THEN (t.amount * 0.15) + t.amount
                                ELSE t.amount
                                END) as 'amount' from transactions t 
                                INNER JOIN
                                items i 
                                ON i.item_name = t.item_name AND
                                i.category = 'Provision'
                                INNER JOIN
                                mess_details m 
                                ON 
                                m.mess_name = t.mess_name 
                                LEFT JOIN
                                (select t_id from temp_transactions where seen = 0) temp
                                ON
                                t.t_id = temp.t_id
                                where (t.t_type='I' or t.t_type = 'GI') and t.mess_name = '".$messName."' and t.t_date >= '".$from."' and t.t_date <='".$to."' and temp.t_id is null order by t.t_date";
		$items=$this->db->query($query);
		foreach($items->result() as $row)
		{	array_push($return['t_id'],$row->t_id);
			array_push($return['itemNames'],$row->item_name);
			array_push($return['quantitySupplied'],$row->quantity);
            array_push($return['suppliedDate'],date('d-m-Y',strtotime($row->t_date)));
			array_push($return['rate'],$row->rate);
			array_push($return['amount'],$row->amount);
		}
		return $return;
	}


	public function generate_mess_average($from,$to,$messName="")
	{
		

		$query = "SELECT t.item_name as 'item_name' from transactions t INNER JOIN items i 
				ON 
				i.item_name = t.item_name and i.category = 'Provision' 
				where t.mess_name='".$messName."' and t.t_date>='".$from."' and t.t_date<='".$to."' group by t.item_name order by t.item_name";
		$this->db->trans_start();
		$items = $this->db->query($query);
		$this->db->trans_complete();
		$output = array();
		
		foreach($items->result() as $row)
		{
			
			
			$temp['itemName'] = $row->item_name;
			$temp['details'] = array();
			//$this->db->select('suppliedDate,messName,quantitySupplied,rate,amount');
			$quantitySum = 0;
			$rateSum = 0;
			$amountSum = 0;
			
			$query1="SELECT t.mess_name as 'mess_name',CAST(t.t_date AS DATE) as 't_date',
					t.quantity as 'quantity',
				
								ROUND((SELECT
                                CASE
								WHEN i.processed = 1 and m.category = 'Party' THEN (t.amount * 0.30) +t.amount
                                WHEN i.processed = 0 and m.category = 'Party' THEN (t.amount * 0.15) + t.amount
                                ELSE t.amount
                                END)/t.quantity,2) as 'rate',
                                ROUND((SELECT
                                CASE
								WHEN i.processed = 1 and m.category = 'Party' THEN (t.amount * 0.30) +t.amount
                                WHEN i.processed = 0 and m.category = 'Party' THEN (t.amount * 0.15) + t.amount
                                ELSE t.amount
                                END),2) as 'amount' from transactions t 
                                INNER JOIN
                                items i 
                                ON i.item_name = t.item_name AND
                                i.category = 'Provision'
                                INNER JOIN
                                mess_details m 
                                ON 
                                m.mess_name = t.mess_name 
                                where (t.t_type='I' or t.t_type = 'GI') and t.mess_name = '".$messName."' and t.t_date >= '".$from."' and t.t_date <='".$to."' and t.item_name = '".$row->item_name."'";
			$this->db->trans_start();
			$itemsObj = $this->db->query($query1);
			$this->db->trans_complete();
			foreach($itemsObj->result() as $itemsRow){
				$items = array();
			    $items['suppliedDate'] = date('d-m-Y',strtotime($itemsRow->t_date));
                $items['quantitySupplied'] = $itemsRow->quantity;
				$items['rate'] = $itemsRow->rate;	
				$items['amount'] = $itemsRow->amount;
				$items['messName'] = $itemsRow->mess_name;
				$quantitySum += $itemsRow->quantity;
			    $rateSum += $itemsRow->rate;
			    $amountSum += $itemsRow->amount;
				array_push($temp['details'],$items);
			}
			$temp['quantitySum'] = $quantitySum;
			$temp['rateSum'] = $rateSum;
			$temp['amountSum'] = $amountSum;

			array_push($output,$temp);
		}
		return $output;
	}

	


	public function generate_mess_return($messName,$from,$to)
	{
	
		$query = "SELECT t_id,item_name,quantity,t_date,amount/quantity as rate,amount from transactions where t_type='R' and vendor_name='".$messName."' and t_date>='".$from."' and t_date<='".$to."'";
		$return['t_id'] = array();
		$return['itemNames'] = array();
		$return['quantityReturned'] = array();
		$return['returnedDate'] = array();
		$return['rate'] = array();
		$return['amount'] = array();
		$items = $this->db->query($query);
		foreach($items->result() as $row)
		{
			array_push($return['t_id'],$row->t_id);
			array_push($return['itemNames'],$row->item_name);
			array_push($return['quantityReturned'],$row->quantity);
			array_push($return['returnedDate'],date('d-m-Y',strtotime($row->t_date)));
			array_push($return['rate'],$row->rate);
			array_push($return['amount'],$row->amount);
		}
		return $return;
	}



	public function generate_edit_mess_return($messName,$from,$to)
	{
	
		$query = "SELECT t.t_id,t.item_name,t.quantity,t.t_date,t.amount/t.quantity as 'rate',t.amount from transactions t
				LEFT JOIN 
				(select t_id from temp_transactions where seen = 0) temp
				on t.t_id = temp.t_id
				where t_type='R' and vendor_name='".$messName."' and t_date>='".$from."' and t_date<='".$to."' and temp.t_id is null ";
		$return['t_id'] = array();
		$return['itemNames'] = array();
		$return['quantityReturned'] = array();
		$return['returnedDate'] = array();
		$return['rate'] = array();
		$return['amount'] = array();
		$items = $this->db->query($query);
		foreach($items->result() as $row)
		{
			array_push($return['t_id'],$row->t_id);
			array_push($return['itemNames'],$row->item_name);
			array_push($return['quantityReturned'],$row->quantity);
			array_push($return['returnedDate'],date('d-m-Y',strtotime($row->t_date)));
			array_push($return['rate'],$row->rate);
			array_push($return['amount'],$row->amount);
		}
		return $return;
	}

	public function generate_mess_vegetable_consumption($messName,$from,$to)
	{
		/*$this->db->select('*');
		$this->db->where('messName',$messName);
		$this->db->where('receivedDate >= ',$from);
		$this->db->where('receivedDate <= ',$to);
		$this->db->order_by('receivedDate','desc');*/
		$query="SELECT t2.t_date as 'suppliedDate',t2.item_name as 'itemName', t2.quantity as 'quantitySupplied', (t2.amount/t2.quantity) as 'proposedRate' ,
			(SELECT
            CASE
			WHEN m1.category = 'Party' THEN t2.amount / t2.quantity
            ELSE t1.min_rate
            END) as 'actualRate',
            (SELECT
            CASE
			WHEN m1.category = 'Party' THEN t2.amount 
            ELSE t1.min_rate * t2.quantity
            END) as 'amount' from
			(select t_date,min(amount/quantity) as min_rate,item_name from transactions group by item_name, t_date) t1 
			INNER JOIN 
			(select t_date, item_name,vendor_name,mess_name,amount, quantity from transactions where t_type = 'VT' and mess_name = '".$messName."' and t_date >='".$from."' and t_date <='".$to."') t2 
			ON 
			t1.t_date = t2.t_date AND t1.item_name = t2.item_name
			INNER JOIN
			(select item_name,category from items) i1
			ON 
			i1.item_name = t1.item_name AND i1.category = 'Vegetable'
            INNER JOIN
            mess_details m1
            ON 
            t2.mess_name = m1.mess_name
			ORDER BY t2.t_date desc";

		$return['vegetableNames'] = array();
		$return['quantitySupplied'] = array();
		$return['suppliedDate'] = array();

		$return['actualRate'] = array();
		$return['proposedRate'] = array();
		$return['amount'] = array();
		$items = $this->db->query($query);
		foreach($items->result() as $row)
		{
			array_push($return['vegetableNames'],$row->itemName);
			array_push($return['quantitySupplied'],$row->quantitySupplied);
            array_push($return['suppliedDate'],date('Y-m-d',strtotime($row->suppliedDate)));

			array_push($return['actualRate'],$row->actualRate);
			array_push($return['proposedRate'],$row->proposedRate);
			array_push($return['amount'],$row->amount);
		}
		return $return;
	}

	public function generate_mess_vegetable_average($from,$to,$messName="")
	{
		
		$query="SELECT itemName as 'itemName', ROUND(sum(amount)/sum(quantitySupplied),2) as 'averagePrice',ROUND(sum(quantitySupplied),2) as 'totalQuantity',ROUND(sum(amount),2) as 'totalAmount' FROM
			(SELECT t2.t_date as 'suppliedDate',t2.item_name as 'itemName', t2.quantity as 'quantitySupplied', (t2.amount/t2.quantity) as 'proposedRate' ,
			(SELECT
            CASE
			WHEN m1.category = 'Party' THEN t2.amount / t2.quantity
            ELSE t1.min_rate
            END) as 'actualRate',
            (SELECT
            CASE
			WHEN m1.category = 'Party' THEN t2.amount 
            ELSE t1.min_rate * t2.quantity
            END) as 'amount' from
			(select t_date,min(amount/quantity) as min_rate,item_name from transactions group by item_name, t_date) t1 
			INNER JOIN 
			(select t_date, item_name,vendor_name,mess_name,amount, quantity from transactions where t_type = 'VT' and mess_name = '".$messName."' and t_date >='".$from."' and t_date <='".$to."') t2 
			ON 
			t1.t_date = t2.t_date AND t1.item_name = t2.item_name
			INNER JOIN
			(select item_name,category from items) i1
			ON 
			i1.item_name = t1.item_name AND i1.category = 'Vegetable'
            INNER JOIN
            mess_details m1
            ON 
            t2.mess_name = m1.mess_name) mt1 group by itemName";
		$items = $this->db->query($query);
		$output = array();


		
		foreach($items->result() as $row)
		{

			$temp['quantitySum'] = $row->totalQuantity;
			$temp['rateSum'] = $row->averagePrice;
			$temp['amountSum'] = $row->totalAmount;
			$temp['vegetableName'] = $row->itemName;
			$temp['details'] = array();
			$query1 ="SELECT t2.t_date as 'suppliedDate', t2.quantity as 'quantitySupplied',t2.vendor_name as 'vendorName', 
			ROUND((SELECT
            CASE
			WHEN m1.category = 'Party' THEN t2.amount / t2.quantity
            ELSE t1.min_rate
            END),2) as 'actualRate',
            ROUND((SELECT
            CASE
			WHEN m1.category = 'Party' THEN t2.amount 
            ELSE t1.min_rate * t2.quantity
            END),2) as 'amount' from
			(select t_date,min(amount/quantity) as min_rate,item_name from transactions group by item_name, t_date) t1 
			INNER JOIN 
			(select t_date, item_name,vendor_name,mess_name,amount, quantity from transactions 
			where item_name = '".$row->itemName."' and t_type = 'VT' and mess_name = '".$messName."' and t_date >='".$from."' and t_date <='".$to."') t2 
			ON 
			t1.t_date = t2.t_date AND t1.item_name = t2.item_name
			INNER JOIN
			(select item_name,category from items) i1
			ON 
			i1.item_name = t1.item_name AND i1.category = 'Vegetable'
            INNER JOIN
            mess_details m1
            ON 
            t2.mess_name = m1.mess_name
			ORDER BY t2.t_date desc";
			

			//$this->db->trans_start();
			$itemsObj = $this->db->query($query1);
			//$this->db->trans_complete();
			foreach($itemsObj->result() as $itemsRow){
				$items = array();
			    $items['receivedDate'] = date('d-m-Y',strtotime($itemsRow->suppliedDate));
				$items['quantityReceived'] = $itemsRow->quantitySupplied;
				$items['actualRate'] = $itemsRow->actualRate;	
				$items['amount'] = $itemsRow->amount;
					
				$items['vendorName'] = $itemsRow->vendorName;
				array_push($temp['details'],$items);


			}
			

			array_push($output,$temp);
		}
		return $output;
	}

	
	public function generate_mess_vegetable_bill($messName,$from,$to)
	{
		
		$query="SELECT CAST(suppliedDate as DATE) as 'suppliedDate',sum(amount) as 'totalAmount' FROM
            (SELECT t2.t_date as 'suppliedDate', 
            (SELECT
            CASE
			WHEN m1.category = 'Party' THEN t2.amount 
            ELSE t1.min_rate * t2.quantity
            END) as 'amount' from
			(select t_date,min(amount/quantity) as min_rate,item_name from transactions group by item_name, t_date) t1 
			INNER JOIN 
			(select t_date, item_name,vendor_name,mess_name,amount, quantity from transactions 
			where t_type = 'VT' and mess_name = '".$messName."' and t_date >='".$from."' and t_date <='".$to."') t2 
			ON 
			t1.t_date = t2.t_date AND t1.item_name = t2.item_name
			INNER JOIN
			(select item_name,category from items) i1
			ON 
			i1.item_name = t1.item_name AND i1.category = 'Vegetable'
            INNER JOIN
            mess_details m1
            ON 
            t2.mess_name = m1.mess_name)mt1 group by suppliedDate";
            
		$return['suppliedDate'] = array();
		$return['totalAmount'] = array();
		$items = $this->db->query($query);
		foreach($items->result() as $row)
		{
			array_push($return['suppliedDate'],date('d-m-Y',strtotime($row->suppliedDate)));
			array_push($return['totalAmount'],$row->totalAmount);
		}
		return $return;
	}

	public function update_mess_consumption($data)
	{
		$query = "insert into temp_transactions(notification_type,t_id,t_type,item_name,quantity,new_quantity,amount,new_amount,t_date,mess_name,vendor_name,entry_time)
			select 'Edit',t_id,t_type,item_name,quantity,".$data['quantity'].",amount,".$data['amount'].",t_date,mess_name,vendor_name,NOW() from transactions where t_id = '".$data['t_id']."'";

		$this->db->trans_start();
		$this->db->query($query);
		$this->db->trans_complete();
		return 1;


	}

	public function update_mess_return($data)
	{
		$query = "insert into temp_transactions(notification_type,t_id,t_type,item_name,quantity,new_quantity,amount,new_amount,t_date,mess_name,vendor_name,entry_time)
			select 'Edit',t_id,t_type,item_name,quantity,".$data['quantity'].",amount,".$data['amount'].",t_date,mess_name,vendor_name,NOW() from transactions where t_id = '".$data['t_id']."'";

		/*$this->db->trans_start();
		if(!($this->db->query($query)))
		{
			$error=$this->db->error();
			$this->db->trans_complete();
			return $error['message'];
		}
		else
			$this->db->trans_complete();*/
		return $data['t_id'];


	}


	public function delete_mess_consumption($t_id)
	{
		$query = "insert into temp_transactions(notification_type,t_id,t_type,item_name,quantity,new_quantity,amount,new_amount,t_date,mess_name,vendor_name,entry_time)
			select 'Delete',t_id,t_type,item_name,quantity,NULL,amount,NULL,t_date,mess_name,vendor_name,NOW() from transactions where t_id = '".$t_id."'";

		$this->db->trans_start();
		$this->db->query($query);
		$this->db->trans_complete();
		return 1;


	}



	public function delete_mess_return($t_id)
	{
		$query = "insert into temp_transactions(notification_type,t_id,t_type,item_name,quantity,new_quantity,amount,new_amount,t_date,mess_name,vendor_name,entry_time)
			select 'Delete',t_id,t_type,item_name,quantity,NULL,amount,NULL,t_date,mess_name,vendor_name,NOW() from transactions where t_id = '".$t_id."'";

		$this->db->trans_start();
		$this->db->query($query);
		$this->db->trans_complete();
		return 1;


	}


	



	
	


}
