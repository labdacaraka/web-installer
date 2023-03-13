<?php

namespace Labdacaraka\WebInstaller\Commands;

use Illuminate\Console\Command;

class WebInstallerCommand extends Command
{
    public $signature = 'web-installer';

    public $description = 'Install project from web';

    public function handle(): int
    {
        if($projectInitCommands = config('web-installer.project_init_commands')){
            foreach ($projectInitCommands as $command => $arguments){
                $this->callSilently($command, $arguments);
            }
        }
        $this->comment('All done');

        return self::SUCCESS;
    }
}
