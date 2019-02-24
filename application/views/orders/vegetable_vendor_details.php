<script>
   function deleteVendor(vendor_name){
         var recipient = vendor_name;// Extract info from data-* attributes
         if(confirm('Do you really want to delete this Vendor ? This cannot be undone' )) {
               $.ajax({
                     type: "POST",
                     url: "<?php echo base_url()."orders/delete_vegetable_vendor";?>",
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




            function addVendor(){

                  var vendor_name = $("#add_vendor_name").val();
                  var owner_name = $("#add_owner_name").val();
                  var address = $("#add_address").val();
                  var contact = $("#add_contact").val();

                  if(vendor_name == '' || owner_name == '' || address == '' || contact == '')
                  alert('Kindly fill all the details');
                  else{

                        var toSend = {};
                        toSend["vendorName"] = encodeURIComponent(vendor_name);
                        toSend["ownerName"] = encodeURIComponent(owner_name);
                        toSend["address"] = encodeURIComponent(address);
                        toSend["contact"] = encodeURIComponent(contact);
                        var toSendJson = JSON.stringify(toSend);
                        console.log(toSendJson);
                        $.ajax({
                              url : "<?php echo base_url().'orders/add_vegetable_vendor';?>",
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
                     function vendorsList(){

                           var report = $("#report");
                           var itemName = $("#itemNames");
                           var quantitySupplied = $("#quantitySupplied");
                           var rate = $("#rate");
                           var amount = $("#amount");
                           var dataToPrint = "";
                           $(report).html("");
                           $.ajax({
                                 url : 'get_vegetable_vendors_list/',
                                 type : 'GET'  ,
                                 dataType : 'json',
                                 success : function(data){
                                       console.log(data);
                                       console.log(data.vendorName.length);
                                       if(data.vendorName.length==0)
                                       dataToPrint += '<div class="row">'+
                                          '<div class="col s8 offset-s2">'+
                                             '<span class="blue-text text-darken-2">No vendors. Add new.</span>'+
                                             '</div></div>';
                                       else{
                                             dataToPrint += '<table>';
                                                dataToPrint += '<tr>'+
                                                   '<th>'+
                                                      '<span class="blue-text">Vendor Name</span>'+
                                                      '</th>'+

                                                   '<th>'+
                                                      ' <span class="blue-text">Owner Name</span>'+
                                                      '</th>'+

                                                   '<th>'+
                                                      '<span class="blue-text">Address</span>'+
                                                      ' </th>'+
                                                   '<th>'+
                                                      '<span class="blue-text">Contact</span>'+
                                                      '</th>';
                                                   if(admin) {
                                                         dataToPrint +=   '<th>'+
                                                            '<span class="blue-text">Edit Action</span>'+
                                                            '</th>'+

                                                         '<th>'+
                                                            '<span class="blue-text">Delete Action</span>'+
                                                            '</th>';
                                                   }
                                                   dataToPrint += '</tr>'

                                                $(data.vendorName).each(function(index){
                                                      var editData = {};

                                                      editData["vendorName"] = encodeURIComponent(data.vendorName[index]);
                                                      editData["ownerName"] = encodeURIComponent(data.ownerName[index]);
                                                      editData["address"] = encodeURIComponent(data.address[index]);
                                                      editData["contact"] = (data.contact[index]);

                                                      var jsonEdit = JSON.stringify(editData);
                                                      console.log(jsonEdit);

                                                      var editID = "edit_"+data.vendorName[index]+'_'+data.ownerName[index]+'_'+data.address[index]+'_'+data.contact[index];

                                                      dataToPrint += '<tr>'+
                                                         '<th>'+
                                                            data.vendorName[index]+
                                                            '</th>'+
                                                         '<th>'+
                                                            data.ownerName[index]+
                                                            '</th>'+
                                                         '<th>'+
                                                            data.address[index]+
                                                            '</th>'+
                                                         '<th>'+
                                                            data.contact[index]+
                                                            '</th>';
                                                         if(admin) {
                                                               dataToPrint +=		'<th>'+
                                                                  '<a class="btn btn-small btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever='+jsonEdit+' data-keyboard="true">'+
                                                                     'Edit'+
                                                                     '</a>'+
                                                                  '</th>'+
                                                               '<th>'+
                                                                  '<a href = "javascript:deleteVendor(\''+editData['vendorName']+'\');" class="btn btn-small btn-primary" >'+
                                                                     'Delete'+
                                                                     '</a>'+
                                                                  '</th>';
                                                         }
                                                         dataToPrint += '</tr>';
                                                      console.log(data.vendorName[index]);
                                                });
                                             }
                                             dataToPrint += '</table>';
                                          console.log(dataToPrint);
                                          $("div#vendorsList").html(dataToPrint);
                                    },
                                    error: function(xhr, status, error) {
                                          var err = eval("(" + xhr.responseText + ")");
                                          alert(err.Message);
                                    }
                              }); 

                           }

                           $(document).ready(function() {
                                 vendorsList();
                                 $('#add_vendor').hide();
                                 $('#print-report').click(function () {
                                       var doc = new jsPDF();
                                       doc.addHTML(document.body,function() {
                                             doc.autoPrint();
                                             doc.save('test.pdf');
                                       });

                                 });
                                 $('#add_button').hide();
                                 if(admin) $('#add_button').show();
                                 var toggle = 0;
                                 $('#add_button').click(function () {
                                       toggle = !toggle;
                                       if(toggle){
                                             $('#add_button').text('Close');
                                             $('#add_vendor').show();
                                       }
                                       else{
                                             $('#add_button').text('Add Vendor');
                                             $('#add_vendor').hide();
                                       }
                                 });

                           });
                        </script>

                        <div class="row">
                           <div class="col s4">

                              <a class="btn waves-effect waves-light btn-large" 
                                 value="add_button" name="add_button" id="add_button">
                                 Add Vendor
                              </a>

                           </div>
                        </div>
                        <div id="add_vendor">
                           <div class="row">
                              <div class = "input-field col s3">
                                 <input type="text" name="add_vendor_name" id="add_vendor_name" placeholder="Vendor Name"/>
                              </div>
                              <div class = "input-field col s3">
                                 <input type="text" name="add_owner_name" id="add_owner_name" placeholder="Owner Name"/>
                              </div>
                              <div class = "input-field col s3">
                                 <input type="text" name="add_address" id="add_address" placeholder="Address"/>
                              </div>
                              <div class = "input-field col s3">
                                 <input type="text" name="add_contact" id="add_contact" placeholder="Contact"/>
                              </div>



                           </div>

                           <div class="row">
                              <div class="col s4 offset-s6">

                                 <a href="javascript:addVendor();" class="btn waves-effect waves-light btn-large" 
                                    value="add_vendor" name="add_vendor" id="add_vendor">
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
                                 <div id="vendorsList"></div>
                              </div>
                           </div>

                        </div>

                     </div>

                     <div class="modal fade in" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="memberModalLabel" aria-hidden="false">
                        <div class="modal-content">
                           <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                              <h4 class="modal-title" id="memberModalLabel">Edit Vendor Detail</h4>
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
                                    url: "<?php echo base_url()."orders/edit_vegetable_vendor_form";?>",
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
