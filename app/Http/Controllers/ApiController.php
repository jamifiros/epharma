<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\Prescription;
use App\Models\Medicine;
use App\Models\MedicineIntake;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\GuardianMail;
use App\Jobs\SendLowStockEmail;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


use Illuminate\Http\Request;

class ApiController extends Controller
{
    
public function login(Request $request)
{
    Log::info('Login function hit.');
    Log::info('Request Data:', $request->all());
    // Validate the input
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    Log::info('validate success.');
    // Find the user by email
    $user = User::where('email', $request->email)->first();

    if ($user && Hash::check($request->password, $user->password)) {
        if ($user->role === 'user') {
            // Generate API token for the user
            $token = $user->createToken('UserToken')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
            ], 200);
        } else {
            // Role is not 'user', deny access
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied',
            ], 403);
        }
    }

    // If authentication fails
    return response()->json([
        'status' => 'error',
        'message' => 'Invalid credentials',
    ], 401);
}

public function logout(Request $request)
{
    // Revoke user's tokens
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Logout successful',
    ], 200);
}

public function register(Request $request)
{
    Log::info('reg function hit.');
    Log::info('Request Data:', $request->all());

    $request->validate([ 
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:4',
        'guardian_name' => 'required|string|max:255',
        'guardian_email' => 'required|email',
        'idproof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        'place' => 'required|string|max:255',
        'district' => 'required|string|max:255',
        'mobile_no' => 'required|string|max:15',
    ]);

    try {
      // Handle ID proof upload manually
      $idProof = $request->file('idproof');
      $idProofName = time() . '_' . $idProof->getClientOriginalName(); // Unique file name
      $destinationPath = public_path('assets/idproofs'); // Path to public/assets/idproofs
      $idProof->move($destinationPath, $idProofName); // Move the file to the destination
     
      
      $user = new User();
      $user->name = $request->name;
      $user->email = $request->email;
      $user->password = Hash::make($request->password);
      $user->role = 'user';
      $user->save();

      $userDetails = new UserDetails();
        $userDetails->userid = $user->id; // foreign key referencing the user's id
        $userDetails->guardian_name = $request->guardian_name;
        $userDetails->guardian_email = $request->guardian_email;
        $userDetails->idproof =  'assets/idproofs/' . $idProofName;
        $userDetails->place = $request->place;
        $userDetails->district = $request->district;
        $userDetails->mobile_no = $request->mobile_no;
        $userDetails->save();
// Return success response with 201 Created status
return response()->json([
    'status' => 'success',
    'message' => 'User registered successfully',
    'user' => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
    ],
], 201); // Correct status code: 201 Created

} catch (\Exception $e) {
// Rollback transaction in case of error
DB::rollBack();

// Log the error message for debugging purposes
\Log::error("Registration error: " . $e->getMessage());

return response()->json([
    'status' => 'error',
    'message' => 'Registration failed',
    'error' => $e->getMessage(),
], 500); // Internal server error
}


}



public function addPrescription(Request $request)
{
    Log::info('function hit.');
    Log::info('Request Data:', $request->all());
    // Validate request data
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif', // Validates an image file
    ]);

    // Get the authenticated user's ID
    $userId = Auth::id();

    // Handle image upload
    $image = $request->file('image');
    $imageName = time() . '_' . $image->getClientOriginalName(); // Unique file name
    $destinationPath = public_path('assets/prescriptions'); // Path to public/assets/prescriptions
    $image->move($destinationPath, $imageName); // Move the file to the destination

    // Create prescription
    $prescription = Prescription::create([
        'userid' => $userId, // Directly use the ID from the `users` table
        'image' => 'assets/prescriptions/' . $imageName, // Store relative path in DB
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Prescription added successfully',
        'data' => $prescription,
    ], 201);
}

public function viewPrescriptions()
{
    // Get the authenticated user's ID
    $userId = Auth::id();

    // Fetch prescriptions for the authenticated user
    $prescriptions = Prescription::where('userid', $userId)->get();

    // Check if the user has any prescriptions
    if ($prescriptions->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'No prescriptions found for this user.',
        ], 404);
    }

    // Add full image URL to each prescription
    foreach ($prescriptions as $prescription) {
        // Assuming the image path is stored as 'assets/prescriptions/p1.jpg'
        $prescription->image_url = asset($prescription->image);
    }

    // Return prescriptions with full image URLs as a JSON response
    return response()->json([
        'status' => 'success',
        'message' => 'Prescriptions retrieved successfully.',
        'data' => $prescriptions,
    ], 200);
}


public function deletePrescription($prescriptionId)
{
    // Get the authenticated user
    $user = Auth::user();

    // Check if the user exists
    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'User not found',
        ], 404);
    }

    // Find the prescription by ID and ensure it belongs to the authenticated user
    $prescription = $user->prescriptions()->where('id', $prescriptionId)->first();

    if (!$prescription) {
        return response()->json([
            'status' => 'error',
            'message' => 'Prescription not found or does not belong to the authenticated user',
        ], 404);
    }

    try {
        // Delete the prescription image file if it exists
        $imagePath = public_path("assets/prescriptions/{$prescription->image}");

        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        // Delete the prescription and its associated medicines
        $prescription->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Prescription, associated medicines, and image deleted successfully',
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to delete prescription. Please try again later.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function viewMedicines()
{
    // Get the authenticated user
    $user = Auth::user();

    // Check if the user exists
    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'User not found',
        ], 404);
    }

    // Get the prescriptions for the authenticated user with related medicines
    $prescriptions = $user->prescriptions()->with('medicines')->get();

    // Check if prescriptions exist
    if ($prescriptions->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'No prescriptions found for the user',
        ], 404);
    }

    // Filter prescriptions with medicines
    $prescriptionsWithMedicines = $prescriptions->filter(function ($prescription) {
        return $prescription->medicines->isNotEmpty();
    });

    // If no prescriptions have medicines, return a message
    if ($prescriptionsWithMedicines->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Your prescription is pending to add medicines',
        ], 404);
    }

    // Initialize messages for low stock medicines
    $lowStockMessages = [];

    // Check stock levels for each medicine
    foreach ($prescriptionsWithMedicines as $prescription) {
        foreach ($prescription->medicines as $medicine) {
            // Calculate the per-day requirement
            $perDay = ($medicine->morning ? 1 : 0) +
                      ($medicine->afternoon ? 1 : 0) +
                      ($medicine->evening ? 1 : 0) +
                      ($medicine->night ? 1 : 0);

            // Check if total stock is less than the daily requirement
            if ($medicine->total_count < $perDay) {
                $lowStockMessages[] = "{$medicine->medicine_name}";
            }
        }
    }

    // Return the prescriptions with a low stock message (if any)
    return response()->json([
        'status' => 'success',
        'message' => 'Prescriptions retrieved successfully.',
        'low_stock_messages' => $lowStockMessages, // Add messages for low stock medicines
        'data' => $prescriptionsWithMedicines->values(), // Reset indices of the collection
    ], 200);
}




// public function addMedicineIntake(Request $request)
// {
//     \Log::info('add medicine intake hitted');
//     // Validate the incoming request for multiple medicine intake data
//     $validator = \Validator::make($request->all(), [
//         'medicines' => 'required|array',  // Validate that the 'medicines' field is an array
//         'medicines.*.medicine_id' => 'required|exists:medicines,id',  // Validate each medicine_id
//         'medicines.*.quantity' => 'required|integer',  // Validate the quantity for each medicine (min:1)
//     ]);

//     // If validation fails, log the errors and return a response
//     if ($validator->fails()) {
//         // Log the validation errors
//         Log::error('Validation error in addMedicineIntake', [
//             'errors' => $validator->errors()->toArray(),
//             'request_data' => $request->all()
//         ]);

//         // Return validation error response
//         return response()->json([
//             'status' => 'error',
//             'message' => 'Validation failed',
//             'errors' => $validator->errors(),
//         ], 422);  // HTTP status code for validation errors
//     }

//     // Get the authenticated user
//     $user = Auth::user();

//     // Initialize an empty array to hold successfully created intakes
//     $successfullyCreated = [];

//     // Loop through each medicine intake data
//     foreach ($request->medicines as $medicineData) {
//         // If the quantity is 0, skip processing this medicine intake
//         if ($medicineData['quantity'] == 0) {
//             continue;
//         }

//         // Find the medicine by its ID
//         $medicine = Medicine::find($medicineData['medicine_id']);

//         // Check if medicine exists
//         if (!$medicine) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => "Medicine with ID {$medicineData['medicine_id']} not found",
//             ], 404);
//         }

//         // Check if there is enough quantity of the medicine available
//         if ($medicine->total_count < $medicineData['quantity']) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => "Not enough stock for medicine ID {$medicineData['medicine_id']}",
//             ], 400);
//         }

//         // Create the medicine intake record
//         $medicineIntake = new MedicineIntake();
//         $medicineIntake->user_id = $user->id;  // Associate the medicine intake with the authenticated user
//         $medicineIntake->medicine_id = $medicineData['medicine_id'];  // Associate the medicine with the intake
//         $medicineIntake->count = $medicineData['quantity'];  // Set the quantity of the medicine taken
//         $medicineIntake->save();  // Save the intake record

//         // Decrease the available count of the medicine
//         $medicine->total_count -= $medicineData['quantity'];
//         $medicine->save();  // Save the updated medicine count

//         // Add the successfully created intake record to the array
//         $successfullyCreated[] = $medicineIntake;
//     }
    

//     // Return a success response
//     return response()->json([
//         'status' => 'success',
//         'message' => 'Medicine intake records created successfully',
//         'data' => $successfullyCreated,
//     ], 201);
// }

public function addMedicineIntake(Request $request)
{
    \Log::info('Add medicine intake process started.');

    // Validate the incoming request for multiple medicine intake data
    $validator = \Validator::make($request->all(), [
        'medicines' => 'required|array',
        'medicines.*.medicine_id' => 'required|exists:medicines,id',
        'medicines.*.quantity' => 'required|integer|min:1',
    ]);

    if ($validator->fails()) {
        \Log::error('Validation error in addMedicineIntake', ['errors' => $validator->errors()]);
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }

    $user = Auth::user();
    $successfullyCreated = [];

    // Use a transaction for database operations
    DB::beginTransaction();
    try {
        foreach ($request->medicines as $medicineData) {
            $medicine = Medicine::find($medicineData['medicine_id']);

            // Check stock availability
            if ($medicine->total_count < $medicineData['quantity']) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Not enough stock for medicine ID {$medicineData['medicine_id']}",
                ], 400);
            }

            // Create intake record and update stock
            $medicineIntake = new MedicineIntake([
                'user_id' => $user->id,
                'medicine_id' => $medicine->id,
                'count' => $medicineData['quantity'],
            ]);
            $medicineIntake->save();

            $medicine->total_count -= $medicineData['quantity'];
            $medicine->save();

            $successfullyCreated[] = $medicineIntake;
        }

        // Commit the transaction
        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error in addMedicineIntake', ['error' => $e->getMessage()]);
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to add medicine intake. Please try again.',
        ], 500);
    }

    // Check for low stock medicines
    $lowStockMedicines = [];

    // Check if the user has prescriptions and medicines in those prescriptions
    if ($user->prescriptions) {
        foreach ($user->prescriptions as $prescription) {
            foreach ($prescription->medicines as $medicine) {
                // Calculate the per-day requirement
                $perDay = ($medicine->morning ? 1 : 0) +
                          ($medicine->afternoon ? 1 : 0) +
                          ($medicine->evening ? 1 : 0) +
                          ($medicine->night ? 1 : 0);

                // If the total count is less than 2 times the per-day requirement, consider it low stock
                if ($perDay > 0 && $medicine->total_count < (2 * $perDay)) {
                    $lowStockMedicines[] = $medicine;
                }
            }
        }
    } else {
        \Log::warning('No prescriptions found for user', ['user_id' => $user->id]);
    }

    // If low stock medicines are found, send the email
    if (!empty($lowStockMedicines)) {
        $guardianMail = $user->userDetails->guardian_email ?? null;

        if ($guardianMail) {
            $emailData = [
                'user_name' => $user->name,
                'low_stock_medicines' => $lowStockMedicines,
            ];

            // Log the email data
            \Log::info('Preparing to send low stock email', [
                'user_name' => $user->name,
                'low_stock_medicines' => $lowStockMedicines,
            ]);

            try {
                // Send the email
                Mail::to($guardianMail)->send(new GuardianMail($emailData));
                \Log::info('Low stock email sent successfully to guardian', ['email' => $guardianMail]);
            } catch (\Exception $e) {
                // Log the error
                \Log::error('Error sending low stock email', [
                    'error_message' => $e->getMessage(),
                    'guardian_email' => $guardianMail,
                    'user_name' => $user->name,
                ]);
            }
        } else {
            \Log::warning('Guardian email not found for user', ['user_id' => $user->id]);
        }
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Medicine intake records created successfully',
        'data' => $successfullyCreated,
    ], 201);
}


public function notifyGuardian()
{
    $user = Auth::user(); // Get the authenticated user
    $guardianMail = $user->userDetails->guardian_email;


    $emailData = [
        'user_name' => $user->name,
        'low_stock_medicines' => [
            (object) ['name' => 'Paracetamol', 'total_count' => 2],
            (object) ['name' => 'Ibuprofen', 'total_count' => 1],
        ],
    ];

   
    
    try {
        // Send the email
        Mail::to($guardianMail)->send(new GuardianMail($emailData));

        // Log success
        Log::info('Low stock email sent successfully', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'guardian_email' => $guardianMail,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Low stock alert email sent successfully.',
        ], 200);
    } catch (\Exception $e) {
        // Log the error
        Log::error('Error sending low stock email', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'guardian_email' => $guardianMail,
            'error_message' => $e->getMessage(),
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to send email.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
}
