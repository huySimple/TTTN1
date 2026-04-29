# Dùng image PHP + Apache
FROM php:8.3.14-apache

# Copy toàn bộ source vào thư mục web
COPY . /var/www/html/

# Set quyền (tránh lỗi permission)
RUN chown -R www-data:www-data /var/www/html

# Enable mod_rewrite (nếu cần)
RUN a2enmod rewrite

RUN docker-php-ext-install mysqli pdo pdo_mysql

# Mở port 80
EXPOSE 80