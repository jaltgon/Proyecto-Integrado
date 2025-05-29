<div class="detailed-explanation">
        <h3>Propósito del Módulo `ansible.builtin.template` y Ejemplo Práctico (`template_test.yml`)</h3>

        <p>
            El módulo <strong><code>ansible.builtin.template</code></strong> es una de las características más poderosas de Ansible para la gestión de configuraciones. Te permite copiar un archivo de plantilla (escrito en Jinja2) desde el controlador de Ansible a tus hosts remotos, pero con una diferencia clave: antes de copiarlo, procesa la plantilla, reemplazando variables y ejecutando lógica condicional. Esto permite generar archivos de configuración dinámicos y personalizados para cada servidor, adaptándose a sus características o rol.
        </p>

        <h4>Desglose y Explicación del Playbook `template_test.yml`:</h4>
        <p>Este playbook demuestra cómo utilizar el módulo <code>template</code> para crear un archivo de "mensaje del día" (MOTD) personalizado en cada host remoto, utilizando datos específicos de cada servidor.</p>

        <h5>1. Encabezado del Playbook y Definición General</h5>
        <pre class="yaml-fragment"><code>---
- name: Demostracion del modulo template
  hosts: all
  become: true</code></pre>
        <ul>
            <li><strong>name</strong>: Un nombre descriptivo para este play.</li>
            <li><strong>hosts</strong>: Indica que este playbook se ejecutará en todos los hosts definidos en tu inventario.</li>
            <li><strong>become</strong>: <i>true</i> significa que Ansible ejecutará las tareas con permisos de superusuario (<code>root</code>), lo cual es necesario para escribir en directorios del sistema como `/etc/`.</li>
        </ul>

        <h5>2. Tarea: Generar /etc/motd a partir de una plantilla</h5>
        <pre class="yaml-fragment"><code>    - name: 1. Generar /etc/motd a partir de una plantilla
      ansible.builtin.template:
        src: motd.j2
        dest: /etc/motd
        owner: root
        group: root
        mode: '0644'</code></pre>
        <p>
            Esta es la tarea principal donde usamos el módulo <strong><code>ansible.builtin.template</code></strong>. Se encarga de tomar el archivo de plantilla <code>motd.j2</code>, procesarlo y copiar el resultado a <code>/etc/motd</code> en el host remoto.
        </p>
        <ul>
            <li><strong>src</strong>: La ruta al archivo de plantilla (<code>.j2</code>) en tu máquina de control Ansible. Por convención, las plantillas se colocan en un subdirectorio llamado <code>templates/</code> al lado de tu playbook.</li>
            <li><strong>dest</strong>: La ruta absoluta en el host remoto donde se guardará el archivo generado (<code>/etc/motd</code> es el "mensaje del día" que se muestra al iniciar sesión en un servidor Linux).</li>
            <li><strong>owner</strong> y <strong>group</strong>: Establecen el propietario y grupo del archivo generado a <code>root</code>.</li>
            <li><strong>mode</strong>: Define los permisos del archivo a <code>0644</code> (lectura/escritura para el propietario, solo lectura para el grupo y otros).</li>
        </ul>

        <h5>3. Tarea: Mostrar el contenido del nuevo /etc/motd</h5>
        <pre class="yaml-fragment"><code>    - name: 2. Mostrar el contenido del nuevo /etc/motd
      ansible.builtin.command: cat /etc/motd
      register: motd_content
      changed_when: false</code></pre>
        <p>
            Esta tarea utiliza el módulo <strong><code>ansible.builtin.command</code></strong> para leer el contenido del archivo <code>/etc/motd</code> que se acaba de generar en el host remoto.
        </p>
        <ul>
            <li><strong>cat /etc/motd</strong>: El comando que lee el contenido del archivo.</li>
            <li><strong>register: motd_content</strong>: Almacena la salida de este comando en una variable llamada <code>motd_content</code>.</li>
            <li><strong>changed_when: false</strong>: Indica a Ansible que, aunque el comando se ejecute, esta tarea no debe marcarse como "changed" (amarillo) en la salida del playbook, ya que solo está leyendo información, no modificando el estado del sistema.</li>
        </ul>

        <h5>4. Tarea: Imprimir el contenido de /etc/motd</h5>
        <pre class="yaml-fragment"><code>    - name: 3. Imprimir el contenido de /etc/motd
      ansible.builtin.debug:
        msg: "Contenido de /etc/motd:\n{{ motd_content.stdout }}"</code></pre>
        <p>
            Esta tarea utiliza el módulo <strong><code>ansible.builtin.debug</code></strong> para mostrar el contenido del archivo <code>/etc/motd</code> en la salida estándar de la ejecución de Ansible. Esto es útil para verificar que la plantilla se ha procesado correctamente.
        </p>
        <ul>
            <li><strong>msg</strong>: El mensaje a imprimir. <code>{{ motd_content.stdout }}</code> accede a la salida estándar del comando <code>cat</code> que se ejecutó en la tarea anterior. El <code>\n</code> añade un salto de línea para una mejor presentación.</li>
        </ul>

        <h3>Conceptos Clave del Módulo `template`:</h3>
        <ul>
            <li><strong>Jinja2</strong>: Es el lenguaje de plantillas que Ansible usa. Permite insertar variables (<code>{{ variable_name }}</code>), ejecutar bucles (<code>{% for item in list %}...{% endfor %}</code>) y usar condiciones (<code>{% if condition %}...{% endif %}</code>) dentro de tus plantillas.</li>
            <li><strong>Factores de Ansible</strong>: Son variables que Ansible recopila automáticamente sobre cada host remoto (ej. <code>ansible_hostname</code>, <code>ansible_distribution</code>, <code>ansible_date_time</code>). Son extremadamente útiles para crear plantillas dinámicas que se adapten a las características específicas de cada servidor.</li>
            <li><strong>Idempotencia</strong>: El módulo <code>template</code> es idempotente. Solo reemplazará el archivo de destino si el contenido generado a partir de la plantilla (y las variables) ha cambiado, o si los permisos/propietarios son diferentes a los deseados.</li>
            <li><strong>Directorio `templates/`</strong>: Por convención, es el lugar recomendado para guardar tus archivos <code>.j2</code>. Ansible los buscará allí por defecto cuando especifiques solo el nombre del archivo en <code>src</code>.</li>
        </ul>
        <p class="call-to-action">
            Una vez que hayas creado la carpeta <code>templates/</code> y el archivo <code>motd.j2</code>, el playbook <strong><code>template_test.yml</code></strong> estará listo para ser ejecutado desde la <a href="index.php">página principal</a>.
        </p>
    </div>