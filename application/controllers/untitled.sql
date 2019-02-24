BEGIN

	 
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN

    		set @log_query_string = CONCAT(@log_query_string,
			'INSERT INTO transaction_log(t_id,step_no, step_details,proc_name,sub_proc_name,start_time,end_time)\r\n\t\t\tVALUES
			(\'',@log_t_id,'\',\'',@log_step_no,'\',\'',@log_step_details,'\',\'',@log_proc_name,'\',\'',@log_sub_proc_name,'\',\'',@log_start_time,'\',\'',NOW(),'\');');

    		set log_query_param_out = @log_query_string;
	END;

    set @log_query_string := '';

	set @log_proc_name := proc_name_param;

	set @log_sub_proc_name := 'redistribute';

	set @log_t_id := issue_id_param;

	set @log_step_details := '';

	set @log_step_no := 1;

	set @log_start_time := NOW();
	
	SELECT @issued_item_name := item_name,
	@issued_time := t_date
	FROM transactions WHERE t_id = issue_id_param;
    
	SELECT @recent_order_id := t_id,
	@recent_order_time := t_date
	FROM provision_orders WHERE item_name = @issued_item_name
	AND consumed_quantity < quantity 
	AND t_date = (SELECT MIN(t_date) FROM provision_orders WHERE item_name = @issued_item_name AND consumed_quantity < quantity);

	
	CREATE TEMPORARY TABLE subsequent_issues
    ( issue_id VARCHAR(15), 
	issue_time DATETIME,
	quantity FLOAT, 
	processed INT);
	
    INSERT INTO subsequent_issues
	(issue_id,issue_time,quantity, processed)
    SELECT t_id,t_date,quantity, 0 FROM transactions
    WHERE item_name = @issued_item_name AND 
	t_date >= @issued_time
	AND t_type = 'I';
    
    
    SELECT * FROM subsequent_issues;

	
	CREATE TEMPORARY TABLE subsequent_orders
    (order_id VARCHAR(15),
	order_time DATETIME, 
	quantity FLOAT, 
    consumed_quantity FLOAT,
	 processed INT);
	 
    INSERT INTO subsequent_orders
    (order_id,order_time,quantity,consumed_quantity,processed)
    SELECT t_id,t_date,quantity,consumed_quantity, 0 
	FROM provision_orders 
	WHERE item_name = @issued_item_name AND
	t_date >= @recent_order_time;
    
    SELECT * FROM subsequent_orders;
	
	SELECT @total_quantity_to_issue :=SUM(quantity) FROM subsequent_issues;
	
	SELECT @total_quantity_on_stock :=SUM(quantity) FROM subsequent_orders;

	set @log_step_details := CONCAT('Item_name is ',@issued_item_name,' and the issue time is ',@issued_time,'.Consumption should start from order id ',@recent_order_id,'and its order time is ',@recent_order_time,'. The total quantity on stock is ',@total_quantity_on_stock,' and the total quantity to issue is ',@total_quantity_to_issue);

	set @log_query_string = CONCAT(@log_query_string,
			'INSERT INTO transaction_log(t_id,step_no, step_details,proc_name,sub_proc_name,start_time,end_time)\r\n\t\t\tVALUES(',@log_t_id,',',@log_step_no,',',@log_step_details,',',@log_proc_name,',',@log_sub_proc_name,',',@log_start_time,',',NOW(),');','|');

	set @log_step_no := @log_step_no + 1;

	set @log_start_time := NOW();
	
	IF @total_quantity_on_stock >= @total_quantity_to_issue THEN
	
		SELECT @min_order_id := order_id,
		@quantity_avail := quantity - consumed_quantity
		FROM subsequent_orders
		WHERE order_id = @recent_order_id;
    
		WHILE EXISTS
		(SELECT issue_id FROM subsequent_issues 
		WHERE processed = 0) DO
    
			SELECT @min_issue_time := MIN(issue_time) FROM subsequent_issues WHERE processed=0 ;
    
			SELECT @min_issue_id := issue_id,
			@quantity_to_issue := quantity 
			FROM subsequent_issues WHERE
			issue_time = @min_issue_time AND processed = 0;
            
            SET @issueing_count :=0;
        
			WHILE (@quantity_to_issue > 0) DO

			SET @issueing_count := @issueing_count + 1;
        
			IF @quantity_to_issue <= @quantity_avail THEN
         
				SET @quantity_avail := @quantity_avail - @quantity_to_issue;
			
				INSERT INTO issue_order_mapping (item_name,issue_id, order_id, mapping_time, consumed_quantity )
				SELECT @issued_item_name,@min_issue_id, @min_order_id, DATE_ADD(@min_issue_time,INTERVAL @issueing_count SECOND),@quantity_to_issue;
			
				UPDATE provision_orders 
				SET consumed_quantity = consumed_quantity + @quantity_to_issue
				WHERE t_id = @min_order_id;
			
				SET @quantity_to_issue := 0;
			
				UPDATE subsequent_issues
				SET processed = 1
				WHERE issue_id = @min_issue_id;
            
			ELSE
			IF @quantity_avail > 0 THEN
        
				SET @quantity_to_issue := @quantity_to_issue - @quantity_avail;
			
				INSERT INTO issue_order_mapping (item_name,issue_id, order_id, mapping_time, consumed_quantity )
				SELECT @issued_item_name,@min_issue_id, @min_order_id, DATE_ADD(@min_issue_time,INTERVAL @issueing_count SECOND),@quantity_avail;
			
				UPDATE provision_orders 
				SET consumed_quantity = consumed_quantity + @quantity_avail
				WHERE t_id = @min_order_id;
			
			
				SET @quantity_avail := 0;
            
			END IF;
        	
            UPDATE subsequent_orders
			SET processed = 1
			WHERE order_id = @min_order_id;
                
			SELECT @min_order_time :=MIN(order_time) FROM subsequent_orders WHERE processed = 0;
            
			SELECT @min_order_id := order_id,
			@quantity_avail := quantity - consumed_quantity 
			FROM subsequent_orders
			WHERE order_time =@min_order_time
            AND processed = 0;
            
			END IF;
        
        END WHILE;
        
     END WHILE;

	set @log_query_string = CONCAT(@log_query_string,
			'INSERT INTO transaction_log(t_id,step_no, step_details,proc_name,sub_proc_name,start_time,end_time)\r\n\t\t\tVALUES(',@log_t_id,',',@log_step_no,',',@log_step_details,',',@log_proc_name,',',@log_sub_proc_name,',',@log_start_time,',',NOW(),');','|');

	set @log_step_no := @log_step_no + 1;

	set @log_start_time := NOW();
	
	 select @recent_order_time :=min(t_date) from provision_orders where item_name = @issued_item_name and consumed_quantity < quantity and type != 'Approximation';

	select @latest_rate := rate, @recent_order_id := t_id, @clearance_stock := (quantity - consumed_quantity) from provision_orders where item_name = @issued_item_name and t_date = @recent_order_time;

	UPDATE provision_stock
	SET quantity_remaining = (SELECT IFNULL((SELECT SUM(quantity) FROM provision_orders WHERE item_name = @issued_item_name  AND t_date >= @recent_order_time AND t_id != @recent_order_id) + @clearance_stock,@clearance_stock)),
		clearance_stock =@clearance_stock,
		latest_rate = @latest_rate
	WHERE item_name =@issued_item_name;
	
	END IF;
	
	DROP TABLE subsequent_issues;
	
	DROP TABLE subsequent_orders;


    set @log_step_details := CONCAT('Provision stock for item ',@issued_item_name,' is updated from the recent order id ',@recent_order_id,' whose time is ',@recent_order_time,',clearance_stock is ',@clearance_stock,',latest rate is ',@latest_rate,'.The temporary tables are dropped.');
     
	set @log_query_string = CONCAT(@log_query_string,
			'INSERT INTO transaction_log(t_id,step_no, step_details,proc_name,sub_proc_name,start_time,end_time)\r\n\t\t\tVALUES(',@log_t_id,',',@log_step_no,',',@log_step_details,',',@log_proc_name,',',@log_sub_proc_name,',',@log_start_time,',',NOW(),');');
	set log_query_param_out = @log_query_string;


END