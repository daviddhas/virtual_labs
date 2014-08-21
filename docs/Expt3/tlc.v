module tlc ( clk, reset,  vehicle_sensor, highway_signal, farm_signal );
  input reset, clk, vehicle_sensor; 
  output [1:0] highway_signal, farm_signal;
  parameter HG=2'b00, HY=2'b01, FG=2'b10, FY=2'b11;
  parameter GREEN = 2'b00, YELLOW = 2'b01, RED = 2'b10;
  reg [1:0] highway_signal, farm_signal, cur_state, next_state;
  reg [1:0] highway_signal1, farm_signal1;
  reg reset_timer; reg [3:0] timer; wire long_timeout, short_timeout;
  always @( negedge clk or negedge reset ) 
    if (reset == 1'b0) 
		cur_state <= HG; 
	 else
	  cur_state <= next_state;
  
   always @( negedge clk or negedge reset )
    if (reset == 1'b0) 
			timer <= 4'h0;
	 else if ( reset_timer == 1'b1 ) 
			timer <= 4'h0; 
    else if ( timer == 4'hf ) 
			timer <= 4'b1111;
    else 
			timer <= (timer + 1) % 16;
			
  assign short_timeout = timer[0] ;  
  assign long_timeout = ( (timer[1]==1) || (timer[2]==1) || (timer[3]==1) ) ? 1'b1 : 1'b0;
  
  
  always @(cur_state or long_timeout or vehicle_sensor or short_timeout) 
  begin
       
    if (cur_state == HG)
			begin
	       highway_signal <= GREEN; farm_signal <= RED;
    
			 if (vehicle_sensor == 1'b1 && long_timeout == 1'b1 )
				begin
						next_state <= HY;reset_timer <= 1'b1;
				end
			 else 
				begin
					next_state <= HG;reset_timer <= 1'b0;
				end
			end					
			
	 else if (cur_state == HY) 
			 begin 
				highway_signal <= YELLOW; farm_signal <= RED;
				if ( short_timeout == 1'b1 ) 
				begin 
					next_state <= FG; reset_timer <= 1'b1;
				end 
				else  
				begin  
					next_state <= HY;reset_timer <= 1'b0; 
				end
			 end 
	 else if (cur_state == FG) 
			 begin 
				highway_signal <= RED; farm_signal <= GREEN;
				if ( long_timeout == 1'b1 ) 
					begin 
					  reset_timer<= 1'b1;
					  //if ( short_timeout == 1'b1 ) begin
						next_state <= FY;//end
					end 
				else  
					begin  
						next_state <= FG;  reset_timer<= 1'b0;
					end
				end 
	else if (cur_state == FY) 
			begin 
				highway_signal <= RED; farm_signal <= YELLOW;
				if ( short_timeout == 1'b1 ) 
					begin 
						next_state <= HG; reset_timer<= 1'b1;
					end 
				else  
					begin  
					next_state <= FY; reset_timer<= 1'b0; 
					end
			 end 
	else  
			begin 
			 highway_signal <= RED; farm_signal <= RED;
			 next_state <= HG;reset_timer <= 1'b1; 
			end
  end // always
endmodule
