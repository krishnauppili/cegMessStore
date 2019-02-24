<script>
$(function(){
    var tabindex = 1;
    $('input,select,button').each(function() {
        if (this.type != "hidden") {
            var $input = $(this);
            $input.attr("tabindex", tabindex);
            tabindex++;
        }
    });
    $('#issuedDate').pickadate();
});

</script>
<style type="text/css">
.controls {
margin: 75px;
}


select {
border-size: 2px;
border-color: #000066;
border-radius: 4px;
}

.controls a {
	border-radius: 4px;
	font-size: 15px;
	text-align: center;
	width: 100px;
}


.custom-border{
border-size: 2px;
border-color: #000066;
border-radius: 4px;
}

</style>
<?php
$decodedTableData = json_decode($tableData,true);
$size = count($decodedTableData['itemNames']);
?>
	<form name="selection" method="post"  action="issue_item" onsubmit="return get_list();"> 
	<div class="row">

		<div class="input-field col s6">
			<select class="browser-default" name="selectedMess" value = "<?php echo isset($selectedMess) ? $selectedMess : "";?>" required tabindex=1 autofocus>
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
	<div class='row'>
		<div class='col s4'>
		<span class='blue-text text-darken-2'>Item Name</span>
		</div>
		
		<div class='col s4'>
		<span class='blue-text text-darken-2'>Quantity Available(Kg/L)</span>
		</div>

		<div class='col s4'>
		<span class='blue-text text-darken-2'>Quantity Required(Kg/L)</span>
		</div>

	</div>

	
			<?php
				for($i=0;$i<count($decodedTableData['itemNames']);$i++)
				{
				?>
				<div class="row margin_row">
		<div class = "col s2">
		<p>
			<input type='text' value='<?php echo $decodedTableData['itemNames'][$i];?>' 
				id='<?php echo $decodedTableData['itemNames'][$i];?>' 
					 name='selectedItems[]' readonly/>
<!--					<label for='<?php /* echo $decodedTableData['itemNames'][$i];?>'><?php /*echo $decodedTableData['itemNames'][$i];?></label>*/?> -->
		</p>
		</div>
		<div class="col s2">
                        <input type="text" name="selectedQuantity[]" value="" id='<?php echo 'txt'.$decodedTableData['itemNames'][$i];?>' placeholder='Enter Quantity'/>
<!--                        <label for="last_name">Enter Quantity</label>-->
        </div>
		<div class="col s2">
                        <span class="blue-text text-darken-2"><h6><?php echo $decodedTableData['quantityAvailable'][$i];?></h6></span>
                        <input type="hidden" name='quantityAvailable[]' value='<?php echo $decodedTableData['quantityAvailable'][$i];?>'/>
                        <input type="hidden" name='latestRate[]' value='<?php echo $decodedTableData['latestRate'][$i];?>'/>
        </div>
        <div class = "col s2">
        <p>
            <input type='text' value='<?php echo $decodedTableData['itemNames'][$i];?>' 
                id='<?php echo $decodedTableData['itemNames'][$i];?>' 
                     name='selectedItems[]' readonly/>
<!--                    <label for='<?php /* echo $decodedTableData['itemNames'][$i];?>'><?php /*echo $decodedTableData['itemNames'][$i];?></label>*/?> -->
        </p>
        </div>
        <div class="col s2">
                        <input type="text" name="selectedQuantity[]" value="" id='<?php echo 'txt'.$decodedTableData['itemNames'][$i];?>' placeholder='Enter Quantity'/>
<!--                        <label for="last_name">Enter Quantity</label>-->
        </div>
        <div class="col s2">
                        <span class="blue-text text-darken-2"><h6><?php echo $decodedTableData['quantityAvailable'][$i];?></h6></span>
                        <input type="hidden" name='quantityAvailable[]' value='<?php echo $decodedTableData['quantityAvailable'][$i];?>'/>
                        <input type="hidden" name='latestRate[]' value='<?php echo $decodedTableData['latestRate'][$i];?>'/>
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
