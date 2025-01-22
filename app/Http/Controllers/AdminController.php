<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\Prescription;
use App\Models\Medicine;
use App\Models\Stock;
use App\Models\StockDetails;
use App\Models\MedicineIntake;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Example data fetching for profit graph
        $stocks = Stock::with('stockDetails')->get();
        
        // Calculate profit for each stock item
        $profitData = $stocks->map(function ($stock) {
            $stockDetail = $stock->stockDetails;
            $profit = ($stock->MRP * $stock->sailed_quatity)-($stockDetail->payout/$stockDetail->quantity ?? 0);
            return [
                'name' => $stock->medicine_name,
                'MRP' => $stock->MRP,
                'sailed_quantity' => $stock->sailed_quatity,
                'payout' => $stockDetail->payout,
                'profit' => $profit,
            ];
        });
    
        // Fetch additional data for the dashboard, like users, prescriptions, etc.
        $userCount = User::count();  // Example for user count
        $prescriptionCount = Prescription::count();  // Example for prescription count
    
        // Pass the data to the view
        return view('dashboard', compact('profitData', 'userCount', 'prescriptionCount','stocks'));
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
        // Fetch all medicines with related stock, prescription, and user data
        $medicines = Medicine::with(['stock', 'prescription.user'])->get();

        // Log the fetched data for debugging
        \Log::info('Fetched Medicines Data:', $medicines->toArray());

        // Pass the data to the view
        return view('Allmedicines', compact('medicines'));
    }

    public function destroyStocks($id)
    {
        // Find the stock by ID
        $stock = Stock::find($id);

        // Check if the stock exists
        if (!$stock) {
            return redirect()->back()->with('error', 'Stock not found.');
        }

        // Delete the stock
        $stock->delete();

        // Redirect with a success message
        return redirect()->back()->with('success', 'Stock deleted successfully.');
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
        $stocks = Stock::all();
        return view('addMedicines', compact('prescription', 'stocks'));
    }

    public function store(Request $request, $prescriptionId)
    {
        // Log the start of the store method
        \Log::info('Store method started for prescription ID: ' . $prescriptionId);

        // Log the incoming request data
        \Log::info('Incoming Request Data:', $request->all());

        // Validate the incoming request
        try {
            $request->validate([
                'medicine_id' => 'required|array', // Ensure 'medicine_id' is an array
                'medicine_id.*' => 'required|integer',
                'meal_time.*' => 'required|string',
                'days' => 'required|array', // Ensure that 'days' is an array
                'days.*' => 'required|integer', // Each day field must be an integer
            ]);
            \Log::info('Validation successful.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', $e->errors());
            return redirect()->back()->withErrors($e->errors());
        }

        // Ensure that the medicine_id field is not empty
        if (empty($request->medicine_id)) {
            \Log::warning('No medicines selected.');
            return redirect()->back()->withErrors('No medicines selected.');
        }

        // Log the medicine IDs to be processed
        \Log::info('Medicine IDs to process:', $request->medicine_id);

        // Loop through the medicines and save each one
        foreach ($request->medicine_id as $index => $medicineId) {
            try {
                // Get the number of days for the current medicine
                $days = $request->days[$index];
                \Log::info("Processing Medicine ID: $medicineId for $days days");

                // Calculate the total count based on the doses per day
                $totalCount = 0;
                $totalCount += isset($request->regime['morning'][$index]) ? 1 : 0;
                $totalCount += isset($request->regime['afternoon'][$index]) ? 1 : 0;
                $totalCount += isset($request->regime['evening'][$index]) ? 1 : 0;
                $totalCount += isset($request->regime['night'][$index]) ? 1 : 0;

                // Multiply the total count by the number of days
                $totalCount *= $days;
                \Log::info("Total count for Medicine ID $medicineId: $totalCount");

                // Save the medicine to the database
                $medicine = Medicine::create([
                    'prescription_id' => $prescriptionId,
                    'medicine_id' => $medicineId,
                    'morning' => isset($request->regime['morning'][$index]) ? true : false,
                    'afternoon' => isset($request->regime['afternoon'][$index]) ? true : false,
                    'evening' => isset($request->regime['evening'][$index]) ? true : false,
                    'night' => isset($request->regime['night'][$index]) ? true : false,
                    'timing' => $request->meal_time[$index],
                    'total_count' => $totalCount,  // Set the total count
                ]);

                // Save the medicine to the database
                $medicine = Medicine::create([
                    'prescription_id' => $prescriptionId,
                    'medicine_id' => $medicineId,
                    'morning' => isset($request->regime['morning'][$index]) ? true : false,
                    'afternoon' => isset($request->regime['afternoon'][$index]) ? true : false,
                    'evening' => isset($request->regime['evening'][$index]) ? true : false,
                    'night' => isset($request->regime['night'][$index]) ? true : false,
                    'timing' => $request->meal_time[$index],
                    'total_count' => $totalCount, // Set the total count
                ]);

                // Update the stocks table
                $stock = Stock::find($medicineId); // Find the stock by medicine_id
                if ($stock) {
                    $stock->sailed_quatity += $totalCount; // Increase sailed_count
                    $stock->save(); // Save the updated stock
                }

                // Update the stock_details table
                $stockDetail = StockDetails::where('medicine_id', $medicineId)->first();
                if ($stockDetail) {
                    $stockDetail->quantity -= $totalCount; // Decrease quantity
                    $stockDetail->save(); // Save the updated stock details
                }


                \Log::info("Medicine stored successfully:", $medicine->toArray());
            } catch (\Exception $e) {
                \Log::error("Error storing medicine ID $medicineId:", ['error' => $e->getMessage()]);
            }
        }

        // Update Prescription Status
        try {
            Prescription::where('id', $prescriptionId)->update(['status' => '1']);
            \Log::info('Prescription status updated to 1 for ID: ' . $prescriptionId);
        } catch (\Exception $e) {
            \Log::error('Error updating prescription status:', ['error' => $e->getMessage()]);
        }

        // Log the successful completion of the store method
        \Log::info('Store method completed successfully for prescription ID: ' . $prescriptionId);

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

    public function allStocks()
    {
        // Fetch all stocks with their stock details
        $stocks = Stock::all();

        // Pass the data to the view
        return view('viewStocks', compact('stocks'));
    }
    public function createStocks(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'medicine_name.*' => 'required|string|max:255',
            'MRP.*' => 'required|integer|min:0',
        ]);

        // Retrieve input data
        $medicineNames = $request->input('medicine_name');
        $mrps = $request->input('MRP');

        // Loop through the provided medicine names and MRPs to create stocks and related stock details
        foreach ($medicineNames as $index => $name) {
            // Insert stock into the stocks table
            $stock = Stock::create([
                'medicine_name' => $name,
                'MRP' => $mrps[$index],
            ]);


            // dd($stock->id);
            // Create the associated stock details for each stock created
            StockDetails::create([
                'medicine_id' => $stock->id, // Set the foreign key for stock details
                'batch_no' => null, // Default value for batch_no
                'expiry_date' => null, // Default value for expiry_date
                'quantity' => 0, // Default quantity
                'payout' => 0, // Default payout
                'balance' => 0, // Default balance
            ]);
        }

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Stocks have been successfully added.');
    }



    public function updateStockDetails(Request $request, $id)
    {
        $validated = $request->validate([
            'batch_no' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
            'quantity' => 'nullable|integer|min:0',
            'payout' => 'nullable|numeric|min:0',
            'balance' => 'nullable|numeric|min:0',
        ]);

        $stock = Stock::findOrFail($id);

        // Update or create stock details
        $stock->stockDetails()->updateOrCreate(
            ['medicine_id' => $id],
            $validated
        );

        return response()->json(['success' => true]);
    }

}
