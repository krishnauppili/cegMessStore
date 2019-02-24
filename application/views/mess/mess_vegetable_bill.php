<script>
   function get_report(){

         var messName = encodeURIComponent($("#selectedMess").val());
         var from = encodeURIComponent($("#from").val());
         var to = encodeURIComponent($("#to").val())
         var report = $("#report");
         var itemName = $("#itemNames");
         var quantitySupplied = $("#quantitySupplied");
         var rate = $("#rate");
         var amount = $("#amount");
         var dataToPrint = "";
         var totalAmount = 0;
         $(report).html("");
         $.ajax({
               url : 'get_mess_vegetable_bill_report/'+messName+'/'+from+'/'+to,
               type : 'GET'  ,
               dataType : 'json',
               success : function(data){
                     dataToPrint +=   '<tr>'+
                        '<th>'+
                           '<span class="blue-text">Date</span>'+
                           '</th>'+
                        '<th>'+
                           '<span class="blue-text">Amount</span>'+
                           '</th>'+
                        '</tr>';   

                     $(data.suppliedDate).each(function(index){
                           totalAmount += parseFloat(data.totalAmount[index],10);
                           dataToPrint += '<tr>'+
                              '<th>'+
                                 data.suppliedDate[index]+
                                 '</th>'+
                              '<th>'+
                                 data.totalAmount[index]+
                                 '</th></tr>';
                           console.log(data.suppliedDate[index]);
                     });
                     dataToPrint += '<tr>'+
                        '<th>'+
                           'Total'+
                           '</th>'+
                        '<th>'+
                           totalAmount+
                           '</th></tr>';

                     $("table#reports").html(dataToPrint);
               }
         }); 

   }

   $(document).ready(function() {


         $("div#reportArea").hide();

         $("#printDiv").hide();
         $("#header_details").hide();
         $("#getButton").click(function(){
               var mess =($("#selectedMess").val());
               var fromDate = ($("#from").val());
               var toDate = ($("#to").val());

               if($("#selectedMess").val() == '') alert('Select the mess from the list');
               else if($("#from").val() == '') alert('Select the start date in the range');
               else if($("#to").val() == '') alert('Select the end date in the range');
               else{
                     $("#reportForMess").html($("#selectedMess").val());
                     $("#reportFrom").html($("#from").val());
                     $("#reportTo").html($("#to").val());
                     $("#printDiv").show();
                     $("div#reportArea").show();  
                     $("#header_details").show();
               }
               var func = "javascript:printPDF('reportArea','"+mess+"','"+fromDate+"','"+toDate+"');";
               $("#print-report").attr("href",func);


         });

         $( "#from" ).pickadate();

         $( "#to" ).pickadate();
   });
</script>
<style type="text/css">

   select {
         border-size: 2px;
         border-color: #000066;
         border-radius: 4px;
      }

   </style>
   <?php
      if(isset($msg)) 
      print_r($msg); 

   ?>
   <form name="selection" method="post"  action="mess_vegetable_bill"> 
      <div class="row">
         <div class="input-field col s6 offset-s3">
            <select class="browser-default" id="selectedMess" name="selectedMess" value = "<?php echo isset($selectedMess) ? $selectedMess : "";?>" required>
               <option value=''>Select Mess</option>
               <?php	
                  foreach($messTypes as $eachType)
                  {
                  ?>
                  <option value='<?php echo $eachType;?>'><?php echo $eachType;?></option>
                  <?php
                  }
               ?>
            </select>
         </div>
      </div>

      <div class="row">
         <div class = "col s6">
            <label for="from">From date</label>
            <input type="date" class="datepicker" id="from"/>

         </div>
         <div class = "col s6">
            <label for="to">To date</label>
            <input type="date" class="datepicker" id="to"/>

         </div>

      </div>

      <div class="row">
         <div class='col s6'>
            <a href="javascript:get_report();" class="btn waves-effect waves-light" id="getButton">
               Get Report
            </a>
         </div>
         <div class="col s6 offset-s6" id="printDiv">

            <a class="btn waves-effect waves-light" 
               value="print" name="print" id="print-report">
               Print
            </a>

         </div>
      </div>

      <div id="header_details">
         <div class="row">
            <div class="col s12">
               <h5><span>MESS BILL - </span><span id="reportForMess"></span></h5>
            </div>
         </div>

         <div class ="row">
            <div class="col s12">
               <h5><div class="col s6"><span>FROM:<span id="reportFrom"></span></span></div></h5>
               <h5><div class="col s6"><span>TO:<span id="reportTo"></span></span></div></h5>
            </div>
         </div>


         <div id="reportArea" value='reportArea'>


            <div class="row">
               <table id="reports"></div>
            </div>

         </div>
      </form> 
   </div>
