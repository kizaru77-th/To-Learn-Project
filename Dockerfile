FROM php:8.2-apache

# เปิดใช้งาน mod_rewrite สำหรับ Apache
RUN a2enmod rewrite

# ติดตั้ง extension เพิ่มเติมสำหรับเชื่อมต่อ MySQL (mysqli / pdo_mysql)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# ก๊อปปี้ไฟล์งานทั้งหมดเข้า container
COPY . /var/www/html/

EXPOSE 80
