@extends('layout')

@section('title', 'User Details')

@section('content')
    <div class="main-content mt-5">
        <div class="card">
            <div class="card-header">
            <h3>{{ $userDetails->user->name }}</h3>
            </div>
            <div class="card-body">
                    <div class="col-md-4">
                        <!-- Display ID Proof Image -->
                        <img src="{{ asset($userDetails->idproof) }}" class="img-fluid" alt="ID Proof Image">
                    </div>
                    <div class="col-md-8">
                        <!-- Display User Details -->
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Guardian Name:</strong> {{ $userDetails->guardian_name }}</li>
                            <li class="list-group-item"><strong>Guardian Email:</strong> {{ $userDetails->guardian_email }}</li>
                            <li class="list-group-item"><strong>Place:</strong> {{ $userDetails->place }}</li>
                            <li class="list-group-item"><strong>District:</strong> {{ $userDetails->district }}</li>
                            <li class="list-group-item"><strong>Mobile No:</strong> {{ $userDetails->mobile_no }}</li>
                        </ul>
                    </div>
            </div>
            <div class="card-footer text-right">
            <a href="" class="btn btn-primary">view medicine intake</a>
                <a href="{{route('admin.user.prescriptions',$userDetails->userid)}}" class="btn btn-success">view prescriptions history</a>
                <!-- Delete Button -->
                <form action="{{ route('admin.userdetails.destroy', $userDetails->userid) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete user</button>
                </form>
            </div>
        </div>
    </div>
@endsection
