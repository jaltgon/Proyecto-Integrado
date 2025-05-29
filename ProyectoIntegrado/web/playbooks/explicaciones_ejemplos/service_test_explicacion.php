 <div class="detailed-explanation">
        <h3>Propósito del Módulo `ansible.builtin.service` y Ejemplo Práctico (`service_test.yml`)</h3>

        <p>
            El módulo <strong><code>ansible.builtin.service</code></strong> es una herramienta fundamental en Ansible para gestionar el estado de los servicios del sistema operativo en tus hosts remotos. Te permite asegurar que un servicio esté iniciado, parado, reiniciado o recargado, y también configurarlo para que se habilite o deshabilite automáticamente al arrancar el sistema.
        </p>

        <h4>Desglose y Explicación del Playbook `service_test.yml`:</h4>
        <p>Este playbook demuestra cómo asegurar que un servicio (en este caso, Nginx) esté instalado y luego cómo gestionar su estado (iniciar, reiniciar, parar y deshabilitar) en los hosts remotos.</p>

        <h5>1. Encabezado del Playbook y Definición General</h5>
        <pre class="yaml-fragment"><code>---
- name: Gestionar servicios con el módulo service
  hosts: all
  become: true</code></pre>
        <ul>
            <li><strong>name</strong>: Un nombre descriptivo para este play.</li>
            <li><strong>hosts</strong>: Indica que este playbook se ejecutará en todos los hosts definidos en tu inventario.</li>
            <li><strong>become</strong>: <i>true</i> significa que Ansible ejecutará las tareas con permisos de superusuario (<code>root</code>), lo cual es indispensable para instalar paquetes y gestionar servicios del sistema.</li>
        </ul>

        <h5>2. Tareas: Instalar Nginx si no está presente (apt y yum/dnf)</h5>
        <pre class="yaml-fragment"><code>    - name: 1. Instalar Nginx si no esta presente (Ubuntu/Debian)
      ansible.builtin.apt:
        name: nginx
        state: present
      when: ansible_os_family == "Debian"

    - name: 2. Instalar Nginx si no esta presente (RedHat/CentOS/Fedora)
      ansible.builtin.yum:
        name: nginx
        state: present
      when: ansible_os_family == "RedHat"</code></pre>
        <p>
            Antes de gestionar un servicio, generalmente necesitas asegurarte de que el paquete que lo proporciona esté instalado. Ansible ofrece módulos específicos para cada tipo de sistema de gestión de paquetes:
        </p>
        <ul>
            <li><strong><code>ansible.builtin.apt</code></strong>: Se utiliza para sistemas basados en Debian/Ubuntu (como Debian, Ubuntu, Mint).</li>
            <li><strong><code>ansible.builtin.yum</code></strong> (o <strong><code>ansible.builtin.dnf</code></strong> para sistemas más modernos): Se utiliza para sistemas basados en Red Hat (como CentOS, Fedora, RHEL).</li>
            <li>Ambos módulos usan <strong><code>name: nginx</code></strong> para especificar el paquete y <strong><code>state: present</code></strong> para asegurar que el paquete esté instalado.</li>
            <li>La condición <strong><code>when: ansible_os_family == "Debian"</code></strong> o <strong><code>when: ansible_os_family == "RedHat"</code></strong> asegura que solo se ejecute la tarea correspondiente al sistema operativo del host remoto.</li>
        </ul>

        <p>
            <strong>Nota Importante sobre Conflictos de Puertos:</strong>
            Al instalar y configurar servicios web como Nginx o Apache, es crucial asegurarse de que no haya otro servicio ya utilizando el puerto estándar (como el puerto 80 para HTTP). Si el playbook falla al intentar iniciar Nginx con un mensaje como "Starting nginx: nginx failed!", es muy probable que otro servidor web (como Apache2, si lo has instalado previamente) esté ocupando el puerto 80. Para que Nginx pueda iniciarse, deberías detener el servicio que está ocupando el puerto 80 (por ejemplo, <code>sudo systemctl stop apache2</code> en tu host remoto) antes de ejecutar este playbook.
        </p>

        <h5>3. Tarea: Asegurar que el servicio Nginx esté iniciado y habilitado al arranque</h5>
        <pre class="yaml-fragment"><code>    - name: 3. Asegurar que el servicio Nginx este iniciado y habilitado al arranque
      ansible.builtin.service:
        name: nginx
        state: started
        enabled: true</code></pre>
        <p>
            Esta es la tarea principal donde usamos el módulo <strong><code>ansible.builtin.service</code></strong> para controlar el servicio Nginx.
        </p>
        <ul>
            <li><strong>name</strong>: El nombre del servicio a gestionar (en este caso, <code>nginx</code>).</li>
            <li><strong>state</strong>: <code>started</code> asegura que el servicio esté actualmente en ejecución. Si no lo está, Ansible lo iniciará. Si ya está corriendo, no hará nada.</li>
            <li><strong>enabled</strong>: <code>true</code> configura el servicio para que se inicie automáticamente cada vez que el sistema remoto arranque.</li>
        </ul>

        <h5>4. Tarea: Reiniciar el servicio Nginx</h5>
        <pre class="yaml-fragment"><code>    - name: 4. Reiniciar el servicio Nginx
      ansible.builtin.service:
        name: nginx
        state: restarted</code></pre>
        <p>
            Esta tarea demuestra cómo forzar un reinicio del servicio Nginx. A menudo se usa después de cambiar archivos de configuración que requieren un reinicio para que los cambios surtan efecto.
        </p>
        <ul>
            <li><strong>state</strong>: <code>restarted</code> detiene y luego inicia el servicio.</li>
        </ul>

        <h5>5. Tarea: Parar el servicio Nginx</h5>
        <pre class="yaml-fragment"><code>    - name: 5. Parar el servicio Nginx
      ansible.builtin.service:
        name: nginx
        state: stopped</code></pre>
        <p>
            Esta tarea se asegura de que el servicio Nginx esté detenido. Si está corriendo, Ansible lo parará. Si ya está parado, no hará nada.
        </p>
        <ul>
            <li><strong>state</strong>: <code>stopped</code> asegura que el servicio no esté en ejecución.</li>
        </ul>

        <h5>6. Tarea: Deshabilitar el servicio Nginx al arranque</h5>
        <pre class="yaml-fragment"><code>    - name: 6. Deshabilitar el servicio Nginx al arranque
      ansible.builtin.service:
        name: nginx
        enabled: false</code></pre>
        <p>
            Esta tarea configura el servicio Nginx para que <strong>no se inicie automáticamente</strong> en el arranque del sistema. Esto no detiene el servicio si ya está en ejecución, solo afecta su comportamiento en futuros arranques.
        </p>
        <ul>
            <li><strong>enabled</strong>: <code>false</code> configura el servicio para que no se inicie automáticamente al arrancar el sistema.</li>
        </ul>

        <h3>Consideraciones Finales al Ejecutar este Playbook:</h3>
        <p>
            Las últimas dos tareas (Parar el servicio y Deshabilitar al arranque) tienen un propósito demostrativo. Si después de ejecutar el playbook quieres que el servicio Nginx quede activo y configurado para iniciarse automáticamente, puedes comentar esas tareas en el archivo <code>service_test.yml</code>.
        </p>
        <p class="call-to-action">
            Puedes ejecutar este playbook para ver cómo Ansible gestiona los servicios desde la <a href="index.php">página principal</a>.
        </p>
    </div>