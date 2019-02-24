<?php echo form_open('orders/edit_order_transaction') ?>
<div class="row">
   <div class="col s6"> 
      <span class="blue-text txt-darken-2">Order Number:</span>
   </div>
   <div class="col s6">
      <span class="blue-text text-darken-2 order_number"><?php echo $t_id;?></span>
   </div>

</div>
  


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
