<script>
   var selectedItems = null;
   selectedItems = <?php if(isset($selectedItems)) echo json_encode($selectedItems); else echo json_encode(NULL);?>;
   var selectedQuantity = <?php if(isset($selectedQuantity)) echo json_encode($selectedQuantity); 
                                else echo json_encode(NULL);?>;
   $(function(){
         var tabindex = 1;
         $('input,select,button,date').each(function() {
               if (this.type != "hidden") {
                     var $input = $(this);
                     $input.attr("tabindex", tabindex);
                     tabindex++;
               }
         });
         $('#approximatedDate').pickadate();

         if(selectedItems)
         {
             // var selectedMess = <?php //if(isset($selectedMess)) echo json_encode($selectedMess);
                                       // else echo json_encode(NULL);?>;
               var approximatedDate = <?php if(isset($approximatedDate)) echo json_encode($approximatedDate);
                                      else echo json_encode(NULL);?>;
               // console.log(selectedMess);
               // $("select#selectedMess").find('option[value="' + selectedMess + '"]').prop('selected', true);
                $("#approximatedDate").pickadate('picker').set('select',new Date(approximatedDate));
               for(var i=0;i<selectedItems.length;i++)
               {
                    var quantityID = "#txt"+selectedItems[i].split(" ").join("_");
                     var quantity = selectedQuantity[i];
                     $(quantityID).attr('value',quantity);
                     $(quantityID).css('background-color','#ffff00');
               }
         }
   });
   function change_color(obj)
   {
         if($(obj).val().length !== 0) $(obj).css('background-color','#ffff00');
         else $(obj).css('background-color','#fff');
   }

</script>
<?php
   $decodedTableData = json_decode($tableData,true);
   $size = count($decodedTableData['itemNames']);
?>
<form name="selection" method="post"  action="stock_approximation" > 
   <div class="row">

      <div class = "input-field col s6">
         <input type="date" class="datepicker" id="approximatedDate" name="approximatedDate" placeholder='Select Date' required/>
      </div>

   </div>
   <div class='row'>
      <div class='col s4'>
         <span class='blue-text text-darken-2'>Item Name</span>
      </div>

      <div class='col s4'>
         <span class='blue-text text-darken-2'>Actual Stock</span>
      </div>

      <div class='col s4'>
         <span class='blue-text text-darken-2'>System Stock</span>
      </div>

   </div>


   <?php
      for($i=0;$i<count($decodedTableData['itemNames']);$i++)
      {
      ?>
      <div class="row margin_row">

         <div class="col s1">
             <p>
               <input type='text' name='sno' value='<?php echo $i+1;?> ' readonly/>
             </p>
         </div>
         <div class = "col s4 ">
            <p>
            <input type='text' value='<?php echo $decodedTableData['itemNames'][$i];?>' 
            id='<?php echo $decodedTableData['itemNames'][$i];?>' 
            name='selectedItems[]' readonly/>
            </p>
         </div>
         <div class="col s4">
            <input type="number" step = 0.01 name="selectedQuantity[]" value="" id='<?php echo 'txt'.str_replace(' ', '_', $decodedTableData['itemNames'][$i]);?>' placeholder='Enter Quantity' onchange="javascript:change_color(this)"/>
       
         </div>
         <div class="col s3">
            <span class="blue-text text-darken-2"><h5><?php echo $decodedTableData['quantityAvailable'][$i];?></h5></span>
            
         </div>

      </div>
      <?php
      }
   ?>


   <div class="row">
      <div class="col s8 offset-s3">

         <button class="btn waves-effect waves-light btn-large" 
            value="submit" type="submit" name="submit">
            Submit
            <i class="glyphicon glyphicon-chevron-right"></i>
         </button>

         <button class="btn waves-effect waves-light red darken-1 btn-large" 
            value="cancel" type="cancel" name="cancel">
            Cancel
            <i class="glyphicon glyphicon-remove"></i>
         </button>
      </div>
   </div>
</form> 
</div>
