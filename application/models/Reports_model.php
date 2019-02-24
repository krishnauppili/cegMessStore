<?php
class Reports_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->db->db_debug = FALSE;
	}


	public function generate_mess_consumption($messName,$from,$to)
	{
		$this->db->select('*');
		$this->db->where('messName',$messName);
		$this->db->where('suppliedDate >= ',$from);
		$this->db->where('suppliedDate <= ',$to);
		$this->db->order_by('suppliedDate','desc');
		$return['itemNames'] = array();
		$return['quantitySupplied'] = array();
		$return['suppliedDate'] = array();
		$return['rate'] = array();
		$return['amount'] = array();
		$items = $this->db->get('messConsumptionTable');
		foreach($items->result() as $row)
		{
			array_push($return['itemNames'],$row->itemName);
			array_push($return['quantitySupplied'],$row->quantitySupplied);
			array_push($return['suppliedDate'],$row->suppliedDate);
			array_push($return['rate'],$row->rate);
			array_push($return['amount'],$row->amount);
		}
		return $return;
	}

public function generate_mess_return($messName,$from,$to)
	{
		$this->db->select('*');
		$this->db->where('messName',$messName);
		$this->db->where('returnedDate >= ',$from);
		$this->db->where('returnedDate <= ',$to);
		$this->db->order_by('returnedDate','desc');
		$return['itemNames'] = array();
		$return['quantityReturned'] = array();
		$return['returnedDate'] = array();
		$return['rate'] = array();
		$return['amount'] = array();
		$items = $this->db->get('messReturnTable');
		foreach($items->result() as $row)
		{
			array_push($return['itemNames'],$row->itemName);
			array_push($return['quantityReturned'],$row->quantityReturned);
			array_push($return['returnedDate'],$row->returnedDate);
			array_push($return['rate'],$row->rate);
			array_push($return['amount'],$row->amount);
		}
		return $return;
	}




	public function generate_mess_bill($messName,$from,$to)
	{
		$this->db->select('*');
		$this->db->where('messName',$messName);
		$this->db->where('date >= ',$from);
		$this->db->where('date <= ',$to);
		$this->db->order_by('date','desc');
		$return['suppliedDate'] = array();
		$return['totalAmount'] = array();
		$items = $this->db->get('messBill');
		foreach($items->result() as $row)
		{
			array_push($return['suppliedDate'],$row->date);
			array_push($return['totalAmount'],$row->totalAmount);
		}
		return $return;
	}

	public function generate_order_history()
	{
		$this->db->select('*');
		$this->db->group_by('orderID');
		$this->db->trans_start();
		$orders = $this->db->get('ordersTable');
		$output = array();
		$this->db->trans_complete();
		foreach($orders->result() as $row)
		{
			$temp['orderID'] = $row->orderID;
			$temp['vendorName'] = $row->vendorName;
			$temp['receivedDate'] = $row->receivedDate;
			$temp['items'] = array();
			$this->db->select('itemName,quantityReceived,rate,amount');
			$this->db->where('orderID',$row->orderID);
			$this->db->trans_start();
			$itemsObj = $this->db->get('ordersTable');
			$this->db->trans_complete();
			foreach($itemsObj->result() as $itemsRow){
				$items = array();
				$items['itemName'] = $itemsRow->itemName;
				$items['quantityReceived'] = $itemsRow->quantityReceived;
				$items['rate'] = $itemsRow->rate;
				$items['amount'] = $itemsRow->amount;
				array_push($temp['items'],$items);
			}
			array_push($output,$temp);
		}
		return ($output);
	}

	public function generate_payment_history()
	{
		$this->db->select('*');
		$this->db->trans_start();
		$orders = $this->db->get('paymentsTable');
		$output = array();
		$this->db->trans_complete();
		foreach($orders->result() as $row)
		{
			$temp['paymentID'] = $row->paymentID;
			$temp['paymentMode'] = $row->paymentMode;
			$temp['bankName'] = $row->bankName;
			$temp['inFavourOf'] = $row->inFavourOf;
			$temp['paymentDate'] = $row->paymentDate;
			$temp['paymentNumber'] = $row->paymentNumber;
			array_push($output,$temp);
		}
		return ($output);
	}

}
