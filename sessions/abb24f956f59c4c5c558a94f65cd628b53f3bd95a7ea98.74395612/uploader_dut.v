module updown_counter(clk, reset, mode, count);
	input clk, reset, mode;
	output [ 7 :0 ] count;
wire reset, mode;
reg [ 7 :0] count;
	
always @(posedge clk )
begin
		 if(!reset)
	count <= 8'b0100;
		 else if (mode == 0)
count <= count + 1;
	else if (mode == 1)
		count <= count - 1;
end
endmodule
