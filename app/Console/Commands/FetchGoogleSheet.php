<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Google_Client;
use Google_Service_Sheets;
use Illuminate\Console\Command;

class FetchGoogleSheet extends Command
{

    protected $signature = 'fetch:google-sheet {--count= : Limit the number of rows to display}';
    protected $description = 'Fetch data from Google Sheet and display Item ID and Comments';

    public function handle()
    {
        $setting = Setting::first();
        if (!$setting || !$setting->google_sheet_url) {
            $this->error('No Google Sheet URL set.');
            return 1;
        }

        preg_match('/\/spreadsheets\/d\/([a-zA-Z0-9-_]+)\//', $setting->google_sheet_url, $matches);
        $spreadsheetId = $matches[1] ?? null;
        if (!$spreadsheetId) {
            $this->error('Invalid Google Sheet URL.');
            return 1;
        }

        $credentialsPath = env('GOOGLE_SHEETS_CREDENTIALS_PATH');
        if (!file_exists($credentialsPath)) {
            $this->error("Credentials file not found at: {$credentialsPath}");
            return 1;
        }

        $client = new Google_Client();
        $client->setApplicationName('Google Sheet Sync');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        try {
            $client->setAuthConfig($credentialsPath);
        } catch (\Exception $e) {
            $this->error('Failed to set auth config: ' . $e->getMessage());
            return 1;
        }

        $service = new Google_Service_Sheets($client);
        $range = 'Sheet1!A1:D';
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $rows = $response->getValues() ?? [];

        if (empty($rows)) {
            $this->info('No data found in Google Sheet.');
            return;
        }

        $count = $this->option('count') ? (int)$this->option('count') : count($rows);
        $rows = array_slice($rows, 0, $count);

        $this->info('Fetching data from Google Sheet...');
        $progress = $this->output->createProgressBar(count($rows));
        $progress->start();

        foreach ($rows as $row) {
            $id = $row[0] ?? 'N/A';
            $comment = isset($row[3]) ? $row[3] : '';
            $this->line("ID: {$id}, Comment: {$comment}");
            $progress->advance();
        }

        $progress->finish();
        $this->newLine();
        $this->info('Fetch complete.');
    }
}
