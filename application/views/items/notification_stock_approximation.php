<script>

var approximationList=[];
function toggle(source) {
   checkboxes = document.getElementsByName('selectedItems[]');
   for (var i=0; i<checkboxes.length; i++) {
     checkboxes[i].checked = source.checked;
  }
}
function approveStockApproximation(details){
   var checkboxes = document.getElementsByName('selectedItems[]');
   var checkboxesChecked = [];
   for (var i=0; i<checkboxes.length; i++) {
     if (checkboxes[i].checked) {
        checkboxesChecked.push(checkboxes[i].value);
     }
   }
   console.log("Approximation List ",approximationList);
   console.log("Checkboxes checked ",checkboxesChecked);
   var itemsToApprove = [];
   var itemNameParams = [];
   var approximatedDate;
   var actualStockParams = [];
   for(var i in approximationList){
      if(checkboxesChecked.includes(approximationList[i].sid)){
         itemNameParams.push(approximationList[i].itemName);
         actualStockParams.push(approximationList[i].actualStock);
         itemsToApprove.push(approximationList[i]);
         approximatedDate = approximationList[i].date;
      }
   }
   console.log("Items to approve ",itemsToApprove);
   if(itemsToApprove.length >= 1080){
      alert("Please select less than or equal to 20 items");
   }
   else{
      itemNameParams = itemNameParams.join(',');
      actualStockParams = actualStockParams.join(',');
         if(confirm('Do you really want to approve this approximation ? This cannot be undone' )) {
            $.ajax({
                  type: "POST",
                  url: "<?php echo base_url()."items/approve_stock_approximation";?>",
                  cache: false,
                  data: {
                     'approximatedDate' : approximatedDate,
                     'itemNameParams' : itemNameParams,
                     'actualStockParams' : actualStockParams,
                  },
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
               console.log("Stock approximation details ",data);
               if(data.itemNames.length==0)
               dataToPrint += '<div class="row">'+
                  '<div class="col s8 offset-s2">'+
                     '<span class="blue-text text-darken-2">No items.</span>'+
                     '</div></div>';
               else{
                     approximationList = [];
                     for(var i in data.itemNames){
                        var approximationObject = {};
                        approximationObject["sid"] = data.sid[i];
                        approximationObject["date"] = data.date[i];
                        approximationObject["itemName"] = data.itemNames[i];
                        approximationObject["systemStock"] = data.systemStock[i];
                        approximationObject["actualStock"] = data.actualStock[i];
                        approximationObject["differencePercentage"] = data.differencePercentage[i];
                        approximationList.push(approximationObject);
                     }
                     dataToPrint+='<form id="stock_approximation_approval">'
                     dataToPrint += '<table style="width:1000px">';
                        dataToPrint += '<tr>'+
                           '<th>'+
                                 // '<input type="checkbox" id="selectAll" value="Select All" name="selectAll" onClick="javascript:toggle(this)"/>'+
                                 // '<label for="selectAll"></label>'+
                              '</th>'+
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
                           dataToPrint += '</tr>';
                           dataToPrint+='<tr>'+
                                        '<th/><th/><th/><th/><th/><th/>'+
                                        '<th>'+
                                          '<a href = \'javascript:approveStockApproximation();\' class="btn btn-small btn-primary" >'+
                                             'Approve'+
                                             '</a>'+
                                          '</th>'+
                                       '<th>'+
                                          '<a href = \'javascript:disapproveStockApproximation();\' class="btn btn-small btn-primary" >'+
                                             'Disapprove'+
                                             '</a>'+
                                          '</th>'+
                                       '</tr>';

                        $(data.itemNames).each(function(index){
                              var editData = {};
                              editData["sid"] = data.sid[index];
                              editData["date"] = data.date[index];
                              editData["itemName"] = encodeURIComponent(data.itemNames[index]);
                              editData["systemStock"] = data.systemStock[index];
                              editData["actualStock"] = data.actualStock[index];
                              editData["differencePercentage"] = data.differencePercentage[index];
                              var jsonEdit = JSON.stringify(editData);
                            //  var editID = "edit_"+data.vendorName[index]+'_'+data.ownerName[index]+'_'+data.address[index]+'_'+data.contact[index];
                              dataToPrint += '<tr>'+
                                             '<th width="100px">'+
                                                '<input type="checkbox" name="selectedItems[]" id="'+data.sid[index]+'" value="'+data.sid[index]+'"/>'+
                                                '<label for="'+data.sid[index]+'"></label>'+
                                                '</th>'+
                                 '<th style="width:200px">'+
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
                                       // dataToPrint +=    '<th>'+
                                       //    '<a href = \'javascript:approveStockApproximation('+jsonEdit+');\' class="btn btn-small btn-primary" >'+
                                       //       'Approve'+
                                       //       '</a>'+
                                       //    '</th>'+
                                       // '<th>'+
                                       //    '<a href = \'javascript:disapproveStockApproximation('+jsonEdit+');\' class="btn btn-small btn-primary" >'+
                                       //       'Disapprove'+
                                       //       '</a>'+
                                       //    '</th>';
                                // }
                        });
                     }
                                 dataToPrint += '</tr>';
                     dataToPrint += '</table>';
                     dataToPrint+='</form>';
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