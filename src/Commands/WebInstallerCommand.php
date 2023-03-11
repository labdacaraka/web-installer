<?php

namespace Labdacaraka\WebInstaller\Commands;

use Illuminate\Console\Command;

class WebInstallerCommand extends Command
{
    public $signature = 'web-installer';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
