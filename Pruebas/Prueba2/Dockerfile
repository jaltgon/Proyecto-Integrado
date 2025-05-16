# Dockerfile
FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt update && \
    apt install -y software-properties-common && \
    apt-add-repository --yes --update ppa:ansible/ansible && \
    apt install -y ansible ssh sudo && \
    apt clean

# Crea un usuario no root opcional
RUN useradd -ms /bin/bash ansible && echo 'ansible ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers
USER ansible
WORKDIR /home/ansible

CMD ["bash"]
