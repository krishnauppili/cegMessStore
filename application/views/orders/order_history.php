<script>
   function get_order_history(){
         var from = encodeURIComponent($("#from").val());
         var to = encodeURIComponent($("#to").val())
         $.ajax({
               url : 'get_order_history/'+from+'/'+to,
               type: 'GET',
               dataType: 'json',
               success : function(data){
                     console.log(data);
                     var jsonObj = data;
                     console.log(data);
                     var htmlContents = '<form name="abstract" action="generate_abstract" method="post">';
                        htmlContents += '<ul class="collapsible" data-collapsible="accordion">';
                           for (i = 0; i < jsonObj.length; i++) {
                                 var t_id = jsonObj[i].t_id;
                                 var vendorName = jsonObj[i].vendor_name;
                                 var receivedDate = jsonObj[i].t_date;
                                 var items = jsonObj[i].items;
                                 console.log(jsonObj[i].vendor_name+'_'+jsonObj[i].t_date+'_'+'HOSTEL STORES');
                                 var split = t_id.split("_");
                                 var billNo = split[2]+'_'+split[3]+'_'+split[5];
                                 console.log(billNo);
                                 htmlContents += '<li>'+
                                 '<div class = "collapsible-header">'+
                                    '<div class= "row margin_row" >'+
                                       '<table >'+
                                          '<tr >'+
                                             '<th width="100px">'+
                                                '<input type="checkbox" name="selectedOrders[]" id="'+billNo+'" value="'+billNo+'"/>'+
                                                '<label for="'+billNo+'"></label>'+
                                                '</th>'+
                                             '<th width="250px">'+
                                                '<span class="blue-text text-darken-2">'+
                                                   vendorName +
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
                                 htmlContents +=  '<div class = "collapsible-body">'+
                                    '<div class= "row">'+
                                       '<div class = "col s12 offset-s1">'+
                                          '<div class = "col s3">'+
                                             '<span class="black-text text-zdarken-2">'+
                                                'Item Name'    +
                                                '</span>'+
                                             '</div>'+
                                          '<div class = "col s3">'+
                                             '<span class="black-text text-darken-2">'+
                                                'Quantity Received' +
                                                '</span>'+
                                             '</div>'+
                                          '<div class = "col s3">'+
                                             '<span class="black-text text-darken-2">'+
                                                'Rate' +
                                                '</span>'+
                                             '</div>'+
                                          '<div class = "col s3">'+
                                             '<span class="black-text text-darken-2">'+
                                                'Amount' +
                                                '</span>'+
                                             '</div>'+
                                          '</div>'+
                                       '</div>';

                                    for(j=0;j<items.length;j++){
                                          htmlContents +=
                                          '<div class= "row">'+
                                             '<div class = "col s12 offset-s1">'+
                                                '<div class = "col s3">'+
                                                   '<span class="black-text text-darken-2">'+
                                                      items[j].item_name +
                                                      '</span>'+
                                                   '</div>'+
                                                '<div class = "col s3">'+
                                                   '<span class="black-text text-darken-2">'+
                                                      items[j].quantity +
                                                      '</span>'+
                                                   '</div>'+
                                                '<div class = "col s3">'+
                                                   '<span class="black-text text-darken-2">'+
                                                      items[j].rate +
                                                      '</span>'+
                                                   '</div>'+
                                                '<div class = "col s3">'+
                                                   '<span class="black-text text-darken-2">'+
                                                      items[j].amount +
                                                      '</span>'+
                                                   '</div>'+
                                                '</div>'+
                                             '</div>';


                                    }
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


      <!--Listing vendor names-->
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
   <!--</div>-->



<!--Listing Item names-->
   <!--<div class="row">-->
       <div class="input-field col s6">
         <select class="browser-default" name="selectedItem" value = "<?php echo isset($selectedItem) ? urlencode($selectedItem) : "";?>" id="selectedItem" required>
         <?php echo isset($selectedItem) ? "<option value = '".urlencode($selectedItem)."'>".$selectedItem."</option>" : "<option value=''>Select Item</option>";?>"                  
            <?php
               foreach($items as $each) 
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

         <a href="#" class="btn waves-effect waves-light" 
            value="print" name="print" id="print-report">
            Print
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
                  <span class="blue-text">Vendor Name</span>
               </th>

               <th width="100px">
                  <span class="blue-text">Received Date</span>
               </th>

               <th width="100px">
                  <span class="blue-text">Bill No</span>
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



