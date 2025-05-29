<div class="detailed-explanation">
        <h3>Propósito del Módulo `ansible.builtin.file` y Ejemplo Práctico (`file_test.yml`)</h3>

        <p>
            El módulo <strong><code>ansible.builtin.file</code></strong> es esencial para gestionar archivos, directorios y enlaces simbólicos en tus hosts remotos. Te permite asegurar que los archivos y directorios existan o no, que tengan los permisos correctos, y que pertenezcan a los usuarios y grupos adecuados. Es una herramienta muy versátil para preparar y mantener el estado de los sistemas.
        </p>

        <h4>Desglose y Explicación del Playbook `file_test.yml`:</h4>
        <p>Este playbook demuestra cómo utilizar el módulo `file` para crear un directorio, crear un archivo vacío, cambiar permisos de un archivo existente y, finalmente, eliminar un archivo.</p>

        <h5>1. Encabezado del Playbook y Definición General</h5>
        <pre class="yaml-fragment"><code>---
- name: Gestionar archivos y directorios con el módulo file
  hosts: all
  become: true</code></pre>
        <ul>
            <li><strong>name</strong>: Un nombre descriptivo para este play.</li>
            <li><strong>hosts</strong>: Define en qué servidores se ejecutarán las tareas. En este caso, <i>all</i> significa que se ejecutará en todos los hosts definidos en tu archivo de inventario.</li>
            <li><strong>become</strong>: <i>true</i> indica que Ansible ejecutará las tareas con permisos de superusuario (root), lo cual es necesario para gestionar archivos y directorios en rutas del sistema.</li>
        </ul>

        <h5>2. Tarea: Asegurar que el directorio de pruebas exista</h5>
        <pre class="yaml-fragment"><code>    - name: 1. Asegurar que el directorio de pruebas exista
      ansible.builtin.file:
        path: /tmp/ansible_pruebas
        state: directory
        owner: root
        group: root
        mode: '0755'</code></pre>
        <p>
            Esta tarea utiliza el módulo <strong><code>ansible.builtin.file</code></strong> para asegurarse de que un directorio específico exista en el host remoto. Si no existe, lo crea. Si ya existe, no hace nada (comportamiento idempotente).
        </p>
        <ul>
            <li><strong>name</strong>: Nombre descriptivo de la tarea.</li>
            <li><strong>path</strong>: La ruta absoluta del directorio que queremos gestionar (`/tmp/ansible_pruebas`).</li>
            <li><strong>state</strong>: <code>directory</code> asegura que la ruta especificada sea un directorio.</li>
            <li><strong>owner</strong> y <strong>group</strong>: Establecen el propietario y grupo del directorio a <code>root</code>.</li>
            <li><strong>mode</strong>: Define los permisos del directorio a <code>0755</code> (lectura, escritura y ejecución para el propietario; lectura y ejecución para el grupo y otros).</li>
        </ul>

        <h5>3. Tarea: Crear un archivo de registro vacío en el directorio de pruebas</h5>
        <pre class="yaml-fragment"><code>    - name: 2. Crear un archivo de registro vacío en el directorio de pruebas
      ansible.builtin.file:
        path: /tmp/ansible_pruebas/log_aplicacion.txt
        state: touch
        owner: root
        group: root
        mode: '0644'</code></pre>
        <p>
            Con esta tarea, creamos un archivo vacío si no existe en la ruta especificada. Si ya existe, simplemente se actualiza su marca de tiempo (como un comando <code>touch</code>).
        </p>
        <ul>
            <li><strong>name</strong>: Nombre descriptivo de la tarea.</li>
            <li><strong>path</strong>: La ruta absoluta del archivo que queremos crear (`/tmp/ansible_pruebas/log_aplicacion.txt`).</li>
            <li><strong>state</strong>: <code>touch</code> asegura que el archivo exista y actualiza su fecha de modificación.</li>
            <li><strong>owner</strong>, <strong>group</strong> y <strong>mode</strong>: Establecen el propietario, grupo y permisos del nuevo archivo de la misma manera que en la tarea anterior.</li>
        </ul>

        <h5>4. Tarea: Cambiar permisos de un archivo existente (ej. /etc/hosts)</h5>
        <pre class="yaml-fragment"><code>    - name: 3. Cambiar permisos de un archivo existente (ej. /etc/hosts)
      ansible.builtin.file:
        path: /etc/hosts
        mode: '0600'</code></pre>
        <p>
            Esta tarea demuestra cómo el módulo <strong><code>file</code></strong> puede modificar los permisos de un archivo ya existente. En este caso, se cambian los permisos del archivo <code>/etc/hosts</code>.
        </p>
        <ul>
            <li><strong>name</strong>: Nombre descriptivo de la tarea.</li>
            <li><strong>path</strong>: La ruta absoluta del archivo cuyos permisos queremos cambiar.</li>
            <li><strong>mode</strong>: Define los nuevos permisos. Aquí, <code>0600</code> significa solo lectura y escritura para el propietario (<code>root</code>), y ningún permiso para el grupo o para otros.</li>
        </ul>

        <h5>5. Tarea: Eliminar un archivo de prueba</h5>
        <pre class="yaml-fragment"><code>    - name: 4. Eliminar un archivo de prueba
      ansible.builtin.file:
        path: /tmp/archivo_a_eliminar.txt
        state: absent</code></pre>
        <p>
            Finalmente, esta tarea se asegura de que un archivo específico (<code>/tmp/archivo_a_eliminar.txt</code>) no exista en el host remoto. Si el archivo está presente, Ansible lo eliminará. Si ya está ausente, la tarea no hará nada (idempotente).
        </p>
        <ul>
            <li><strong>name</strong>: Nombre descriptivo de la tarea.</li>
            <li><strong>path</strong>: La ruta absoluta del archivo que queremos eliminar.</li>
            <li><strong>state</strong>: <code>absent</code> indica que el archivo debe ser eliminado si existe.</li>
        </ul>

        <h3>Requisito para el Playbook: Archivo `archivo_a_eliminar.txt`</h3>
        <p>
            Para que la tarea "Eliminar un archivo de prueba" (Tarea 4) muestre un resultado de <strong><code>changed</code></strong> (indicando que se ha realizado una acción) al ejecutar el playbook por primera vez, es recomendable que el archivo <code>/tmp/archivo_a_eliminar.txt</code> <strong>exista previamente</strong> en tus hosts remotos.
        </p>
        <p>
            Si el archivo no existe en el host remoto antes de ejecutar el playbook, la tarea se completará con un estado <strong><code>ok</code></strong> (verde), lo cual es el comportamiento normal y <strong>correcto</strong> de Ansible (ya que el estado deseado, "ausente", ya se cumple). Sin embargo, para fines de demostración y para ver la acción de eliminación, es mejor que el archivo esté presente.
        </p>
        <p>
            Puedes crear este archivo manualmente en tus hosts remotos antes de la ejecución del playbook, por ejemplo, usando el comando:
            <pre><code class="language-bash">sudo touch /tmp/archivo_a_eliminar.txt</code></pre>
        </p>
        <p class="call-to-action">
            Una vez comprendidos estos conceptos y requisitos, el playbook <strong><code>file_test.yml</code></strong> estará listo para ser ejecutado desde la <a href="index.php">página principal</a>.
        </p>
    </div>