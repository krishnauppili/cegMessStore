BEGIN

    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN

            set @io_status = 'exception';

            set @io_log_step_details = 'exception in order_transactions';

            set @io_log_query_string = CONCAT(@io_log_query_string,
            'INSERT INTO transaction_log(t_id,step_no, step_details,proc_name,sub_proc_name,start_time,end_time)\r\n\t\t\tVALUES(''',@io_log_t_id,''',',@io_log_step_no,',''',@io_log_step_details,''',''',@io_log_proc_name,''',''',@io_log_sub_proc_name,''',''',@io_log_start_time,''',''',NOW(),''');');

            set log_query_param_out = @io_log_query_string;

            set status_param_out = @io_status;
            
            set bill_no_param_out = @io_log_t_id;

    END;



    
    set @io_status = 'error';

    set @io_query = '';

    set @io_log_query_string := '';

    set @io_log_proc_name := 'insert_order';

    set @io_log_sub_proc_name := '';

    set @io_log_step_details := '';

    set @io_log_step_no := 1;

    set @io_log_start_time := NOW();

    set @io_revert_issue_ids = '';

    set @io_count = 1;

    
    

    select @io_vendor_id := vendor_id from vendor_details where vendor_name = vendor_name_param;

    select @io_mess_id := mess_id from mess_details where mess_name='HOSTEL STORES';

    set @io_log_t_id = CONCAT('LOGT','_','IO','_',@io_vendor_id,'_',@io_mess_id,'_',DATE_FORMAT(date_param,'%d%m%Y'));


	set bill_no_param_out = RIGHT(@io_log_t_id,LENGTH(@io_log_t_id)-5);
    

    WHILE (SELECT REPLACE(SUBSTRING(SUBSTRING_INDEX(item_name_params, ',', @io_count),
    LENGTH(SUBSTRING_INDEX(item_name_params, ',', @io_count-1)) + 1),
    ',', '') != '') DO

        select @io_item_name_param := replace(substring(substring_index(item_name_params, ',', @io_count),
        length(substring_index(item_name_params, ',', @io_count-1)) + 1),
        ',', '');

        select @io_max_date := max(t_date) from provision_orders where item_name=@io_item_name_param;

        IF @io_max_date IS NOT NULL  THEN

            IF date_param <= @io_max_date THEN
                

                select @io_max_date1 := max(t_date) from provision_orders where t_date < date_param and item_name = @io_item_name_param;
                
                IF @io_max_datel IS NOT NULL THEN

                    select @io_t_id := t_id from provision_orders where t_date=@io_max_date1 and item_name =@io_item_name_param;
                    
                    select @io_max_mapping_time := max(mappping_time) from issue_order_mapping where order_id = @io_t_id;
                
                    IF @io_max_mapping_time IS NOT NULL THEN
                    
                        select @io_revert_issue_id :=issue_id from issue_order_mapping where mapping_time = @io_max_mapping_time and order_id= @io_t_id;

                        IF @io_revert_issue_ids != '' THEN
                            
                            set @io_revert_issue_ids = CONCAT(@io_revert_issue_ids,',',@io_revert_issue_id);

                        ELSE

                            set @io_revert_issue_ids = @io_revert_issue_id;

                        END IF;

                    END IF;
                
                ELSE 

                
                    
                    select @io_min_mapping_time := min(mapping_time) from issue_order_mapping where item_name = @io_item_name_param;
                
                    IF @io_min_mapping_time IS NOT NULL THEN
                    
                        select @io_revert_issue_id :=issue_id from issue_order_mapping where mapping_time = @io_min_mapping_time and item_name = @io_item_name_param;
                        
                        IF @io_revert_issue_ids != '' THEN
                            
                            set @io_revert_issue_ids = CONCAT(@io_revert_issue_ids,',',@io_revert_issue_id);

                        ELSE

                            set @io_revert_issue_ids = @io_revert_issue_id;

                        END IF;

                    END IF;
            
                END IF;

            END IF;

        END IF;

        set @io_count = @io_count + 1;

    END WHILE;

    set @io_log_step_details = CONCAT('The issue_ids to be reverted are',@io_revert_issue_ids);

    set @io_log_query_string = CONCAT(@io_log_query_string,
    'INSERT INTO transaction_log(t_id,step_no, step_details,proc_name,sub_proc_name,start_time,end_time)\r\n\t\t\tVALUES(''',@io_log_t_id,''',',@io_log_step_no,',''',@io_log_step_details,''',''',@io_log_proc_name,''',''',@io_log_sub_proc_name,''',''',@io_log_start_time,''',''',NOW(),''');','|');
    
    set @io_log_step_no := @io_log_step_no + 1;

    set @io_log_start_time := NOW();

    

    IF @io_revert_issue_ids != '' THEN

        CALL revert_back_to_issue (@io_revert_issue_ids,@io_query,'insert_order',@io_status);

        set @io_log_query_string = CONCAT(@io_log_query_string, @io_query,'|');

        set @io_query = '';
    
    END IF;


    

    IF (@io_revert_issue_ids != '' AND @io_status = 'success') OR (@io_revert_issue_ids = '') THEN

        IF @io_revert_issue_ids != '' THEN 

            set @io_status = 'error';

            set @io_log_step_details = CONCAT('revert back successfully completed');

            set @io_log_query_string = CONCAT(@io_log_query_string,
            'INSERT INTO transaction_log(t_id,step_no, step_details,proc_name,sub_proc_name,start_time,end_time)\r\n\t\t\tVALUES(''',@io_log_t_id,''',',@io_log_step_no,',''',@io_log_step_details,''',''',@io_log_proc_name,''',''',@io_log_sub_proc_name,''',''',@io_log_start_time,''',''',NOW(),''');','|');
    
            set @io_log_step_no := @io_log_step_no + 1;

            set @io_log_start_time := NOW();

        END IF;

        
        
        set @io_count = 1;

        WHILE (SELECT REPLACE(SUBSTRING(SUBSTRING_INDEX(item_name_params, ',', @io_count),
       LENGTH(SUBSTRING_INDEX(item_name_params, ',', @io_count-1)) + 1),
       ',', '') != '') DO

             select @io_item_name_param := replace(substring(substring_index(item_name_params, ',', @io_count),
            length(substring_index(item_name_params, ',', @io_count-1)) + 1),
            ',', '');

            select @io_quantity_param := cast(replace(substring(substring_index(quantity_params, ',', @io_count),
            length(substring_index(quantity_params, ',', @io_count-1)) + 1),
            ',', '') as decimal(10,5));

            IF type_param = 'O' THEN

                select @io_rate_param := cast(replace(substring(substring_index(rate_params, ',', @io_count),
                length(substring_index(rate_params, ',', @io_count-1)) + 1),
                ',', '') as decimal(10,5));

            ELSE 

                select @io_rate_param := latest_rate from provision_stock where item_name = @io_item_name_param;

            END IF;

            select @io_item_id := item_id from items where item_name= @io_item_name_param;

            select @io_new_t_id := CONCAT('T','_',type_param,'_',DATE_FORMAT(date_param,'%d%m%Y'),'_',@io_vendor_id,'_',@io_item_id,'_',@io_mess_id);

            insert into transactions(t_id,t_type,item_name,quantity,amount,t_date,mess_name,vendor_name) 
            values(@io_new_t_id,type_param,@io_item_name_param,@io_quantity_param,@io_rate_param*@io_quantity_param,date_param,'Hostel Stores',vendor_name_param);

            insert into provision_orders(t_id,item_name,t_type,quantity,rate,t_date,consumed_quantity) 
            values(@io_new_t_id,@io_item_name_param,type_param,@io_quantity_param,@io_rate_param,date_param,0);

            set @io_count = @io_count + 1;


            
                               
               
            select @io_recent_order_time :=min(t_date) from provision_orders where item_name = @io_item_name_param and consumed_quantity < quantity ;

            select @io_latest_rate := rate, @io_recent_order_id := t_id, @io_clearance_stock := (quantity - consumed_quantity) from provision_orders where item_name = @io_item_name_param and t_date = @io_recent_order_time;

            UPDATE provision_stock
            SET quantity_remaining = (SELECT IFNULL((SELECT SUM(quantity) FROM provision_orders WHERE item_name = @io_item_name_param  AND t_date >= @io_recent_order_time AND t_id != @io_recent_order_id) + @io_clearance_stock,@io_clearance_stock)),
            clearance_stock = @io_clearance_stock,
            latest_rate = @io_latest_rate
            WHERE item_name = @io_item_name_param ;

            set @io_log_step_details = CONCAT('Provision stock for item ',@io_item_name_param,' is updated from the recent order id ',@io_recent_order_id,' whose time is ',@io_recent_order_time,',clearance_stock is ',@io_clearance_stock,',latest rate is ',@io_latest_rate,' and the issue_ids ',@io_revert_issue_ids,' are to be redistributed.');
     
            set @io_log_query_string = CONCAT(@io_log_query_string,
            'INSERT INTO transaction_log(t_id,step_no, step_details,proc_name,sub_proc_name,start_time,end_time)\r\n\t\t\tVALUES(''',@io_log_t_id,''',',@io_log_step_no,',''',@io_log_step_details,''',''',@io_log_proc_name,''',''',@io_log_sub_proc_name,''',''',@io_log_start_time,''',''',NOW(),''');','|');


            set @io_log_step_no := @io_log_step_no + 1;

            set @io_log_start_time := NOW();

        END WHILE;

        set @io_log_step_details = CONCAT('insert_order operation completed.');

        set @io_log_query_string = CONCAT(@io_log_query_string,
        'INSERT INTO transaction_log(t_id,step_no, step_details,proc_name,sub_proc_name,start_time,end_time)\r\n\t\t\tVALUES(''',@io_log_t_id,''',',@io_log_step_no,',''',@io_log_step_details,''',''',@io_log_proc_name,''',''',@io_log_sub_proc_name,''',''',@io_log_start_time,''',''',NOW(),''');','|');
    
        set @io_log_step_no := @io_log_step_no + 1;

        set @io_log_start_time := NOW();



        


        IF @io_revert_issue_ids != '' THEN

            CALL redistribute (@io_revert_issue_ids,@io_query,'insert_order',@io_status);

            set @io_log_query_string = CONCAT(@io_log_query_string,@io_query,'|');

            IF @io_status = 'success' THEN

                set @io_status = 'error';

                set @io_log_step_details = 'redistribution completed';
     
                set @io_log_query_string = CONCAT(@io_log_query_string,
                'INSERT INTO transaction_log(t_id,step_no, step_details,proc_name,sub_proc_name,start_time,end_time)\r\n\t\t\tVALUES(''',@io_log_t_id,''',',@io_log_step_no,',''',@io_log_step_details,''',''',@io_log_proc_name,''',''',@io_log_sub_proc_name,''',''',@io_log_start_time,''',''',NOW(),''');','|');

            END IF;

        END IF;

        set @io_status = 'success';


    END IF;


    IF (right(@io_log_query_string,1) = '|') THEN

        set @io_log_query_string = left(@io_log_query_string,length(@io_log_query_string)-1);

    END IF;

    set status_param_out = @io_status;

    set log_query_param_out = @io_log_query_string;

    


END