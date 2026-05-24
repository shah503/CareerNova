<?php

namespace App\Http\Controllers;

use App\Models\McqReport;
use App\Models\Mcq;
use Illuminate\Http\Request;

class McqReportController extends Controller
{
    /**
     * Submit report for MCQ
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mcq_id' => 'required|exists:mcqs,id',
            'issue_type' => 'required|in:wrong_answer,ambiguous_question,typo_grammar,duplicate_question,outdated_information,other',
            'description' => 'required|string|max:500',
            'suggested_correction' => 'nullable|string|max:500'
        ]);

        $report = McqReport::create([
            'user_id' => auth()->id(),
            'mcq_id' => $validated['mcq_id'],
            'issue_type' => $validated['issue_type'],
            'description' => $validated['description'],
            'suggested_correction' => $validated['suggested_correction'],
            'status' => 'pending'
        ]);

        // Increment report count on MCQ
        Mcq::find($validated['mcq_id'])->increment('report_count');

        // If report count exceeds threshold, mark for review
        $mcq = Mcq::find($validated['mcq_id']);
        if ($mcq->report_count >= 3) {
            $mcq->update(['needs_review' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Report submitted. Thank you for helping improve our content!'
        ]);
    }

    /**
     * Admin: Review reports
     */
    public function adminReview()
    {
        $this->middleware('admin');

        $reports = McqReport::where('status', 'pending')
            ->with('mcq', 'user')
            ->latest()
            ->paginate(15);

        return view('admin.mcq-reports.index', compact('reports'));
    }

    /**
     * Admin: Approve report & update MCQ
     */
    public function approve(Request $request, $reportId)
    {
        $validated = $request->validate([
            'question' => 'nullable|string',
            'option_a' => 'nullable|string',
            'option_b' => 'nullable|string',
            'option_c' => 'nullable|string',
            'option_d' => 'nullable|string',
            'correct_option' => 'nullable|in:A,B,C,D',
            'explanation' => 'nullable|string',
        ]);

        $report = McqReport::findOrFail($reportId);

        // Update MCQ if corrections provided
        if (!empty(array_filter($validated))) {
            $report->mcq->update($validated);
        }

        $report->update([
            'status' => 'accepted',
            'reviewed_by' => auth()->id(),
            'admin_notes' => 'Report accepted and MCQ updated if needed',
            'reviewed_at' => now()
        ]);

        return redirect()->back()->with('success', 'Report approved');
    }

    /**
     * Admin: Reject report
     */
    public function reject(Request $request, $reportId)
    {
        $validated = $request->validate([
            'reason' => 'required|string'
        ]);

        $report = McqReport::findOrFail($reportId);

        $report->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'admin_notes' => $validated['reason'],
            'reviewed_at' => now()
        ]);

        return redirect()->back()->with('success', 'Report rejected');
    }
}