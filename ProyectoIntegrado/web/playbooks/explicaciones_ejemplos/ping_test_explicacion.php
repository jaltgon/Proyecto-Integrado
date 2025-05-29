<div class="detailed-explanation">
    <h3>Propósito del Playbook: Prueba de Conectividad (ping_test.yml)</h3>

    <p>
        Este playbook es el "Hola Mundo" de Ansible. Su objetivo principal es verificar si Ansible puede comunicarse exitosamente con los hosts definidos en el inventario. Utiliza un módulo muy simple para enviar una "señal" a los servidores y esperar una respuesta.
    </p>

    <h4>Desglose y Explicación del Playbook:</h4>

    <h5>1. Encabezado del Playbook y Definición General</h5>
    <pre class="yaml-fragment"><code>---
- name: Prueba de conectividad con ping
  hosts: localhost
  become: false</code></pre>
    <p>
        Todo playbook comienza con tres guiones (<i>---</i>) para indicar que es un archivo YAML. Luego, se define el primer "play" (un conjunto de tareas a ejecutar en un grupo de hosts).
    </p>
    <ul>
        <li><strong>name</strong>: Un nombre descriptivo para este play. Ayuda a entender qué hace el play cuando se ejecuta.</li>
        <li><strong>hosts</strong>: Define en qué servidores se ejecutarán las tareas de este play. Aquí, <i>localhost</i> significa que se ejecutará en la misma máquina donde se ejecuta Ansible.</li>
        <li><strong>become</strong>: Indica si las tareas necesitan privilegios de superusuario (root). <i>false</i> significa que no se necesitan permisos elevados para esta prueba de ping.</li>
    </ul>

    <h5>2. Definición de Tareas</h5>
    <pre class="yaml-fragment"><code>  tasks:
    - name: Verificar si los hosts están accesibles
      ansible.builtin.ping:</code></pre>
    <p>
        La sección <i>tasks</i> contiene la lista de acciones específicas que Ansible realizará en los hosts. Un playbook puede tener una o varias tareas.
    </p>
    <ul>
        <li><strong>name</strong>: Un nombre descriptivo para esta tarea individual. Se mostrará en la salida de Ansible.</li>
        <li><strong><i>ansible.builtin.ping</i></strong>: Este es el módulo de Ansible que se utiliza. El módulo <i>ping</i> es el más básico para probar la conectividad; si responde "pong", significa que la conexión es exitosa. No requiere parámetros adicionales.</li>
    </ul>

    <h3>Cómo Funciona y Salida Esperada:</h3>
    <p>
        Al ejecutar este playbook, Ansible intentará conectarse a <i>localhost</i> y ejecutar el módulo <i>ping</i>. Si todo va bien, verás una línea en la salida que indica <code>"pong"</code> para el host. Si hay algún problema (ej. SSH no configurado, firewall), verás un mensaje de error <code>"FAILED!"</code>.
    </p>
    <p class="call-to-action">
        Puedes ver la <strong>salida exacta de Ansible</strong> al ejecutar este playbook desde la <a href="index.php">página principal</a>, usando el botón <strong>"Ejecutar"</strong>.
    </p>
</div>