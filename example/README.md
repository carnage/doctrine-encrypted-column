composer install

vendor/bin/doctrine orm:schema-tool:create

php create.php
php read.php
php update.php
php read.php