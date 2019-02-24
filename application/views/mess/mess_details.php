<script>

   function deleteMess(mess_name){
         var recipient = mess_name;// Extract info from data-* attributes
         if(confirm('Do you really want to delete this Mess ? This cannot be undone' )) {
               $.ajax({
                     type: "POST",
                     url: "<?php echo base_url()."mess/delete_mess";?>",
                     cache: false,
                     data: {'data' : recipient},
                     dataType: 'html',

                     success: function (resp) {
                           console.log(resp);
                           alert(resp);
                           location.reload(true);
                     },
                     error: function(err) {
                           console.log(err);
                     }
               });  
         }

      }


      function addMess(){

            var mess_name = $("#add_mess_name").val();
            var mess_incharge = $("#add_mess_incharge").val();

            var contact = $("#add_contact").val();
            var category = $("#selectedCategory").val();

            if(mess_name == '' || mess_incharge == '' || contact == '' || category == '' )
            alert('Kindly fill all the details');
            else{

                  var toSend = {};
                  toSend["messName"] = encodeURIComponent(mess_name);
                  toSend["messIncharge"] = encodeURIComponent(mess_incharge);
                  toSend["contact"] = encodeURIComponent(contact);
                  toSend["category"] = encodeURIComponent(category);
                  var toSendJson = JSON.stringify(toSend);
                  console.log(toSendJson);
                  $.ajax({
                        url : "<?php echo base_url().'mess/add_mess';?>",
                        type : "POST",
                        data: {'data' :toSendJson},
                        cache: false,
                        dataType : "html",
                        success : function(resp){

                              console.log(resp);
                              alert(resp);

                              location.reload(true);
                        },
                        error: function(xhr, status, error) {
                              var err = eval("(" + xhr.responseText + ")");
                              alert(err.Message);
                        }

                  }); 
            }
         }

         function messList(){

               var report = $("#report");
               var itemName = $("#itemNames");
               var quantitySupplied = $("#quantitySupplied");
               var rate = $("#rate");
               var amount = $("#amount");
               var dataToPrint = "";
               $(report).html("");
               $.ajax({
                     url : '../mess/get_mess_details/',
                     type : 'GET'  ,
                     dataType : 'json',
                     success : function(data){
                           console.log(data);
                           console.log(data.messName.length);
                           if(data.messName.length==0)
                           dataToPrint += '<div class="row">'+
                              '<div class="col s8 offset-s2">'+
                                 '<span class="blue-text text-darken-2">No mess. Add new.</span>'+
                                 '</div></div>';
                           else{
                                 dataToPrint += '<tr>'+
                                    '<th>'+
                                       '<span class="blue-text">Mess Name</span>'+
                                       '</th>'+

                                    '<th>'+
                                       '<span class="blue-text">Mess Incharge</span>'+
                                       '</th>'+

                                    ' <th>'+
                                       ' <span class="blue-text">Contact</span>'+
                                       ' </th>';
                                    if(admin){
                                          dataToPrint +=         '<th>'+
                                             '<span class="blue-text">Edit Action</span>'+
                                             '</th>'+
                                          '<th>'+
                                             '<span class="blue-text">Delete Action</span>'+
                                             '</th>';
                                    }
                                    dataToPrint += ' </tr>';

                                 $(data.messName).each(function(index){

                                       var editData = {};
                                       editData["messName"] = encodeURIComponent(data.messName[index]);
                                       editData["messIncharge"] = encodeURIComponent(data.messIncharge[index]);
                                       editData["contact"] = (data.contact[index]);

                                       var jsonEdit = JSON.stringify(editData);
                                       console.log(jsonEdit);

                                       var editID = "edit_"+data.messName[index]+'_'+data.messIncharge[index]+'_'+data.contact[index];
                                       dataToPrint += '<tr>'+
                                          '<th>'+
                                             data.messName[index]+
                                             '</th>'+
                                          '<th>'+
                                             data.messIncharge[index]+
                                             '</th>'+
                                          '<th>'+
                                             data.contact[index]+
                                             '</th>';
                                          if(admin) {
                                                dataToPrint +=	'<th>'+
                                                   '<a class="btn btn-small btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever='+jsonEdit+' data-keyboard="true">'+
                                                      'Edit'+
                                                      '</a>'+
                                                   '</th>'+
                                                '<th>'+
                                                   '<a href = "javascript:deleteMess(\''+editData['messName']+'\');" class="btn btn-small btn-primary" >'+
                                                      'Delete'+
                                                      '</a>'+
                                                   '</th>';
                                          }
                                          dataToPrint += '</th>';
                                       console.log(data.messName[index]);
                                 });
                           }
                           $("table#messList").html(dataToPrint);
                     },
                     error: function(xhr, status, error) {
                           var err = eval("(" + xhr.responseText + ")");
                           alert(err.Message);
                     }
               }); 

         }

         $(document).ready(function() {
               messList();
               $('#add_mess').hide();
               $('#print-report').click(function () {
                     var doc = new jsPDF();
                     doc.addHTML(document.body,function() {
                           doc.autoPrint();
                           doc.save('test.pdf');
                     });

               });

               $('#add_button').hide();
               if(admin || provision) $('#add_button').show();
               var toggle = 0;
               $('#add_button').click(function () {
                     toggle = !toggle;
                     if(toggle){
                           $('#add_button').text('Close');
                           $('#add_mess').show();
                     }
                     else{
                           $('#add_button').text('Add Mess');
                           $('#add_mess').hide();
                     }
               });

         });
      </script>


      <div class="row">
         <div class="col s4">

            <a class="btn waves-effect waves-light btn-large" 
               value="add_button" name="add_button" id="add_button">
               Add Mess
            </a>

         </div>

      </div>
      <div id="add_mess">
         <div class="row">
            <div class="col s12">
               <div class = "col s3">
                  <input type="text" name="add_mess_name" id="add_mess_name" placeholder="Mess Name"/>
               </div>
               <div class = "col s3">
                  <input type="text" name="add_mess_incharge" id="add_mess_incharge" placeholder="Mess Incharge"/>
               </div>
               <div class = "col s3">
                  <input type="text" name="add_contact" id="add_contact" placeholder="Contact"/>
               </div>
               <div class="col s3">
                  <select class="browser-default" id="selectedCategory" name="selectedCategory" value = "<?php echo isset($selectedCategory) ? $selectedCategory : "";?>" required>
                     <option value="">Select Category</option>
                     <?php 
                        foreach($messCategory as $eachCategory)
                        {
                        ?>
                        <option value='<?php echo $eachCategory;?>'><?php echo $eachCategory;?></option>
                        <?php
                        }
                     ?>
                  </select>
               </div>
            </div>


         </div>

         <div class="row">
            <div class="col s4 offset-s6">

               <a href="javascript:addMess();" class="btn waves-effect waves-light btn-large" 
                  value="add_mess" name="add_mess" id="add_mess">
                  Submit
               </a>

            </div>
         </div>
      </div>

      <div class="col s6 offset-s6" id="printDiv">
         <a href="javascript:printPDF('reportArea')" class="btn waves-effect waves-light" value="print" name="print" id="print-report">
            Print
         </a>
      </div>


      <div id="reportArea" value='reportArea'>

         <div class="row">
            <div class="col s12">
               <table id="messList"></table>
            </div>
         </div>

      </div>
   </div>
   <div class="modal fade in" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="memberModalLabel" aria-hidden="false">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="memberModalLabel">Edit Mess Details</h4>
         </div>
         <div class="modal-body">
         </div>
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
               url: "<?php echo base_url()."mess/edit_mess_form";?>",
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

