<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlossaryTerm;
use App\Support\AdminAccess;
use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GlossaryAdminController extends Controller
{
    public function index(Request $request)
    {
        AdminAccess::require($request->user(), 'publishing.view');
        $terms = GlossaryTerm::orderBy('term')->paginate(40);
        return view('admin.publishing.glossary', compact('terms'));
    }

    public function store(Request $request)
    {
        AdminAccess::require($request->user(), 'publishing.manage');
        $data = $request->validate(['term' => ['required', 'string', 'max:255', 'unique:glossary_terms'], 'definition' => ['required', 'string', 'max:10000'], 'status' => ['required', Rule::in(['draft', 'published'])]]);
        $term = GlossaryTerm::create($data + ['author_user_id' => $request->user()->id]);
        AdminAudit::record($request, 'glossary.created', $term, "Created glossary term: {$term->term}", $term->toArray());
        return back()->with('status', 'Glossary term created.');
    }

    public function update(Request $request, GlossaryTerm $term)
    {
        AdminAccess::require($request->user(), 'publishing.manage');
        $data = $request->validate(['term' => ['required', 'string', 'max:255', Rule::unique('glossary_terms')->ignore($term)], 'definition' => ['required', 'string', 'max:10000'], 'status' => ['required', Rule::in(['draft', 'published'])]]);
        $term->update($data);
        AdminAudit::record($request, 'glossary.updated', $term, "Updated glossary term: {$term->term}", AdminAudit::changes($term));
        return back()->with('status', 'Glossary term updated.');
    }
}
