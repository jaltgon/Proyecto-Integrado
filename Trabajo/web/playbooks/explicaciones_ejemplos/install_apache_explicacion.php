<div class="detailed-explanation">
    <h3>Propósito del Playbook: Instalación y Configuración Básica de Apache (install_apache.yml)</h3>

    <p>
        Este playbook automatiza la instalación y el arranque del servicio Apache HTTP Server. Es un ejemplo clásico de cómo Ansible puede gestionar paquetes y servicios, siendo una habilidad fundamental para cualquier administrador de sistemas.
    </p>

    <h4>Desglose y Explicación del Playbook:</h4>

    <h5>1. Encabezado del Playbook y Definición General</h5>
    <pre class="yaml-fragment"><code>---
- name: Instalar y configurar Apache (Ubuntu/Debian)
  hosts: localhost
  become: true</code></pre>
    <p>
        Todo playbook comienza con la cabecera YAML. Aquí se define el nombre del "play", los hosts objetivo y que las tareas se ejecutarán con privilegios de superusuario (root).
    </p>
    <ul>
        <li><strong>name</strong>: Un nombre descriptivo para este play.</li>
        <li><strong>hosts</strong>: Define en qué servidores se ejecutarán las tareas. <i>localhost</i> significa que se ejecutará en la misma máquina donde corre Ansible.</li>
        <li><strong>become</strong>: <i>true</i> indica que Ansible ejecutará las tareas con permisos de superusuario, lo cual es necesario para instalar software y gestionar servicios del sistema.</li>
    </ul>

    <h5>2. Tarea: Actualizar caché de paquetes</h5>
    <pre class="yaml-fragment"><code>    - name: Actualizar caché de paquetes
      ansible.builtin.apt:
        update_cache: yes</code></pre>
    <p>
        Esta tarea asegura que la lista de paquetes disponibles en el sistema esté actualizada antes de intentar instalar Apache. Es una buena práctica para garantizar que se instale la versión más reciente y que se resuelvan correctamente las dependencias.
    </p>
    <ul>
        <li><strong>name</strong>: Nombre descriptivo de la tarea.</li>
        <li><strong><i>ansible.builtin.apt</i></strong>: El módulo de Ansible para gestionar paquetes en sistemas basados en Debian/Ubuntu (como el que se usa en este entorno Docker).</li>
        <li><strong>update_cache</strong>: <i>yes</i> es un parámetro que le dice al módulo <i>apt</i> que actualice la caché de paquetes, similar a ejecutar <code>sudo apt update</code>.</li>
    </ul>

    <h5>3. Tarea: Instalar Apache HTTP Server</h5>
    <pre class="yaml-fragment"><code>    - name: Instalar Apache HTTP Server
      ansible.builtin.apt:
        name: apache2
        state: present</code></pre>
    <p>
        Aquí se realiza la instalación del paquete de Apache. Ansible es idempotente, lo que significa que si Apache ya está instalado, Ansible no intentará instalarlo de nuevo.
    </p>
    <ul>
        <li><strong>name</strong>: El nombre del paquete a instalar (<i>apache2</i>).</li>
        <li><strong>state</strong>: <i>present</i> asegura que el paquete <i>apache2</i> esté presente (instalado) en el sistema.</li>
    </ul>

    <h5>4. Tarea: Iniciar y habilitar el servicio Apache</h5>
    <pre class="yaml-fragment"><code>    - name: Iniciar y habilitar el servicio Apache
      ansible.builtin.service:
        name: apache2
        state: started
        enabled: true</code></pre>
    <p>
        Una vez instalado Apache, esta tarea garantiza que el servicio esté en ejecución y que se inicie automáticamente cada vez que el servidor arranque.
    </p>
    <ul>
        <li><strong><i>ansible.builtin.service</i></strong>: Módulo de Ansible para gestionar servicios.</li>
        <li><strong>name</strong>: El nombre del servicio (<i>apache2</i>).</li>
        <li><strong>state</strong>: <i>started</i> asegura que el servicio esté actualmente activo y corriendo.</li>
        <li><strong>enabled</strong>: <i>true</i> configura el servicio para que se inicie automáticamente en el arranque del sistema.</li>
    </ul>

    <h3>Cómo Funciona y Salida Esperada:</h3>
    <p>
        Al ejecutar este playbook, Ansible primero actualizará la lista de paquetes, luego instalará Apache si no está presente y, finalmente, se asegurará de que el servicio esté en ejecución y configurado para iniciar automáticamente. La salida de Ansible te mostrará si cada tarea realizó un cambio (<code>changed=X</code>) o si el estado deseado ya existía (<code>ok=X</code>).
    </p>
    <p class="call-to-action">
        Puedes ver la <strong>salida exacta de Ansible</strong> al ejecutar este playbook desde la <a href="index.php">página principal</a>, usando el botón <strong>"Ejecutar"</strong>.
    </p>
</div>