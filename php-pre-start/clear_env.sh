#/bin/sh

echo "clear_env = no" >> /etc/php-fpm.d/www.conf
echo "variables_order = "GPCS" >> /etc/php.ini
