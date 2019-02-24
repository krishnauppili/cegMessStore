</form>
<?php echo form_open('items/add_vegetable_confirmation') ?>
<div class="row"></div>
<div class="row" style = "width: 600px">
   <div class="col s6" style = "width: 200px">
      <span class="blue-text text-darken-2">Item Name</span>
   </div>
   <div class="col s6" style = "width: 200px">
      <span class="blue-text text-darken-2">Item Type</span>
   </div>
   <div class="col s6" style = "width: 200px">
      <span class="blue-text text-darken-2">Precedence</span>
   </div>
</div>


<?php 
   for($i=0;$i<count($itemName);$i++)
   {				
   ?>
   <div class="row" style = "width: 600px">
      <div class="blue-text text-darken-2 col s6" style = "width: 200px">
         <input type='hidden' name='itemName[]' value='<?php echo $itemName[$i];?>'/>
         <?php echo $itemName[$i];?>
      </div>
      <div class="blue-text text-darken-2 col s6" style = "width: 200px">
         <input type='hidden' name='selectedType[]' value='<?php echo $selectedType[$i];?>'/>
         <?php echo $selectedType[$i];?>
      </div>
       <div class="blue-text text-darken-2 col s6" style = "width: 200px">
         <input type='hidden' name='precedence[]' value='<?php echo $precedence[$i];?>'/>
         <?php echo $precedence[$i];?>
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
