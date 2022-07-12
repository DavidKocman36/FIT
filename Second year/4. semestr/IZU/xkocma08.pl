% Zadání è. 11:
% Napište program øešící úkol daný predikátem u11(LIN,VIN,SOUT,LOUT), kde 
% LIN je vstupní celoèíselný seznam s nejménì dvìma prvky, VIN je vstupní 
% promìnná obsahující pøirozené èíslo/práh, SOUT je výstupní seznam obsahující 
% všechna èísla seznamu LIN, která jsou menší nebo stejná jako práh, LOUT je 
% výstupní seznam obsahující všechna èísla seznamu LIN, která jsou vìtší než práh. 

% Testovací predikáty:                                  % SOUT  	LOUT
u11_1:- u11([5,27,-1,28,19,-4,10],10,SOUT,LOUT),write(SOUT),write(' '),write(LOUT).		% [5,-1,-4,10]	[27,28,19]

u11_2:- u11([5,27,-1,28,19,-4,10],28,SOUT,LOUT),write(SOUT),write(' '),write(LOUT).		% LIN           []		

u11_3:- u11([5,27,-1,28,19,-4,10],-8,SOUT,LOUT),write(SOUT),write(' '),write(LOUT).		% [] 		LIN

u11_r:- write('Zadej LIN: '),read(LIN),write('Zadej VIN: '),read(VIN),u11(LIN,VIN,SOUT,LOUT),write(SOUT),write(' '),write(LOUT).


% MAIN
% Prochazi seznam dvakrat, jednou naplnuje SOUT, podruhe LOUT
u11(LIN,VIN,SOUT,LOUT) :- 
    lt(LIN,VIN,SOUT),
    gt(LIN,VIN,LOUT), !.

% SOUT
% Dojde az nakonec pole a podle podminky vlozi do SOUT prvek seznamu LIN
lt([],_,[]) :- !.
lt([H|T],VIN,[H|SOUT]) :-  
    H =< VIN, !, lt(T,VIN,SOUT).
% pokud nesplnuje podminu, prvek je preskocen
lt([_|T],VIN,SOUT) :-  
    lt(T,VIN,SOUT).
    
% LOUT
% Dojde az nakonec pole a podle podminky vlozi do LOUT prvek seznamu LIN
gt([],_,[]) :- !.
gt([H|T],VIN,[H|LOUT]) :- 
    H > VIN, !, gt(T,VIN,LOUT).
gt([_|T],VIN,LOUT) :- 
    gt(T,VIN,LOUT).

