<?php
chdir('/ansible');
$comando = "ansible-playbook -i /ansible/inventory/hosts.ini /ansible/playbooks/deploy_web.yml -e 'ansible_remote_tmp=/tmp/.ansible/tmp' 2>&1";
$output = shell_exec($comando);
echo "<pre>$output</pre>";
echo "<a href='index.php'>Volver</a>";
?>