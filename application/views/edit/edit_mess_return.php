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
               url : 'get_edit_mess_return_report/'+messName+'/'+from+'/'+to,
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
                              var editData = {};
                                    editData["t_id"] = data.t_id[index];
                                    editData["returnedDate"] = data.returnedDate[index];
                                    editData["itemName"] = encodeURIComponent(data.itemNames[index].split(' ').join('_'));
                                    editData["messName"] = messName;
                                    editData["rate"] = data.rate[index];
                                    editData["quantityReturned"] = data.quantityReturned[index];
                                    var jsonEdit = JSON.stringify(editData);

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
                                       '</th>';
                                       dataToPrint +=  '<th>'+
                                                '<a class="btn btn-small btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever='+jsonEdit+' data-keyboard="true">'+
                                                   'Edit'+
                                                   '</a>'+
                                                '</th>'+'<th>'+
                                                                  '<a href = \'javascript:deleteItemReturn('+jsonEdit+');\' class="btn btn-small btn-primary" >'+
                                                                     'Delete'+
                                                                     '</a>'+
                                                                  '</th>';
                                                                      
                                       dataToPrint += '</tr>';
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

      function deleteItemReturn(details)
      {
         var t_id = details['t_id'];
         //var messName = details['messName'];
         //var itemName = details['itemName'];
         //console.log(suppliedDate);
         //console.log(messName);
         console.log(t_id);// Extract info from data-* attributes
         if(confirm('Do you really want to delete this transaction ? This cannot be undone' )) {
               $.ajax({
                     type: "POST",
                     url: "<?php echo base_url()."mess/delete_mess_return";?>",
                     cache: false,
                     data: {'t_id' : t_id },
                     dataType: 'html',

                     success: function (resp) {
                           console.log(resp);
                           alert(resp);
                           location.reload(true);
                     },
                     error: function(err) {
                           console.log(err);
                        }
                  });  
               }

            }


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
         <form name="selection" method="post" > 
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
            <div class="modal fade in" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="memberModalLabel" aria-hidden="false">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="memberModalLabel">Edit Returns</h4>
               </div>
               <div class="modal-body">
               </div>
            </div>
         </div>

         <script>
            $('#exampleModal').on('show.bs.modal', function (event) {
                  $('body').css("margin-left", "0px");
                  var button = $(event.relatedTarget) // Button that triggered the modal
                  var recipient = button.data('whatever') // Extract info from data-* attributes
                  var modal = $(this);
                  var dataString = 'id=' + recipient;
                  console.log(recipient);
                  $.ajax({
                        type: "POST",
                        url: "<?php echo base_url()."mess/edit_mess_return_form";?>",
                        cache: false,
                        data: recipient,
                        dataType: 'html',

                        success: function (resp) {
                             console.log(resp);
                              modal.find('.modal-body').html(resp);
                        },
                        error: function(err) {
                              console.log(err);
                        }
                  });  
            })

         </script>
