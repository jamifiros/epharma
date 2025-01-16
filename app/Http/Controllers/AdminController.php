<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\Prescription;
use App\Models\Medicine;
use App\Models\MedicineIntake;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
{

    // Pass the data to the view
    return view('dashboard');
}

    public function index()
    {
        // Fetch all users with the role 'user' from the users table
        $users = User::where('role', 'user')->get();

        // Pass the users to the view
        return view('users', compact('users'));
    }

    // Show details of a specific user
    public function show($id)
    {
        // Fetch user details using the user ID
        $userDetails = UserDetails::find($id);

        // If user details not found, return a 404 response
        if (!$userDetails) {
            return redirect()->route('admin.users')->with('error', 'User not found.');
        }

        // Return the view with the user details
        return view('userdetails', compact('userDetails'));
    }

    // Delete a specific user's details
    public function destroy($id)
    {
        // Begin transaction to ensure data consistency
        DB::beginTransaction();

        try {
            // Find the user by ID
            $user = User::find($id);

            if ($user) {
                // Delete related user details from the 'user_details' table
                $userDetails = $user->userDetails;
                if ($userDetails) {
                    $userDetails->delete();
                }

                // Add more deletions for other related tables as needed
                // Example: Delete from another table where 'userid' is a foreign key
                // Example: $user->projects()->delete(); // if there are related projects

                // Finally, delete the user from the 'users' table
                $user->delete();

                // Commit the transaction
                DB::commit();

                // Redirect with success message
                return redirect()->route('admin.users')->with('success', 'User and related data deleted successfully.');
            }

            return redirect()->route('admin.users')->with('error', 'User not found.');

        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();
            return redirect()->route('admin.users')->with('error', 'An error occurred while deleting the user.');
        }
    }

    public function allMedicines()
{
    // Fetch all medicines with their related prescription and user data
    $medicines = Medicine::with('prescription.user')->get();

    // Pass the data to the view
    return view('Allmedicines', compact('medicines'));
}
    public function showPrescriptions()
    {
        // Fetch prescriptions with a status of 0 and their related user data
        $prescriptions = Prescription::with('user')
            ->where('status', '0')
            ->get();
    
        return view('prescriptions', compact('prescriptions'));
    }

    public function showPrescriptionsHistory()
    {
        // Fetch prescriptions with a status of 0 and their related user data
        $prescriptions = Prescription::with('user')
            ->where('status', '1')
            ->get();
    
        return view('prescriptionhistory', compact('prescriptions'));
    }
    
    

public function showMedicineAddForm($id)
{
    $prescription = Prescription::findOrFail($id);
    return view('addMedicines', compact('prescription'));
}

public function store(Request $request, $prescriptionId)
{
    // Validate the incoming request
    $request->validate([
        'medicine_name.*' => 'required|string|max:255',
        'meal_time.*' => 'required|string',
        'days' => 'required|array', // Ensure that 'days' is an array
        'days.*' => 'required|integer', // Each day field must be an integer
    ]);

    // Loop through the medicines and save each one
    foreach ($request->medicine_name as $index => $medicineName) {
        // Get the number of days for the current medicine
        $days = $request->days[$index];

        // Calculate the total count based on the doses per day
        $totalCount = 0;
        $totalCount += isset($request->regime['morning'][$index]) ? 1 : 0;
        $totalCount += isset($request->regime['afternoon'][$index]) ? 1 : 0;
        $totalCount += isset($request->regime['evening'][$index]) ? 1 : 0;
        $totalCount += isset($request->regime['night'][$index]) ? 1 : 0;

        // Multiply the total count by the number of days
        $totalCount *= $days;

        // Save the medicine to the database
        Medicine::create([
            'prescription_id' => $prescriptionId,
            'medicine_name' => $medicineName,
            'morning' => isset($request->regime['morning'][$index]) ? true : false,
            'afternoon' => isset($request->regime['afternoon'][$index]) ? true : false,
            'evening' => isset($request->regime['evening'][$index]) ? true : false,
            'night' => isset($request->regime['night'][$index]) ? true : false,
            'timing' => $request->meal_time[$index],
            'total_count' => $totalCount,  // Set the total count
        ]);
    }

    // Update Prescription Status
    Prescription::where('id', $prescriptionId)->update(['status' => '1']);

    // Redirect to the prescriptions route with a success message
    return redirect()
        ->route('admin.prescriptions')
        ->with('success', 'Medicines added successfully!');
}


public function showMedicines($prescriptionId)
{
    // Fetch the prescription with the related medicines
    $prescription = Prescription::with('medicines')->find($prescriptionId);

    // Check if the prescription exists
    if (!$prescription) {
        return redirect()->route('admin.prescriptions')->with('error', 'Prescription not found.');
    }

    // Return the view with the prescription and associated medicines
    return view('medicines', compact('prescription'));
}

public function prescriptions($id)
{
    // Fetch all prescriptions for the user
    $prescriptions = Prescription::where('userid', $id)->get();

    // Pass the prescriptions to the view
    return view('userPrescriptions', compact('prescriptions'));
}

public function showUserMedicines($userId)
{
    // Fetch the user along with their prescriptions and medicines
    $user = User::with('prescriptions.medicines')->findOrFail($userId);

    // Check if the user has prescriptions and medicines in those prescriptions
    $medicines = [];
    foreach ($user->prescriptions as $prescription) {
        foreach ($prescription->medicines as $medicine) {
            $medicines[] = $medicine;
        }
    }

    // Return the data to the view
    return view('usermedicines', compact('user', 'medicines'));
}



}
