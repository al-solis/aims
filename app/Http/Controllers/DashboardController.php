<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\access_right;
use App\Models\module;
use App\Models\role;
use App\Models\sub_module;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $roleId = $user->role_id;

        // Fetch all modules and their sub-modules
        $modules = module::with('subModules')->where('is_active', true)->get();

        // Fetch access rights for the user's role
        $accessRights = access_right::where('role_id', $roleId)->get()->keyBy(function ($item) {
            return $item->module_id . '-' . ($item->sub_module_id ?? 'null');
        });

        return view('dashboard', compact('modules', 'accessRights'));
    }
}

