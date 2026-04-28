@extends('layouts.app')
@section('title', 'Coming Soon')

@section('content')
<div class="card" style="text-align:center; padding:5rem 2rem;">
    <div style="font-size:4rem; color:var(--primary); margin-bottom:1rem;">
        </div>
    <h2 style="font-weight:600; font-size:1.5rem; margin-bottom:0.5rem; color:var(--text)">Module Under Construction</h2>
    <p style="color:var(--text-muted); max-width:500px; margin:0 auto 2rem auto;">
        We are rapidly building Keka's expansive feature set. This specific module is currently deep in development and will be released in an upcoming milestone.
    </p>
    <button onclick="window.history.back()" class="btn btn-primary" style="padding:0.75rem 2rem; font-size:1rem;">
        Go Back
    </button>
</div>
@endsection
