<script>
   function get_order_history(){
         var from = encodeURIComponent($("#from").val());
         var to = encodeURIComponent($("#to").val());
         var vendorName = encodeURIComponent($("#selectedVendor").val());
        
         if(vendorName == "" )
            urlPost = 'get_vegetable_order_history/'+from+'/'+to;
         else
             {
           
            urlPost = 'get_vegetable_order_history/'+from+'/'+to+'/'+vendorName; }
         $.ajax({
               url : urlPost,
               type: 'GET',
               dataType: 'json',
               success : function(data){
                     console.log(data);
                     var jsonObj = data;
                      console.log(data);
                     var htmlContents = '<form name="abstract" action="generate_vegetable_abstract" method="post">'+
                      '<div class="row">'+
                              '<div class="col s6">'+
                                 '<input type="checkbox" id="selectAll" value="Select All" name="selectAll" onClick="javascript:toggle(this)"/>'+
                                 '<label for="selectAll">Select All</label>'+
                              '</div>'+
                        '</div>';

                        htmlContents += '<ul class="collapsible" data-collapsible="accordion">';
                           for (i = 0; i < jsonObj.length; i++) {

                                 var t_id = jsonObj[i].t_id;
                                 var vendorName = jsonObj[i].vendorName;
                                 var receivedDate = jsonObj[i].receivedDate;
                                 var messName = jsonObj[i].messName;
                                 var items = jsonObj[i].items;
                                 var split = t_id.split("_");
                                 var billNo = split[2]+'_'+split[3]+'_'+split[5];
                                 console.log(billNo);

                                 htmlContents += '<li>'+		  
                                 '<div class = "collapsible-header">'+
                                    '<div class= "row margin_row">'+
                                       '<table>'+
                                          '<tr>'+
                                             '<th width="100px">'+
                                                '<input type="checkbox" name="selectedOrders[]" id="'+billNo+'" value="'+billNo+'"/>'+
                                                '<label for="'+billNo+'"></label>'+

                                                
                                                
                                                '</th>'+

                                             '<th width="250px">'+
                                                '<span class="blue-text text-darken-2">'+
                                                   vendorName +
                                                   '</span>'+
                                                '</th>'+
                                                '<th width="250px">'+
                                                '<span class="blue-text text-darken-2">'+
                                                   messName +
                                                   '</span>'+
                                                '</th>'+
                                             '<th width="100px">'+
                                                '<span class="blue-text text-darken-2">'+
                                                   receivedDate +
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
                                          '<th>'+
                                             '<span class="black-text text-darken-2">'+
                                                'Item Name'    +
                                                '</span>'+
                                             '</th>'+
                                          '<th>'+
                                             '<span class="black-text text-darken-2">'+
                                                'Quantity Received' +
                                                '</span>'+
                                             '</th>'+
                                          '<th>'+
                                             '<span class="black-text text-darken-2">'+
                                                'Proposed Rate' +
                                                '</span>'+
                                             '</th>'+

                                          '<th>'+
                                             '<span class="black-text text-darken-2">'+
                                                'Actual Rate' +
                                                '</span>'+
                                             '</th>'+
                                          '<th>'+
                                             '<span class="black-text text-darken-2">'+
                                                'Amount' +
                                                '</span>'+
                                             '</th>'+
                                          '</tr>';
                                        var tableTotal=0.0;
                                        var proposedRateTotal =0.0;
                                       for(j=0;j<items.length;j++){
                                          tableTotal+= Math.round(parseFloat(items[j].amount)*100)/100;
                                          proposedRateTotal += Math.round(parseFloat(items[j].proposedRate*items[j].quantityReceived)*100)/100;
                                             htmlContents +=  
                                             '<tr>'+
                                                '<th>'+
                                                   '<span class="black-text text-darken-2">'+
                                                      items[j].itemName +
                                                      '</span>'+
                                                   '</th>'+
                                                '<th>'+
                                                   '<span class="black-text text-darken-2">'+
                                                      items[j].quantityReceived +
                                                      '</span>'+
                                                   '</th>'+
                                                '<th>'+
                                                   '<span class="black-text text-darken-2">'+
                                                      items[j].proposedRate +
                                                      '</span>'+
                                                   '</th>'+

                                                '<th>'+
                                                   '<span class="black-text text-darken-2">'+
                                                      items[j].rate +
                                                      '</span>'+
                                                   '</th>'+
                                                '<th>'+
                                                   '<span class="black-text text-darken-2">'+
                                                      items[j].amount +
                                                      '</span>'+
                                                   '</th>'+
                                                '</tr>';
                                                


                                       }
                                       htmlContents+='<tr>'+
                                                        '<th></th>'+
                                                        
                                                        '<th>'+
                                                             '<span class="black-text text-darken-2"> TOTAL '+
                                                             '</span>'+
                                                         '</th>'+
                                                         '<th>'+
                                                              '<span class="black-text text-darken-2">'+
                                                                  proposedRateTotal+
                                                              '</span>'+
                                                         '</th>'+
                                                         '<th></th>'+
                                                         '<th>'+
                                                              '<span class="black-text text-darken-2">'+
                                                                   tableTotal+
                                                              '</span>'+
                                                         '</th>'+
                                                     ' </tr>';
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

               },
               error: function(err) {
                           console.log(err);
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
       <div class="input-field col s6">
         <select class="browser-default" name="selectedVendor" value = "<?php echo isset($selectedVendor) ? urlencode($selectedVendor) : "";?>" id="selectedVendor" required>
         <?php echo isset($selectedVendor) ? "<option value = '".urlencode($selectedVendor)."'>".$selectedVendor."</option>" : "<option value=''>Select Vendor</option>";?>"                  
            <?php
               foreach($vendors as $each) 
               {
           ?>
            <option value='<?php echo urlencode($each);?>' id='<?php echo $each;?>'><?php echo $each;?></option>
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
      <div class="col s6 offset-s6" id="printDiv">

      




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
                  <span class="blue-text">Vendor Name</span>
               </th>

                <th width="250px">
                  <span class="blue-text">Mess Name</span>
               </th>

               <th width="100px">
                  <span class="blue-text">Received Date</span>
               </th>

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



