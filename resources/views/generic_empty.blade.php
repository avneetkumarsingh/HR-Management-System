@extends('layouts.app')
@section('title', $title)

@section('content')
<div class="card">
    <div class="card-header" style="border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1rem;">
        <h3 class="card-title" style="margin: 0; display:flex; align-items:center; gap:0.5rem">
            <i class="fas {{ $icon }}"></i> {{ $title }}
        </h3>
    </div>
    <div class="card-body" style="text-align: center; padding: 4rem 2rem;">
        <div style="font-size: 3rem; color: var(--border); margin-bottom: 1.5rem;">
            <i class="fas {{ $icon }}"></i>
        </div>
        <h2 style="margin-bottom: 1rem; color: var(--text);">{{ $message }}</h2>
        <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto 2rem;">
            You currently have no pending {{ strtolower($title) }} or active items to manage in this module right now. Any new incoming data will automatically populate here.
        </p>
        <button class="btn btn-primary" onclick="history.back()"><i class="fas fa-arrow-left"></i> Go Back</button>
    </div>
</div>
@endsection
