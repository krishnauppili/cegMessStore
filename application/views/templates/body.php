<body>
   <script>

function toggle(source) {
checkboxes = document.getElementsByName('selectedOrders[]');
  for(var i=0, n=checkboxes.length;i<n;i++) {
    checkboxes[i].checked = source.checked;
  }
}

      function change_color(obj)
      {
            if($(obj).val().length !== 0) $(obj).css('background-color','#ffff00');
            else $(obj).css('background-color','#fff');
      }

      function getDateFunction(arg)
      {
      		if(arg == null)
       		var date = new Date();
       		else
       		var date = new Date(arg);
            var setDate = date.getDate(); 
            var setMonth = date.getMonth()+1;
            var setYear = date.getFullYear();
            console.log(setDate);
            var newDate = setDate.toString();
            var newMonth = setMonth.toString();
            if(setDate < 10)
            newDate = '0'+setDate.toString();
            if(setMonth < 10)
            newMonth = '0'+setMonth.toString();
            var newYear = setYear.toString();
            return newDate+"-"+newMonth+"-"+newYear;
       }

      function printPDF(div,mess,from,to)
      {
            var htmlString = document.getElementById(div).innerHTML;
            var fromDate = getDateFunction(from);
            var toDate = getDateFunction(to);
            console.log(fromDate);
            

            var title = "<?php if(isset($title)) echo $title; else echo ""?>";
            console.log(title);
            $.ajax({
                  type: "POST",
                  url: "<?php echo base_url()."reports/printReport/";?>"+title+"/"+mess+"/"+fromDate+"/"+toDate,
                  cache: false,
                  data: {'toSend': htmlString}, 
                  success: function (resp) {
                     
            			var todayDate = getDateFunction();
                        if(title.indexOf("Vegetable Bill") >= 0)
	                        var file = "Mess Vegetable Bill"+"/"+mess+"_"+fromDate+"_"+toDate+"_"+todayDate;
                    	else if(title.indexOf("Bill") >= 0)
    	                    var file = "Mess Bill"+"/"+mess+"_"+fromDate+"_"+toDate+"_"+todayDate;
                    	else if(title.indexOf("Vegetable Consumption") >= 0)
        	                var file = "Mess Vegetable Consumption"+"/"+mess+"_"+fromDate+"_"+toDate+"_"+todayDate;
                     else if(title.indexOf("Vegetable Average") >= 0)
                         var file = "Mess Vegetable Average"+"/"+mess+"_"+fromDate+"_"+toDate+"_"+todayDate;
                     else if(title.indexOf("Average") >= 0)
                         var file = "Mess Average"+"/"+mess+"_"+fromDate+"_"+toDate+"_"+todayDate;
                     
            	      else if(title.indexOf("Consumption") >= 0)
                	        var file = "Mess Consumption"+"/"+mess+"_"+fromDate+"_"+toDate+"_"+todayDate;
                   	else if(title.indexOf("Returns") >= 0)
                        	var file = "Mess Returns"+"/"+mess+"_"+fromDate+"_"+toDate+"_"+todayDate;
                        

                        var loc = "http://localhost/cegMessStore_new/reports/"+file+".pdf";
                        console.log(loc);
                        window.open(loc);           
            
                       // console.log('Success');

                       // window.open('/cegMessStore/reports/report.pdf');
                        // window.open('http://localhost/cegMessStore/reports/report.pdf');

                  },
                  async : false,
                  error: function(err) {
                        console.log(err);
                  }
            });  

      }

      function printAbstract(div,vendor,total,startDate,endDate)
      {
            var htmlString = document.getElementById(div).innerHTML;

            var title = "<?php if(isset($title)) echo $title; else echo ""?>";
            console.log(title);
            
            $.ajax({
                  type: "POST",
                  url: "<?php echo base_url()."reports/printAbstract/";?>"+title+"/"+vendor+"/"+total+"/"+startDate+"/"+endDate,
                  cache: false,
                  data: {'toSend': htmlString},
                  success: function (resp) {
                        var todayDate = getDateFunction();
                        if(title.indexOf("Vegetable") >= 0)
                        var file = "Vegetable Abstract"+"/"+vendor+"_"+startDate+"_"+endDate+"_"+todayDate;
                        else 
                        var file = "Items Abstract"+"/"+vendor+"_"+startDate+"_"+endDate+"_"+todayDate;
                        var loc = "http://localhost/cegMessStore_new/reports/"+file+".pdf";
                        console.log(loc);
                        window.open(loc);           
                        //window.open('/cegMessStore/reports/report.pdf');
                        // window.open('http://localhost/cegMessStore/reports/report.pdf');
                  },
                  async : false,
                  error: function(err) {
                        console.log(err);
                        console.log("hi");
                  }
            });  
      }

      $( document ).ready(function(){
            $(".dropdown-button").dropdown({
                  inDuration: 300,
                  outDuration: 225,
                  constrain_width: false, // Does not change width of dropdown to that of the activator
                  hover: true, // Activate on hover
                  gutter: 2, // Spacing from edge
                  belowOrigin: true // Displays dropdown below the button
            });
      });
      var admin = false;
      var provison = false;
      var vegetable = false;
   </script>
   <style>
      .input-field{
            border-size: 2px;
            border-color: #000066;
            border-radius: 4px;
      }
   </style>

   <?php
      $admin = false;
      $provision =false;
      $vegetable = false;
      if(isset($group))
      if(in_array('admin',$group))
      {
         $admin =true;
      ?>
      <script>
         admin = true;
      </script>
      <?php
      }
      else if(in_array('vegetable',$group))
      {
         $vegetable =true;
      ?>
      <script>
         vegetable = true;
      </script>
      <?php
      }
      else if(in_array('provision',$group))
      {
         $provision = true;
      ?>
      <script>
         provision = true;
      </script>
      <?php
      }

   ?>

   <ul id="mess" class="dropdown-content">
      <?php 
         if(isset($username)) {
            if($provision || $admin)
            {
            ?>
            <li><a href='<?php echo base_url()."mess/mess_consumption";?>'>Mess Consumption</a></li>
            <li class="divider"></li>
            <li><a href='<?php echo base_url()."mess/mess_bill";?>'>Mess Bill</a></li>
            <li class="divider"></li>
             <li><a href='<?php echo base_url()."mess/mess_average";?>'>Mess Average</a></li>
               <li class="divider"></li>
            <li><a href='<?php echo base_url()."mess/mess_return";?>'>Mess Returns</a></li>
            <li class="divider"></li>
            <?php }
               if($vegetable || $admin) {
               ?>
               <li><a href='<?php echo base_url()."mess/mess_vegetable_consumption";?>'>Mess Vegetable Consumption</a></li>
               <li class="divider"></li>
               <li><a href='<?php echo base_url()."mess/mess_vegetable_average";?>'>Mess Vegetable Average</a></li>
               <li class="divider"></li>
               <li><a href='<?php echo base_url()."mess/mess_vegetable_bill";?>'>Mess Vegetable Bill</a></li>
               <li class="divider"></li>
               <?php }
               ?>
               <li><a href='<?php echo base_url()."mess/mess_details";?>'>Mess Details</a></li>
            </ul>
            

            <?php
               if($provision || $admin) {
               ?>
               <ul id="items" class="dropdown-content">
                  <li><a href="<?php echo base_url()."orders/order_receive";?>">Order Receival</a></li>
                  <li class="divider"></li>
                  <li><a href='<?php echo base_url()."orders/vendor_details";?>'>Vendor Details</a></li>
                  <li class="divider"></li>
                  
                  <li><a href="<?php echo base_url()."orders/order_history";?>">Order History</a></li>
                  <li class="divider"></li>
                  <li><a href='<?php echo base_url()."items/issue_item";?>'>Item Issue</a></li>
                  <li class="divider"></li>
                  <li><a href='<?php echo base_url()."items/return_item";?>'>Item Return</a></li>
                  <li class="divider"></li>
                  <li><a href='<?php echo base_url()."items/items_search";?>'>Items in Stock</a></li>
                  <li class="divider"></li>
                  <li><a href='<?php echo base_url()."items/stock_approximation";?>'>Stock Approximation</a></li>
                  <li class="divider"></li>
                  <li class="divider"></li>
                  <li><a href='<?php echo base_url()."items/add_item";?>'>Add New Items</a></li>
                  <li class="divider"></li>
                  <!-- <li><a href='<?php //echo base_url()."orders/grind";?>'>Grind</a></li>
                  <li class="divider"></li>-->
               </ul>
               <?php
               }
            ?>
           
            <ul id="edit" class="dropdown-content">
            <?php
            if($provision || $admin) {
            ?>
                  <li><a href="<?php echo base_url()."orders/edit_order_receival";?>">Edit Order Receival</a></li>
                  <li class="divider"></li>
                  <li class="divider"></li>
                  <li><a href='<?php echo base_url()."mess/edit_mess_consumption";?>'>Edit Mess Consumption</a></li>
                  <li class="divider"></li>
                  <li><a href='<?php echo base_url()."mess/edit_mess_return";?>'>Edit Item Return</a></li>
                  <li class="divider"></li>
                  <?php }
                  if($admin || $vegetable) {?>
                  <li><a href='<?php echo base_url()."orders/edit_vegetable_transactions";?>'>Edit Vegetable Transactions</a></li>
                  <li class="divider"></li>
                   <?php
               }
            ?>
            </ul>
              

            <?php 
               if($vegetable || $admin) 
               {
               ?>
               <ul id="vegetables" class="dropdown-content">
                  <li><a href="<?php echo base_url()."orders/vegetable_order";?>">Order Receival</a></li>
                  <li class="divider"></li>
                  <li><a href='<?php echo base_url()."orders/vegetable_vendor_details";?>'>Vegetable Vendor Details</a></li>
                  <li class="divider"></li>
      
                  <li><a href="<?php echo base_url()."orders/vegetable_order_history";?>">Order History</a></li>
                  <li class="divider"></li>
                  <li><a href='<?php echo base_url()."items/add_vegetable";?>'>Add New Vegetables</a></li>
               </ul>
               <?php
               }
            ?>


            <ul id="account" class="dropdown-content">
               <li><a href='<?php echo base_url()."auth/change_password";?>'>Change Password</a></li>
               <?php
                  if($admin)
                  {
                  ?>
                  <li><a href='<?php echo base_url()."auth/edit_existing_users";?>'>Edit Users</a></li>
                  <?php
                  }
               ?>
               <li class="divider"></li>
               <li><a href='<?php echo base_url()."auth/logout";?>'>Logout</a></li>
            </ul>
            <?php
                  if($admin || $provision)
                  {
                  ?>

            <ul id="notifications" class="dropdown-content">

               <li><a href='<?php echo base_url()."items/notification_edit_history";?>'>Edit notifications</a></li>
              
                  <li><a href='<?php echo base_url()."items/notification_stock_approximation";?>'>Stock approximations</a></li>
                  <?php
                  }
               ?>
            </ul>

            <div class="navbar-fixed">
               <nav>
               <div class="nav-wrapper">
                  <!--<a href="#!" class="brand-logo">Logo</a>-->
                  <?php
                     if(isset($username))
                     {
                     ?>
                     <ul class="left">
                        <li>
                        <a class='dropdown-button' href='#'><?php echo "CEG Mess Store V2.0";?></a></li>
                        <li>

                        <a class='dropdown-button' href='#'><?php echo $username;?></a>

                        </li>
                        
                     </ul>
                     <?php
                     }
                  ?>
                  <ul class="right hide-on-med-and-down">

                     <!-- Dropdown Trigger -->
                     <?php
                        if($provision || $admin) 
                        {
                        ?>
                        <li>
                        <a class='dropdown-button' href='#' data-activates='items'>Items
                           <span class="glyphicon glyphicon-chevron-down"></span>
                        </a>
                        </li>
                        <?php
                        }
                     ?>
                     <?php 
                        if($admin || $vegetable || $provision)
                        {
                        ?>
                        <li>
                        <a class='dropdown-button' href='#' data-activates='edit'>Edit
                           <span class="glyphicon glyphicon-chevron-down"></span>
                        </a>
                        </li>
                        <?php
                        }
                     ?>

                     <?php 
                        if($admin || $provision)
                        {
                        ?>
                        <li>
                        <a class='dropdown-button' href='#' data-activates='notifications'>Notifications
                           <span class="glyphicon glyphicon-chevron-down"></span>
                        </a>
                        </li>
                        <?php
                        }
                     ?>

                     <?php 
                        if($vegetable || $admin)
                        {
                        ?>
                        <li>
                        <a class='dropdown-button' href='#' data-activates='vegetables'>Vegetables
                           <span class="glyphicon glyphicon-chevron-down"></span>
                        </a>
                        </li>
                        <?php
                        }
                     ?>
                     <li>
                     <a class='dropdown-button' href='#' data-activates='mess'>Mess
                        <span class="glyphicon glyphicon-chevron-down"></span>
                     </a>
                     </li>	


                     <li>
                     <a class='dropdown-button' href='#' data-activates='account'>Account
                        <span class="glyphicon glyphicon-chevron-down"></span>
                     </a>
                     </li>	
                     <?php }
                     ?>
                  </ul>
               </div>
               </nav>
            </div>
            <div class="container">
               <?php 
                  $msg= validation_errors(); 
                  if(isset($message))
                  $msg = $message;
                  if(isset($msg))
                  {
                     $pre = '<div class="card-panel msg teal lighten-2">
                        <div class="row" style="float:right;margin:4px" >
                           <a href="#" class="remove_field-1" style="color:red">
                              <span class="glyphicon glyphicon-remove">
                              </span>
                           </a>
                        </div>';
                        $post='</div>';
                     $main = "";
                     if(is_array($msg) && count($msg)>0)
                     {
                        foreach($msg as $each)
                        {
                           $main .=	'<div class="row">
                              <h6 class="black-text text-darken-2">
                                 '.$each.'
                              </h6>
                           </div>';
                        }
                        echo $pre.$main.$post;
                     }
                     else if($msg !=""){
                        $main = '<div class="row">
                           <h6 class="black-text text-darken-2">
                              '.$msg.'
                           </h6>
                        </div>';

                        echo $pre.$main.$post;
                     }


                  }

                  if(isset($error))
                  {
                     $pre = '<div class="card-panel error red darken-3">
                        <div class="row" style="float:right;margin:4px" >
                           <a href="#" class="remove_field-2" style="color:red">
                              <span class="glyphicon glyphicon-remove">
                              </span>
                           </a>
                        </div>';

                        $post='</div>';

                     $main = "";
                     if(is_array($error) && count($error) > 0)
                     {
                        foreach($error as $each)
                        {
                           $main .=	'<div class="row">
                              <h6 class="black-text text-darken-2">
                                 '.$each.'
                              </h6>
                           </div>';
                        }

                        echo $pre.$main.$post;
                     }
                     else if($error != ""){
                        $main = '<div class="row">
                           <h6 class="black-text text-darken-2">
                              '.$error.'
                           </h6>
                        </div>';


                        echo $pre.$main.$post;
                     }
                  }

                  if(isset($lesser_items))
                  {
                     $pre = '<div class="card-panel lesser-items red darken-3">
                        <div class="row" style="float:right;margin:4px" >
                           <a href="#" class="remove_field-3" style="color:black">
                              <span class="glyphicon glyphicon-remove">
                              </span>
                           </a>
                        </div>';

                        $main = "";
                        $post='</div>';
                     if(is_array($lesser_items['itemNames']) && count($lesser_items['itemNames']) > 0)
                     {
                        for($i=0;$i<count($lesser_items['itemNames']);$i++)
                        {
                           $main .=	'<div class="row">
                              <h6 class="white-text text-darken-2">
                                 '.$lesser_items['itemNames'][$i].' --> Only '.$lesser_items['quantityAvailable'][$i].'(kgs/l) is available!
                              </h6>
                           </div>';
                        }

                        echo $pre.$main.$post;
                     }
                  }
               ?>
               <script>

                  $(document).on("click",".remove_field-1", function(e){ //user click on remove text                 
                     e.preventDefault(); $(".msg").remove(); 
               });

               $(document).on("click",".remove_field-2", function(e){ //user click on remove text                 
                  e.preventDefault(); $(".error").remove(); 
            });

            $(document).on("click",".remove_field-3", function(e){ //user click on remove text                 
               e.preventDefault(); $(".lesser-items").remove(); 
         });

      </script>
      <?php
         if(isset($title) && $title != "")
         {
         ?>
         <div class="row">
            <div class="col s8 offset-s2">
               <h3 align='center'>	<span class="black-text text-darken-2">
                     <?php
                        echo $title;
                     ?>
               </h3></span>
            </div>
         </div>
         <?php
         }
      ?>
