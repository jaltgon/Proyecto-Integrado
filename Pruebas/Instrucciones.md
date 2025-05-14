#### Abrimos una terminal en la carpeta del proyecto y ejecutamos los siguientes comandos:
```markdown
docker-compose build
docker-compose up -d
docker exec -it ansible bash
```

#### Una vez dentro del contenedor podemos probar Andsible de la siguiente forma:
```markdown
ansible all -i inventory/hosts.ini -m ping
ansible-playbook -i inventory/hosts.ini playbooks/ejemplo.yml
```
