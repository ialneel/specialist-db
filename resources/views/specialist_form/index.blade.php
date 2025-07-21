@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Submitted Specialist Forms</h1>

    <!-- Filter Form -->
    <form method="GET" action="{{ url('/forms') }}" class="mb-4">
        <div style="margin-bottom: 10px;">
            <input type="text" name="country" placeholder="Search by country" value="{{ request('country') }}">
            <input type="text" name="major" placeholder="Search by major" value="{{ request('major') }}">
            <input type="text" name="interest" placeholder="Search by interest" value="{{ request('interest') }}">
            <button type="submit">Filter</button>
        </div>
    </form>

    <!-- Export All CSV Button -->
    <a href="{{ url('/forms/export') . '?' . http_build_query(request()->query()) }}">
        <button type="button">Export All (CSV)</button>
    </a>

    <!-- Bulk Actions Form -->
   <!-- Bulk Action Form -->
<form method="POST" action="{{ url('/forms/bulk-action') }}">
    @csrf
    <div style="margin-bottom: 10px;">
        <button type="submit" name="action" value="export">Export Selected</button>
        <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure you want to delete the selected records?')">Delete Selected</button>
    </div>

    <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Country</th>
                <th>Major</th>
                <th>Interest</th>
                <th>Resume</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($forms as $form)
                <tr>
                    <td><input type="checkbox" name="selected_forms[]" value="{{ $form->id }}"></td>
                    <td>{{ $form->first_name }}</td>
                    <td>{{ $form->middle_name }}</td>
                    <td>{{ $form->last_name }}</td>
                    <td>{{ $form->country }}</td>
                    <td>{{ $form->major }}</td>
                    <td>{{ $form->interest }}</td>
                    <td>
                        @if($form->resume_path)
                            <a href="{{ Storage::url($form->resume_path) }}" target="_blank">Download</a>
                        @else
                            No Resume
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $forms->links() }}
    </div>
</form>

<!-- Select all script -->
<script>
    document.getElementById('select-all').addEventListener('change', function (e) {
        const checkboxes = document.querySelectorAll('input[name="selected_forms[]"]');
        checkboxes.forEach(cb => cb.checked = e.target.checked);
    });
</script>

@endsection
