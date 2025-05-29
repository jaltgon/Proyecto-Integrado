 <div class="detailed-explanation">
        <h3>Propósito del Módulo `ansible.builtin.user` y Ejemplo Práctico (`user_test.yml`)</h3>

        <p>
            El módulo <strong><code>ansible.builtin.user</code></strong> es fundamental para la administración de sistemas, ya que te permite gestionar usuarios y grupos en tus hosts remotos de manera automatizada. Con este módulo, puedes crear nuevos usuarios, modificar sus propiedades (UID, GID, grupos a los que pertenecen, shell, directorio home, contraseña) o eliminarlos por completo. Es una herramienta indispensable para configurar entornos de trabajo y acceso a aplicaciones.
        </p>

        <h4>Desglose y Explicación del Playbook `user_test.yml`:</h4>
        <p>Este playbook demuestra cómo crear un usuario, añadirlo a grupos y, finalmente, cómo eliminar un usuario existente del sistema.</p>

        <h5>1. Encabezado del Playbook y Definición General</h5>
        <pre class="yaml-fragment"><code>---
- name: Gestionar usuarios con el modulo user
  hosts: all
  become: true</code></pre>
        <ul>
            <li><strong>name</strong>: Un nombre descriptivo para este play.</li>
            <li><strong>hosts</strong>: Indica que este playbook se ejecutará en todos los hosts definidos en tu inventario.</li>
            <li><strong>become</strong>: <i>true</i> significa que Ansible ejecutará las tareas con permisos de superusuario (<code>root</code>), lo cual es indispensable para crear, modificar o eliminar usuarios y grupos en el sistema.</li>
        </ul>

        <h5>2. Tarea: Crear un nuevo usuario de sistema 'devops_user'</h5>
        <pre class="yaml-fragment"><code>    - name: 1. Crear un nuevo usuario de sistema 'devops_user'
      ansible.builtin.user:
        name: devops_user
        comment: "Usuario para tareas de DevOps"
        uid: 1001
        group: users
        groups: sudo,docker
        shell: /bin/bash
        home: /home/devops_user
        state: present
        create_home: true
        password: "{{ 'password_secreto' | password_hash('sha512') }}"</code></pre>
        <p>
            Esta tarea utiliza el módulo <strong><code>ansible.builtin.user</code></strong> para crear un nuevo usuario con propiedades específicas en el host remoto. Si el usuario ya existe, Ansible solo ajustará las propiedades para que coincidan con la configuración deseada (comportamiento idempotente).
        </p>
        <ul>
            <li><strong>name</strong>: El nombre del usuario a crear o gestionar (<code>devops_user</code>).</li>
            <li><strong>comment</strong>: Un comentario descriptivo para el usuario (se almacena en <code>/etc/passwd</code>).</li>
            <li><strong>uid</strong>: El User ID numérico del usuario. Es importante elegir un UID que no esté ya en uso o permitir que el sistema lo asigne automáticamente si se omite este parámetro.</li>
            <li><strong>group</strong>: El grupo primario al que pertenecerá el usuario (ej. <code>users</code>).</li>
            <li><strong>groups</strong>: Una lista de grupos secundarios a los que añadir el usuario (ej. <code>sudo,docker</code>). Asegúrate de que estos grupos existan en el sistema.</li>
            <li><strong>shell</strong>: El shell por defecto para el usuario (ej. <code>/bin/bash</code>).</li>
            <li><strong>home</strong>: La ruta al directorio home del usuario.</li>
            <li><strong>state</strong>: <code>present</code> asegura que el usuario exista en el sistema.</li>
            <li><strong>create_home</strong>: <i>true</i> asegura que se cree el directorio home si no existe.</li>
            <li><strong>password</strong>: Define la contraseña del usuario. <strong>Importante:</strong> Se utiliza un filtro de Jinja2 (<code>password_hash('sha512')</code>) para generar un hash seguro de la contraseña. <strong>Nunca se deben poner contraseñas en texto plano en playbooks de producción.</strong></li>
        </ul>

        <h5>3. Tarea: Asegurar que 'devops_user' sea miembro del grupo 'developers'</h5>
        <pre class="yaml-fragment"><code>    - name: 2. Asegurar que 'devops_user' sea miembro del grupo 'developers'
      ansible.builtin.group:
        name: developers
        state: present

    - name: 3. Anadir 'devops_user' al grupo 'developers'
      ansible.builtin.user:
        name: devops_user
        groups: developers
        append: true
        state: present</code></pre>
        <p>
            Estas dos tareas trabajan juntas para asegurar que el usuario <code>devops_user</code> pertenezca al grupo secundario <code>developers</code>.
        </p>
        <ul>
            <li>La primera tarea usa el módulo <strong><code>ansible.builtin.group</code></strong> con <code>state: present</code> para asegurar que el grupo <code>developers</code> exista en el sistema antes de intentar añadir usuarios a él.</li>
            <li>La segunda tarea vuelve a usar <strong><code>ansible.builtin.user</code></strong>. Aquí, <strong><code>groups: developers</code></strong> especifica el grupo adicional, y <strong><code>append: true</code></strong> es crucial: asegura que el usuario sea añadido a este grupo *sin eliminarlo de ningún otro grupo* al que ya pertenezca. Si <code>append: false</code> (o se omite), el usuario solo pertenecería a los grupos listados, eliminándolo de cualquier otro.</li>
        </ul>

        <h5>4. Tarea: Eliminar el usuario 'old_user' (si existe)</h5>
        <pre class="yaml-fragment"><code>    - name: 4. Eliminar el usuario 'old_user' (si existe)
      ansible.builtin.user:
        name: old_user
        state: absent
        remove: true</code></pre>
        <p>
            Esta tarea demuestra cómo eliminar un usuario del sistema. Si el usuario <code>old_user</code> existe, Ansible lo eliminará. Si ya no existe, la tarea se marcará como <code>ok</code> (comportamiento idempotente).
        </p>
        <ul>
            <li><strong>name</strong>: El nombre del usuario a eliminar (<code>old_user</code>).</li>
            <li><strong>state</strong>: <code>absent</code> asegura que el usuario no esté presente en el sistema.</li>
            <li><strong>remove</strong>: <i>true</i> indica que también se debe eliminar el directorio home del usuario y su spool de correo (si aplica). Si se omite o se establece a <code>false</code>, solo se eliminará la entrada del usuario, dejando el home y el correo intactos.</li>
        </ul>

        <h3>Requisito para la Tarea de Eliminación:</h3>
        <p>
            Para que la tarea "Eliminar el usuario 'old_user'" (Tarea 4) muestre un resultado de <strong><code>changed</code></strong> (indicando que se ha realizado una acción) al ejecutar el playbook, deberías asegurarte de que un usuario con el nombre <code>old_user</code> <strong>exista previamente</strong> en tus hosts remotos.
        </p>
        <p>
            Puedes crear este usuario manualmente en tus hosts remotos antes de la ejecución del playbook, por ejemplo, usando el comando:
            <pre><code class="language-bash">sudo adduser old_user</code></pre>
        </p>
        <p class="call-to-action">
            Una vez que hayas creado el usuario de prueba si lo deseas, el playbook <strong><code>user_test.yml</code></strong> estará listo para ser ejecutado desde la <a href="index.php">página principal</a>.
        </p>
    </div>