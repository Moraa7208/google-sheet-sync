<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SheetController extends Controller
{

    public function getSheetUrl()
    {
        $setting = Setting::first() ?? new Setting();
        return response()->json(['google_sheet_url' => $setting->google_sheet_url]);
    }

    public function saveSheetUrl(Request $request)
    {
        $request->validate(['google_sheet_url' => 'required|url']);
        $setting = Setting::first() ?? new Setting();
        $setting->google_sheet_url = $request->google_sheet_url;
        $setting->save();
        return response()->json(['google_sheet_url' => $setting->google_sheet_url], 200);
    }
}
