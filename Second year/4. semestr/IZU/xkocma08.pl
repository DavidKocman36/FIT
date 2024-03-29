% Zad�n� �. 11:
% Napi�te program �e��c� �kol dan� predik�tem u11(LIN,VIN,SOUT,LOUT), kde 
% LIN je vstupn� celo��seln� seznam s nejm�n� dv�ma prvky, VIN je vstupn� 
% prom�nn� obsahuj�c� p�irozen� ��slo/pr�h, SOUT je v�stupn� seznam obsahuj�c� 
% v�echna ��sla seznamu LIN, kter� jsou men�� nebo stejn� jako pr�h, LOUT je 
% v�stupn� seznam obsahuj�c� v�echna ��sla seznamu LIN, kter� jsou v�t�� ne� pr�h. 

% Testovac� predik�ty:                                  % SOUT  	LOUT
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

