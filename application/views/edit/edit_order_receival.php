<script>


   function get_order_history(){
         var from = encodeURIComponent($("#from").val());
         var to = encodeURIComponent($("#to").val())
         $.ajax({
               url : 'get_edit_order_history/'+from+'/'+to,
               type: 'GET',
               dataType: 'json',
               success : function(data){
                     console.log(data);
                     var jsonObj = data;
                     console.log(data);
                     var htmlContents = '<form name="abstract" id ="abstract" action="edit_order_form" method="post">';	
                        htmlContents += '<ul class="collapsible" data-collapsible="accordion" style = "width: 900px">';
                           for (i = 0; i < jsonObj.length; i++) {



                                 var t_id = jsonObj[i].t_id;
                                 
                                 var vendor_name = jsonObj[i].vendor_name;
                                 var t_date = jsonObj[i].t_date;
                                 var items = jsonObj[i].items;
                                 var items_to_edit = JSON.stringify(jsonObj[i]);

                                 htmlContents += '<li>'+		  
                                 '<div class = "collapsible-header">'+
                                    '<div class= "row margin_row">'+
                                       '<table>'+
                                          '<tr>'+
                                             '<th width="250px">'+
                                                '<span class="blue-text text-darken-2">'+
                                                   vendor_name +
                                                   '</span>'+
                                                '</th>'+
                                             '<th width="100px">'+
                                                '<span class="blue-text text-darken-2">'+
                                                   t_date +
                                                   '</span>'+
                                                '</th>'+

                                             '<th width="100px">'+
                                                '<span class="blue-text text-darken-2">'+
                                                   'View' +
                                                   '</span>'+
                                                '</th>'+
                                                '<th width="100px" >'+
                                                /*'<div class = "col s3">'+
                                                   '<a class="btn btn-small btn-primary" data-toggle="modal" data-target="#exampleModal1" data-whatever='+items_to_edit+' data-keyboard="true">'+
                                                      'Edit'+
                                                      '</a>'+
                                                   '</div>'+*/
                                                
                                                '</th>'+

                                             '</tr>'+
                                          '</table>'+
                                       '</div>'+
                                    '</div>';
                                 htmlContents +=  '<div class = "collapsible-body" >'+
                                    '<div class= "row" style = "width: 500px">'+
                                       '<div class = "col s12 offset-s1">'+
                                          '<div class = "col s3" style = "width: 150px">'+
                                             '<span class="black-text text-darken-2">'+
                                                'Item Name'    +
                                                '</span>'+
                                             '</div>'+
                                          '<div class = "col s3" style = "width: 150px">'+
                                             '<span class="black-text text-darken-2">'+
                                                'Quantity Received' +
                                                '</span>'+
                                             '</div>'+
                                          '<div class = "col s3" style = "width: 150px">'+
                                             '<span class="black-text text-darken-2">'+
                                                'Rate' +
                                                '</span>'+
                                             '</div>'+
                                          '</div>'+
                                       '</div>';

                                      /* htmlContents +=
                                          '<input type ="hidden" name = "t_id" id ="t_id" value ="kjh"/>'*/

                                    for(j=0;j<items.length;j++){
                                          var temp = {};
                                   
                                          temp["item_name"] = items[j].item_name.split(' ').join('_');
                                          temp["t_id"] = items[j].t_id;
                                          temp["quantity"] = items[j].quantity;
                                          temp["amount"] = items[j].amount;
                                          temp["rate"] = items[j].rate;
                                          var jsonEdit = JSON.stringify(temp);
                                          //console.log(jsonEdit);

                                          htmlContents +=  
                                          '<div class= "row" style = "width: 900px">'+
                                             '<div class = "col s12 offset-s1">'+
                                                '<div class = "col s3" style = "width: 150px">'+
                                                   '<span class="black-text text-darken-2">'+
                                                      items[j].item_name +
                                                      '</span>'+
                                                   '</div>'+
                                                '<div class = "col s3" style = "width: 150px">'+
                                                   '<span class="black-text text-darken-2">'+
                                                      items[j].quantity +
                                                      '</span>'+
                                                   '</div>'+
                                                '<div  class = "col s3" style = "width: 150px">'+
                                                   '<span class="black-text text-darken-2">'+
                                                      items[j].rate +
                                                      '</span>'+
                                                   '</div>'+
                                                   
                                             '<div class = "col s3" style = "width: 150px">'+
                                                   '<a class="btn btn-small btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever='+jsonEdit+' data-keyboard="true">'+
                                                      'Edit'+
                                                      '</a>'+
                                                  '</div>'+
                                                  '<div class = "col s3" style = "width: 150px">'+
                                                
                                                                  '<a href = \'javascript:deleteItemOrder('+jsonEdit+');\' class="btn btn-small btn-primary" >'+
                                                                     'Delete'+
                                                                     '</a>'+
                                                                     '</div>'+
                                                                  
                                              '</div>'+
                                          '</div>'
                                    ;


                                    }
                              }
                              htmlContents += "</li></div></ul>";
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

      function deleteItemOrder(details)
      {
         var t_id = details['t_id'];
         //var messName = details['messName'];
         //var itemName = details['itemName'];
         console.log(t_id);
         //console.log(messName);
         //console.log(itemName);// Extract info from data-* attributes
         if(confirm('Do you really want to delete this transaction ? This cannot be undone' )) {
               $.ajax({
                     type: "POST",
                     url: "<?php echo base_url()."orders/delete_order_details";?>",
                     cache: false,
                     data: {'t_id' : t_id},
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
               <th width="250px">
                  <span class="blue-text">Vendor Name</span>
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

  

      <div class="modal fade in" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="memberModalLabel" aria-hidden="false">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="memberModalLabel">Edit Order Details</h4>
         </div>
         <div class="modal-body">
         </div>
      </div>
   </div>


   <div class="modal fade in" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="memberModalLabel" aria-hidden="false">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="memberModalLabel">Edit Order Details</h4>
         </div>
         <div class="modal-body">
         </div>
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
               url: "<?php echo base_url()."orders/edit_order_form";?>",
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
   });
</script>

<script>
   $('#exampleModal1').on('show.bs.modal', function (event) {
         $('body').css("margin-left", "0px");
         var button = $(event.relatedTarget) // Button that triggered the modal
         var recipient = button.data('whatever') // Extract info from data-* attributes
         var modal = $(this);
         var dataString = 'id=' + recipient;
         console.log(recipient);
         $.ajax({
               type: "POST",
               url: "<?php echo base_url()."orders/edit_order_primary_form";?>",
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


      





