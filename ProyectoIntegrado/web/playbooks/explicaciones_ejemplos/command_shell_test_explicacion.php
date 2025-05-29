<div class="detailed-explanation">
        <h3>Propósito de los Módulos `ansible.builtin.command` y `ansible.builtin.shell` en Ansible (`command_shell_test.yml`)</h3>

        <p>
            Los módulos <strong><code>ansible.builtin.command</code></strong> y <strong><code>ansible.builtin.shell</code></strong> son la base para ejecutar comandos y scripts arbitrarios en tus hosts remotos. Son herramientas fundamentales cuando no existe un módulo específico de Ansible para una tarea, o cuando necesitas ejecutar un comando que ya conoces de la línea de comandos de Linux.
        </p>
        <p>
            Aunque ambos ejecutan comandos, su diferencia es crucial: <strong><code>command</code></strong> es más seguro y no invoca un shell, mientras que <strong><code>shell</code></strong> sí lo hace, permitiendo operaciones más complejas.
        </p>

        <h4>Desglose y Explicación del Playbook `command_shell_test.yml`:</h4>
        <p>Este playbook demuestra las capacidades y, más importante, las diferencias entre el módulo <code>command</code> y el módulo <code>shell</code>, mostrando cuándo usar cada uno.</p>

        <h5>1. Encabezado del Playbook y Definición General</h5>
        <pre class="yaml-fragment"><code>---
- name: Demostracion de los modulos command y shell
  hosts: all
  become: true</code></pre>
        <ul>
            <li><strong>name</strong>: Un nombre descriptivo para este play.</li>
            <li><strong>hosts</strong>: Indica que este playbook se ejecutará en todos los hosts definidos en tu inventario.</li>
            <li><strong>become</strong>: <i>true</i> significa que Ansible ejecutará las tareas con permisos de superusuario (<code>root</code>), lo cual es necesario para escribir en directorios del sistema como `/tmp/`.</li>
        </ul>

        <h5>2. Tarea: Ejecutar un comando simple con 'command' (no usa shell)</h5>
        <pre class="yaml-fragment"><code>    - name: 1. Ejecutar un comando simple con 'command' (no usa shell)
      ansible.builtin.command: date
      register: command_output</code></pre>
        <p>
            Esta tarea ejecuta un comando sencillo (<code>date</code>) en el host remoto. El módulo <strong><code>ansible.builtin.command</code></strong> es adecuado para comandos directos que no requieren ninguna función de shell (como tuberías <code>|</code>, redirecciones <code>></code>, variables de entorno complejas, etc.). Es más seguro porque previene inyecciones de shell accidentales.
        </p>
        <ul>
            <li><strong>name</strong>: Nombre descriptivo de la tarea.</li>
            <li><strong>ansible.builtin.command</strong>: El módulo para ejecutar el comando.</li>
            <li><strong>date</strong>: El comando a ejecutar.</li>
            <li><strong>register: command_output</strong>: Almacena la salida estándar (stdout), la salida de error estándar (stderr) y el código de retorno del comando en una variable llamada <code>command_output</code>.</li>
        </ul>

        <h5>3. Tarea: Mostrar la salida del comando simple</h5>
        <pre class="yaml-fragment"><code>    - name: 2. Mostrar la salida del comando simple
      ansible.builtin.debug:
        msg: "Salida de 'command date': {{ command_output.stdout }}"</code></pre>
        <p>
            Esta tarea utiliza el módulo <strong><code>ansible.builtin.debug</code></strong> para imprimir la salida del comando <code>date</code> que se ejecutó en la tarea anterior.
        </p>
        <ul>
            <li><strong>ansible.builtin.debug</strong>: Módulo para imprimir mensajes de depuración.</li>
            <li><strong>msg</strong>: El mensaje a imprimir. <code>{{ command_output.stdout }}</code> accede a la salida estándar del comando registrado.</li>
        </ul>

        <h5>4. Tarea: Intentar un comando complejo con 'command' (fallará si tiene redirección)</h5>
        <pre class="yaml-fragment"><code>    - name: 3. Intentar un comando complejo con 'command' (fallara si tiene redireccion)
      ansible.builtin.command: echo "Hola desde command" > /tmp/command_salida.txt
      register: command_complex_output
      ignore_errors: true</code></pre>
        <p>
            Esta tarea está diseñada para <strong>demostrar una limitación importante del módulo <code>command</code></strong>. Intenta usar una redirección de salida (<code>></code>), que es una función del shell. Dado que <code>command</code> no invoca un shell, esta tarea <strong>fallará</strong>.
        </p>
        <ul>
            <li><strong>echo "Hola desde command" > /tmp/command_salida.txt</strong>: El comando que incluye una redirección.</li>
            <li><strong>ignore_errors: true</strong>: Se añade este parámetro para que el playbook continúe ejecutándose incluso si esta tarea falla, permitiendo que las siguientes tareas demuestren el módulo <code>shell</code>.</li>
        </ul>

        <h5>5. Tarea: Ejecutar un comando complejo con 'shell' (sí usa shell)</h5>
        <pre class="yaml-fragment"><code>    - name: 4. Ejecutar un comando complejo con 'shell' (si usa shell)
      ansible.builtin.shell: echo "Hola desde shell" > /tmp/shell_salida.txt
      register: shell_output</code></pre>
        <p>
            Aquí, el módulo <strong><code>ansible.builtin.shell</code></strong> se utiliza para ejecutar el mismo tipo de comando complejo. A diferencia de <code>command</code>, <code>shell</code> sí invoca un shell en el host remoto, lo que le permite manejar características como redirecciones de E/S, tuberías, variables de entorno y operadores lógicos.
        </p>
        <ul>
            <li><strong>ansible.builtin.shell</strong>: El módulo para ejecutar el comando que requiere un shell.</li>
            <li><strong>echo "Hola desde shell" > /tmp/shell_salida.txt</strong>: El comando que incluye una redirección de salida, que se ejecutará correctamente esta vez.</li>
            <li><strong>register: shell_output</strong>: Almacena la salida de esta ejecución.</li>
        </ul>

        <h5>6. Tarea: Mostrar la salida de 'shell' y verificar el archivo</h5>
        <pre class="yaml-fragment"><code>    - name: 5. Mostrar la salida de 'shell' y verificar el archivo
      ansible.builtin.debug:
        msg: "Contenido de /tmp/shell_salida.txt: {{ lookup('file', '/tmp/shell_salida.txt') }}"
      delegate_to: localhost</code></pre>
        <p>
            Esta tarea utiliza <strong><code>ansible.builtin.debug</code></strong> para verificar el contenido del archivo creado por la tarea anterior.
        </p>
        <ul>
            <li><strong>lookup('file', '/tmp/shell_salida.txt')</strong>: Es un "lookup plugin" de Ansible que lee el contenido de un archivo. <strong>Importante:</strong> por defecto, <code>lookup('file')</code> intenta leer el archivo en la máquina de control (donde se ejecuta Ansible), no en el host remoto.</li>
            <li><strong>delegate_to: localhost</strong>: Este parámetro es crucial aquí. Asegura que la operación de <code>lookup('file', ...)</code> se intente realizar en el propio controlador de Ansible (tu máquina local), y no en el host remoto. Si el archivo <code>/tmp/shell_salida.txt</code> no se sincroniza de vuelta al controlador, este lookup fallará. Para verificar directamente en el host remoto, se usaría un <code>ansible.builtin.command: cat /tmp/shell_salida.txt</code>. Este ejemplo lo usa para ilustrar una posible forma de leer contenido local en el controlador.</li>
        </ul>

        <h5>7. Tareas: Comprobar y Mostrar Permisos del archivo creado por 'shell'</h5>
        <pre class="yaml-fragment"><code>    - name: 6. Comprobar permisos del archivo creado por 'shell'
      ansible.builtin.command: ls -l /tmp/shell_salida.txt
      register: shell_file_perms

    - name: 7. Mostrar permisos del archivo creado por 'shell'
      ansible.builtin.debug:
        msg: "Permisos de /tmp/shell_salida.txt: {{ shell_file_perms.stdout_lines[0] }}"</code></pre>
        <p>
            Estas tareas utilizan el módulo <strong><code>ansible.builtin.command</code></strong> para obtener los permisos y la información del archivo creado por el módulo <code>shell</code>, y luego imprimen esa información.
        </p>
        <ul>
            <li><strong>ls -l /tmp/shell_salida.txt</strong>: Comando para listar los detalles del archivo.</li>
            <li><strong>register: shell_file_perms</strong>: Guarda la salida del comando.</li>
            <li><strong>{{ shell_file_perms.stdout_lines[0] }}</strong>: Accede a la primera línea de la salida del comando <code>ls -l</code>, que contiene los permisos y otros detalles del archivo.</li>
        </ul>

        <h5>8. Tarea: Limpiar archivos de prueba</h5>
        <pre class="yaml-fragment"><code>    - name: 8. Limpiar archivos de prueba
      ansible.builtin.file:
        path: "{{ item }}"
        state: absent
      loop:
        - /tmp/command_salida.txt
        - /tmp/shell_salida.txt</code></pre>
        <p>
            Finalmente, esta tarea utiliza el módulo <strong><code>ansible.builtin.file</code></strong> para limpiar los archivos temporales que se crearon durante la ejecución del playbook, asegurando que el entorno quede limpio.
        </p>
        <ul>
            <li><strong>path: "{{ item }}"</strong>: Utiliza la variable <code>item</code> que proviene del bucle.</li>
            <li><strong>state: absent</strong>: Asegura que los archivos especificados sean eliminados si existen.</li>
            <li><strong>loop</strong>: Define una lista de elementos sobre los cuales la tarea se ejecutará de forma repetida. En este caso, eliminará cada archivo de la lista.</li>
        </ul>

        <h3>Conceptos Clave y Consideraciones:</h3>
        <ul>
            <li><strong>`command` vs `shell`</strong>: La diferencia principal es que <code>shell</code> invoca un shell y <code>command</code> no. Usa <code>command</code> para comandos simples por seguridad y <code>shell</code> solo cuando sea estrictamente necesario para funcionalidades de shell (redirecciones, tuberías, variables de entorno como <code>$HOME</code>, etc.).</li>
            <li><strong>`ignore_errors: true`</strong>: Útil para la depuración o para permitir que un playbook continúe incluso si una tarea específica no es crítica para el éxito general. Sin embargo, úsalo con precaución, ya que puede enmascarar problemas.</li>
            <li><strong>Idempotencia</strong>: Aunque <code>command</code> y <code>shell</code> no son inherentemente idempotentes (ejecutan el comando cada vez), Ansible intentará detectar si el comando ha cambiado algo para marcar la tarea como "changed". Para operaciones que deben ser idempotentes, siempre es preferible usar un módulo específico de Ansible (como <code>ansible.builtin.file</code> para crear archivos) en lugar de <code>shell</code> o <code>command</code>.</li>
        </ul>
        <p class="call-to-action">
            Puedes ejecutar este playbook para observar el comportamiento de <code>command</code> y <code>shell</code> desde la <a href="index.php">página principal</a>.
        </p>
    </div>