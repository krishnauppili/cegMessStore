<script>
  $(".search").keyup(function() {
  var searchid = $(this).val();
  
});

 

      function get_report(){
            

            $(report).html("");
            $.ajax({
                  url : 'get_mess_consumption_report/'+messName+'/'+from+'/'+to,
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
                                    '<span class="blue-text">Supplied Date</span>'+
                                    '</th>'+
                                 '<th>'+
                                    '<span class="blue-text">Item Name</span>'+
                                    '</th>'+
                                 '<th>'+
                                    '<span class="blue-text">Quantity Supplied</span>'+
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
                                    editData["suppliedDate"] = data.suppliedDate[index];
                                    editData["itemName"] = encodeURIComponent(data.itemNames[index]);
                                    editData["messName"] = messName;
                                    editData["latestRate"] = data.rate[index];
                                    editData["quantitySupplied"] = data.quantitySupplied[index];

                                    var jsonEdit = JSON.stringify(editData);
                                    console.log(jsonEdit);
                                    var itemName = encodeURIComponent(data.itemNames[index]);
                                    var editID = "edit_"+data.suppliedDate[index]+'_'+itemName+'_'+messName;
                                    dataToPrint += '<tr>'+
                                       '<th>'+
                                          data.suppliedDate[index]+
                                          '</th>'+
                                       '<th>'+
                                        data.itemNames[index]+
                                          '</th>'+
                                       '<th>'+
                                          data.quantitySupplied[index]+
                                          '</th>'+
                                       '<th>'+
                                          data.rate[index]+
                                          '</th>'+
                                       '<th>'+
                                          data.amount[index]+
                                          '</th>';
                                       dataToPrint += '</tr>';
                                    console.log(data.suppliedDate[index]);
                              });
                        }

                        $("table#reports").html(dataToPrint);
                  },
                  error : function(xhr, textStatus, errorThrown){
                        console.log(errorThrown);
                  } 
            }); 

         }


      </script>
      <style type="text/css">

         select {
               border-size: 2px;
               border-color: #000066;
               border-radius: 4px;
            }


         </style>
         <form name="selection" method="post"  action="items_in_stock">
         <div class="container">
   <div class="row">
        <div class="col-md-6">
         <h5>Search</h5>
            <div id="custom-search-input">
                <div class="input-group col-md-12">
                    <div class="col-md-12">  
                        <input type="text" name="itemName" placeholder="Enter Item Name" onkeyup="printDetails(this.value)"/>
                     </div>
                    <span class="input-group-btn">
                        <button class="btn btn-info btn-lg" type="button">
                            <i class="glyphicon glyphicon-search"></i>
                        </button>
                    </span>
                </div>
            </div>
        </div>
   </div>
</div> 
            <div id="reportArea" value='reportArea'>
                  <div class="row">
                     <div class="col s12">
                        <table id="reports"></table>
                     </div>
                  </div>

               </div>

         </form> 
       

         