@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ __('Specialist Registration Form') }}</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ url('/form') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>{{ __('First Name') }}</label>
            <input type="text" name="first_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>{{ __('Middle Name') }}</label>
            <input type="text" name="middle_name" class="form-control">
        </div>

        <div class="mb-3">
            <label>{{ __('Last Name') }}</label>
            <input type="text" name="last_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>{{ __('Country') }}</label>
            <select name="country" class="form-control" required>
                <option value="">{{ __('Choose...') }}</option>
                <option>Kuwait</option>
                <option>Saudi Arabia</option>
                <option>UAE</option>
                <option>Qatar</option>
                <option>Bahrain</option>
                <option>Oman</option>
            </select>
        </div>

        <div class="mb-3">
            <label>{{ __('Major/Specialist') }}</label>
            <input type="text" name="major" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>{{ __('Interest') }}</label>
            <textarea name="interest" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label>{{ __('Research Papers') }}</label>
            <textarea name="research_papers" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label>{{ __('Upload Resume') }}</label>
            <input type="file" name="resume" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </form>
</div>
@endsection
