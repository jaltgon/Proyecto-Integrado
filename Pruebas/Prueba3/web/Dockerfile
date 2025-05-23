# ./web/Dockerfile
FROM php:8.2-apache

# Instala dependencias necesarias
RUN apt-get update && apt-get install -y \
    ansible \
    python3 \
    sshpass \
    sudo \
    && apt-get clean

# Crea carpeta temporal accesible
RUN mkdir -p /tmp/.ansible/tmp && chown -R www-data:www-data /tmp/.ansible

RUN echo "ansible ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers
RUN echo "www-data ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers


# Define variable para que Ansible use esa carpeta
ENV ANSIBLE_LOCAL_TEMP=/tmp/.ansible

# Habilita mod_rewrite
RUN a2enmod rewrite

# Copia inicial
COPY . /var/www/html