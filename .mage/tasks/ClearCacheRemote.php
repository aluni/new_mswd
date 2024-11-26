<?php
namespace Task;

use Mage\Task\AbstractTask;

class ClearCacheRemote extends AbstractTask
{
    public function getName()
    {
        return 'Limpiando cache en remoto y reload de apache';
    }

    public function run()
    {
        $command = 'bin/console ca:cl --env=prod';
        $result = $this->runCommandRemote($command);
        
        $command2 = 'service apache2 reload';
        $result2 = $this->runCommandRemote($command2);

        return $result;
    }
}
