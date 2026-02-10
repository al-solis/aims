<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\setting;
use Illuminate\Support\Facades\Auth;
class SettingController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'alert_days_before' => 'required|integer|min:0',
        ]);

        $setting = setting::updateOrCreate(
            ['key' => 'license_expiring_days'],
            [
                'value' => $request->input('alert_days_before'),
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ]
        );

        return redirect()->route('licenses.index')->with('success', 'Settings updated successfully.');
    }
}
