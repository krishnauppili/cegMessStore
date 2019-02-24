<?php echo form_open('orders/edit_order_confirmation') ?>
<div class="row">

   



</div>

<div class="row" style = "width: 900px">
  <div class="col s4 "  style = "width: 150px" >
      <span class="blue-text text-darken-2">S No</span>
   </div>
   <div class="col s4 "  style = "width: 150px" >
      <span class="blue-text text-darken-2">Item Name</span>
   </div>
   <div class="col s4"  style = "width: 150px">
      <span class="blue-text text-darken-2">Old Quantity(Kg/L)</span>
   </div>
   <div class="col s4"  style = "width: 150px">
      <span class="blue-text text-darken-2">Edited Quantity(Kg/L)</span>
   </div>
   <div class="col s4"  style = "width: 150px">
      <span class="blue-text text-darken-2">Old Rate(Kg/L)</span>
   </div>
   <div class="col s4"  style = "width: 150px">
      <span class="blue-text text-darken-2">Edited Rate</span>
   </div>
</div>


<?php 
   for($i=0;$i<count($itemName);$i++)
   {				
   ?>
   <div class="row" style = "width: 900px">
       <div class="blue-text text-darken-2 col s3" style = "width: 150px" >
         <input type='hidden' name='sno' value='<?php echo $i;?>'/>
         <?php echo $i+1;?>
      </div>
      <div class="blue-text text-darken-2 col s3" style = "width: 150px">
         <input type='hidden' name='itemName' value='<?php echo $itemName;?>'/>
         <input type='hidden' name='t_id' value='<?php echo $t_id;?>'/>
         <?php echo $itemName;?>
      </div>

      <div class="blue-text text-darken-2 col s3" style = "width: 150px">
         <input type='hidden' name='oldQuantity'
         value='<?php echo $oldQuantity;?>'/>

         <?php echo $oldQuantity;?>
      </div>


      <div class="blue-text text-darken-2 col s3" style = "width: 150px">
         <input type='hidden' name='editedQuantity'
         value='<?php echo $editedQuantity;?>'/>

         <?php echo $editedQuantity;?>
      </div>

      <div class="blue-text text-darken-2 col s3" style = "width: 150px">
         <input type='hidden' name='oldRate'
         value='<?php echo $oldRate;?>'/>
         <?php echo $oldRate;?>
      </div>

      <div class="blue-text text-darken-2 col s3" style = "width: 150px">
         <input type='hidden' name='editedRate'
         value='<?php echo $editedRate;?>'/>
         <?php echo $editedRate;?>
      </div>

   </div>
   <?php
   }
?>

<div class="row">
   <div class="col s8 offset-s3">

      <button class="btn waves-effect waves-light btn-large" 
         value="submit" type="submit" name="submit">
         Confirm
         <i class="glyphicon glyphicon-chevron-right"></i>
      </button>

      <button class="btn waves-effect waves-light red darken-1 btn-large" 
         value="cancel" type="cancel" name="cancel">
         Back
         <i class="glyphicon glyphicon-remove"></i>
      </button>
   </div>
</div>



</form>
</div>

