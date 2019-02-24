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
         $(report).html("");
         $.ajax({
               url : 'get_mess_return_report/'+messName+'/'+from+'/'+to,
               type : 'GET'  ,
               dataType : 'json',
               success : function(data){

                     if(data.itemNames.length  == 0){
                           dataToPrint += '<tr>'+
                              '<th>'+
                                 'There are no items issued within this period'+
                                 '</th>'+
                              '</tr>';

                     }
                     else {
                           dataToPrint += '<tr>'+                            
                              '<th>'+
                                 '<span class="blue-text">Returned Date</span>'+
                                 '</th>'+
                              '<th>'+
                                 '<span class="blue-text">Item Name</span>'+
                                 '</th>'+
                              '<th>'+
                                 '<span class="blue-text">Quantity Returned</span>'+
                                 '</th>'+
                              '<th>'+
                                 '<span class="blue-text">Rate</span>'+
                                 '</th>'+
                              '<th>'+
                                 '<span class="blue-text">Amount</span>'+
                                 '</th>'+
                              '</tr>' ; 

                           $(data.itemNames).each(function(index){
                                 dataToPrint += '<tr>'+
                                    '<th>'+
                                       data.returnedDate[index]+
                                       '</th>'+
                                    '<th>'+
                                       data.itemNames[index]+
                                       '</th>'+
                                    '<th>'+
                                       data.quantityReturned[index]+
                                       '</th>'+
                                    '<th>'+
                                       data.rate[index]+
                                       '</th>'+
                                    '<th>'+
                                       data.amount[index]+
                                       '</th></tr>';
                                 console.log(data.itemNames[index]);
                           });

                     }
                     $("table#reports").html(dataToPrint);
               } 

         });
      }

   </script>
   <script>
      $(document).ready(function() {



            $("div#reportArea").hide();

            $("div#printDiv").hide();
            $("div#header_details").hide();
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
                        $("div#header_details").show(); 
                  }
                  var func = "javascript:printPDF('reportArea','"+mess+"','"+fromDate+"','"+toDate+"');";
                  $("#print-report").attr("href",func);
            });

            $( "#from" ).pickadate();

            $( "#to" ).pickadate();
      });
   </script>
   <style type="text/css">
      .controls {
            margin: 75px;
         }

         select {
               border-size: 2px;
               border-color: #000066;
               border-radius: 4px;
            }



         </style>
         <form name="selection" method="post"  action="mess_return"> 
            <div class="row">
               <div class="col s6 offset-s3">
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
               <div class = 'col s6'>
                  <a href="javascript:get_report();" class="btn waves-effect waves-light" id="getButton">
                     Get Report
                  </a>
               </div>

               <div class="col s6 offset-s6" id="printDiv">
                  <a class="btn waves-effect waves-light" value="print" name="print" id="print-report">
                     Print
                  </a>
               </div>

               <div id="header_details">
                  <div class="row">
                     <div class="col s8">
                        <h5><span>MESS RETURN - <span id="reportForMess"></span></span></h5>
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
                        <div class="col s12">
                           <table id="reports"></div>
                        </div>
                     </div>

                  </div>

               </form> 
            </div>
