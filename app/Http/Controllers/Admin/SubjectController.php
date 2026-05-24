<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubjectController extends Controller
{
    /**
     * List all subjects
     */
    public function index()
    {
        $subjects = Subject::paginate(10);
        return view('admin.subjects.index', compact('subjects'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.subjects.create');
    }

    /**
     * Store subject
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:subjects',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        Subject::create($validated);

        return redirect('/admin/subjects')->with('success', 'Subject created successfully!');
    }

    /**
     * Show edit form
     */
    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    /**
     * Update subject
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:subjects,name,' . $subject->id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $subject->update($validated);

        return redirect('/admin/subjects')->with('success', 'Subject updated successfully!');
    }

    /**
     * Delete subject
     */
    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect('/admin/subjects')->with('success', 'Subject deleted successfully!');
    }

    /**
     * Toggle status
     */
    public function toggle(Subject $subject)
    {
        $subject->status = $subject->status === 'active' ? 'inactive' : 'active';
        $subject->save();

        return back()->with('success', 'Subject status updated!');
    }
}