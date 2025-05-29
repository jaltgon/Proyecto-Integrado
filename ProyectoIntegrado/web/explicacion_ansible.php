<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>¿Qué es un Playbook Ansible?</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>¿Qué es un Playbook Ansible?</h1>
        <p>
            Un Playbook Ansible es mucho más que un simple script. Es un archivo en formato YAML que actúa como un conjunto de instrucciones declarativas, describiendo el estado deseado de tus sistemas. Con un playbook, puedes automatizar desde la configuración de un solo servidor hasta el despliegue complejo de aplicaciones en una infraestructura entera.
        </p>
        <p>
            Son el corazón de la automatización con Ansible, permitiendo una gestión de la infraestructura que es <strong>reproducible, consistente y fácil de entender</strong> para cualquier miembro del equipo.
        </p>

        <h2>Anatomía de un Playbook: Componentes Clave</h2>
        <p>
            Aunque los playbooks pueden volverse muy complejos, su estructura fundamental es simple y se basa en unos pocos componentes clave:
        </p>

        <h3>1. Encabezado del Playbook (<i>---</i>)</h3>
        <p>
            Todos los archivos YAML de Ansible (incluidos los playbooks) comienzan con tres guiones (<i>---</i>). Esto es una convención YAML que indica el inicio de un documento.
        </p>
        <pre class="yaml-example"><code>---</code></pre>

        <h3>2. El "Play" Principal (Definición de Play)</h3>
        <p>
            Un playbook se compone de uno o más "plays". Cada play es una unidad lógica que define qué tareas se ejecutarán en qué grupo de servidores, y bajo qué condiciones.
        </p>
        <pre class="yaml-example"><code>- name: Nombre descriptivo del Play (ej: Configurar Servidor Web)
  hosts: nombre_del_grupo_o_host # Ej: webservers, localhost, all
  become: true                   # Opcional: Si las tareas requieren permisos de root (true/false)
  # vars:                        # Opcional: Variables específicas para este Play
  #   paquete_web: apache2</code></pre>
        <ul>
            <li><strong>name</strong>: Un nombre legible y descriptivo para este play. Es lo primero que verás cuando se ejecute el playbook.</li>
            <li><strong>hosts</strong>: Define <strong>dónde</strong> se aplicarán las tareas de este play. Puede ser:
                <ul>
                    <li>Un host específico (ej: <code>server1.example.com</code>)</li>
                    <li>Un grupo de hosts definido en tu inventario (ej: <code>webservers</code>, <code>databases</code>)</li>
                    <li>La palabra clave <i>all</i> para ejecutar en todos los hosts de tu inventario.</li>
                    <li><strong><i>localhost</i></strong> para ejecutar el playbook en la misma máquina donde se lanza Ansible (útil para pruebas o configuraciones locales).</li>
                </ul>
            </li>
            <li><strong>become</strong>: (Opcional) Si se establece en <i>true</i>, Ansible intentará escalar privilegios (usar <i>sudo</i>, <i>su</i>, etc.) para ejecutar las tareas con permisos de superusuario (root). Es fundamental para la mayoría de las tareas de administración de sistemas.</li>
            <li><strong>vars</strong>: (Opcional) Una sección para definir variables que serán usadas dentro de este play. Ayuda a hacer los playbooks más dinámicos y reutilizables.</li>
        </ul>

        <h3>3. Tareas (<i>tasks</i>)</h3>
        <p>
            La sección <i>tasks</i> es el corazón del playbook. Es una lista de las acciones específicas que Ansible realizará en los hosts definidos en el play. Cada tarea llama a un "módulo" de Ansible y le pasa parámetros.
        </p>
        <pre class="yaml-example"><code>  tasks: # Lista de tareas a ejecutar
    - name: Nombre de la tarea (ej: Instalar Nginx)
      ansible.builtin.modulo: # Módulo de Ansible a usar (ej: apt, yum, copy, service)
        parametro1: valor1
        parametro2: valor2

    - name: Otra tarea (ej: Asegurar que el servicio está activo)
      ansible.builtin.modulo:
        # ... otros parámetros</code></pre>
        <ul>
            <li><strong>name</strong>: Un nombre descriptivo para cada tarea individual. Ansible lo mostrará en la salida durante la ejecución, lo que facilita el seguimiento del progreso.</li>
            <li><strong>Módulo de Ansible</strong>: Cada tarea invoca un módulo específico de Ansible. Los módulos son las unidades de trabajo que realizan acciones reales en los sistemas remotos (ej: instalar paquetes, copiar archivos, gestionar servicios, etc.). Los módulos se organizan por colecciones, siendo <strong><i>ansible.builtin</i></strong> la colección estándar de módulos integrada.</li>
            <li><strong>Parámetros del Módulo</strong>: Debajo del nombre del módulo, se especifican los parámetros que controlan su comportamiento. Estos varían mucho según el módulo.</li>
        </ul>

        <h3>4. Ejemplo de Tarea con Módulo y Parámetros: Módulo <i>copy</i></h3>
        <p>
            Para ilustrar cómo funcionan los módulos y sus parámetros, veamos un ejemplo común: copiar un archivo desde la máquina donde ejecutas Ansible (controlador) a un servidor remoto.
        </p>
        <pre class="yaml-example"><code>    - name: Copiar archivo de configuración
      ansible.builtin.copy:
        src: /ruta/en/mi/maquina/mi_config.conf
        dest: /etc/nginx/conf.d/mi_config.conf
        owner: root
        group: root
        mode: '0644'</code></pre>
        <ul>
            <li><strong><i>ansible.builtin.copy</i></strong>: El módulo encargado de copiar archivos.</li>
            <li><strong><i>src</i></strong>: El parámetro <i>src</i> (source) especifica la <strong>ruta del archivo en tu máquina local</strong> (la máquina donde estás ejecutando Ansible).</li>
            <li><strong><i>dest</i></strong>: El parámetro <i>dest</i> (destination) especifica la <strong>ruta completa donde se copiará el archivo en el servidor remoto</strong>.</li>
            <li><strong><i>owner</i></strong>: (Opcional) Define el usuario propietario del archivo copiado en el destino.</li>
            <li><strong><i>group</i></strong>: (Opcional) Define el grupo propietario del archivo copiado en el destino.</li>
            <li><strong><i>mode</i></strong>: (Opcional) Establece los permisos del archivo en el destino (en formato octal, <code>0644</code> significa lectura/escritura para el propietario, solo lectura para grupo y otros).</li>
        </ul>

        <p>
            Comprender estos componentes te dará una base sólida para empezar a construir y modificar tus propios playbooks de Ansible. Explora los ejemplos de demostración para ver estas ideas en acción y observa cómo se ve la salida de Ansible al ejecutarlos.
        </p>

        <a href="index.php" class="button">Volver al inicio</a>
    </div>
</body>
</html>