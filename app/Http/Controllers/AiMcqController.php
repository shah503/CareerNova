<?php

namespace App\Http\Controllers;

use App\Models\AiMcqGeneration;
use App\Models\Mcq;
use App\Services\AiMcqGenerationService;
use App\Services\McqValidationService;
use Illuminate\Http\Request;

class AiMcqController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Show AI MCQ generation form
     */
    public function generateForm()
    {
        $classes = ['1st Year', '2nd Year', '3rd Year', '4th Year'];
        $subjects = ['Biology', 'Chemistry', 'Physics', 'English', 'Analytical Reasoning'];
        $difficulties = ['Easy', 'Medium', 'Hard'];

        return view('admin.ai-mcq.generate', compact('classes', 'subjects', 'difficulties'));
    }

    /**
     * Generate MCQs
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'class' => 'required|string',
            'subject' => 'required|string',
            'book' => 'nullable|string',
            'chapter' => 'required|string',
            'topic' => 'nullable|string',
            'difficulty' => 'required|in:Easy,Medium,Hard',
            'count' => 'required|integer|min:1|max:50'
        ]);

        $result = AiMcqGenerationService::generateMcqs(
            auth()->id(),
            $validated['class'],
            $validated['subject'],
            $validated['book'],
            $validated['chapter'],
            $validated['topic'],
            $validated['difficulty'],
            $validated['count']
        );

        if ($result['success']) {
            return redirect()->route('admin.ai-mcq.review', $result['generation_id'])
                ->with('success', "Generated {$result['count']} MCQs");
        }

        return back()->with('error', $result['error']);
    }

    /**
     * Review generated MCQs
     */
    public function review($generationId)
    {
        $generation = AiMcqGeneration::findOrFail($generationId);

        if ($generation->admin_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $mcqs = $generation->generated_mcqs ?? [];

        return view('admin.ai-mcq.review', compact('generation', 'mcqs'));
    }

    /**
     * Approve & save MCQ
     */
    public function approveMcq(Request $request)
    {
        $validated = $request->validate([
            'generation_id' => 'required|exists:ai_mcq_generations,id',
            'mcq_index' => 'required|integer',
            'question' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_option' => 'required|in:A,B,C,D',
            'explanation' => 'required|string',
            'cognitive_level' => 'required|string',
        ]);

        $generation = AiMcqGeneration::findOrFail($validated['generation_id']);

        // Create MCQ
        $mcq = Mcq::create([
            'teacher_id' => auth()->id(),
            'class' => $generation->class,
            'subject' => $generation->subject,
            'book' => $generation->book,
            'chapter' => $generation->chapter,
            'topic' => $generation->topic,
            'question' => $validated['question'],
            'option_a' => $validated['option_a'],
            'option_b' => $validated['option_b'],
            'option_c' => $validated['option_c'],
            'option_d' => $validated['option_d'],
            'correct_option' => $validated['correct_option'],
            'explanation' => $validated['explanation'],
            'difficulty' => $generation->difficulty,
            'cognitive_level' => $validated['cognitive_level'],
            'ai_generated' => true,
            'verified' => true,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'confidence_score' => 0.95
        ]);

        // Run validations
        McqValidationService::validateComplete([
            'question' => $validated['question'],
            'option_a' => $validated['option_a'],
            'option_b' => $validated['option_b'],
            'option_c' => $validated['option_c'],
            'option_d' => $validated['option_d'],
            'correct_option' => $validated['correct_option'],
            'explanation' => $validated['explanation'],
        ], $mcq->id);

        return response()->json(['success' => true, 'mcq_id' => $mcq->id]);
    }

    /**
     * Reject MCQ
     */
    public function rejectMcq(Request $request)
    {
        // Log rejection for analytics
        return response()->json(['success' => true]);
    }

    /**
     * View generations history
     */
    public function history()
    {
        $generations = AiMcqGeneration::where('admin_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('admin.ai-mcq.history', compact('generations'));
    }
}