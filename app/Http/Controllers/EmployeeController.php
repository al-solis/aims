<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Employee;
use App\Models\Location;

class EmployeeController extends Controller
{
    private $tempFolder = 'temp/employees';

    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $searchloc = $request->query('searchloc');

        $locations = Location::get();
        $query = Employee::query();
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 1)->count();
        $onleaveEmployees = Employee::where('status', 2)->count();
        $inactiveEmployees = Employee::where('status', 0)->count();

        // If status or location is selected and search exists, redirect without search
        if (($status || $searchloc) && $search) {
            return redirect()->route('employee.index', [
                'status' => $status,
                'searchloc' => $searchloc,
            ]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('last_name', 'like', '%' . $search . '%');
                $q->orWhere('first_name', 'like', '%' . $search . '%');
                $q->orWhere('middle_name', 'like', '%' . $search . '%');
                $q->orWhere('employee_code', 'like', '%' . $search . '%');
                $q->orWhere('position', 'like', '%' . $search . '%');
                // $q->orWhereHas('location', function ($locQuery) use ($search) {
                //     $locQuery->where('name', 'like', '%' . $search . '%');
                // }, '=', 0);
                $q->orWhere('email', 'like', '%' . $search . '%');
                $q->orWhere('mobile', 'like', '%' . $search . '%');
            });
        }

        if ($searchloc) {
            $query->where('location_id', $searchloc);
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $employees = $query->paginate(config('app.paginate'))
            ->appends([
                'search' => $search,
                'searchloc' => $searchloc,
                'status' => $status
            ]);

        return view('employee.index', compact(
            'employees',
            'locations',
            'totalEmployees',
            'activeEmployees',
            'onleaveEmployees',
            'inactiveEmployees',
            'search',
            'status',
            'searchloc'
        ));
    }

    public function create()
    {
        $locations = Location::where('status', 1)->get();
        return view('employee.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idno' => 'required|string|max:20|unique:employees,employee_code',
            'fname' => 'required|string|max:50',
            'mname' => 'nullable|string|max:50',
            'lname' => 'required|string|max:50',
            'date' => 'nullable|date',
            'bday' => 'nullable|date',
            'email' => 'nullable|email|max:100',
            'mobile' => 'nullable|string|max:15',
            'position' => 'nullable|string|max:60',
            'department' => 'nullable',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:50',
            'zip' => 'nullable|string|max:20',
            'emergency' => 'nullable|string|max:50',
            'e_no' => 'nullable|string|max:15',
            'status' => 'required|integer|in:0,1,2',
            'employee_path' => 'nullable|string'
        ]);

        $photoPath = null;

        try {
            // Check if there's a temp photo to move
            if (
                $request->filled('employee_path') &&
                Str::contains($request->employee_path, $this->tempFolder)
            ) {
                $tempPath = $request->employee_path;

                if (Storage::disk('public')->exists($tempPath)) {

                    Storage::disk('public')->makeDirectory('employees');

                    $extension = pathinfo($tempPath, PATHINFO_EXTENSION);
                    $photoPath = 'employees/employee_' . time() . '_' . Str::random(8) . '.' . $extension;

                    Storage::disk('public')->move($tempPath, $photoPath);
                }
            }

            Employee::create([
                'employee_code' => $request->idno,
                'first_name' => $request->fname,
                'middle_name' => $request->mname,
                'last_name' => $request->lname,
                'position' => $request->position,
                'location_id' => $request->department ?? null,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'hire_date' => $request->date,
                'date_of_birth' => $request->bday,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->zip,
                'emergency_contact' => $request->emergency,
                'emergency_phone' => $request->e_no,
                'status' => $request->status,
                'photo_path' => $photoPath,
                'created_by' => Auth::id(),
            ]);

        } catch (\Exception $e) {
            if ($request->filled('employee_path')) {
                Storage::disk('public')->delete($request->employee_path);
            }

            return back()->withErrors([
                'error' => 'Failed to save employee: ' . $e->getMessage()
            ]);
        }
        return redirect()
            ->route('employee.index')
            ->with('success', 'Employee created successfully.');
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            // Generate unique filename with timestamp
            $fileName = 'temp_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            // Store in temp folder
            $path = $file->storeAs($this->tempFolder, $fileName, 'public');

            return response()->json([
                'path' => $path,
                'temp_name' => $fileName,
                'url' => asset('storage/' . $path)
            ]);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }

    // Cleanup old temp files (optional, can be run via scheduler)
    private function cleanupTempFiles()
    {
        $files = Storage::disk('public')->files($this->tempFolder);

        foreach ($files as $file) {
            if (Storage::disk('public')->lastModified($file) < now()->subDay()->timestamp) {
                Storage::disk('public')->delete($file);
            }
        }
    }

    public function cleanupTempFile(Request $request)
    {
        $request->validate([
            'file_path' => 'required|string'
        ]);

        // Only allow deletion from temp folder
        if (Str::contains($request->file_path, $this->tempFolder)) {
            Storage::disk('public')->delete($request->file_path);
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Invalid file path'], 400);
    }
}
