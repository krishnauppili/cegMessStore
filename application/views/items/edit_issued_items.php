<script>

   $(document).ready(function() {
         console.log(admin);
         $('#print-report').click(function () {
               var doc = new jsPDF();
               doc.addHTML(document.body,function() {
                     doc.autoPrint();
                     doc.save('test.pdf');
               });

         });


         $("div#reportArea").hide();

         $("#getButton").click(function(){

               $("#reportForMess").html($("#selectedMess").val());
               $("#reportFrom").html($("#from").val());
               $("#reportTo").html($("#to").val());
               $("div#reportArea").show();
         });

         $( "#from" ).pickadate();

         $( "#to" ).pickadate();
   });

   function submit_update(){

         var messName = encodeURIComponent($('[name="modalMessName"]').val());
         var itemName = encodeURIComponent($('[name="modalItemName"]'));
         var quantitySupplied = $('[name="modalQuantitySupplied"]');
         var rate = $('[name="modalRate"]');
         var dataToPrint = "";
         $.ajax({
               url : this.action,
               type : this.method,
               dataType : 'html',
               success : function(data){

                     alert('Data updated succesfully ');
               },
               error : function(data) {
                     alert('Error');
               }
         });

   }


   function get_report(){

         var messName = encodeURIComponent($("#selectedMess").val());
         var from = encodeURIComponent($("#from").val());
         var to = encodeURIComponent($("#to").val())
         var report = $("#report");
         var itemName = $("#itemNames");
         var quantitySupplied = $("#quantitySupplied");
         var rate = $("#rate");
         var amount = $("#amount");
         var dataToPrint = "";
         $(report).html("");
         $.ajax({
               url : '../reports/get_mess_consumption_report/'+messName+'/'+from+'/'+to,
               type : 'GET'  ,
               dataType : 'json',
               success : function(data){
                     $(data.itemNames).each(function(index){
                           var editData = {};
                           editData["suppliedDate"] = data.suppliedDate[index];
                           editData["itemName"] = encodeURIComponent(data.itemNames[index]);
                           editData["messName"] = messName;
                           editData["latestRate"] = data.rate[index];
                           editData["quantitySupplied"] = data.quantitySupplied[index];

                           var jsonEdit = JSON.stringify(editData);
                           console.log(jsonEdit);
                           var itemName = encodeURIComponent(data.itemNames[index]);
                           var editID = "edit_"+data.suppliedDate[index]+'_'+itemName+'_'+messName;
                           dataToPrint += '<div class="row">'+
                              '<div class="col s2">'+
                                 data.suppliedDate[index]+
                                 '</div>'+
                              '<div class="col s2">'+
                                 data.itemNames[index]+
                                 '</div>'+
                              '<div class="col s2">'+
                                 data.quantitySupplied[index]+
                                 '</div>'+
                              '<div class="col s2">'+
                                 data.rate[index]+
                                 '</div>'+
                              '<div class="col s2">'+
                                 data.amount[index]+
                                 '</div>';
                              if(admin)
                              {
                                    dataToPrint +=	'<div class="col s2">'+
                                       '<a class="btn btn-small btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever='+jsonEdit+' data-keyboard="true">'+
                                          'Edit'+
                                          '</a>'+
                                       '</div>';
                              }
                              dataToPrint += '</div>';
                           console.log(data.suppliedDate[index]);
                     });
                     $("div#reports").html(dataToPrint);
               }
         }); 

   }


</script>
<style type="text/css">

   select {
         border-size: 2px;
         border-color: #000066;
         border-radius: 4px;
      }


   </style>
   <form name="selection" method="post"  action="mess_consumption"> 
      <div class="row">
         <div class="input-field col s6 offset-s3">
            <select class="browser-default" id="selectedMess" name="selectedMess" value = "<?php echo isset($selectedMess) ? $selectedMess : "";?>" required>
               <option></option>
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
      </div>

      <div class="row">
         <div class = "col s6">
            <input type="date" class="datepicker" id="from"/>

            <label for="from">From date</label>
         </div>
         <div class = "col s6">
            <input type="date" class="datepicker" id="to"/>


            <label for="to">To date</label>
         </div>

      </div>

      <div class="row">
         <a href="javascript:get_report();" class="btn waves-effect waves-light" id="getButton">
            &gt;&gt;
         </a>
      </div>

      <div id="reportArea" value='reportArea'>

         <div class="row">
            <div class="col s12 offset-s2">
               <span>MESS CONSUMPTION - <span id="reportForMess"></span></span>
            </div>
         </div>

         <div class ="row">
            <div class="col s12 offset-s2">
               <div class="col s6"><span>FROM:<span id="reportFrom"></span></span></div>
               <div class="col s6"><span>TO:<span id="reportTo"></span></span></div>
            </div>
         </div>

         <div class="row">
            <div class="col s12">
               <div class="col s2">
                  <span class="blue-text">Supplied Date</span>
               </div>

               <div class="col s2">
                  <span class="blue-text">Item Name</span>
               </div>

               <div class="col s2">
                  <span class="blue-text">Quantity Supplied</span>
               </div>
               <div class="col s2">
                  <span class="blue-text">Rate</span>
               </div>
               <div class="col s2">
                  <span class="blue-text">Amount</span>
               </div>	
            </div>
         </div>

         <div class="row">
            <div class="col s12">
               <div id="reports"></div>
            </div>
         </div>

      </div>

   </form> 
</div>
<div class="modal fade in" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="memberModalLabel" aria-hidden="false">
   <div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
         <h4 class="modal-title" id="memberModalLabel">Edit Member Detail</h4>
      </div>
      <div class="modal-body">
      </div>
   </div>
</div>
<script>
   $('#exampleModal').on('show.bs.modal', function (event) {
         $('body').css("margin-left", "0px");
         var button = $(event.relatedTarget) // Button that triggered the modal
         var recipient = button.data('whatever') // Extract info from data-* attributes
         var modal = $(this);
         var dataString = 'id=' + recipient;
         console.log(recipient);
         $.ajax({
               type: "POST",
               url: "<?php echo base_url()."items/edit_row_form";?>",
               cache: false,
               data: recipient,
               dataType: 'html',

               success: function (resp) {
                     console.log(resp);
                     modal.find('.modal-body').html(resp);
               },
               error: function(err) {
                     console.log(err);
               }
         });  
   })
</script>
