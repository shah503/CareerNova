<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Services\CsvImportService;
use App\Models\Subject;
use Illuminate\Http\Request;

class CsvImportController extends Controller
{
    protected $csvImportService;

    public function __construct(CsvImportService $csvImportService)
    {
        $this->csvImportService = $csvImportService;
    }

    /**
     * Show CSV import form
     */
    public function showForm()
    {
        $subjects = Subject::where('status', 'active')->get();
        return view('import.csv-form', compact('subjects'));
    }

    /**
     * Import CSV file
     */
    public function import(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'csv_file' => 'required|file|mimes:csv,txt|max:5120', // 5MB max
        ]);

        $results = $this->csvImportService->import(
            $request->file('csv_file'),
            $validated['subject_id'],
            auth()->id()
        );

        return view('import.results', compact('results'));
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $csvContent = $this->csvImportService->getCsvTemplate();

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, 'mcq-template.csv');
    }
}