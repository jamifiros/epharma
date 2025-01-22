@extends('layout')

@section('title', 'Prescription Details')

@section('content')
<div class="main-content">
    <h2 class="text-success mb-4">Prescription Details</h2>

    <div class="row">
        <!-- Left Side: Prescription Image -->
        <div class="col-md-6">
            <img src="{{ asset($prescription->image) }}" alt="Prescription Image" class="img-fluid rounded">
        </div>

        <!-- Right Side: Medicine Form -->
        <div class="col-md-6">
            <form action="{{ route('admin.medicines.store', $prescription->id) }}" method="POST"
                style="border: 1px solid #ddd;padding:20px">
                @csrf
                <div class="container-fluid">
                    <h3>Add Medicines</h3>
                </div>
                <div id="medicineFormsContainer" style="height: 400px; overflow-y: auto; padding: 10px;">
                    <!-- Initial Medicine Form -->
                    <div class="medicine-form card p-3 mb-3">
                        <div class="mb-3">
                            <label for="medicineName" class="form-label">Medicine Name</label>
                            <select class="form-control" name="medicine_id[]" required>
                                @if($stocks->isNotEmpty())
                                    @foreach($stocks as $stock)
                                        <option value="{{ $stock->id }}" @if($stock->stockdetails->quantity < 1)
                                        disabled @endif>
                                            {{ $stock->medicine_name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option disabled>--no medicines in stock--</option>
                                @endif
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Regime</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="regime[morning][]" value="morning">
                                <label class="form-check-label">Morning</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="regime[afternoon][]" value="afternoon">
                                <label class="form-check-label">Afternoon</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="regime[evening][]" value="evening">
                                <label class="form-check-label">Evening</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="regime[night][]" value="night">
                                <label class="form-check-label">Night</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="days" class="form-label">Days</label>
                            <input type="number" class="form-control" name="days[]" placeholder="Days" required>
                        </div>

                        <div class="mb-3">
                            <label for="mealTime" class="form-label">Timing</label>
                            <select name="meal_time[]" class="form-select" required>
                                <option value="" disabled selected>Select Meal Timing</option>
                                <option value="before_meals">Before Meals</option>
                                <option value="after_meals">After Meals</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="btn-div d-flex" style="justify-content:right">
                    <button id="addMedicineBtn" class="btn btn-primary mb-2">
                        + Add more Medicine
                    </button>
                </div>
                <button type="submit" class="btn btn-success w-100 m-3 px-5">Save</button>
            </form>
        </div>
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

        // Clear input fields in the cloned form
        newMedicineForm.querySelectorAll('input').forEach(input => {
            if (input.type === 'checkbox') {
                input.checked = false;
            } else {
                input.value = '';
            }
        });

        newMedicineForm.querySelector('select').selectedIndex = 0; // Reset the select box

        // Append the cloned form to the container
        medicineFormsContainer.appendChild(newMedicineForm);

        // Scroll to the newly created form
        newMedicineForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
</script>
@endsection
