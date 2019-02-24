<script>
function get_notification_details(){

         $.ajax({
               url : 'get_notification_details',
               type: 'GET',
               dataType: 'json',
               success : function(data){
                     console.log(data);
                     var jsonObj = data;
                     console.log(data);
                     var htmlContents = '<form name="abstract" action="notification_approval" method="post">';	
                        htmlContents += '<ul class="collapsible" data-collapsible="accordion" style = "width: 1100px">';
                           for (i = 0; i < jsonObj.length; i++) {

                                 var date = jsonObj[i].t_date;
                                 var type = jsonObj[i].t_type;
                                 var notification_type = jsonObj[i].notification_type;
                                 var vendor_name = jsonObj[i].vendor_name;
                                 
                                 var mess_name = jsonObj[i].mess_name;
                                 var items = jsonObj[i].items;
                                 console.log(items);

                                
                                 htmlContents += '<li >'+		  
                                 '<div class = "collapsible-header" >'+
                                    '<div class= "row margin_row" >'+
                                       '<table>'+
                                          '<tr >'+
                                           
                                             '<th width="100px">'+
                                                '<span class="blue-text text-darken-2">'+
                                                   date +
                                                   '</span>'+
                                                '</th>'+
                                             '<th width="50px">'+
                                                '<span class="blue-text text-darken-2">'+
                                                   type +
                                                   '</span>'+
                                                '</th>'+
                                                '<th width="100px">'+
                                                '<span class="blue-text text-darken-2">'+
                                                   notification_type +
                                                   '</span>'+
                                                '</th>'+
                                                '<th width="100px">'+
                                                '<span class="blue-text text-darken-2">'+
                                                   vendor_name +
                                                   '</span>'+
                                                '</th>'+
                                                '<th width="100px">'+
                                                '<span class="blue-text text-darken-2">'+
                                                   mess_name +
                                                   '</span>'+
                                                '</th>'+

                                             
                                             '</tr>'+
                                          '</table>'+
                                       '</div>'+
                                    '</div>';
                                 htmlContents +=  '<div class = "collapsible-body" >'+
                                    '<div class= "row" style = "width: 1200px">'+
                                       '<div class = "col s12 offset-s1">'+
                                    
                                          '<div class = "col s3" style = "width: 100px">'+
                                         

                                             '<span class="black-text text-darken-2">'+
                                                'Item Name'    +
                                                '</span>'+
                                             '</div>'+
                                          '<div class = "col s3" style = "width: 100px">'+
                                         
                                             '<span class="black-text text-darken-2">'+
                                                'Quantity' +
                                                '</span>'+
                                             '</div>'+
                                          '<div class = "col s3" style = "width: 100px">'+
                                             '<span class="black-text text-darken-2">'+
                                                'New Quantity' +
                                                '</span>'+
                                             '</div>'+
                                          '<div class = "col s3" style = "width: 100px">'+
                                             '<span class="black-text text-darken-2">'+
                                                'Rate' +
                                                '</span>'+
                                             '</div>'+
                                             '<div class = "col s3" style = "width: 100px">'+
                                             '<span class="black-text text-darken-2">'+
                                                'New Rate' +
               
                                                '</span>'+
                                             '</div>'+
                                          '</div>'+
                                       '</div>';

                                    for(j=0;j<items.length;j++){
                                    	var editData = {};
                                    editData["date"] = date;
                                    editData["type"] = type;
                                    editData["notification_type"] = notification_type;
                                    editData["vendor_name"] = vendor_name;
                                    editData["mess_name"] = mess_name;
                                    editData["t_id"] = items[j].t_id;
                                    editData["item_name"] = items[j].item_name;
                                    editData["quantity"] = items[j].quantity;
 									         editData["new_quantity"] = items[j].new_quantity;
 									         editData["rate"] = items[j].rate;
 									         editData["new_rate"] = items[j].new_rate;

                                    var jsonEdit = JSON.stringify(editData);
                                    console.log(jsonEdit);
                                          htmlContents +=  
                                          '<div class= "row" style = "width: 1100px">'+
                                             '<div class = "col s12 offset-s1" >'+
                                                '<div class = "col s3" style = "width: 100px">'+
                                                   '<span class="black-text text-darken-2" >'+
                                                      items[j].item_name +
                                                      '</span>'+
                                                   '</div>'+
                                               '<div class = "col s3" style = "width: 100px">'+
                                                   '<span class="black-text text-darken-2" >'+
                                                      items[j].quantity +
                                                      '</span>'+
                                                  '</div>'+
                                                '<div class = "col s3" style = "width: 100px">'+
                                                   '<span class="black-text text-darken-2">'+
                                                      items[j].new_quantity +
                                                      '</span>'+
                                                   '</div>'+
                                                   '<div class = "col s3" style = "width: 100px">'+
                                                   '<span class="black-text text-darken-2">'+
                                                      items[j].rate +
                                                      '</span>'+
                                                   '</div>'+
                                                   '<div class = "col s3" style = "width: 150px">'+
                                                   '<span class="black-text text-darken-2">'+
                                                      items[j].new_rate +
                                                      '</span>'+
                                                   '</div>';
                                                
                                             
                                             htmlContents += '<div class = "col s3" style = "width: 200px">'+

                                                                  '<a href = \'javascript:approveEdit('+jsonEdit+');\' class="btn btn-small btn-primary" >'+
                                                                     'Approve'+
                                                                     '</a>'+
                                                                  '</div>'+
                                                            
                                                               '<div class = "col s3" style = "width: 200px">'+
                                                                  '<a href = \'javascript:disapproveEdit('+jsonEdit+');\' class="btn btn-small btn-primary" >'+
                                                                     'Disapprove'+
                                                                     '</a>'+
                                                                  '</div>'+
                                                                  '</div>'+
                                                                  '</div>';

                                    }
                              }
                              htmlContents += "</li></div></ul>";
                        
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


            $("div#reportArea").show();
            get_notification_details();

          
      });

function approveEdit(details)
   {
         var t_id = details['t_id'];
         var date = details['date'];
         var type = details['type'];
         var notification_type = details['notification_type'];
         var vendor_name = details['vendor_name'];
         var mess_name = details['mess_name'];
         var item_name = details['item_name'];
         var quantity = details['quantity'];
         var new_quantity = details['new_quantity'];
         var rate = details['rate'];
         var new_rate = details['new_rate'];

         console.log(vendor_name);
         console.log(mess_name);
         console.log(item_name);// Extract info from data-* attributes
         if(confirm('Do you really want to approve the Edit ? This cannot be undone' )) {
               $.ajax({
                     type: "POST",
                     url: "<?php echo base_url()."items/approve_edit";?>",
                     cache: false,
                     data: {'t_id' : t_id, 'date' : date, 'type' : type, 'notification_type' : notification_type, 'vendor_name' : vendor_name , 'mess_name' : mess_name, 'item_name' : item_name, 'quantity' : quantity, 'new_quantity' : new_quantity, 'rate' : rate, 'new_rate' : new_rate},
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


   function disapproveEdit(details)
   {
         var t_id = details['t_id'];
         var date = details['date'];
         var type = details['type'];
         var notification_type = details['notification_type'];
         var vendor_name = details['vendor_name'];
         var mess_name = details['mess_name'];
         var item_name = details['item_name'];
         var quantity = details['quantity'];
         var new_quantity = details['new_quantity'];
         var rate = details['rate'];
         var new_rate = details['new_rate'];

         console.log(vendor_name);
         console.log(mess_name);
         console.log(item_name);// Extract info from data-* attributes
         if(confirm('Do you really want to approve the Edit ? This cannot be undone' )) {
               $.ajax({
                     type: "POST",
                     url: "<?php echo base_url()."items/disapprove_edit";?>",
                     cache: false,
                     data: {'t_id' : t_id, 'date' : date, 'type' : type, 'notification_type' : notification_type, 'vendor_name' : vendor_name , 'mess_name' : mess_name, 'item_name' : item_name, 'quantity' : quantity, 'new_quantity' : new_quantity, 'rate' : rate, 'new_rate' : new_rate},
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

<div id="reportArea" value='reportArea'>

      <div class="row" style = "width: 1200px" >
         <table>
            <tr>
               <th style = "width: 200px">

                  <span class="blue-text">Date</span>
               </th>
               <th style = "width: 200px">
                  <span class="blue-text">Type</span>
               </th>
               <th style = "width: 200px">
                  <span class="blue-text">Notification Type</span>
               </th>

               <th style = "width: 200px">
                  <span class="blue-text">Vendor Name</span>
               </th>

    
               <th style = "width: 200px">
                  <span class="blue-text">Mess Name</span>
               </th>
            </tr>
            </table>
            </div>


            <div id="vendorsList">
            </div>

</div>