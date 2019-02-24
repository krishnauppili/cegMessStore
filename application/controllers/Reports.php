<?php
   defined('BASEPATH') OR exit('No direct script access allowed');

   class Reports extends CI_Controller {

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

         $this->load->model('reports_model');
         $this->load->model('mess_model');
         $this->load->helper('form');
         $this->load->helper('url');
         $this->load->library('session');
         $this->load->library('form_validation');
         $this->load->helper('date');

         $this->load->library('ion_auth');
         $this->load->library('pdf');


      }

      public function printReport ($title="",$mess,$from,$to)
      {
         set_time_limit(0);
         
         $pdf = new Pdf("P", PDF_UNIT, "A4",true, 'UTF-8', false);

         $date = date('d-m-Y');
         $from =  date('d-m-Y',strtotime($from));
         $to =  date('d-m-Y',strtotime($to));
         // set header and footer fonts
         // set margins
         $pdf->setTitle(strtoupper($title));
         $pdf->Header();
         $pdf->Footer();
         $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT,true);
         $pdf->SetFooterMargin(40);

         //$pdf->SetMargins (15, 27, 15, true);

         $pdf->SetFont('times', '', 16);
         $pdf->SetAutoPageBreak(TRUE,10);
         $pdf->AddPage();
         

         $pdf->SetFont('times', '', 14);

         if (strpos($title,'Vegetable Bill') !== false) {
            $text = "Bill for ". $mess;
            $filename = $mess."_".$from."_".$to."_".$date;
 		 	$dir = "Mess Vegetable Bill\\".$filename.".pdf";
         }
         else if (strpos($title,'Mess Bill') !== false) {
            $text = "Bill for ". $mess;
            $filename = $mess."_".$from."_".$to."_".$date;
 		 	$dir = "Mess Bill\\".$filename.".pdf";
         }
         else if (strpos($title,'Vegetable Consumption') !== false) {
            $text = "Consumption of ". $mess;
            $filename = $mess."_".$from."_".$to."_".$date;
 		 	$dir = "Mess Vegetable Consumption\\".$filename.".pdf";
         }
         else if (strpos($title,'Consumption') !== false) {
            $text = "Consumption of ". $mess;
            $filename = $mess."_".$from."_".$to."_".$date;
 		 	$dir = "Mess Consumption\\".$filename.".pdf";
         }
         
         else if (strpos($title,'Returns') !== false) {
            $text = "Item returns report of ". $mess;
            $filename = $mess."_".$from."_".$to."_".$date;
 		       	$dir = "Mess Returns\\".$filename.".pdf";
         }
         else if (strpos($title,'Vegetable Average') !== false) {


            $text = "Mess Vegetable Average report of ". $mess;

            $filename = $mess."_".$from."_".$to."_".$date;
            $dir = "Mess Vegetable Average\\".$filename.".pdf";
         }
         else if (strpos($title,'Average') !== false) {
            $text = "Item average report of ". $mess;
            $filename = $mess."_".$from."_".$to."_".$date;
            $dir = "Mess Average\\".$filename.".pdf";
         }
         else if (strpos($title,'Details') !== false) {
            $text = "Details are given below";
         }



         $pdf->Ln();
         if($from != "undefined")
         {
            if($from == $to)
            $text .= " on the date ".$from;
            else
            $text .= " during the period from ".$from." to ".$to;
         }
         $pdf->SetFont('times', '', 12);

         $pdf->Cell(0, 0, $text, 0, 0, 'C');
         $pdf->Ln();


         $pdf->SetFont('times', '', 12);
         $html = "";
         //create html
         $html .= '<html><head><title>Report</title>';

               $html .= '</head><body >';
               $base_path = base_url();

               $html .= '<style>table,tr,th{border: 1px solid black;}
                  tr[name=no_border],th[name=no_border]{border: 0px solid black;}</style>';
               $html .= $_POST['toSend'];
               $html .= ('</body></html>');

         $pdf->writeHTML($html, false, false, false, false, '');


    $pdf->SetFont('times', '', 14);
         $pdf->Ln();

         $pdf->Cell(0, 0, '', 0, 0, 'R');

         $pdf->Ln();
         $pdf->Ln();
         $pdf->Ln();
         $pdf->Ln();
         $pdf->Ln();
        // $html = '<table><tr><th>Store Manager</th><th>Deputy Warden</th><th>Hostel Warden</th></tr></table>';
        // $pdf->writeHTML($html, false, false, false, false, '');

        //$pdf->Output("Applications\\xampp\\htdocs\\cegMessStore_new\\reports\\".$dir, 'FD');
        //$pdf->Output("file:///Applications/XAMPP/xamppfiles/htdocs/cegMessStore_new/reports/".$dir, 'FD');
          //save pdf
         $pdf->Output("C:\\xampp\\htdocs\\cegMessStore_new\\reports\\".$dir, 'FD');  //save pdf
         
         //		$pdf->Output('file.pdf', 'I'); // show pdf

         return true;
      }

      public function printAbstract ($title="",$vendorName,$total,$startDate,$endDate)
      {
         set_time_limit(0);

         $pdf = new Pdf("P", PDF_UNIT, "A4",true, 'UTF-8', false);

         $date = date('d-m-Y');
         $startDate =  date('d-m-Y',strtotime($startDate));
         $endDate =  date('d-m-Y',strtotime($endDate));
         
         $filename = $vendorName."_".$startDate."_".$endDate."_".$date;

         // set header and footer fonts
         // set margins
         $pdf->setTitle('');
         $pdf->Header();
         $pdf->Footer();
         $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT,true);
         $pdf->SetFooterMargin(40);

         //$pdf->SetMargins (15, 27, 15, true);

         $pdf->SetFont('times', '', 14);
         $pdf->SetAutoPageBreak(TRUE,0);
         $pdf->AddPage();


         $pdf->Cell(0, 0, 'ENGINEERING COLLEGE HOSTELS', 0, 0, 'C');
         $pdf->Ln();

         $pdf->SetFont('times', '', 12);
         $pdf->Cell(0, 0, 'COLLEGE OF ENGINEERING, GUINDY', 0, 0, 'C');
         $pdf->Ln();
         $pdf->Cell(0, 0, 'ANNA UNIVERSITY, CHENNAI-25', 0, 0, 'C');
         $pdf->Ln();

         $pdf->SetFont('times', '', 14);
         $pdf->Cell(0, 0, 'ABSTRACT OF SUPPLIER\'S BILL', 0, 0, 'C');


         $pdf->Ln();
         $pdf->Ln();



         $pdf->SetFont('times', '', 14);
         $text = "BILL RECEIVED FROM ".$vendorName.""; //FOR THE SUPPLIES ";

         $pdf->Cell(0, 0, $text, 0, 0, 'L');
         $pdf->Ln();
         if($startDate == $endDate)
         $text = "FOR THE SUPPLIES MADE ON ".$startDate;
         else
         $text = "FOR THE SUPPLIES MADE BETWEEN ".$startDate." AND ".$endDate;


         $pdf->Ln();
         $pdf->MultiCell(0, 0, $text, 0, 'L', false);

         $pdf->SetFont('times', '', 12);

         $html = "";
         //create html
         $html .= '<html><head><title>Report</title>';
               $html .= '</head><body >';
               $base_path = base_url();

               $html .= '<style>tr,th{border: 1px solid black;}</style>';
               $html .= $_POST['toSend'];
               $html .= ('</body></html>');

         $pdf->writeHTML($html, false, false, false, false, '');

         $inWords = $this->convert_number($total);
         $text = "BILL PASSED FOR Rs.".$total."  (RUPEES ".$inWords.") ONLY";



         $pdf->SetFont('times', '', 14);
         $pdf->Ln();

         $pdf->MultiCell(0,0,$text,0,'L',false);


        
         $pdf->Ln();
         $pdf->Ln();
        // $html = '<table><tr><th>Store Manager</th><th>Deputy Warden </th><th>Executive Warden</th></tr><tr><th></th><th>(Accounts)</th></tr></table>';
        //$pdf->writeHTML($html, false, false, false, false, '');


         //	$pdf->Output('C:\xampp\htdocs\cegMessStore\reports\report.pdf', 'FD');  //save pdf
         if(strpos($title,"Vegetable") !== false)
         $dir = "Vegetable Abstract\\".$filename.".pdf";
       
         else
         $dir = "Items Abstract\\".$filename.".pdf";
        $pdf->Output("C:\\xampp\\htdocs\\cegMessStore_new\\reports\\".$dir, 'FD'); 

     	//$pdf->Output("C:\\xampp\\htdocs\\cegMessStore\\reports\\".$dir, 'FD');  //save pdf
     	
         //$pdf->Output("/var/www/cegMessStore/reports/".$dir, 'FD');  //save pdf
         //		$pdf->Output('file.pdf', 'I'); // show pdf

         return true;
      }
 
     
  public function convert_number($number) {
    $number = floor($number);
         
   $words = array('0'=> '' ,'1'=> 'ONE' ,'2'=> 'TWO' ,'3' => 'THREE','4' => 'FOUR','5' => 'FIVE','6' => 'SIX','7' => 'SEVEN','8' => 'EIGHT','9' => 'NINE','10' => 'TEN','11' => 'ELEVEN','12' => 'TWELVE','13' => 'THIRTEEN','14' => 'FOURTEEN','15' => 'FIFTEEN','16' => 'SIXTEEN','17' => 'SEVENTEEN','18' => 'EIGHTEEN','19' => 'NINETEEN','20' => 'TWENTY','30' => 'THIRTY','40' => 'FOURTY','50' => 'FIFTY','60' => 'SIXTY','70' => 'SEVENTY','80' => 'EIGHTY','90' => 'NINETY','100' => 'HUNDRED AND','1000' => 'THOUSAND','100000' => 'LAKH','10000000' => 'CRORE');
    if($number == 0)
        return ' ';
    else {
   $novalue='';
   $highno=$number;
   $remainno=0;
   $value=100;
   $value1=1000;       
            while($number>=100)    {
                if(($value <= $number) &&($number  < $value1))    {
                $novalue=$words["$value"];
                $highno = (int)($number/$value);
                $remainno = $number % $value;
                break;
                }
                $value= $value1;
                $value1 = $value * 100;
            }       
          if(array_key_exists("$highno",$words))
              return $words["$highno"]." ".$novalue." ".$this->convert_number($remainno);
          else {
             $unit=(int)($highno % 10);
             $ten =(int)($highno/10)*10;     
          
             return $words["$ten"]." ".$words["$unit"]." ".$novalue." ".$this->convert_number($remainno);
           }
    }
      


   }

}