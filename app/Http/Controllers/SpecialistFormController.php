<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SpecialistForm;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;


class SpecialistFormController extends Controller
{
    // Show the form
    public function create()
    {
        return view('specialist_form.create');
    }

    // Handle form submission
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'country' => 'required|string|in:Kuwait,Saudi Arabia,UAE,Qatar,Bahrain,Oman',
            'major' => 'required|string',
            'interest' => 'nullable|string',
            'research_papers' => 'nullable|string',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        // Handle resume upload
        if ($request->hasFile('resume')) {
            $validated['resume_path'] = $request->file('resume')->store('resumes', 'public');
        }

        $form = SpecialistForm::create($validated);

        // Optional: Send email to someone
        Mail::raw('New specialist form submitted.', function ($message) {
            $message->to('your-email@example.com') // Change to your recipient
                    ->subject('New Specialist Form');
        });

        return redirect()->back()->with('success', __('Form submitted successfully!'));
    }

    // Show all entries (read-only for now)
public function index(Request $request)
{
    $query = SpecialistForm::query();

    // Filtering
    if ($request->filled('first_name')) {
        $query->where('first_name', 'LIKE', '%' . $request->first_name . '%');
    }

    if ($request->filled('last_name')) {
        $query->where('last_name', 'LIKE', '%' . $request->last_name . '%');
    }

    if ($request->filled('country')) {
        $query->where('country', $request->country);
    }

    if ($request->filled('major')) {
        $query->where('major', $request->major);
    }

    if ($request->filled('interest')) {
        $query->where('interest', 'LIKE', '%' . $request->interest . '%');
    }

    if ($request->filled('start_date')) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }

    if ($request->filled('end_date')) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    // Sorting
    $sort = $request->get('sort', 'created_at');
    $direction = $request->get('direction', 'desc');

    if (!in_array($sort, ['first_name', 'last_name', 'country', 'major', 'created_at'])) {
        $sort = 'created_at';
    }

    if (!in_array($direction, ['asc', 'desc'])) {
        $direction = 'desc';
    }

    $query->orderBy($sort, $direction);

    // Pagination
    $forms = $query->paginate(10)->appends($request->query());

    return view('specialist_form.index', compact('forms'));
}


public function exportCsv()
{
    $forms = SpecialistForm::all();

    $response = new StreamedResponse(function () use ($forms) {
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['First Name', 'Middle Name', 'Last Name', 'Country', 'Major', 'Interest']);

        foreach ($forms as $form) {
            fputcsv($handle, [
                $form->first_name,
                $form->middle_name,
                $form->last_name,
                $form->country,
                $form->major,
                $form->interest
            ]);
        }

        fclose($handle);
    });

    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', 'attachment; filename="specialist_forms.csv"');

    return $response;
}


public function export(Request $request): StreamedResponse
{
    $query = SpecialistForm::query();

    if ($request->filled('country')) {
        $query->where('country', $request->country);
    }

    if ($request->filled('major')) {
        $query->where('major', $request->major);
    }

    if ($request->filled('interest')) {
        $query->where('interest', 'LIKE', '%' . $request->interest . '%');
    }

    $forms = $query->get();

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="specialist_forms.csv"',
    ];

    $callback = function () use ($forms) {
        $file = fopen('php://output', 'w');
        fputcsv($file, [
            'First Name', 'Middle Name', 'Last Name',
            'Country', 'Major', 'Interest', 'Resume'
        ]);

        foreach ($forms as $form) {
            fputcsv($file, [
                $form->first_name,
                $form->middle_name,
                $form->last_name,
                $form->country,
                $form->major,
                $form->interest,
                $form->resume_path ? url(Storage::url($form->resume_path)) : 'No Resume'
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

public function bulkAction(Request $request): \Symfony\Component\HttpFoundation\Response
{
    $action = $request->input('action');
    $selected = $request->input('selected_forms', []);

    if (empty($selected)) {
        return back()->with('error', 'No forms selected.');
    }

    if ($action === 'delete') {
        SpecialistForm::whereIn('id', $selected)->delete();
        return back()->with('success', 'Selected forms deleted.');
    }

    if ($action === 'export') {
        $forms = SpecialistForm::whereIn('id', $selected)->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="selected_specialist_forms.csv"',
        ];

        $callback = function () use ($forms) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['First Name', 'Middle Name', 'Last Name', 'Country', 'Major', 'Interest', 'Resume']);

            foreach ($forms as $form) {
                fputcsv($file, [
                    $form->first_name,
                    $form->middle_name,
                    $form->last_name,
                    $form->country,
                    $form->major,
                    $form->interest,
                    $form->resume_path ? url(Storage::url($form->resume_path)) : 'No Resume'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    return back()->with('error', 'Invalid action.');
}


}
