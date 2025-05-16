## (Prueba1)

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
## (Prueba2)

### Vamos a instalar manualmente el comando curl para poder hacer la comprobación:
```markdown
sudo apt update && sudo apt install curl -y
```
### El comando para utilizar curl y saber si funciona apache es:
```markdown
curl localhost
```
A mi me ha dado un error ya que apache se ha instalado pero no se ha iniciado asique para solucionarlo simplemente habria que poner
```markdown
sudo service apache2 start
```
y volveriamos a probar con el comando 'curl localhost' y nos deberia dar el mensaje de: '¡Apache instalado con Ansible!'
