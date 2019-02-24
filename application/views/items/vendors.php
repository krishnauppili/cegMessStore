<script>
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
                     url : "<?php echo base_url().'items/add_vendor';?>",
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
                  url : 'get_vendors_list/',
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
                              $(data.vendorName).each(function(index){
                                    dataToPrint += '<div class="row">'+
                                       '<div class="col s2">'+
                                          data.vendorName[index]+
                                          '</div>'+
                                       '<div class="col s2">'+
                                          data.ownerName[index]+
                                          '</div>'+
                                       '<div class="col s2">'+
                                          data.address[index]+
                                          '</div>'+
                                       '<div class="col s2">'+
                                          data.contact[index]+
                                          '</div>'+
                                       '</div>';
                                    console.log(data.vendorName[index]);
                              });
                        }
                        $("div#vendorsList").html(dataToPrint);
                  },
                  error: function(xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        alert(err.Message);
                  }
            }); 

      }



   </script>
   <script>
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

            $('#add_button').click(function () {
                  $('#add_vendor').show();
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
            <input type="text" name="add_vendor_name" id="add_vendor_name"/>
            <label for="add_vendor_name">Vendor KrishnaName</label>
         </div>
         <div class = "input-field col s3">
            <input type="text" name="add_owner_name" id="add_owner_name"/>
            <label for="add_owner_name">Owner Name</label>
         </div>
         <div class = "input-field col s3">
            <input type="text" name="add_address" id="add_address"/>
            <label for="add_address">Address</label>
         </div>
         <div class = "input-field col s3">
            <input type="text" name="add_contact" id="add_contact"/>
            <label for="add_contact">Contact</label>
         </div>
          <div class = "input-field col s3">
            <input type="text" name="add_vendor_code" id="add_vendor_code"/>
            <label for="add_vendor_code">Vendor Code</label>
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



   <div id="reportArea" value='reportArea'>

      <div class="row">
         <div class="col s8 offset-s2">
            <span>VENDORS LIST</span>
         </div>
      </div>


      <div class="row">
         <div class="col s12">
            <div class="col s3">
               <span class="blue-text">Vendor Name</span>
            </div>

            <div class="col s3">
               <span class="blue-text">Owner Name</span>
            </div>

            <div class="col s3">
               <span class="blue-text">Address</span>
            </div>
            <div class="col s3">
               <span class="blue-text">Contact</span>
            </div>

         </div>
      </div>



      <div class="row">
         <div class="col s12">
            <div id="vendorsList"></div>
         </div>
      </div>

   </div>

   <div class="row">
      <div class="col s4 offset-s6">

         <a href="javascript:demoFromHTML();" class="btn waves-effect waves-light btn-large" 
            value="print" name="print" id="print-report">
            Print
         </a>

      </div>
   </div>
</div>
