<style>

   tr,th{

         border: 2px solid black;
   } 

   tr[name=no_border],th[name=no_border]{
         border: 0px solid black;
   }
   .no-border {
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
         <?php
            $grandTotal = 0;
         ?>
         <div class="row">
            <table>
               <tr>
                  <th>
                     S.No
                  </th>
                  <th>
                     Particulars
                  </th>
                  <th>
                     Bill Date
                  </th>
                  <th>
                     Amount
                  </th>
               </tr>

               <?php
               $count = count($billNos);
                  for($i=$count-1,$j=0;$i>=0;$i--,$j++){ 
                  ?>
                  <tr>
                     <th>
                        <?php echo $j+1; ?>
                     </th>
                     <th>
                        Vegetable & Fruits
                     </th>
                     <th>
                        <?php echo $receivedDates[$i]; ?>
                     </th>
                     <th>
                        <?php echo $totalAmount[$i]; ?>
                     </th>
                  </tr>

                  <?php
                     $grandTotal += $totalAmount[$i];;
                  }
               ?>
               <tr>
                  <th>
                  </th>
                  <th>
                  </th>
                  
                  <th>
                     Total
                  </th>
                  <th>
                     <?php echo $grandTotal;?>
                  </th>
               </tr>
            </table>
         </div>
      </div> 

      <div class="row">
         <div class="col s8 offset-s3">

            <a class="btn waves-effect waves-light btn-large" href="javascript:printAbstract('abstract',
               '<?php echo $vendorName;?>',
               '<?php echo $grandTotal;?>',
               '<?php echo $receivedDates[count($receivedDates)-1];?>',
               '<?php echo $receivedDates[0];?>'
               );"
               value="submit" type="submit" name="submit">
               Print
               <i class="glyphicon glyphicon-chevron-right"></i>
            </a>

            <a class="btn waves-effect waves-light red darken-1 btn-large" href="vegetable_order_history"
               value="cancel" type="reset" name="cancel">
               Cancel
               <i class="glyphicon glyphicon-remove"></i>
            </a>
         </div>
      </div>

   </form>
</div>
   </div>

