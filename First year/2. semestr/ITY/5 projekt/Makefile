NAME = proj5
LOGIN = xkocma08

all: 
	pdflatex $(NAME).tex
	pdflatex $(NAME).tex
	
zip:
	zip $(LOGIN)-fit.zip *.tex Makefile
	
clean:
	rm -f $(NAME).{aux,dvi,log,pdf,ps} $(LOGIN)-fit.zip