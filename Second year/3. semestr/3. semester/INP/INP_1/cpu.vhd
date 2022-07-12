-- cpu.vhd: Simple 8-bit CPU (BrainLove interpreter)
-- Copyright (C) 2021 Brno University of Technology,
--                    Faculty of Information Technology
-- Author(s): David Kocman, xkocma08
--

library ieee;
use ieee.std_logic_1164.all;
use ieee.std_logic_arith.all;
use ieee.std_logic_unsigned.all;

-- ----------------------------------------------------------------------------
--                        Entity declaration
-- ----------------------------------------------------------------------------
entity cpu is
 port (
   CLK   : in std_logic;  -- hodinovy signal
   RESET : in std_logic;  -- asynchronni reset procesoru
   EN    : in std_logic;  -- povoleni cinnosti procesoru
 
   -- synchronni pamet ROM
   CODE_ADDR : out std_logic_vector(11 downto 0); -- adresa do pameti
   CODE_DATA : in std_logic_vector(7 downto 0);   -- CODE_DATA <- rom[CODE_ADDR] pokud CODE_EN='1'
   CODE_EN   : out std_logic;                     -- povoleni cinnosti
   
   -- synchronni pamet RAM
   DATA_ADDR  : out std_logic_vector(9 downto 0); -- adresa do pameti
   DATA_WDATA : out std_logic_vector(7 downto 0); -- ram[DATA_ADDR] <- DATA_WDATA pokud DATA_EN='1'
   DATA_RDATA : in std_logic_vector(7 downto 0);  -- DATA_RDATA <- ram[DATA_ADDR] pokud DATA_EN='1'
   DATA_WREN  : out std_logic;                    -- cteni z pameti (DATA_WREN='0') / zapis do pameti (DATA_WREN='1')
   DATA_EN    : out std_logic;                    -- povoleni cinnosti
   
   -- vstupni port
   IN_DATA   : in std_logic_vector(7 downto 0);   -- IN_DATA obsahuje stisknuty znak klavesnice pokud IN_VLD='1' a IN_REQ='1'
   IN_VLD    : in std_logic;                      -- data platna pokud IN_VLD='1'
   IN_REQ    : out std_logic;                     -- pozadavek na vstup dat z klavesnice
   
   -- vystupni port
   OUT_DATA : out  std_logic_vector(7 downto 0);  -- zapisovana data
   OUT_BUSY : in std_logic;                       -- pokud OUT_BUSY='1', LCD je zaneprazdnen, nelze zapisovat,  OUT_WREN musi byt '0'
   OUT_WREN : out std_logic                       -- LCD <- OUT_DATA pokud OUT_WE='1' a OUT_BUSY='0'
 );
end cpu;


-- ----------------------------------------------------------------------------
--                      Architecture declaration
-- ----------------------------------------------------------------------------
architecture behavioral of cpu is

--PC (program counter)
   signal pc_to_rom : std_logic_vector(11 downto 0);
	signal pc_inc : std_logic;
	signal pc_dec : std_logic;
	
--PTR (ukazatel do pameti dat)
	signal ptr_to_ram : std_logic_vector(9 downto 0);
	signal ptr_inc : std_logic;	
	signal ptr_dec : std_logic;
	
--CNT (while counter)
	signal cnt_to_fsm : std_logic_vector(7 downto 0);
	signal cnt_inc : std_logic;
	signal cnt_dec : std_logic;
	
--MX (multiplexor)
	signal mx_select : std_logic_vector(1 downto 0);
	signal mx_out :	std_logic_vector(7 downto 0);
	
--FSM
	type fsm_state is(
		start, fetch, decode, execute,
		pointer_inc, pointer_dec, value_inc_start, value_dec_start, value_inc, value_dec, value_inc_end, value_dec_end,
		while_begin,while_begin_1,while_begin_loop, while_end,while_end_1,while_end_loop,while_end_loop_end, while_break, while_break_1, while_break_end, while_begin_loop_en,
		while_end_loop_en,
		print_start, print, print_end, get_start, get,
		halt
	);
	signal state  : fsm_state := start;
	signal nstate : fsm_state := start;

begin

 -- zde dopiste vlastni VHDL kod

 -- pri tvorbe kodu reflektujte rady ze cviceni INP, zejmena mejte na pameti, ze 
 --   - nelze z vice procesu ovladat stejny signal,
 --   - je vhodne mit jeden proces pro popis jedne hardwarove komponenty, protoze pak
 --      - u synchronnich komponent obsahuje sensitivity list pouze CLK a RESET a 
 --      - u kombinacnich komponent obsahuje sensitivity list vsechny ctene signaly.

	--PROGRAM COUNTER
	pc: process (CLK, RESET) 
	begin
		--pri resetu na 1 se pc vynuluje
		if(RESET = '1') then
			pc_to_rom <= (others =>'0');
		--jinak pc zvysime nebo snizime
		elsif (rising_edge(CLK)) then
			if(pc_inc = '1') then 
				pc_to_rom <= pc_to_rom + 1;
			elsif(pc_dec = '1') then
				pc_to_rom <= pc_to_rom - 1;
			end if;
		end if;
	end process;
	CODE_ADDR <= pc_to_rom;
	
	--PTR REGISTR
	ptr: process (CLK, RESET) 
	begin
		--pri resetu na 1 se ptr vynuluje
		if(RESET = '1') then
			ptr_to_ram <= (others =>'0');
		--jinak ptr zvysime nebo snizime
		elsif (rising_edge(CLK)) then
			if(ptr_inc = '1') then 
				ptr_to_ram <= ptr_to_ram + 1;
			elsif(ptr_dec = '1') then
				ptr_to_ram <= ptr_to_ram - 1;
			end if;
		end if;
	end process;
	DATA_ADDR <= ptr_to_ram;
	
	--MULTIPLEXOR
	mx: process (mx_select, CLK, RESET) 
	begin
			--pri resetu mx inicializujeme na data
			if(RESET = '1') then
				mx_out <= DATA_RDATA;
			--jinak pøi:
			-- 00: mx pøijme INPUT
			-- 01: mx zvýší hodnotu v bunce
			-- 10: mx snizi hodnotu v bunce
			-- jiné: do mx se nahrajou DATA_RDATA
			elsif (rising_edge(CLK)) then
				case (mx_select) is
					when "00" => mx_out <= IN_DATA;
					when "01" => mx_out <= DATA_RDATA + 1;
					when "10" => mx_out <= DATA_RDATA - 1;
					when others => mx_out <= DATA_RDATA;
				end case;
			end if;
	end process;
	DATA_WDATA <= mx_out;
	--propojeni hodnot bunek do OUTPUTu
	OUT_DATA <= DATA_RDATA;
	
	--CNT
	cnt: process (CLK, RESET)
	begin
			--pri resetu se cnt vynuluje
			if(RESET = '1') then
				cnt_to_fsm <= (others => '0');
			--jinak se zvysi nebo snizi
			elsif (rising_edge(CLK)) then
				if(cnt_inc = '1') then 
					cnt_to_fsm <= cnt_to_fsm + 1;
				elsif(cnt_dec = '1') then
					cnt_to_fsm<= cnt_to_fsm - 1;
				end if;
			end if;
	end process;
	
	--STATE REGISTER
	SR: process (CLK, RESET, EN) 
	begin
		--nahravani nstatu do aktualniho stat
		--pri resetu se inicializuje na start
		if(RESET = '1') then
			state <= start;
		--jinak pri povolene cinnosti se prepinaji stavy
		elsif (rising_edge(CLK) and EN = '1') then
			state <= nstate;
		end if;
	end process;
	
	--FSM
	fsm: process (state, OUT_BUSY, IN_VLD, EN, DATA_RDATA, CODE_DATA)
	begin
		--inicializace signalu
		--pc
		pc_inc <= '0';
		pc_dec <= '0';
		--cnt
		cnt_dec <= '0';
		cnt_inc <= '0';
		--ptr
		ptr_inc <= '0';
		ptr_dec <= '0';
		--ostatni
		CODE_EN <= '0';
		DATA_EN <= '0';
		DATA_WREN <= '0';
		IN_REQ <= '0';
		OUT_WREN <= '0';
		--mx
		mx_select <= "00";
		
		case state is
			--zacinajici stav
			when start => 	nstate <= fetch;
			--poslani instrukce z ROM
			when fetch => 	CODE_EN <= '1';
								nstate <= decode;
			--DECODE AN OPERATION
			when decode => 
								case CODE_DATA is
									-- >
									when x"3E" => nstate <= pointer_inc;
									-- <
									when x"3C" => nstate <= pointer_dec;
									-- +
									when x"2B" => nstate <= value_inc_start;
									-- -
									when x"2D" => nstate <= value_dec_start;
									-- ]
									when x"5D" => nstate <= while_end;
									--[
									when x"5B" => nstate <= while_begin;
									-- ,
									when x"2C" => nstate <= get_start;
									-- .
									when x"2E" => nstate <= print_start;
									-- ~
									when x"7E" => nstate <= while_break;
									-- nul
									when x"00" => nstate <= halt;
									-- komentare
									when others => pc_inc <= '1';
														nstate <= fetch;
								end case;
			--POINTER OPERATIONS
			when pointer_inc => 
									pc_inc <= '1';
									ptr_inc <= '1';
									nstate <= fetch;
									
			when pointer_dec => 
									pc_inc <= '1';
									ptr_dec <= '1';
									nstate <= fetch;
			--VALUE INCREASE
			when value_inc_start => 
									DATA_EN <= '1';
									DATA_WREN <= '0';
									nstate <= value_inc;
			--prvni precte data, pak prectene data zvysi
			when value_inc =>
									mx_select <= "01";
									nstate <= value_inc_end;
			--nakonec zapise
			when value_inc_end =>
									DATA_EN <= '1';
									DATA_WREN <= '1';
									pc_inc <= '1';
									nstate <= fetch;
			--VALUE DECREASE
			when value_dec_start => 
									DATA_EN <= '1';
									DATA_WREN <= '0';
									nstate <= value_dec;
			--prvni precte data, pak prectene data snizi
			when value_dec =>
									mx_select <= "10";
									nstate <= value_dec_end;
			--nakonec zapise
			when value_dec_end =>
									DATA_EN <= '1';
									DATA_WREN <= '1';
									pc_inc <= '1';
									nstate <= fetch;
			--PRINT
			when print_start =>
									if(OUT_BUSY = '0') then
										DATA_EN <= '1';
										nstate <= print;
									else
										nstate <= print_start;
									end if;
			--dokud je OUT_BUSY na 1 tak cekame na zapsani
			when print =>		
									OUT_WREN <= '1';
									pc_inc <= '1';
									nstate <= fetch;
			--GET STDIN
			when get_start => 
									IN_REQ <= '1';
									mx_select <= "00";
									nstate <= get;
			--Pokud je INPUT nevalidni, dostavame dalsi dokud neni
			when get => 		
									if (IN_VLD = '0') then
										IN_REQ <= '1';
										mx_select <= "00";
										nstate <= get;
									else
										DATA_EN <= '1';
										DATA_WREN <= '1';
										pc_inc <= '1';
										nstate <= fetch;
									end if;
			--WHILE "["
			when while_begin => 
									pc_inc <= '1';
									DATA_EN <= '1';
									DATA_WREN <= '0';
									nstate <= while_begin_1;
			--pri 0 hodnote cyklus skonci =>  while(a != 0)
			when while_begin_1 =>
									if(DATA_RDATA = (DATA_RDATA'range => '0')) then
										cnt_inc <= '1';
										nstate <= while_begin_loop_en;
									else
										nstate <= fetch;
									end if;
			--loopujeme dokud nenarazime na ] prvniho while
			when while_begin_loop =>
									if(cnt_to_fsm /= (cnt_to_fsm'range => '0')) then
										if(CODE_DATA = x"5D") then
											cnt_dec <= '1';
										elsif(CODE_DATA = x"5B") then
											cnt_inc <= '1';
										end if;
										
										nstate <= while_begin_loop_en;
										pc_inc <= '1';
									else
										nstate <= fetch;
									end if;
			--posleme si instrukci z romky
			when while_begin_loop_en =>
									CODE_EN <= '1';
									nstate <= while_begin_loop;
			--WHILE "]"
			when while_end =>
									DATA_EN <= '1';
									DATA_WREN <= '0';
									nstate <= while_end_1;
			when while_end_1=>
									--pokud jsou data 0, cyklus vlastne konci, jinak loopujeme nazpet
									if(DATA_RDATA /= (DATA_RDATA'range => '0')) then
										cnt_inc <= '1';
										pc_dec <= '1';
										nstate <= while_end_loop_en;
									else
										pc_inc<='1';
										nstate <= fetch;
									end if;
			when while_end_loop=>
									--loopujeme dokud nenarazime na [ prvniho while
									if(cnt_to_fsm /= (cnt_to_fsm'range => '0')) then
										if(CODE_DATA = x"5B") then
											cnt_dec <= '1';
										elsif(CODE_DATA = x"5D") then
											cnt_inc <= '1';
										end if;
										nstate <= while_end_loop_en;
									else 
										nstate <= fetch;
									end if;
									--pri 0 cnt jsme na [
									if(cnt_to_fsm = (cnt_to_fsm'range => '0')) then
										pc_inc <= '1';
									else
										pc_dec <= '1';
									end if;
			--posleme si instrukci z romky 
			when while_end_loop_en =>
									CODE_EN <= '1';
									nstate <= while_end_loop;
			--BREAK
			when while_break =>
									pc_inc <= '1';
									cnt_inc <= '1';
									nstate <= while_break_1;
			--posleme si instrukci z romky
			when while_break_1 =>
									CODE_EN <= '1';
									nstate <= while_break_end;
			--dokud nenarazime na ] tak ignorujeme jakekoli intrukce
			when while_break_end =>
									if(cnt_to_fsm /= (cnt_to_fsm'range => '0')) then
										if (CODE_DATA = x"5D") then
											cnt_dec <= '1';
										elsif (CODE_DATA = x"5B") then 
											cnt_inc <= '1';
										end if;
										pc_inc <= '1';
										nstate <= while_break_1;
									else 
										nstate <= fetch;
									end if;
									
			--NULL
			--pri halt stavu nic nedelame
			when halt => nstate <= halt;
			
			when others => nstate <= start;
		end case;
	end process;
	
end behavioral;
 
