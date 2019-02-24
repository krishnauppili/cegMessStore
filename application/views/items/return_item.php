<script>
   function generate_elements(data,tabindex,providedItems)
   {
         console.log(data);
         $('.info').hide();
         if((data.itemNames.length) == 0) $('.info').show();
         $(data.itemNames).each(function(index){

               var divRow = $(document.createElement('div')).attr({
                     class: 'row margin_row'
               });

               var divSelected = $(document.createElement('div')).attr({
                     class: 'col s4'
               });

               $(divRow).append(divSelected);

               $(divSelected).append(
                  $(document.createElement('input')).attr({
                        id:    data.itemNames[index]
                        ,name:  'selectedItems[]'
                        ,value: data.itemNames[index]
                        ,type:  'text'
                        ,tabindex: tabindex++
                        ,readonly: 'readonly' 
                  })
               );


               var divSelected2 = $(document.createElement('div')).attr({
                     class: 'col s4'
               });



               $(divRow).append(divSelected2);

               $(divSelected2).append(
                  $(document.createElement('input')).attr({
                        id : 'txt'+data.itemNames[index]
                        ,name:  'selectedQuantity[]'
                        ,type:  'number'
                        ,placeholder: 'Enter Quantity'
                        ,step : 0.01
                        ,onchange:"javascript:change_color(this)"
                        ,tabindex: tabindex++
                  })
               );




               var divSelected1 = $(document.createElement('div')).attr({
                     class: 'col s4'
               });






               $(divRow).append(divSelected1);

             $(divSelected1).append(
                  $(document.createElement('label')).attr({
                        class: 'blue-text text-darken-2'
                  }).html(data.quantitySupplied[index])
               );

               $(divSelected1).append(
                  $(document.createElement('input')).attr({
                        id:    data.quantitySupplied[index]
                        ,name:  'quantitySupplied[]'
                        ,value: data.quantitySupplied[index]
                        ,type:  'hidden'
                  })
               );

               $(divSelected1).append(
                  $(document.createElement('input')).attr({
                        id:    data.latestRate[index]
                        ,name:  'latestRate[]'
                        ,value: data.latestRate[index]
                        ,type:  'hidden'
                  })
               );




               $(providedItems).append(divRow);


         });
      }
      $(function(){
            $('#issuedDate').pickadate();
            var tabindex = 1;
            $('select,input').each(function() {
                  if (this.type != "hidden") {
                        var $input = $(this);
                        $input.attr("tabindex", tabindex);
                        tabindex++;
                  }
            });

            $('#selectedMess').change(function(){

                  var messName = encodeURIComponent($(this).val());
                  var issuedDate = encodeURIComponent($('#issuedDate').val());
                  var providedItems = $("#issuedItems");
                  $(providedItems).html("");
                  $.ajax({
                        url : 'getMessConsumptionTillToday/'+messName+'/'+issuedDate,
                        type : 'GET'  ,
                        dataType : 'json',
                        success : function(data){
                              generate_elements(data,tabindex,providedItems);	
                        },
                        error: function(err) {
                           console.log(err);
                     }


                  }); 

            });

            $('#issuedDate').change(function(){

                  var messName = encodeURIComponent($('#selectedMess').val());
                  var issuedDate = encodeURIComponent($(this).val());
                  var providedItems = $("#issuedItems");
                  $(providedItems).html("");
                  $.ajax({
                        url : 'getMessConsumptionTillToday/'+messName+'/'+issuedDate,
                        type : 'GET'  ,
                        dataType : 'json',
                        success : function(data){
                              generate_elements(data,tabindex,providedItems);	
                        },
                        error: function(err) {
                           console.log(err);
                        }
                  }); 

            });


      }) 
   </script>
   <form name="selection" method="post"  action="return_item"> 
      <div class="row">
         <div class="input-field col s6">
            <select class="browser-default" id="selectedMess" name="selectedMess" value = "<?php echo isset($selectedMess) ? $selectedMess : "";?>" required>
               <option value="">Select Mess</option>
               <?php	
                  foreach($messTypes as $eachType)
                  {
                  ?>
                  <option value='<?php echo $eachType;?>'><?php echo $eachType;?></option>
                  <?php
                  }
               ?>
            </select>
         </div>
         <div class = "input-field col s6">
            <input type="date" class="datepicker" id="issuedDate" name="issuedDate" placeholder='Select Date' required/>
         </div>

      </div>

      <div class="row">
         <div id="issuedItems">
         </div>
      </div>


      <div class="info row">
         <div class="col s8 offset-s2">
            <span class="blue-text text-darken-2"><h5>Select an item from the list for return.</h5></span>
         </div>
      </div>



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
