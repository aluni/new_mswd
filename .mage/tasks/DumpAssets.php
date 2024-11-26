<?php

namespace Task;

use Mage\Task\AbstractTask;

class DumpAssets extends AbstractTask {

    public function getName(): string {
        return 'Limpiando cache e instalando assets';
    }

    public function run(): bool {
        $command = 'bin/console asset-map:compile';

        return $this->runCommand($command);
    }

}
