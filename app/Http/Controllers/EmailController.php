<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class EmailController extends Controller
{
    public function email(Request $request)
{
    \Log::info('Email endpoint hit');
    
    // Get the user ID from the request
    $userId = $request->input('user_id');
    
    // Retrieve the user by the provided user ID
    $user = User::find($userId);

    // Check if the user exists
    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'User not found',
        ], 404);
    }

    // Log the authenticated user details
    \Log::info('Authenticated user details', [
        'name' => $user->name,
        'id' => $user->id,
    ]);
    
    // Get the user's guardian email from the user_details table
    $guardianMail = $user->userDetails->guardian_mail ?? null;
    \Log::info('Guardian email:', ['email' => $guardianMail]);

    // Check if guardian email is found
    if (!$guardianMail) {
        return response()->json([
            'status' => 'error',
            'message' => 'Guardian email not found for the user',
        ], 404);
    }

    // Get medicines for the user
    $medicines = $user->medicines()->get(); // Assuming the user has a relationship with medicines

    // Initialize an array to hold medicines with low stock
    $lowStockMedicines = [];

    foreach ($medicines as $medicine) {
        // Calculate the per-day requirement
        $perDay = ($medicine->morning ? 1 : 0) +
                  ($medicine->afternoon ? 1 : 0) +
                  ($medicine->evening ? 1 : 0) +
                  ($medicine->night ? 1 : 0);

        // Check if the total count is less than 2 times the per-day requirement
        if ($perDay > 0 && $medicine->total_count < (2 * $perDay)) {
            $lowStockMedicines[] = $medicine;
        }
    }

    // If no low stock medicines are found, return success without sending email
    if (empty($lowStockMedicines)) {
        return response()->json([
            'status' => 'success',
            'message' => 'All medicines have sufficient stock',
        ], 200);
    }

    // Prepare the email content
    $emailData = [
        'user_name' => $user->name,
        'low_stock_medicines' => $lowStockMedicines,
    ];

    // Send email to the guardian
    try {
        Mail::send('emails.low_stock_alert', $emailData, function ($message) use ($guardianMail) {
            $message->to($guardianMail)
                ->subject('Low Stock Alert for Medicines');
        });

        \Log::info('Mail sent successfully');

        return response()->json([
            'status' => 'success',
            'message' => 'Low stock alert email sent to guardian',
        ], 200);

    } catch (\Exception $e) {
        // Log the error
        \Log::error('Error sending low stock email', [
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to send email. Please try again later.',
        ], 500);
    }
}
}
