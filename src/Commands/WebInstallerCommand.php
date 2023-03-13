<?php

namespace Labdacaraka\WebInstaller\Commands;

use Illuminate\Console\Command;

class WebInstallerCommand extends Command
{
    public $signature = 'web-installer';

    public $description = 'Install project from web';

    public function handle(): int
    {
        $this->callSilently('migrate:fresh');
        $this->callSilently('db:seed');
        $this->callSilently('storage:link');
//        $this->callSilently('key:generate', ['--force' => true]);
        $this->comment('All done');

        return self::SUCCESS;
    }
}
