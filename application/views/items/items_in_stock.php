<script>
   function printPDF()
   {
         var htmlString = document.getElementById('reportArea').innerHTML;
         $.ajax({
               type: "POST",
               url: "<?php echo base_url()."reports/printReport/";?>",
               cache: false,
               data: {'toSend': htmlString},
               success: function (resp) {
                     console.log('Success');


               },
               error: function(err) {
                     console.log(err);
               }
         });  

      }

      function printDetails(val)
      {
         $.ajax({
            url : 'get_items_stock_report/'+val+'/',
            type : 'GET'  ,
            dataType : 'json',
            success : function(data){
               

            }

         });


      }
   </script>
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
   <div class="container">
      <div class="row">
         <div class="col s6" id="printDiv">
            <a href="javascript:printPDF()" class="btn waves-effect waves-light" value="print" name="print" id="print-report">
               Print
            </a>
         </div>
      </div>
      <div class="row" id="reportArea">
         <table>
            <?php
               if(!isset($itemNames) || count($itemNames) == 0)
               {
               ?>
               <tr>
                  <th>
                     <span text="blue text text-darken-2">
                        There are no items in stock. Kindly go to "Add Items" to give the opening balance of the items
                     </span>
                  </th>
               </tr>
               <?php
               }
               else{
               ?>
               <tr>
                  <th>
                     <span class="blue-text text-darken-2">Item Name</span>
                  </th>
                  <th>
                     <span class="blue-text text-darken-2">Quantity Available</span>
                  </th>
                  <th>
                     <span class="blue-text text-darken-2">Rate</span>
                  </th>
                  <th>
                     <span class="blue-text text-darken-2">Old Stock</span>
                  </th>
               </tr>
               <?php
                  for($i=0;$i<count($itemNames);$i++)
                  {
                  ?>
                  <tr>
                     <th>
                        <?php echo $itemNames[$i];?>
                     </th>
                     <th>
                        <?php echo $quantityAvailable[$i];?>
                     </th>
                     <th>
                        <?php echo $latestRate[$i];?>
                     </th>
                     <th>
                        <?php echo $clearanceStock[$i];?>
                     </th>
                  </tr>
                  <?php
                  }
               }
            ?>
         </table>
      </div>
   </div>
</div>


