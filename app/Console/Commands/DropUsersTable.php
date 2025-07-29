<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class DropUsersTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:drop-users-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drops the users table from the mysql_users database (because migrate:fresh only drops the tables of the default connection)';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Schema::connection('mysql_users')->dropIfExists('users');
    }
}
