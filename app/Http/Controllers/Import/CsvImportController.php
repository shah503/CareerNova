<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Services\CsvImportService;
use Illuminate\Http\Request;

class CsvImportController extends Controller
{
    protected $csvImportService;

    public function __construct(CsvImportService $csvImportService)
    {
        $this->csvImportService = $csvImportService;
        
    }

    /**
     * Show import form
     */
    public function showForm()
    {
        $subjects = Subject::where('status', 'active')->get();
        return view('import.csv-form', compact('subjects'));
    }

    /**
     * Handle CSV import
     */
    public function import(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'csv_file' => 'required|file|mimes:csv,txt',
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