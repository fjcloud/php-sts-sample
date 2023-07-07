#/bin/sh

echo 'clear_env = no' >> /etc/php-fpm.d/www.conf
sed -i 's/EGPCS/GPCS/g' /etc/php.ini
