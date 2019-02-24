<script>


function approveStockApproximation(details)
   {
         var approximatedDate = details['date'];
         var itemName = details['itemName'];
         var systemStock = details['systemStock'];
         var actualStock = details['actualStock'];
         var differencePercentage = details['differencePercentage'];
         console.log(approximatedDate);
         console.log(itemName);
         console.log(systemStock);// Extract info from data-* attributes
         if(confirm('Do you really want to approve this approximation ? This cannot be undone' )) {
               $.ajax({
                     type: "POST",
                     url: "<?php echo base_url()."items/approve_stock_approximation";?>",
                     cache: false,
                     data: {'approximatedDate' : approximatedDate, 'itemName' : itemName, 'systemStock' : systemStock , 'actualStock' : actualStock , 'differencePercentage' : differencePercentage  },
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

function disapproveStockApproximation(details)
{
   console.log(details);
   var sid = details['sid'];
   console.log(sid);
   if(confirm('Do you really want to disapprove this approximation ? This cannot be undone' )) {
         $.ajax({
                     type: "POST",
                     url: "<?php echo base_url()."items/disapprove_stock_approximation";?>",
                     cache: false,
                     data: {'sid' : sid },
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
  
function get_stock_approximation_details(){

                           var report = $("#report");
                           var dataToPrint = "";
                           $(report).html("");
                           $.ajax({
                                 url : 'get_stock_approximation_details/',
                                 type : 'GET'  ,
                                 dataType : 'json',
                                 success : function(data){
                                       console.log(data);
                                       console.log(data.itemNames.length);
                                       if(data.itemNames.length==0)
                                       dataToPrint += '<div class="row">'+
                                          '<div class="col s8 offset-s2">'+
                                             '<span class="blue-text text-darken-2">No items.</span>'+
                                             '</div></div>';
                                       else{
                                             dataToPrint += '<table>';
                                                dataToPrint += '<tr>'+
                                             
                                                   '<th>'+
                                                      '<span class="blue-text">Date</span>'+
                                                      '</th>'+

                                                   '<th>'+
                                                      ' <span class="blue-text">Item Name</span>'+
                                                      '</th>'+

                                                   '<th>'+
                                                      '<span class="blue-text">System Stock</span>'+
                                                      ' </th>'+
                                                   '<th>'+
                                                      '<span class="blue-text">Actual Stock</span>'+
                                                      '</th>'+
                                                      '<th>'+
                                                      '<span class="blue-text">Difference Percentage</span>'+
                                                      '</th>'+
                                                      '<th>'+
                                                      '<span class="blue-text">Action</span>'+
                                                      '</th>'+
                                                      '<div class="row">';
                                                  
                                                   dataToPrint += '</tr>'

                                                $(data.itemNames).each(function(index){
                                                      var editData = {};
                                                      editData["sid"] = data.sid[index];
                                                      editData["date"] = data.date[index];      
                                                      editData["itemName"] = encodeURIComponent(data.itemNames[index]);
                                                      editData["systemStock"] = data.systemStock[index];
                                                      editData["actualStock"] = data.actualStock[index];
                                                      editData["differencePercentage"] = data.differencePercentage[index];

                                                      var jsonEdit = JSON.stringify(editData);
                                                      console.log(jsonEdit);

                                                    //  var editID = "edit_"+data.vendorName[index]+'_'+data.ownerName[index]+'_'+data.address[index]+'_'+data.contact[index];

                                                      dataToPrint += '<tr>'+
                                                      

                                                         '<th>'+
                                                            data.date[index]+
                                                            '</th>'+
                                                         '<th>'+
                                                            data.itemNames[index]+
                                                            '</th>'+
                                                         '<th>'+
                                                            data.systemStock[index]+
                                                            '</th>'+
                                                         '<th>'+
                                                            data.actualStock[index]+
                                                            '</th>'+
                                                            '<th>'+
                                                            data.differencePercentage[index]+
                                                            '</th>';
                                                         //   if(admin) {
                                                               dataToPrint +=    '<th>'+
                                                                  '<a href = \'javascript:approveStockApproximation('+jsonEdit+');\' class="btn btn-small btn-primary" >'+
                                                                     'Approve'+
                                                                     '</a>'+
                                                                  '</th>'+
                                                               '<th>'+
                                                                  '<a href = \'javascript:disapproveStockApproximation('+jsonEdit+');\' class="btn btn-small btn-primary" >'+
                                                                     'Disapprove'+
                                                                     '</a>'+
                                                                  '</th>';
                                                        // }
                                                         
                                                         
                                                      console.log(data.itemNames[index]);
                                                });
                                             }
                                             
                                                         dataToPrint += '</tr>';
                                             dataToPrint += '</table>';
                                          console.log(dataToPrint);
                                          $("div#stockApproximationList").html(dataToPrint);
                                    },
                                    error: function(xhr, status, error) {
                                          var err = eval("(" + xhr.responseText + ")");
                                          alert(err.Message);
                                    }
                              }); 

                           }

                           $(document).ready(function() {
                                 get_stock_approximation_details();
                                
                                 

                           });
</script>

                   
<div id="reportArea" value='reportArea'>

   <div class="row">
         <div class="col s12">
               <div id="stockApproximationList"></div>
         </div>
   </div>

</div>

                     
                     