@extends('layouts.app')
@section('title', 'Leave Types')

@section('content')
<div class="grid grid-cols-3 gap-6">
    <div class="card" style="grid-column: span 1">
        <div class="card-header"><h3 class="card-title">Add New Type</h3></div>
        <div class="card-body">
            <form action="{{ route('leaves.types.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Code</label>
                    <input type="text" name="code" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Days Allowed</label>
                    <input type="number" name="days_allowed" class="form-input" required>
                </div>
                <div class="form-group flex justify-between items-center mb-4">
                    <label>Is Paid Leave?</label>
                    <input type="hidden" name="is_paid" value="0">
                    <input type="checkbox" name="is_paid" value="1">
                </div>
                <div class="form-group flex justify-between items-center mb-4">
                    <label>Carry Forward?</label>
                    <input type="hidden" name="carry_forward" value="0">
                    <input type="checkbox" name="carry_forward" value="1">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Add Type</button>
            </form>
        </div>
    </div>

    <div class="card" style="grid-column: span 2">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Code</th>
                        <th>Days</th>
                        <th>Properties</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($types as $type)
                    <tr>
                        <td>{{ $type->name }}</td>
                        <td>{{ $type->code }}</td>
                        <td>{{ $type->days_allowed }}</td>
                        <td>
                            @if($type->is_paid) <span class="badge badge-success">Paid</span> @else <span class="badge badge-danger">Unpaid</span> @endif
                            @if($type->carry_forward) <span class="badge badge-info">Carry Fwd</span> @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
