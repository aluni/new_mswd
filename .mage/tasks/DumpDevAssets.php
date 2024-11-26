<?php
namespace Task;

use Mage\Task\AbstractTask;

class DumpDevAssets extends AbstractTask
{
    public function getName(): string {
        return 'Limpiando cache e instalando assets de desarrollo';
    }

    public function run(): bool {

        $command = 'rm -rf public/assets';
        return $this->runCommand($command);
    }
}
