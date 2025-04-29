#!/bin/bash



brew install php
curl -sS https://getcomposer.org/installer | php
brew install mysql
brew services start mysql
sudo mv composer.phar /usr/local/bin/composer
composer --version
