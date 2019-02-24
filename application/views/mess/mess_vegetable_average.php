<script>


   function get_order_history(){
          var messName = encodeURIComponent($("#selectedMess").val());
         var from = encodeURIComponent($("#from").val());
         var to = encodeURIComponent($("#to").val());

          var urlPost;
          if(messName == "" ){
            urlPost = 'get_mess_vegetable_average_report/'+from+'/'+to;
            console.log(urlPost);
         }
         else
             {
            urlPost = 'get_mess_vegetable_average_report/'+from+'/'+to+'/'+messName; 
            console.log(urlPost);
         }

         $.ajax({
               url : urlPost,
               type: 'GET',
               dataType: 'json',
               success : function(data){
                  console.log(from);
                  console.log(to);
                     console.log(data);
                     var jsonObj = data;
                     console.log(data);
                     var htmlContents = '<form name="abstract" action="generate_mess_vegetable_average_abstract" method="post">'+
             
                        '<div class="row">'+
                              '<div class="col s6">'+
                                 '<input type="checkbox" id="selectAll" value="Select All" name="selectAll" onClick="javascript:toggle(this)"/>'+
                                 '<label for="selectAll">Select All</label>'+
                              '</div>'+
                        '</div>'+




                     '<input type="hidden" name="from" value="'+$("#from").val()+'"/>' +
                  '<input type="hidden" name="to" value="'+$("#to").val()+'"/>' ; 
                        htmlContents += '<ul class="collapsible" data-collapsible="accordion">';
                           for (i = 0; i < jsonObj.length; i++) {

                                 var vegetableName = jsonObj[i].vegetableName;
                                 var totalQuantity = jsonObj[i].quantitySum;
                                 
                                 var totalSum = jsonObj[i].rateSum;
                                 var totalAmount = jsonObj[i].amountSum;
                                 //var vendorName = jsonObj[i].vendorName;
                                 var details = jsonObj[i].details;

                                 htmlContents += '<li>'+      
                                 '<div class = "collapsible-header">'+
                                    '<div class= "row margin_row">'+
                                       '<table>'+
                                          '<tr>'+
                                             '<th width="100px">'+
                                                '<input type="checkbox" name="selectedOrders[]" id="'+vegetableName.split(' ').join('_')+'" class="checkme" value="'+vegetableName+'"/>'+
                                                 '<label for="'+vegetableName.split(' ').join('_')+'"></label>'+
                     '<input type="hidden" name="messName" id="'+messName.split(' ').join('_')+'" value="'+messName+'"/>'+
                                               

                                                '</th>'+
                                             '<th width="250px">'+
                                             '<input type="hidden" name="vegetableNames[]" value="'+vegetableName+'"/>'+
                                                '<span class="blue-text text-darken-2">'+
                                                   vegetableName +
                                                   '</span>'+
                                                '</th>'+

                                                 '<th width="100px">'+
                                                '<input type="hidden" name="averagePrice[]" value="'+(totalAmount/totalQuantity).toLocaleString()+'"/>'+
                                                '<span class="blue-text text-darken-2">'+
                                                   (totalAmount/totalQuantity).toLocaleString() +
                                                   '</span>'+
                                                '</th>'+


                                             '<th width="100px">'+
                                             '<input type="hidden" name="totalQuantity[]" value="'+totalQuantity+'"/>'+
                                                '<span class="blue-text text-darken-2">'+
                                                    totalQuantity +
                                                   '</span>'+
                                                '</th>'+
                                            /* '<th width="100px">'+
                                                '<span class="blue-text text-darken-2">'+
                                                   totalSum +
                                                   '</span>'+
                                                '</th>'+
                                               */ 
                                             '<th width="100px">'+
                                             '<input type="hidden" name="totalAmount[]" value="'+totalAmount+'"/>'+
                                                '<span class="blue-text text-darken-2">'+
                                                   totalAmount +
                                                   '</span>'+
                                                '</th>'+
                                               
                                             '<th width="100px">'+
                                                '<span class="blue-text text-darken-2">'+
                                                   'View' +
                                                   '</span>'+
                                                '</th>'+
                                             '</tr>'+
                                          '</table>'+
                                       '</div>'+
                                    '</div>';
                                 htmlContents +=  '<div class = "collapsible-body"><table>'+
                                       '<tr>'+
                                          '<th width = "100px">'+
                                             '<span class="black-text text-darken-2">'+
                                                'Received Date'    +
                                                '</span>'+
                                             '</th>'+
                                          '<th width = "100px">'+
                                             '<span class="black-text text-darken-2">'+
                                                'Quantity Received' +
                                                '</span>'+
                                             '</th>'+
                                              '<th width = "250px">'+
                                                   '<span class="black-text text-darken-2">'+
                                                      'Vendor Name' +
                                                      '</span>'+
                                                   '</th>'+
                                          
                                          '<th width = "100px">'+
                                             '<span class="black-text text-darken-2">'+
                                                'Actual Rate' +
                                                '</span>'+
                                             '</th>'+
                                          '<th width = "100px">'+
                                             '<span class="black-text text-darken-2">'+
                                                'Amount' +
                                                '</span>'+
                                             '</th>'+
                                          '</tr>';

                                       for(j=0;j<details.length;j++){
                                             htmlContents +=  
                                             '<tr>'+
                                                '<th width = "100px">'+
                                                   '<span class="black-text text-darken-2">'+
                                                      details[j].receivedDate +
                                                      '</span>'+
                                                   '</th>'+
                                                '<th width = "100px">'+
                                                   '<span class="black-text text-darken-2">'+
                                                      details[j].quantityReceived +
                                                      '</span>'+
                                                   '</th>'+
                                                   '<th width = "250px">'+
                                                   '<span class="black-text text-darken-2">'+
                                                      details[j].vendorName +
                                                      '</span>'+
                                                   '</th>'+
                                                '<th width = "100px">'+
                                                   '<span class="black-text text-darken-2">'+
                                                      details[j].actualRate +
                                                      '</span>'+
                                                   '</th>'+

                                                
                                                '<th width = "100px">'+
                                                   '<span class="black-text text-darken-2">'+
                                                      details[j].amount +
                                                      '</span>'+
                                                   '</th>'+
                                                '</tr>';


                                       }
                                       htmlContents += '</table>';
                              }
                              htmlContents += "</li></div></ul>";
                        htmlContents +=  '<button class="btn waves-effect waves-light btn-large" type="submit"'+ 
                           'value="submit" name="submit" id="submit">'+
                           ' Generate Abstract'+
                           ' </button>';
                        htmlContents += '</form>';

                     $("div#vendorsList").html(htmlContents);
                     $('.collapsible').collapsible({
                           accordion : false // A setting that changes the collapsible behavior to expandable instead of the default accordion style
                     });

               }

         });
      }
      $(document).ready(function() {


            $("div#reportArea").hide();

            $("#printDiv").hide();

            $("#getButton").click(function(){
                  if($("#from").val() == '') alert('Select start date for the range');
                  else if($("#to").val() == '') alert('Select end date for the range');
                  else {
                        $("#reportFrom").html($("#from").val());
                        $("#reportTo").html($("#to").val());
                        $("div#reportArea").show();
                        $("#printDiv").show();
                  }
            });

            $( "#from" ).pickadate();

            $( "#to" ).pickadate();
      });
   </script>

   <div class="row">
               <div class="input-field col s6 offset-s3">
                  <select class="browser-default" id="selectedMess" name="selectedMess" value = "<?php echo isset($selectedMess) ? $selectedMess : "";?>" required>
                     <option value="">Select Mess</option>
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
         <a href="javascript:get_order_history();" class="btn waves-effect waves-light" id="getButton">
            Get Report
         </a>
      </div>   
   </div>




   <div id="reportArea" value='reportArea'>



      <div class="row">
         <table>
            <tr>
               <th width="100px">

                  <span class="blue-text">Select</span>
               </th>
           
               <th width="250px">
                  <span class="blue-text">Vegetable Name</span>
               </th>

               </th>
                 <th width="100px">
                  <span class="blue-text">Average Price</span>
               </th>
               
               <th width="100px">
                  <span class="blue-text">Total Quantity </span>
               </th>

             
               <th width="100px">
                  <span class="blue-text">Total Amount</span>
                  
               
               <th width="100px">
                  <span class="blue-text">Action</span>
               </th>
            </tr>
            <table>
            </div>



            <div id="vendorsList">
            </div>

         </div>


      </div>



