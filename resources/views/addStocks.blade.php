@extends('layout')

@section('title', 'Prescription Details')

@section('content')
<div class="main-content">
    <h2 class="text-success mb-4">Prescription Details</h2>

    <div class="container">
        <!-- Right Side: Medicine Form -->
        <form action="{{ route('admin.stocks.create') }}" method="POST" style="border: 1px solid #ddd;padding:20px">
            @csrf
            <div class="container-fluid">
                <h3>Add stocks</h3>
            </div>
            <div id="medicineFormsContainer" style="height: 400px; overflow-y: auto; padding: 10px;">
                <!-- Initial Medicine Form -->
                <div class="medicine-form card p-3 mb-3">
                    <div class="mb-3">
                        <label for="medicineName" class="form-label">Medicine Name</label>
                        <input type="text" class="form-control" name="medicine_name[]" placeholder="Enter Medicine Name"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="medicineName" class="form-label">MRP</label>
                        <input type="number" class="form-control" name="MRP[]" placeholder="Enter MRP"
                            required>
                    </div>

                </div>
            </div>

            <div class="btn-div d-flex" style="justify-content:right">
                <button id="addMedicineBtn" class="btn btn-primary mb-2">
                    + Add more Medicine
                </button>
            </div>
            <button type="submit" class="btn btn-success w-100 m-3 px-5">save</button>
        </form>
    </div>
</div>

<!-- Script for Adding More Forms -->
<script>
    document.getElementById('addMedicineBtn').addEventListener('click', function (e) {
        e.preventDefault(); // Prevent form submission on button click
        const medicineFormsContainer = document.getElementById('medicineFormsContainer');

        // Clone the first medicine form
        const firstMedicineForm = medicineFormsContainer.querySelector('.medicine-form');
        const newMedicineForm = firstMedicineForm.cloneNode(true);

        newMedicineForm.querySelector('select').selectedIndex = 0; // Reset the select box

        // Append the cloned form to the container
        medicineFormsContainer.appendChild(newMedicineForm);

        // Scroll to the newly created form
        newMedicineForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
</script>
@endsection