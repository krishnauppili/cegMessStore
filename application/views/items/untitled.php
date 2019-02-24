<script>
   $(document).ready(function() {
    get_report();

  $(".search").keyup(function() {
  
  var searchid = $(this).val();
  console.log(searchid);
  get_report(searchid);
  
  });

   });



  function get_report(searchid="") {
  var report = $("#report");
  var searchid = $(this).val();
  var dataToPrint = "";
  console.log(searchid);
  $(report).html("");
            $.ajax({
                  url : 'get_items_stock_report/'+searchid,
                  type : 'GET'  ,
                  dataType : 'json',
                  success : function(data){
                     console.log(data);
                        if(data.itemNames.length  == 0){
                              dataToPrint += '<tr>'+
                                 '<th>'+
                                    'There are no such items'+
                                    '</th>'+
                                 '</tr>';

                        }
                        else {
                              dataToPrint += '<tr>'+                            
                                 '<th>'+
                                    '<span class="blue-text">Item Name</span>'+
                                    '</th>'+
                                 '<th>'+
                                    '<span class="blue-text">Quantity Available</span>'+
                                    '</th>'+
                                 '<th>'+
                                    '<span class="blue-text">Latest Rate</span>'+
                                    '</th>'+
                                 '<th>'+
                                    '<span class="blue-text">Old Stock</span>'+
                                    '</th>'+
                                 '</tr>' ; 

                              $(data.itemNames).each(function(index){
                                    var editData = {};
                                    editData["itemName"] = encodeURIComponent(data.itemNames[index]);
                                    editData["latestRate"] = data.rate[index];
                                    editData["quantityAvailable"] = data.quantityAvailable[index];
                                    editData["clearanceStock"] = data.clearanceStock[index];

                                    var jsonEdit = JSON.stringify(editData);
                                    console.log(jsonEdit);
                                    var itemName = encodeURIComponent(data.itemNames[index]);
                                    //var editID = "edit_"+data.suppliedDate[index]+'_'+itemName+'_'+messName;
                                    dataToPrint += '<tr>'+
                                       '<th>'+
                                          data.itemNames[index]+
                                          '</th>'+
                                       '<th>'+
                                        data.quantityAvailable[index]+
                                          '</th>'+
                                       '<th>'+
                                          data.rate[index]+
                                          '</th>'+
                                       '<th>'+
                                          data.clearanceStock[index]+
                                          '</th>';
                                       dataToPrint += '</tr>';
                                    console.log(data.itemNames[index]);
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
     <!-- <form name="selection" method="post"  action="items_search"> -->
            <div class="row">
              
            </div>

            <div class="row">
               
               <div class = "col s6">
                  <label for="to">Search</label>
                   <h1><input type="text" name="search_bar" class="search" placeholder="Enter the item name to search" /></h1>

               </div>  
            </div>

            <div id="header_details">
              


               <div id="reportArea" value='reportArea'>
                  <div class="row">
                     <div class="col s12">
                        <table id="reports"></table>
                     </div>
                  </div>

               </div>

           <!-- </form> -->
         </div>
         

         

       

         