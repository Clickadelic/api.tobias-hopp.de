<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SwitchEnvironment extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'env:switch {env : dev, stage oder prod}';

    /**
     * The console command description.
     */
    protected $description = 'Wechselt zwischen dev, stage und prod Umgebungen';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $env = $this->argument('env');

        // Mapping der Env-Dateien
        $envFiles = [
            'dev' => base_path('.env.dev'),
            'stage' => base_path('.env.stage'),
            'prod' => base_path('.env.prod'),
        ];

        if (!isset($envFiles[$env])) {
            $this->error("Umgebung \"$env\" existiert nicht! Bitte wähle dev, stage oder prod.");
            return 1;
        }

        $targetEnvFile = $envFiles[$env];

        if (!file_exists($targetEnvFile)) {
            $this->error("Datei $targetEnvFile existiert nicht!");
            return 1;
        }

        // 1️⃣ .env Datei ersetzen
        copy($targetEnvFile, base_path('.env'));
        $this->info("Env gewechselt zu \"$env\".");

        // 2️⃣ Config und Cache neu laden
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->info("Config und Cache erfolgreich geleert.");

        // 3️⃣ Optional: DB testen
        try {
            DB::purge('mysql'); // alte Verbindung löschen
            DB::reconnect('mysql'); // neue Verbindung aufbauen
            DB::connection()->getPdo(); // Verbindung testen
            $this->info("Datenbankverbindung erfolgreich getestet: " . DB::connection()->getDatabaseName());
        } catch (\Exception $e) {
            $this->error("Fehler bei der Datenbankverbindung: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
