<?php

namespace Billy\MongodbCommenter\Commands;

use Illuminate\Console\Command;

class MongodbCommenterCommand extends Command
{
    public $signature = 'mongodb-commenter';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
