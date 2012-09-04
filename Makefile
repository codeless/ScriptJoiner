CURL=/usr/bin/curl
PHP=php
MKDIR=/bin/mkdir

install:
	@echo "Installing packages provided by composer:"
	$(CURL) -s http://getcomposer.org/installer | php
	$(PHP) composer.phar install
	@echo "Downloading the NaturalDocs2Markdown converter:"
	$(CURL) https://raw.github.com/codeless/nd2md/master/nd2md.sh > nd2md.sh
	chmod ugo+x nd2md.sh

doc:
	./nd2md.sh README.txt > README.md
	./nd2md.sh HISTORY.txt > HISTORY.md

update:
	$(PHP) composer.phar update

clean:
	rm -fr vendor/ composer.lock composer.phar tests/*_outfile.php nd2md.sh
