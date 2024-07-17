<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RestoreDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore db from database/db/main.sql';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dropping all tables...');

        $this->call('db:wipe');

        $sqlFile = base_path('database\db\main.sql');

        // check if file exists
        if (!file_exists($sqlFile)) {
            $this->error('File not found');
            return 1;
        }

        try {
            $this->info('Restoring database...');
            $sql = file_get_contents($sqlFile);
            DB::unprepared($sql);
            $this->info('Database restored successfully.');
        } catch (\Exception $e) {
            $this->error('Failed to restore database: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
