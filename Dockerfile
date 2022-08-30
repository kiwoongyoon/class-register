FROM php:8.1.8-apache
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite
# Add rewrite to make api requests go to apirequestcontroller  