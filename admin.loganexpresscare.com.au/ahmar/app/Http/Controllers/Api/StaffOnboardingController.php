<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Models\StaffOnboarding;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Examyou\RestAPI\ApiResponse;

class StaffOnboardingController extends ApiBaseController
{
    protected $model = StaffOnboarding::class;

    /**
     * Public API endpoint to receive staff onboarding submissions from the frontend form.
     */
    public function store()
    {
        $request = request();
        
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'position' => 'required|in:disability_support_worker,nurse,other',
            'employment_type' => 'required|in:permanent,casual,contractor',
            'abn' => 'nullable|string|max:50',
            'business_name' => 'nullable|string|max:255',
            
            // Documents - now accepting file uploads
            'ndis_screening' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'children_check' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'children_check_expiry' => 'required|date',
            'passport' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'visa' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'drivers_license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'ndis_orientation' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'meals_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'meals_certificate_expiry' => 'required|date',
            'ipc_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'ipc_certificate_expiry' => 'required|date',
            'first_aid_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'first_aid_expiry' => 'required|date',
            'cpr_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'cpr_expiry' => 'required|date',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'staff_contract' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'police_check' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'police_check_expiry' => 'required|date',
            
            // Acknowledgments
            'ack_staff_details' => 'nullable|boolean',
            'ack_induction' => 'nullable|boolean',
            'ack_code_conduct' => 'nullable|boolean',
            'ack_training_tracker' => 'nullable|boolean',
            'ack_handbook' => 'nullable|boolean',
            'ack_tax_declaration' => 'nullable|boolean',
            'ack_bank_details' => 'nullable|boolean',
            'final_consent' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Handle file uploads
            $uploadedFiles = $this->handleFileUploads($request);
            
            // Prepare data for database
            $data = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'position' => $request->position,
                'employment_type' => $request->employment_type,
                'abn' => $request->abn,
                'business_name' => $request->business_name,
                
                // File paths
                'ndis_screening' => $uploadedFiles['ndis_screening'] ?? null,
                'children_check' => $uploadedFiles['children_check'] ?? null,
                'children_check_expiry' => $request->children_check_expiry,
                'passport' => $uploadedFiles['passport'] ?? null,
                'visa' => $uploadedFiles['visa'] ?? null,
                'drivers_license' => $uploadedFiles['drivers_license'] ?? null,
                'ndis_orientation' => $uploadedFiles['ndis_orientation'] ?? null,
                'meals_certificate' => $uploadedFiles['meals_certificate'] ?? null,
                'meals_certificate_expiry' => $request->meals_certificate_expiry,
                'ipc_certificate' => $uploadedFiles['ipc_certificate'] ?? null,
                'ipc_certificate_expiry' => $request->ipc_certificate_expiry,
                'first_aid_certificate' => $uploadedFiles['first_aid_certificate'] ?? null,
                'first_aid_expiry' => $request->first_aid_expiry,
                'cpr_certificate' => $uploadedFiles['cpr_certificate'] ?? null,
                'cpr_expiry' => $request->cpr_expiry,
                'resume' => $uploadedFiles['resume'] ?? null,
                'staff_contract' => $uploadedFiles['staff_contract'] ?? null,
                'police_check' => $uploadedFiles['police_check'] ?? null,
                'police_check_expiry' => $request->police_check_expiry,
                
                // Acknowledgments
                'ack_staff_details' => $request->boolean('ack_staff_details'),
                'ack_induction' => $request->boolean('ack_induction'),
                'ack_code_conduct' => $request->boolean('ack_code_conduct'),
                'ack_training_tracker' => $request->boolean('ack_training_tracker'),
                'ack_handbook' => $request->boolean('ack_handbook'),
                'ack_tax_declaration' => $request->boolean('ack_tax_declaration'),
                'ack_bank_details' => $request->boolean('ack_bank_details'),
                'final_consent' => $request->boolean('final_consent'),
            ];

            // Create the staff onboarding record
            $staffOnboarding = StaffOnboarding::create($data);

            // Send notification email
            $this->sendNotificationEmail($staffOnboarding);

            return response()->json([
                'success' => true,
                'message' => 'Staff onboarding application submitted successfully',
                'data' => $staffOnboarding
            ], 201);
            
        } catch (\Exception $e) {
            // If there's an error, delete any uploaded files
            if (isset($uploadedFiles)) {
                $this->deleteUploadedFiles($uploadedFiles);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle file uploads and return array of file paths
     */
    private function handleFileUploads(Request $request)
    {
        $uploadedFiles = [];
        
        $fileFields = [
            'ndis_screening',
            'children_check',
            'passport',
            'visa',
            'drivers_license',
            'ndis_orientation',
            'meals_certificate',
            'ipc_certificate',
            'first_aid_certificate',
            'cpr_certificate',
            'resume',
            'staff_contract',
            'police_check'
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                
                // Generate unique filename
                $timestamp = time();
                $random = mt_rand(1000, 9999);
                $extension = $file->getClientOriginalExtension();
                $filename = $field . '_' . $timestamp . '_' . $random . '.' . $extension;
                
                // Store file in storage/app/public/staff-onboarding
                $path = $file->storeAs('staff-onboarding', $filename, 'public');
                
                $uploadedFiles[$field] = $filename;
            }
        }

        return $uploadedFiles;
    }

    /**
     * Delete uploaded files if there's an error
     */
    private function deleteUploadedFiles(array $files)
    {
        foreach ($files as $filename) {
            if ($filename) {
                Storage::disk('public')->delete('staff-onboarding/' . $filename);
            }
        }
    }

    /**
     * Send notification email to HR
     */
    private function sendNotificationEmail($staffOnboarding)
    {
        try {
            $hrEmail = env('HR_EMAIL', 'hr@loganexpresscare.com.au');
            
            $subject = 'New Staff Onboarding Application - ' . $staffOnboarding->first_name . ' ' . $staffOnboarding->last_name;
            
            $emailBody = $this->prepareEmailBody($staffOnboarding);
            
            // Send email
            Mail::raw($emailBody, function ($message) use ($hrEmail, $subject, $staffOnboarding) {
                $message->to($hrEmail)
                        ->subject($subject)
                        ->replyTo($staffOnboarding->email, $staffOnboarding->first_name . ' ' . $staffOnboarding->last_name);
            });
            
        } catch (\Exception $e) {
            // Log error but don't fail the submission
            \Log::error('Failed to send onboarding notification email: ' . $e->getMessage());
        }
    }

    /**
     * Prepare email body content
     */
    private function prepareEmailBody($staffOnboarding)
    {
        $body = "=== STAFF ONBOARDING APPLICATION ===\n\n";
        $body .= "PERSONAL INFORMATION\n";
        $body .= "====================\n";
        $body .= "Name: " . $staffOnboarding->first_name . " " . $staffOnboarding->last_name . "\n";
        $body .= "Email: " . $staffOnboarding->email . "\n";
        $body .= "Phone: " . $staffOnboarding->phone . "\n";
        $body .= "Position: " . ucwords(str_replace('_', ' ', $staffOnboarding->position)) . "\n";
        $body .= "Employment Type: " . ucwords($staffOnboarding->employment_type) . "\n";

        if ($staffOnboarding->employment_type === 'contractor') {
            $body .= "\nCONTRACTOR DETAILS\n";
            $body .= "==================\n";
            $body .= "ABN: " . $staffOnboarding->abn . "\n";
            $body .= "Business Name: " . $staffOnboarding->business_name . "\n";
        }

        $body .= "\nDOCUMENTS UPLOADED\n";
        $body .= "==================\n";
        $body .= "1. NDIS Worker Screening Check: " . ($staffOnboarding->ndis_screening ?: "Not uploaded") . "\n";
        $body .= "2. Working with Children Check: " . ($staffOnboarding->children_check ?: "Not uploaded") . "\n";
        $body .= "   Expiry Date: " . $staffOnboarding->children_check_expiry . "\n";
        $body .= "3. 100 Points of ID:\n";
        $body .= "   - Passport: " . ($staffOnboarding->passport ?: "Not uploaded") . "\n";
        $body .= "   - Visa: " . ($staffOnboarding->visa ?: "Not uploaded") . "\n";
        $body .= "   - Driver's License: " . ($staffOnboarding->drivers_license ?: "Not uploaded") . "\n";
        $body .= "4. NDIS Worker Orientation Module: " . ($staffOnboarding->ndis_orientation ?: "Not uploaded") . "\n";
        $body .= "5. Supporting Safe and Enjoyable Meals: " . ($staffOnboarding->meals_certificate ?: "Not uploaded") . "\n";
        $body .= "   Expiry Date: " . $staffOnboarding->meals_certificate_expiry . "\n";
        $body .= "6. Infection Prevention and Control: " . ($staffOnboarding->ipc_certificate ?: "Not uploaded") . "\n";
        $body .= "   Expiry Date: " . $staffOnboarding->ipc_certificate_expiry . "\n";
        $body .= "7. First Aid Certificate: " . ($staffOnboarding->first_aid_certificate ?: "Not uploaded") . "\n";
        $body .= "   Expiry: " . $staffOnboarding->first_aid_expiry . "\n";
        $body .= "8. CPR Certificate: " . ($staffOnboarding->cpr_certificate ?: "Not uploaded") . "\n";
        $body .= "   Expiry: " . $staffOnboarding->cpr_expiry . "\n";
        $body .= "9. Qualifications/Resume: " . ($staffOnboarding->resume ?: "Not uploaded") . "\n";
        $body .= "10. Staff Contract: " . ($staffOnboarding->staff_contract ?: "Not uploaded") . "\n";
        $body .= "11. National Police Check: " . ($staffOnboarding->police_check ?: "Not uploaded") . "\n";
        $body .= "    Expiry Date: " . $staffOnboarding->police_check_expiry . "\n";

        $body .= "\nACKNOWLEDGMENTS\n";
        $body .= "===============\n";
        $body .= "Staff Detail Form: " . ($staffOnboarding->ack_staff_details ? 'Yes' : 'No') . "\n";
        $body .= "Staff Induction Checklist: " . ($staffOnboarding->ack_induction ? 'Yes' : 'No') . "\n";
        $body .= "Code of Conduct: " . ($staffOnboarding->ack_code_conduct ? 'Yes' : 'No') . "\n";
        $body .= "Training Tracker: " . ($staffOnboarding->ack_training_tracker ? 'Yes' : 'No') . "\n";
        $body .= "Employee Handbook: " . ($staffOnboarding->ack_handbook ? 'Yes' : 'No') . "\n";
        $body .= "Tax Declaration: " . ($staffOnboarding->ack_tax_declaration ? 'Yes' : 'No') . "\n";
        $body .= "Bank Details: " . ($staffOnboarding->ack_bank_details ? 'Yes' : 'No') . "\n";
        $body .= "Final Consent: " . ($staffOnboarding->final_consent ? 'Yes' : 'No') . "\n";

        $body .= "\nSubmission Date: " . $staffOnboarding->created_at->format('Y-m-d H:i:s') . "\n";
        $body .= "\nView full application in the admin dashboard.\n";
        $body .= "Application ID: " . $staffOnboarding->id . "\n";

        return $body;
    }

    /**
     * Custom index method for staff onboarding (overrides parent to match Vue expectations)
     */
    public function index()
    {
        $request = request();
        $query = StaffOnboarding::query();

        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        // Status filter
        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Position filter
        if ($request->has('position') && !empty($request->position) && $request->position !== 'all') {
            $query->where('position', $request->position);
        }

        // Employment type filter
        if ($request->has('employment_type') && !empty($request->employment_type) && $request->employment_type !== 'all') {
            $query->where('employment_type', $request->employment_type);
        }

        // Date range filter
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Order by latest first
        $query->orderBy('created_at', 'desc');

        // Paginate
        $perPage = $request->per_page ?? 15;
        $applications = $query->paginate($perPage);

        // Transform the collection to manually add xid
        $transformedData = $applications->toArray();
        foreach ($transformedData['data'] as &$item) {
            if (isset($item['id'])) {
                $item['xid'] = $this->getHashFromId($item['id']);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $transformedData
        ]);
    }

    /**
     * Custom show method to match Vue expectations
     */
    public function show(...$args)
    {
        $identifier = $args[0] ?? null;
        
        // Check if it's a numeric ID or hashed ID
        if (is_numeric($identifier)) {
            $id = $identifier;
        } else {
            // Decode the hashed ID
            $id = $this->getIdFromHash($identifier);
        }
        
        $application = StaffOnboarding::with('reviewer')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'application' => $application,
                'expired_certificates' => $application->expired_certificates,
                'expiring_soon_certificates' => $application->expiring_soon_certificates
            ]
        ]);
    }

    /**
     * Update the status of a staff onboarding application.
     */
    public function updateStatus(Request $request, $identifier)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,under_review,approved,rejected,completed',
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if it's a numeric ID or hashed ID
        if (is_numeric($identifier)) {
            $id = $identifier;
        } else {
            $id = $this->getIdFromHash($identifier);
        }
        
        $application = StaffOnboarding::findOrFail($id);
        
        $application->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application status updated successfully',
            'data' => $application
        ]);
    }

    /**
     * Delete a staff onboarding application.
     */
    public function destroy(...$args)
    {
        $identifier = $args[0] ?? null;
        
        // Check if it's a numeric ID or hashed ID
        if (is_numeric($identifier)) {
            $id = $identifier;
        } else {
            $id = $this->getIdFromHash($identifier);
        }
        
        $application = StaffOnboarding::findOrFail($id);
        
        // Delete associated files from storage
        $this->deleteApplicationFiles($application);
        
        $application->delete();

        return response()->json([
            'success' => true,
            'message' => 'Application deleted successfully'
        ]);
    }

    /**
     * Download a specific document from a staff onboarding application.
     */
    public function downloadDocument($identifier, $documentType)
    {
        // Check if it's a numeric ID or hashed ID
        if (is_numeric($identifier)) {
            $id = $identifier;
        } else {
            $id = $this->getIdFromHash($identifier);
        }
        
        $application = StaffOnboarding::findOrFail($id);
        
        if (!$application->$documentType) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
        }

        $filePath = 'staff-onboarding/' . $application->$documentType;
        
        if (!Storage::disk('public')->exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found on server'
            ], 404);
        }

        // Check if we should view inline or download
        $inline = request()->query('inline', false);
        
        if ($inline) {
            // Return file for inline viewing in browser
            $file = Storage::disk('public')->get($filePath);
            $mimeType = Storage::disk('public')->mimeType($filePath);
            
            return response($file, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="' . basename($application->$documentType) . '"');
        }

        return Storage::disk('public')->download($filePath);
    }

    /**
     * Bulk update status for multiple applications.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:staff_onboarding,id',
            'status' => 'required|in:pending,under_review,approved,rejected,completed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        StaffOnboarding::whereIn('id', $request->ids)->update([
            'status' => $request->status,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Applications updated successfully'
        ]);
    }

    /**
     * Get statistics for dashboard.
     */
    public function statistics()
    {
        $stats = [
            'total_applications' => StaffOnboarding::count(),
            'pending' => StaffOnboarding::where('status', 'pending')->count(),
            'under_review' => StaffOnboarding::where('status', 'under_review')->count(),
            'approved' => StaffOnboarding::where('status', 'approved')->count(),
            'rejected' => StaffOnboarding::where('status', 'rejected')->count(),
            'completed' => StaffOnboarding::where('status', 'completed')->count(),
            'this_month' => StaffOnboarding::whereMonth('created_at', now()->month)->count(),
            'this_week' => StaffOnboarding::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'today' => StaffOnboarding::whereDate('created_at', now()->toDateString())->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Delete files associated with an application.
     */
    private function deleteApplicationFiles($application)
    {
        $documentFields = [
            'ndis_screening', 'children_check', 'passport', 'visa', 'drivers_license',
            'ndis_orientation', 'meals_certificate', 'ipc_certificate', 'first_aid_certificate',
            'cpr_certificate', 'resume', 'staff_contract', 'police_check'
        ];

        foreach ($documentFields as $field) {
            if ($application->$field) {
                Storage::disk('public')->delete('staff-onboarding/' . $application->$field);
            }
        }
    }
}

