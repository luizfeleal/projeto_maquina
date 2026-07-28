<?php

namespace App\Console\Commands;

use App\Mocks\MockStore;
use Illuminate\Console\Command;

class MockResetCommand extends Command
{
    protected $signature = 'mock:reset';

    protected $description = 'Restaura os dados mock ao estado inicial (seed)';

    public function handle(): int
    {
        MockStore::reset();
        $this->info('Dados mock restaurados ao estado inicial.');

        return 0;
    }
}
