@extends('layout') <!-- Extend the master layout -->

@section('title', 'Home Page') <!-- Override the title section -->

@section('content')
<div class="main-content">

    <!-- Dashboard Content -->
    <div class="container mt-4">
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                        <i class="fa-solid fa-users"></i>
                            Users</h5>
                        <p class="card-text">Manage your users here.</p>
                        <a href="{{ route('admin.users') }}" class="btn btn-green">Go to Users</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                        <i class="fa-regular fa-file-powerpoint"></i>
                            Prescriptions</h5>
                        <p class="card-text">Manage prescriptions here.</p>
                        <a href="{{ route('admin.prescriptions.history') }}" class="btn btn-green">View Prescriptions</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
