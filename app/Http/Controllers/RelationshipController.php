<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RelationshipController extends Controller
{
    /**
     * Assign teacher to student (by admin or teacher)
     */
    public function assignTeacher(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'teacher_id' => 'required|exists:users,id',
            'subject' => 'nullable|string',
        ]);
        
        $student = User::findOrFail($validated['student_id']);
        $teacher = User::findOrFail($validated['teacher_id']);
        
        // Check if teacher is actually a teacher
        if ($teacher->role !== 'teacher') {
            return response()->json(['error' => 'Invalid teacher role'], 400);
        }
        
        // Attach relationship
        $student->teachers()->attach($teacher->id, [
            'subject' => $validated['subject'],
            'assigned_at' => now()
        ]);
        
        return response()->json(['success' => 'Teacher assigned successfully']);
    }
    
    /**
     * Assign parent to student
     */
    public function assignParent(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'parent_id' => 'required|exists:users,id',
            'relationship' => 'nullable|in:Mother,Father,Guardian,Other',
        ]);
        
        $student = User::findOrFail($validated['student_id']);
        $parent = User::findOrFail($validated['parent_id']);
        
        // Check if parent is actually a parent
        if ($parent->role !== 'parent') {
            return response()->json(['error' => 'Invalid parent role'], 400);
        }
        
        // Attach relationship
        $student->parents()->attach($parent->id, [
            'relationship' => $validated['relationship'],
            'assigned_at' => now()
        ]);
        
        return response()->json(['success' => 'Parent assigned successfully']);
    }
    
    /**
     * Remove teacher-student relationship
     */
    public function removeTeacher(Request $request)
    {
        $student = User::findOrFail($request->student_id);
        $student->teachers()->detach($request->teacher_id);
        
        return response()->json(['success' => 'Teacher removed']);
    }
    
    /**
     * Remove parent-student relationship
     */
    public function removeParent(Request $request)
    {
        $student = User::findOrFail($request->student_id);
        $student->parents()->detach($request->parent_id);
        
        return response()->json(['success' => 'Parent removed']);
    }
}