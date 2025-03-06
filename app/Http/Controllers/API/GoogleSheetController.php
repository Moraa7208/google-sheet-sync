<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GoogleSheetController extends Controller
{
    public function fetch(Request $request, $count = null)
    {
        $command = 'fetch:google-sheet';
        if ($count !== null && is_numeric($count)) {
            $command .= " --count=" . (int)$count;
        }

        $output = [];
        \Artisan::call($command, [], new class($output) extends \Symfony\Component\Console\Output\Output {
            private $output;

            public function __construct(&$output)
            {
                parent::__construct();
                $this->output = &$output;
            }

            protected function doWrite(string $message, bool $newline)
            {
                $this->output[] = $message . ($newline ? PHP_EOL : '');
            }
        });

        return response()->json(['output' => implode('', $output)]);
    }
}
