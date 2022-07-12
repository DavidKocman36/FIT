-- uart.vhd: UART controller - receiving part
-- Author(s): David Kocman xkocma08
--
library ieee;
use ieee.std_logic_1164.all;
use ieee.std_logic_unsigned.all;
use ieee.numeric_std.all;

-------------------------------------------------
entity UART_RX is
port(	
    CLK		 : in std_logic;
	RST		 : in std_logic;
	DIN		 : in std_logic;
	DOUT	 : out std_logic_vector(7 downto 0);
	DOUT_VLD : out std_logic
);
end UART_RX;  

-------------------------------------------------
architecture behavioral of UART_RX is
signal cnt_clk: std_logic_vector(4 downto 0);
signal cnt_b: std_logic_vector(3 downto 0);
signal write_en: std_logic;
signal cnt_enb: std_logic;
signal dout_int: std_logic;
signal cnt_enb_bit: std_logic;
signal first_st: std_logic;

begin
	FSM: entity work.UART_FSM(behavioral)
    port map (
        CLK 	    => CLK,
        RST 	    => RST,
        DIN 	    => DIN,
        DOUT_EN     => dout_int,
		CNT_CLK		=> cnt_clk,
		CNT_B		=> cnt_b,
		WRITE_EN	=> write_en,
		CNT_EN		=> cnt_enb,
		FIRST_ST	=> first_st
    );
	--clock counter
	CLOCK_COUNT: process(CLK, RST) begin
		
		if write_en = '1' then
			if cnt_clk = "10000" then
				cnt_clk<= "00000";
				cnt_enb_bit<='1';
			else
				cnt_enb_bit<='0';
			end if;
		end if;

		if rising_edge(CLK) and RST='0' then
			
			if cnt_enb = '1' then
				cnt_clk <= cnt_clk + 1;
			else 
				cnt_clk <= "00000";
			end if;
			
		end if;
		

	end process;
	--bit counter
	BIT_COUNT: process(CLK, RST,cnt_clk) begin
		
		if RST='1' then
			cnt_b <= "0000";
		end if ;
		
		if rising_edge(CLK) and RST='0' then

			if write_en = '1' then
				
				if cnt_enb_bit = '1' then
					cnt_b <= cnt_b + 1;
				end if;

			end if;
			
		end if;

		if first_st = '1' then
			cnt_b <= "0000";
		end if;

	end process;
	--multiplexer
	RECEIVER: process(CLK, RST,cnt_b) begin
		--writing din on its corresponding index
		if write_en = '1' then	
			if cnt_b = "0000" then
				DOUT(0) <= DIN;
			elsif cnt_b = "0001" then
				DOUT(1) <= DIN;
			elsif cnt_b = "0010" then
				DOUT(2) <= DIN;
			elsif cnt_b = "0011" then
				DOUT(3) <= DIN;
			elsif cnt_b = "0100" then
				DOUT(4) <= DIN;
			elsif cnt_b = "0101" then
				DOUT(5) <= DIN;
			elsif cnt_b = "0110" then
				DOUT(6) <= DIN;
			elsif cnt_b = "0111" then
				DOUT(7) <= DIN;
			end if;
		end if;

		if RST='1' then
			DOUT <= "00000000";
		end if;

	end process;

	VLD: process(CLK, RST) begin

		if rising_edge(CLK) and RST='0' then

			if dout_int = '1' then
				DOUT_VLD<='1';
			else
				DOUT_VLD<='0';
			end if ;

		end if;
		if RST='1' then

			DOUT_VLD<='0';

		end if;

	end process;
end behavioral;