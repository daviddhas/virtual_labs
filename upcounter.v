module up_counter(clk, reset, count);

  parameter INPUT_WIDTH = 16;
  parameter OUTPUT_WIDTH = 8;
  parameter N_TV = 256; // number of test vectors
  parameter LOG_N_TV = 8;

	input clk;
	output [ 7 :0 ] count;
	inout sample_inout;
	input reset;
	
  wire reset;
//  assign reset = dutin[0];
  reg    [ 7 :0] count;
//	assign dutout = count ;

	always @(posedge clk ) begin
  	  if(!reset)
	     count <= 8'b0100;
	   else 
	     count <= count + 1; 
  end   
endmodule

