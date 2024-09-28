<?php

namespace App\Console\Commands\App;

use Illuminate\Console\Command;

class productionCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:production {--APP_CLIENT_ID=} {--APP_SECRET=} {--REDIRECT_URL=} {--PLUGIN_LINK=} {--DB_HOST=} {--DB_DATABASE=} {--DB_USERNAME=} {--DB_PASSWORD=} {--QUEUE_CONNECTION=} {--URL_AUTHORIZE=} {--URL_ACCESS_TOKEN=} {--URL_RESOURCE_OWNER_DETAILS=} {--SCOPE=} {--PLUGIN_CODE=} {--ACCESS_TYPE=} {--DOMAIN=} {--DB_PORT=} {--APP_URL=} {--HOST_NAME=} {--APP_NAME=} {--PLUGIN_DOMAIN=} {--GOOGLE_REDIRECT_URL=} {--GOOGLE_CLIENT_ID=} {--GOOGLE_CLIENT_SECRET=} {--PLUGIN_ORIGIN=} {--REPORT_TOKENS_EXPIRY=} {--ACCESS_TOKEN_SOURCE=} {--PLUGIN_NAME=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->updateEnv([
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'SCOPE' => str_replace('-', ' ', $this->option('SCOPE')),
            'PLUGIN_CODE' => $this->option('PLUGIN_CODE'),
            'DB_PORT' => $this->option('DB_PORT'),
            'APP_URL' => $this->option('APP_URL'),
            'ACCESS_TYPE' => $this->option('ACCESS_TYPE'),
            'DOMAIN' => $this->option('DOMAIN'),
            'URL_AUTHORIZE' => $this->option('URL_AUTHORIZE'),
            'URL_ACCESS_TOKEN' => $this->option('URL_ACCESS_TOKEN'),
            'URL_RESOURCE_OWNER_DETAILS' => $this->option('URL_RESOURCE_OWNER_DETAILS'),
            'APP_CLIENT_ID' => $this->option('APP_CLIENT_ID'),
            'APP_SECRET' => $this->option('APP_SECRET'),
            'REDIRECT_URL' => $this->option('REDIRECT_URL'),
            'PLUGIN_LINK' => $this->option('PLUGIN_LINK'),
            'DB_HOST' => $this->option('DB_HOST'),
            'DB_DATABASE' => $this->option('DB_DATABASE'),
            'DB_USERNAME' => $this->option('DB_USERNAME'),
            'DB_PASSWORD' => $this->option('DB_PASSWORD'),
            'QUEUE_CONNECTION' => $this->option('QUEUE_CONNECTION'),
            'HOST_NAME' => $this->option('HOST_NAME'),
            'APP_NAME' => $this->option('APP_NAME'),
            'PLUGIN_DOMAIN' => $this->option('PLUGIN_DOMAIN'),
            'GOOGLE_REDIRECT_URL' => $this->option('GOOGLE_REDIRECT_URL'),
            'GOOGLE_CLIENT_ID' => $this->option('GOOGLE_CLIENT_ID'),
            'GOOGLE_CLIENT_SECRET' => $this->option('GOOGLE_CLIENT_SECRET'),
            'REPORT_TOKENS_EXPIRY' => $this->option('REPORT_TOKENS_EXPIRY'),
            'PLUGIN_NAME' => $this->option('PLUGIN_NAME'),
            'ACCESS_TOKEN_SOURCE' => $this->option('ACCESS_TOKEN_SOURCE'),
            'PLUGIN_ORIGIN' => $this->option('PLUGIN_ORIGIN')
        ]);
    }

    public function updateEnv($data = array())
    {
        if (!count($data)) {
            return;
        }
        $pattern = '/([^\=]*)\=[^\n]*/';
        $envFile = base_path() . '/.env';
        $lines = file($envFile);
        $newLines = [];
        foreach ($lines as $line) {
            preg_match($pattern, $line, $matches);
            if (!count($matches)) {
                $newLines[] = $line;
                continue;
            }
            if (!key_exists(trim($matches[1]), $data)) {
                $newLines[] = $line;
                continue;
            }
            $line = trim($matches[1]) . "='{$data[trim($matches[1])]}'\n";
            $newLines[] = $line;
        }
        $newContent = implode('', $newLines);
        file_put_contents($envFile, $newContent);
    }
}
