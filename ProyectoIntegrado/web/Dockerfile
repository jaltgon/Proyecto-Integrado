# ./web/Dockerfile
FROM php:8.2-apache

# Instala dependencias necesarias
RUN apt-get update && apt-get install -y \
    ansible \
    python3 \
    sshpass \
    sudo \
    && apt-get clean

# Dar permisos NOPASSWD a www-data
RUN echo "www-data ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

# Habilita mod_rewrite
RUN a2enmod rewrite

RUN usermod -d /tmp www-data

# --- Configuraciones y permisos para Ansible ---

# Copia el ansible.cfg a una ubicación estándar y segura
COPY ansible.cfg /etc/ansible/ansible.cfg
# Establece la variable de entorno para que Ansible use esta configuración
ENV ANSIBLE_CONFIG=/etc/ansible/ansible.cfg

# Crea y da permisos a los directorios temporales y de caché que Ansible usará
# ANSIBLE_LOCAL_TMP se usa para los archivos temporales en la máquina de control (aquí, el propio contenedor)
# ANSIBLE_CACHE_DIR es donde Ansible guarda los datos de caché
RUN mkdir -p /tmp/.ansible_tmp \
           /tmp/.ansible_cache \
    && chown -R www-data:www-data /tmp/.ansible_tmp \
    && chown -R www-data:www-data /tmp/.ansible_cache \
    && chmod -R 755 /tmp/.ansible_tmp \
    && chmod -R 755 /tmp/.ansible_cache

# Configura las variables de entorno para Ansible
ENV ANSIBLE_LOCAL_TEMP=/tmp/.ansible_tmp
ENV ANSIBLE_CACHE_DIR=/tmp/.ansible_cache

# --- Copiar la aplicación y establecer permisos ---

# Copia todo tu código de la aplicación
COPY . /var/www/html

# Establece los permisos correctos para todo /var/www/html
# Esto evita la advertencia de "world writable" y asegura la seguridad
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} + && \
    find /var/www/html -type f -exec chmod 644 {} +

# Asegúrate de que la carpeta 'playbooks' dentro de /var/www/html sea escribible por www-data
# Esto es crucial porque tu aplicación PHP genera archivos aquí.
RUN chmod -R 775 /var/www/html/playbooks

# Luego, asegura que la subcarpeta 'playbooks/ejemplos' sea de solo lectura para www-data
# Esto es crucial para que no se puedan borrar ni modificar desde la web
RUN chmod -R 555 /var/www/html/playbooks/ejemplos