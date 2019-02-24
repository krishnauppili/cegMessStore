<?php echo form_open('items/stock_approximation_confirmation') ?>
<div class="row"></div>

<div class="row">
   <div class="col s6">
      <span class="blue-text text-darken-2">Approximated Date</span>
   </div>
   <div class="col s6">
      <span class="blue-text text-darken-2"><?php echo $approximatedDate;?></span>
      <input type='hidden' name='approximatedDate' value='<?php echo $approximatedDate;?>'/>
   </div>
</div>


<div class="row">
   <div class="col s3">
      <span class="blue-text text-darken-2">S No</span>
   </div>
   <div class="col s3">
      <span class="blue-text text-darken-2">Selected Items</span>
   </div>
   <div class="col s3">
      <span class="blue-text text-darken-2">Actual Stock</span>
   </div>
   <div class="col s3">
      <span class="blue-text text-darken-2">System Stock</span>
   </div>
</div>


<?php 
   for($i=0;$i<count($selectedItems);$i++)
   {				
   ?>
   <div class="row">
      <div class="blue-text text-darken-2 col s2">
         <input type='hidden' name='sno' value='<?php echo $i;?>'/>
         <?php echo $i+1;?>
      </div>
      <div class="blue-text text-darken-2 col s5">
         <input type='hidden' name='selectedItems[]' value='<?php echo $selectedItems[$i];?>'/>
         <?php echo $selectedItems[$i];?>
      </div>

      <input type="hidden" name='quantityAvailable[]' value='<?php echo $quantityAvailable[$i];?>'/>
      <input type="hidden" name='latestRate[]' value='<?php echo $latestRate[$i];?>'/>

      <div class="blue-text text-darken-2 col s5">
         <input type='hidden' name='selectedQuantity[]'
         value='<?php echo $selectedQuantity[$i];?>'/>
         <?php echo $selectedQuantity[$i];?>
      </div>

      <div class="blue-text text-darken-2 col s5">
         <input type='hidden' name='quantityAvailable[]'
         value='<?php echo $quantityAvailable[$i];?>'/>
         <?php echo $quantityAvailable[$i];?>
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
