<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>¿Qué es el Inventario Ansible?</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>¿Qué es el Inventario de Ansible (<code>hosts.ini</code>)?</h1>
        <p>
            El <strong>inventario</strong> es un archivo fundamental en Ansible que define los hosts (servidores, máquinas virtuales, dispositivos de red, etc.) con los que Ansible se va a comunicar y sobre los que va a ejecutar los playbooks. Por defecto, Ansible busca un archivo llamado <code>hosts.ini</code> en la carpeta <code>inventory/</code> de tu proyecto o en la configuración global de Ansible.
        </p>

        <h3>Propósito del Inventario:</h3>
        <ul>
            <li><strong>Definir hosts:</strong> Lista las direcciones IP o nombres de host de tus servidores.</li>
            <li><strong>Agrupar hosts:</strong> Permite organizar tus servidores en grupos lógicos (ej: <code>webservers</code>, <code>databases</code>, <code>development</code>, <code>production</code>). Esto es muy útil para ejecutar tareas en conjuntos específicos de máquinas.</li>
            <li><strong>Variables específicas:</strong> Puedes definir variables para hosts individuales o para grupos enteros directamente en el inventario.</li>
            <li><strong>Escalabilidad:</strong> Facilita la gestión de grandes infraestructuras, ya que puedes referirte a grupos en lugar de a hosts individuales en tus playbooks.</li>
        </ul>

        <h3>Estructura Básica del Archivo <code>hosts.ini</code>:</h3>
        <p>
            El formato más común para el inventario es el formato INI, que utiliza secciones para definir grupos y listas de hosts.
        </p>
        <pre class="yaml-example"><code># Este es un comentario en el inventario
[grupo_de_ejemplo]       # Define un grupo de hosts
host1.example.com
host2.example.com

[otro_grupo]
192.168.1.100

[servidores_web]
web1.midominio.com
web2.midominio.com

[servidores_web:vars]    # Variables específicas para el grupo 'servidores_web'
ansible_user=miusuario   # Con qué usuario SSH se conectará Ansible
ansible_port=22          # Puerto SSH (si no es el 22 por defecto)
http_port=80             # Una variable personalizada

[grupo_con_variables]
host3.example.com ansible_port=2222 custom_var="Valor personalizado"

[all:vars]               # Variables que aplican a TODOS los hosts en el inventario
ansible_python_interpreter=/usr/bin/python3
</code></pre>
        <ul>
            <li>Las líneas que empiezan con <code>#</code> son comentarios.</li>
            <li>Los nombres de grupo se definen entre corchetes (ej: <code>[webservers]</code>).</li>
            <li>Debajo de cada grupo, lista los hosts, uno por línea. Pueden ser nombres de host o direcciones IP.</li>
            <li>Puedes añadir variables a un grupo usando <code>[nombre_del_grupo:vars]</code>.</li>
            <li>Variables que aplican a todos los hosts se definen en <code>[all:vars]</code>.</li>
            <li>Las variables específicas de un host se añaden en la misma línea del host.</li>
        </ul>

        <h3>Ejemplo de tu Inventario Predeterminado:</h3>
        <p>
            Al iniciar, el contenido de tu inventario predeterminado suele verse similar a esto:
        </p>
        <pre class="yaml-example"><code>[local]
localhost ansible_connection=local

[webservers]
server1.example.com
server2.example.com

[databases]
dbserver1.example.com</code></pre>
        <ul>
            <li>El grupo <code>[local]</code> se utiliza para ejecutar playbooks directamente en la máquina donde está Ansible, sin necesidad de SSH.</li>
            <li>Las entradas como <code>ansible_connection=local</code> son variables específicas del host o grupo que le dicen a Ansible cómo conectarse (en este caso, localmente).</li>
        </ul>

        <a href="index.php" class="button">Volver al inicio</a>
    </div>
</body>
</html>