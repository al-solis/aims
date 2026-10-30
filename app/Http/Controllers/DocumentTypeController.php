<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\document_type;
use Illuminate\Support\Facades\Auth;

class DocumentTypeController extends Controller
{
    private const SET_DOCUMENT = 'SET-06';

    private function authorizeDocumentSetup(string $action): void
    {
        abort_unless(
            Auth::user()->hasAccess(self::SET_DOCUMENT, $action),
            403,
            'You do not have permission in Document Type Management to perform this action.'
        );
    }
    public function index(Request $request)
    {
        $this->authorizeDocumentSetup('read');
        $canCreate = Auth::user()->hasAccess(self::SET_DOCUMENT, 'create');
        $canUpdate = Auth::user()->hasAccess(self::SET_DOCUMENT, 'update');
        $canDelete = Auth::user()->hasAccess(self::SET_DOCUMENT, 'delete');

        $search = $request->input('search');
        $status = $request->input('status');
        $totalDocumentTypes = document_type::count();
        $activeDocumentTypes = document_type::where('is_active', 1)->count();
        $inactiveDocumentTypes = document_type::where('is_active', 0)->count();

        $query = document_type::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
                $q->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($status !== null) {
            $query = $query->where('is_active', $status);
        }

        $docTypes = $query->paginate(config('app.paginate'));

        return view('setup.doctype.index', compact(
            'docTypes',
            'totalDocumentTypes',
            'activeDocumentTypes',
            'inactiveDocumentTypes',
            'canCreate',
            'canUpdate',
            'canDelete'
        ));
    }

    public function store(Request $request)
    {
        $this->authorizeDocumentSetup('create');

        $request->validate([
            'name' => 'required|string|max:60',
            'description' => 'nullable|string|max:150',
        ]);

        document_type::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('doctype.index')->with('success', 'Document Type created successfully.');
    }

    public function update(Request $request, $id)
    {
        $this->authorizeDocumentSetup('update');

        $request->validate([
            'edit_name' => 'required|string|max:60',
            'edit_description' => 'nullable|string|max:150',
        ]);

        $docType = document_type::findOrFail($id);
        $docType->update([
            'name' => $request->edit_name,
            'description' => $request->edit_description,
            'is_active' => $request->edit_status,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('doctype.index')->with('success', 'Document Type updated successfully.');
    }
}
