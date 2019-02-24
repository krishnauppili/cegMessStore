<?php echo form_open('items/return_confirmation') ?>
<div class="row"></div>
<div class="row">
   <div class="col s6">
      <span class="blue-text text-darken-2">Selected Mess</span>
   </div>
   <div class="col s6">
      <span class="blue-text text-darken-2"><?php echo $selectedMess;?></span>
      <input type='hidden' name='selectedMess' value='<?php echo $selectedMess;?>'/>
   </div>

</div>

<div class="row">
   <div class="col s6">
      <span class="blue-text text-darken-2">Issued Date</span>
   </div>
   <div class="col s6">
      <span class="blue-text text-darken-2"><?php echo $issuedDate;?></span>
      <input type='hidden' name='issuedDate' value='<?php echo $issuedDate;?>'/>
   </div>
</div>



<div class="row">
   <div class="col s6">
      <span class="blue-text text-darken-2">Selected Items</span>
   </div>
   <div class="col s6">
      <span class="blue-text text-darken-2">Quantity to Return</span>
   </div>
</div>


<?php 
   for($i=0;$i<count($selectedItems);$i++)
   {
   ?>
   <div class="row">
      <div class="blue-text text-darken-2 col s6">
         <input type='hidden' name='selectedItems[]' value='<?php echo $selectedItems[$i];?>'/>
         <?php echo $selectedItems[$i];?>
      </div>
      <input type="hidden" name='quantitySupplied[]' value='<?php echo $quantitySupplied[$i];?>'/>
      <input type="hidden" name='quantityAvailable[]' value='<?php echo $quantityAvailable[$i];?>'/>
      <input type="hidden" name='latestRate[]' value='<?php echo $latestRate[$i];?>'/>
      <div class="blue-text text-darken col s6">
         <input type='hidden' name='selectedQuantity[]' value='<?php echo $selectedQuantity[$i];?>'/>
         <?php echo $selectedQuantity[$i]." kg/l";?>
      </div>
   </div>
   <?php
   }
?>

<div class="row"></div>
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
