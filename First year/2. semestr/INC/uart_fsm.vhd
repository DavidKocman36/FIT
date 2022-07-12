-- uart_fsm.vhd: UART controller - finite state machine
-- Author(s): David Kocman xkocma08
--
library ieee;
use ieee.std_logic_1164.all;

-------------------------------------------------
entity UART_FSM is
port(
   CLK      : in std_logic;
   RST      : in std_logic;
   DIN      : in std_logic;
   CNT_CLK  : in std_logic_vector(4 downto 0);
   CNT_B    : in std_logic_vector(3 downto 0);
   WRITE_EN : out std_logic;
   CNT_EN   : out std_logic;
   DOUT_EN  : out std_logic;
   FIRST_ST : out std_logic
   );
end entity UART_FSM;

-------------------------------------------------
architecture behavioral of UART_FSM is
type STATE_T is (WAIT_START_BIT, WAIT_FIRST_BIT, READ_DATA, WAIT_STOP_BIT, VALIDATION);
signal STATE: STATE_T:= WAIT_START_BIT;
begin
   --auxillary signals
   WRITE_EN    <= '1' when STATE = READ_DATA else '0';
   DOUT_EN     <= '1' when STATE = VALIDATION else '0';
   CNT_EN      <= '1' when STATE = WAIT_FIRST_BIT or STATE = READ_DATA or STATE = WAIT_STOP_BIT else '0';
   FIRST_ST    <= '1' when STATE = WAIT_START_BIT else '0';
   --the state changing process
   process (CLK, RST, STATE) 
   begin
   
      if RST = '1' and rising_edge(CLK) then

         STATE <= WAIT_START_BIT;

      elsif RST = '0' and rising_edge(CLK) then
         
         case( STATE ) is
            when WAIT_START_BIT  => if DIN = '0' then
                                       STATE <= WAIT_FIRST_BIT;
                                    end if ;
            when WAIT_FIRST_BIT  => if CNT_CLK = "01000" then
                                       STATE <= READ_DATA;
                                    end if ;
            when READ_DATA       => if CNT_B = "1000" then
                                       STATE <= WAIT_STOP_BIT;
                                    end if ;
            when WAIT_STOP_BIT   => if CNT_CLK="11000" then
                                       STATE <= VALIDATION;
                                    end if ;
            when VALIDATION      => STATE <= WAIT_START_BIT;
            when others          => null;
         end case ;

      end if ;

   end process;

end behavioral;