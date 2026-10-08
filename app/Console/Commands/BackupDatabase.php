<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

#[Signature('app:backup-database {--keep=7 : How many recent backups to retain}')]
#[Description('Dump the MySQL database to a timestamped zip in storage/app/backups')]
class BackupDatabase extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (config('database.default') !== 'mysql') {
            $this->error('Database backups support MySQL connections only.');

            return self::FAILURE;
        }

        $config = config('database.connections.mysql');
        $filename = 'bidaya-'.Carbon::now()->format('Y-m-d-His').'.sql';
        $directory = storage_path('app/backups');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $dump = new Process([
            (string) config('services.bidaya.mysqldump_path', 'mysqldump'),
            '--host='.$config['host'],
            '--port='.$config['port'],
            '--user='.$config['username'],
            '--password='.$config['password'],
            '--result-file='.$directory.DIRECTORY_SEPARATOR.$filename,
            '--single-transaction',
            $config['database'],
        ]);
        $dump->setTimeout(300);
        $dump->run();

        if (! $dump->isSuccessful()) {
            @unlink($directory.DIRECTORY_SEPARATOR.$filename);
            $this->error('mysqldump failed: '.$dump->getErrorOutput());

            return self::FAILURE;
        }

        $zipPath = $directory.DIRECTORY_SEPARATOR.$filename.'.zip';
        $zip = new \ZipArchive();

        if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
            $this->error('Could not create the backup archive.');

            return self::FAILURE;
        }

        $zip->addFile($directory.DIRECTORY_SEPARATOR.$filename, $filename);
        $zip->close();
        unlink($directory.DIRECTORY_SEPARATOR.$filename);

        $backups = collect(Storage::disk('local')->files('backups'))
            ->filter(fn (string $file): bool => str_ends_with($file, '.zip'))
            ->sortDesc()
            ->values();

        foreach ($backups->slice((int) $this->option('keep')) as $oldBackup) {
            Storage::disk('local')->delete($oldBackup);
        }

        $this->info("Database backup stored: backups/{$filename}.zip");

        return self::SUCCESS;
    }
}
