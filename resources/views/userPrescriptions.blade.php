@extends('layout') <!-- Extend the master layout -->

@section('title', 'Prescriptions') <!-- Override the title section -->

@section('content')
<div class="main-content">
    <h2 class="text-success mb-4">All Prescriptions</h2>
   
    @if($prescriptions->isEmpty())
        <p>No prescriptions found.</p>
    @else
        <div class="table-responsive">
        <table class="table table-responsive table-striped table-success">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Prescription Image</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prescriptions as $prescription)
                        <tr>
                            <td>{{ $prescription->id }}</td>
                            <td>{{ $prescription->created_at->format('Y-m-d') }}
                            <td>
                                <img src="{{ asset( $prescription->image) }}" alt="Prescription Image" style="width: 100px; height: 100px;">
                            </td>
                            <td>{{ $prescription->status == 1 ? 'done' : 'pending' }}</td>
                            <td>
                            @if($prescription->status == 1)
                                <!-- If the status is 1 (active), show the "view medicines" button -->
                                <a href="{{ route('admin.medicines.show', $prescription->id) }}" class="btn btn-success">View Medicines</a>
                            @else
                                <!-- If the status is not 1 (inactive), show the "add" button -->
                                <a href="{{ route('admin.prescription.show', $prescription->id) }}" class="btn btn-success">Add</a>
                            @endif
                        </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

