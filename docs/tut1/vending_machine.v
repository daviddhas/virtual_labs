module vending_machine
(
	re1, rs2,		
	enable,
	drink,			// 0 for tea and 1 for coffee
	tea,			//Rs. 7
	coffee,			//Rs. 9
	clk, rst
);

input	[3 : 0]		re1;
input	[2 : 0]		rs2;
input	enable, drink;
input	clk, rst;

output	reg	tea, coffee;

always @(posedge clk)
begin
if(!rst)
	case (drink)
	1'b0:	
	begin
		case ({rs2, re1})
		7'h07:
		begin
			tea 	= 1'b1;
			coffee 	= 1'b0;
		end
		7'h15:
		begin
			tea 	= 1'b1;
			coffee 	= 1'b0;
		end
		7'h23:
		begin
			tea		= 1'b1;
			coffee 	= 1'b0;
		end
		7'h31:
		begin
			tea 	= 1'b1;
			coffee 	= 1'b0;
		end
		7'h40:
		begin
			tea 	= 1'b1;
			coffee 	= 1'b0;
		end
		default:
		begin
			tea 	= 1'b0;
			coffee 	= 1'b0;
		end
		endcase
	end
	1'b1:	
	begin
		case ({rs2, re1})
		7'h0A:
		begin
			tea 	= 1'b0;
			tea 	= 1'b1;
		end
		7'h18:
		begin
			tea 	= 1'b0;
			tea 	= 1'b1;
		end
		7'h26:
		begin
			tea		= 1'b1;
			tea 	= 1'b1;
		end
		7'h34:
		begin
			tea 	= 1'b0;
			tea 	= 1'b1;
		end
		7'h42:
		begin
			tea 	= 1'b0;
			tea 	= 1'b1;
		end
		7'h50:
		begin
			tea 	= 1'b0;
			tea 	= 1'b1;
		end
		default:
		begin
			tea 	= 1'b0;
			coffee 	= 1'b0;
		end
		endcase
	end
	endcase
else
begin
	tea 	= 1'b0;
	coffee 	= 1'b0;
end
	
end
endmodule
	
	