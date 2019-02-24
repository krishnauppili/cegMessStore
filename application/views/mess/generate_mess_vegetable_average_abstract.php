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
            <div class='col s12'>
               <h5><span class='black-text text-darken-2'>
                <?php
                   if(empty($messName))
                     $messName = "All Mess";
                  ?>
                  Average vegetable consumption of <?php  echo $messName; ?> from <?php echo $from; ?> to <?php echo $to; ?>
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
                        Average Price
                     </span>
                  </th>
                  <th>
                     <span class='black-text text-darken-2'>
                        Total Quantity
                     </span>
                  </th>
                  <th>
                     <span class='black-text text-darken-2'>
                       Amount
                     </span>
                  </th>
               </tr>   
            <?php
               $count = count($orders);
               $tableTotal=0;
               for($i=$count-1,$j=0;$i>=0;$i--,$j++){ 
                  $tableTotal+=$totalAmount[$i];
               ?>
               <tr>
                  <th>
                     <span class='black-text text-darken-2'>
                        <?php echo $j+1; ?>
                     </span>
                  </th>
                  <th>
                     <span class='black-text text-darken-2'>
                        
                         <?php echo $vegetableNames[$i]; ?>
                     </span>
                  </th>
                   <th>
                     <span class='black-text text-darken-2'>
                        <?php echo $averagePrice[$i]; ?>
                     </span>
                  </th>
                  <th>
                     <span class='black-text text-darken-2'>
                        <?php echo $totalQuantity[$i]; ?>
                     </span>
                  </th>
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

            <a class="btn waves-effect waves-light btn-large" href="javascript:printPDF('abstract',
                '<?php $mess = str_replace(".","",$messName); echo ($mess);?>',
               '<?php echo $from;?>',
               '<?php echo $to;?>'
            );"
               value="submit" type="submit" name="submit">
               Print
               <i class="glyphicon glyphicon-chevron-right"></i>
            </a>

            <a class="btn waves-effect waves-light red darken-1 btn-large" href="mess_vegetable_average"
               value="cancel" type="reset" name="cancel">
               Cancel
               <i class="glyphicon glyphicon-remove"></i>
            </a>
         </div>
      </div>

   </form>
</div>
</div>

