CURL=/usr/bin/curl
PHP=/usr/bin/php
MKDIR=/bin/mkdir

install:
	@echo "Installing packages provided by composer:"
	$(CURL) -s http://getcomposer.org/installer | php
	$(PHP) composer.phar install

update:
	$(PHP) composer.phar update

clean:
	rm -fr vendor/ composer.lock composer.phar tests/*_outfile.php
