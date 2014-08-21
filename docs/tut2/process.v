//Main module for processor
module process(opcode,data,clk,rst,fout);
input [9:0]opcode;
input [4:0]data;
input clk,rst;
output [7:0]fout;
wire [7:0]r1,r2,r3,r4,a1,aout,bout,r11,r22,r33,r44;
wire [2:0]s,d;
wire [3:0]alf,de;
wire g;
control con(opcode,clk,alf,de,s,d,g);
muxl mu1(r11,r22,r33,r44,data,s,clk,a1);
alu al1(r1,a1,alf,clk,g,aout);
bus bu1(aout,clk,bout);
demux dd1(bout,d,clk,r1,r2,r3,r4);
regs reg1(r1,rst,clk,r11);
regs reg2(r2,rst,clk,r22);
regs reg3(r3,rst,clk,r33);
regs reg4(r4,rst,clk,r44);
assign fout=aout;
endmodule

//Submodule for control unit
module control(opcode,clk,alufn,dd,sou,des,r);
input [9:0]opcode;
input clk;
output reg[3:0]alufn,dd;
output reg[2:0]sou,des;
output reg r;
always@(posedge clk)
begin
dd=opcode[9:6];
sou=opcode[5:3];
des=opcode[2:0];
case(dd)
0:begin
r=1'b1;
alufn=4'b0000;
end
1:begin
r=1'b0;
alufn=4'b0000;
end
2:begin
r=1'b0;
alufn=4'b0001;
end
3:begin
r=1'b0;
alufn=4'b0010;
end
4:begin
r=1'b0;
alufn=4'b0011;
end
5:begin
r=1'b0;
alufn=4'b0100;
end
6:begin
r=1'b0;
alufn=4'b0101;
end
7:begin
r=1'b0;
alufn=4'b0110;
end
8:begin
r=1'b0;
alufn=4'b0111;
end
9:begin
r=1'b0;
alufn=4'b1000;
end
10:begin
r=1'b0;
alufn=4'b1001;
end
11:begin
r=1'b0;
alufn=4'b1010;
end
12:begin
r=1'b0;
alufn=4'b1011;
end
endcase
end
endmodule

//Main module for Arithmetic and Logic Unit
module alu(a,b,ch,clk,rst,out);
input [7:0]a,b;
input clk,rst;
input[3:0]ch;
output [7:0]out;
reg [7:0]out;
wire [7:0]q;
div8 s1(a,b,q);
always@(posedge clk)
begin
if(rst)
begin
out=8'b0;
end
else if(!rst)
begin
case(ch)
0 : out=a+b;
1 : out=a-b;
2 : out=a*b;
3 : out=q;
4 : out=~a;
5 : out=a&b;
6: out=a|b;
7 : out=a^b;
8 : out=b<<1;
9 : out=b>>1;
10 : out={b[0],b[7:1]};
11 : out={b[6:0],b[7]};
endcase
end
end
endmodule

//Sub module for 8-bit Divider
module div8(divs,div,q);
input [7:0]divs,div;
output [7:0]q;
wire [3:0]r1,r2,r3,r4,r5;
assign q[7:5]=0;
div4 d1(divs[7:4],div[3:0],r1[3:0],q[4]);
div4 d2({r1[2:0],divs[3]},div[3:0],r2[3:0],q[3]);
div4 d3({r2[2:0],divs[2]},div[3:0],r3[3:0],q[2]);
div4 d4({r3[2:0],divs[1]},div[3:0],r4[3:0],q[1]);
div4 d5({r4[2:0],divs[0]},div[3:0],r5[3:0],q[0]);
endmodule

//Sub module for 4-bit Division
module div4(divs,div,rem,q);
input [3:0]divs,div;
output [3:0]rem;
output q;
reg [3:0]rem;
reg q;
wire [3:0]r,c;
cas1 c1(divs[0],div[0],1'b0,r[0],c[0]);
cas1 c2(divs[1],div[1],c[0],r[1],c[1]);
cas1 c3(divs[2],div[2],c[1],r[2],c[2]);
cas1 c4(divs[3],div[3],c[2],r[3],c[3]);
always@(divs or c or r)
begin
if(c[3])
begin
q=1'b0;
rem=divs;
end
else
begin
q=1'b1;
rem=r;
end
end
endmodule

//Sub module for CAS
module cas1(a,b,c,diff,brow);
input a,b,c;
output diff,brow;
assign diff=a^b^c;
assign brow=((~a)&b)|(b&c)|((~a)&c);
endmodule

//Main module for demux
module demux(busout,des,clk,rea,reb,rec,red);
input [7:0]busout;
input clk;
input[2:0]des;
output reg[7:0]rea,reb,rec,red;
always@(posedge clk)
begin
case(des)
0:begin
rea=8'b0;
reb=8'b0;
rec=8'b0;
red=8'b0;
end
1:rea=busout;
2:reb=busout;
3:rec=busout;
4:red=busout;
endcase
end
endmodule

//Main module for MUX
module muxl(rea1,reb1,rec1,red1,exin,sou,clk,alin);
input [7:0] rea1,reb1,rec1,red1;
input	[4:0] exin;
input[2:0] sou;
input clk;
output reg [7:0]alin;
always@(posedge clk)
begin
case(sou)
0:alin=8'b0;
1:alin=rea1;
2:alin=reb1;
3:alin=rec1;
4:alin=red1;
5:alin=exin;
endcase
end
endmodule

//Main module for bus
module bus(aluout,clk,buout);
input[7:0]aluout;
input clk;
output[7:0]buout;
reg [7:0]buout;
always@(posedge clk)
begin
buout=aluout;
end
endmodule

//Main module for reg
module regs(re,rst,clk,re1);
input [7:0]re;
input rst,clk;
output [7:0]re1;
reg [7:0]re1;
always@(posedge clk)
begin
if(rst)
begin
re1=8'b0;
end
else
begin
re1=re;
end
end
endmodule


////Reference: http://12mvl002-verilogcoding.blogspot.in/2012/12/8-bit-simple-processor.html