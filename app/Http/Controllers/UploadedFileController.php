<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\employee;
use App\Models\uploaded_file as UploadedFile;

class UploadedFileController extends Controller
{
    public function uploadFiles(Request $request, $empId)
    {
        $employee = employee::findOrFail($empId);

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            foreach ($files as $file) {
                $path = $file->store('employee_files', 'public');

                UploadedFile::create([
                    'employee_id' => $employee->id,
                    'module' => 'employee',
                    'note' => $request->input('note', ''),
                    'file_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'uploaded_by' => Auth::id(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Files uploaded successfully'
        ]);
    }

    public function getFiles($empId)
    {
        $employee = employee::findOrFail($empId);
        $files = UploadedFile::where('employee_id', $employee->id)->get();

        return response()->json(['files' => $files]);
    }
}
