NAME = proj3
LOGIN = xkocma08

all:
	latex  $(NAME).tex
	latex  $(NAME).tex
	dvips  $(NAME).dvi
	ps2pdf -sPAPERSIZE=a4 $(NAME).ps
	ps2pdf -sPAPERSIZE=a4 $(NAME).ps
zip:
	zip $(LOGIN)-fit.zip *.tex Makefile
clean:
	rm -f $(NAME).{aux, dvi, log, pdf, ps} $(LOGIN)-fit.zip