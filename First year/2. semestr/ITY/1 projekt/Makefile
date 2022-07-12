NAME = proj1
LOGIN = xkocma08

all:
	latex $(NAME).tex
	dvips -t a4 $(NAME).dvi
	ps2pdf $(NAME).ps

zip:
	zip $(LOGIN)-fit.zip *.tex Makefile


clean:
	rm -f $(NAME).{aux, dvi, log, pdf, ps} $(LOGIN)-fit.zip