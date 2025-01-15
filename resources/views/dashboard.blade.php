@extends('layout') <!-- Extend the master layout -->

@section('title', 'Home Page') <!-- Override the title section -->

@section('content')
<div class="main-content">
        <!-- Dashboard Content -->
        <div class="container mt-4">
            <h1>Welcome to the Admin Dashboard</h1>
            <p>Use the sidebar to navigate through different sections.</p>

            <!-- Example Cards -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Users</h5>
                            <p class="card-text">Manage your users here.</p>
                            <a href="#" class="btn btn-green">Go to Users</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">medicines</h5>
                            <p class="card-text">View system reports.</p>
                            <a href="#" class="btn btn-green">View Reports</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Settings</h5>
                            <p class="card-text">Update system settings.</p>
                            <a href="#" class="btn btn-green">Go to Settings</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection