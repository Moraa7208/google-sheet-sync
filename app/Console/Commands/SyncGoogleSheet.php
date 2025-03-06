<?php
namespace App\Console\Commands;

use App\Models\Item;
use App\Models\Setting;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ClearValuesRequest;
use Google_Service_Sheets_ValueRange;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncGoogleSheet extends Command
{
    protected $signature = 'sync:google-sheet';
    protected $description = 'Sync Allowed items to Google Sheet';

    public function handle()
    {
        try {
            $setting = Setting::first();
            if (!$setting || !$setting->google_sheet_url) {
                $this->error('No Google Sheet URL set.');
                return;
            }

            // Extract spreadsheet ID
            preg_match('/\/spreadsheets\/d\/([a-zA-Z0-9-_]+)\//', $setting->google_sheet_url, $matches);
            $spreadsheetId = $matches[1] ?? null;
            if (!$spreadsheetId) {
                $this->error('Invalid Google Sheet URL.');
                return;
            }

            // Credentials path diagnostics
            // $credentialsPath = storage_path(str_replace('storage/', '', env('GOOGLE_SHEETS_CREDENTIALS_PATH')));
            $credentialsPath = env('GOOGLE_SHEETS_CREDENTIALS_PATH');

            // Diagnostic prints
            $this->info("Credentials Path: " . $credentialsPath);

            if (!file_exists($credentialsPath)) {
                $this->error("Credentials file does not exist!");
                return;
            }

            // Read and display service account email
            $credentials = json_decode(file_get_contents($credentialsPath), true);
            $this->info("Service Account Email: " . ($credentials['client_email'] ?? 'Not found'));

            // Set up Google Sheets API
            $client = new Google_Client();
            $client->setApplicationName('Google Sheet Sync');
            $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
            $client->setAuthConfig($credentialsPath);

            $service = new Google_Service_Sheets($client);

                      // Step 1: Fetch existing data from Sheet to preserve comments
            $range = 'Sheet1!A1:D';
            $existingData = [];
            try {
                $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                $existingData = $response->getValues() ?? [];
            } catch (\Exception $e) {
                $this->info('No existing data in Sheet or error fetching: ' . $e->getMessage());
            }

                    // Build a map of ID -> Comment from existing Sheet data
           $sheetComments = [];
           if (!empty($existingData)) {
              foreach ($existingData as $index => $row) {
                  if ($index === 0) continue; // Skip header
                  $id = $row[0] ?? null; // ID is first column
                  $comment = $row[3] ?? ''; // Comments is fourth column
                  if ($id) {
                     $sheetComments[$id] = $comment;
                 }
             }
           }


            // Fetch Allowed items
            $items = Item::allowed()->get(['id', 'name', 'status', 'comments'])->toArray();

            $data = [['ID', 'Name', 'Status', 'Comments']]; // Header

            foreach ($items as $item) {
                $itemId = $item['id'];
                if (isset($sheetComments[$itemId]) && $sheetComments[$itemId] !== ($item['comments'] ?? '')) {
                    Item::where('id', $itemId)->update(['comments' => $sheetComments[$itemId]]);
                }
            }

            $data = [['ID', 'Name', 'Status', 'Comments']]; // Header
            foreach ($items as $item) {
                $itemId = $item['id'];
                $comment = $sheetComments[$itemId] ?? $item['comments'] ?? '';
                $data[] = [$item['id'], $item['name'], $item['status'], $comment];
            }

            // Sync to Sheet
            $range = 'Sheet1!A1:D';

            // Clear existing values
            $clearBody = new Google_Service_Sheets_ClearValuesRequest();
            $service->spreadsheets_values->clear($spreadsheetId, $range, $clearBody);

            // Update with new values
            $body = new Google_Service_Sheets_ValueRange([
                'values' => $data
            ]);
            $params = [
                'valueInputOption' => 'RAW'
            ];
            $result = $service->spreadsheets_values->update(
                $spreadsheetId,
                $range,
                $body,
                $params
            );

            $this->info('Synced ' . count($items) . ' items to Google Sheet.');
        } catch (\Google_Service_Exception $e) {
            $this->error('Google Service Error: ' . $e->getMessage());
            Log::error('Google Sheet Sync Service Error', [
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
                'trace' => $e->getTraceAsString()
            ]);
        } catch (\Exception $e) {
            $this->error('Sync failed: ' . $e->getMessage());
            Log::error('Google Sheet Sync Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
