@extends('layout') <!-- Extend the master layout -->

@section('title', 'Prescriptions') <!-- Override the title section -->

@section('content')
<div class="main-content">
    <h2 class="text-success mb-4">Prescriptions</h2>

    @if($prescriptions->isEmpty())
        <p>No prescriptions found.</p>
    @else
        <div class="table-responsive">
        <table class="table table-responsive table-striped table-success">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>User Name</th>
                        <th>Prescription Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prescriptions as $prescription)
                        <tr>
                            <td>{{ $prescription->id }}</td>
                            <td>{{ $prescription->created_at->format('Y-m-d') }}
                            <td>{{ $prescription->user->name }}</td> <!-- Display the user's name -->
                            <td>
                                <img src="{{ asset( $prescription->image) }}" alt="Prescription Image" style="width: 100px; height: 100px;">
                            </td>
                            <td>
                            <a href="{{ route('admin.prescription.show', $prescription->id) }}" class="btn btn-success">add</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
