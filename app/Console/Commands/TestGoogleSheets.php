<?php

namespace App\Console\Commands;

use Google_Client;
use Google_Service_Sheets;
use Illuminate\Console\Command;

class TestGoogleSheets extends Command
{

    protected $signature = 'test:google-sheets';
    protected $description = 'Test Google Sheets API connection';

    public function handle()
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Sheet Sync');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(env('GOOGLE_SHEETS_CREDENTIALS_PATH'));

        $service = new Google_Service_Sheets($client);
        $this->info('Google Sheets API connected successfully!');
    }
}
