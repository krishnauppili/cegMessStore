<style>

   tr,th{
         border: 1px solid black;
   } 
</style>
<script>
   $(document).ready(function() {
 

         $( "#paymentDate" ).pickadate();
   });

</script>

<div class='col s12 offset-s2'>
   <form>
    <div class='row'>
            <div class='col s6'>
               <h5><span class='black-text text-darken-2'>
                   <?php echo $vendorName; ?>
                  </span>
               </h5>
            </div>
         </div>
         
      <div id='abstract'>
        
         <table>
            <tr>
                  <th>
                     <span class='black-text text-darken-2'>
                        S.No
                     </span>
                  </th>
                  <th>
                     <span class='black-text text-darken-2'>
                        Item Name
                     </span>
                  </th>
                  <th>
                     <span class='black-text text-darken-2'>
                        Received Date
                     </span>
                  </th>
                  <!--<th>
                     <span class='black-text text-darken-2'>
                        Bill no
                     </span>
                  </th>-->
                  <th>
                     <span class='black-text text-darken-2'>
                       Amount
                     </span>
                  </th>
               </tr>   
            <?php
               $count = count($billNos);
              
               for($i=0;$i<$count;$i++){ 
                  
               ?>
               <tr>
                  <th>
                     <span class='black-text text-darken-2'>
                        <?php echo $i+1; ?>
                     </span>
                  </th>
                  <th>
                     <span class='black-text text-darken-2'>
                        <?php echo "Provisions     "; ?>
                     </span>
                  </th>
                  <th>
                     <span class='black-text text-darken-2'>
                        <?php echo $receivedDates[$i]; ?>
                     </span>
                  </th>
                  <!--<th>
                     <span class='black-text text-darken-2'>
                        <?php echo $billNos[$i]; ?>
                     </span>
                  </th>-->
                  <th>
                     <span class='black-text text-darken-2'>
                        <?php echo $totalAmount[$i]; ?>
                     </span>
                  </th>
               </tr>

               <?php
               }
            ?>
            <tr>
                  <th>
                     
                  </th>
                  <th>
                     
                  
                    
                  </th>
                  <th>
                     <span class='black-text text-darken-2'>
                        Total
                     </span>
                  </th>
                  <th>
                     <span class='black-text text-darken-2'>
                       <?php echo $tableTotal; ?>
                     </span>
                  </th>
               </tr>
         </table>
      </div>
      <div class="row"></div>
      <div class="row">
         <div class="col s8 offset-s3">

            <a class="btn waves-effect waves-light btn-large" href="javascript:printAbstract('abstract',
               '<?php echo $vendorName;?>',
               '<?php echo $tableTotal;?>',
               '<?php echo $receivedDates[count($receivedDates)-1];?>',
               '<?php echo $receivedDates[0];?>'
               );"
               value="submit" type="submit" name="submit">
               Print
               <i class="glyphicon glyphicon-chevron-right"></i>
            </a>

            <a class="btn waves-effect waves-light red darken-1 btn-large" href="order_history"
               value="cancel" type="reset" name="cancel">
               Cancel
               <i class="glyphicon glyphicon-remove"></i>
            </a>
         </div>
      </div>

   </form>
</div>
</div>

