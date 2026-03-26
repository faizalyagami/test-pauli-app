<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Applicant;
use App\Models\TestSession;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    /**
     * Admin Dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_testers' => User::where('role', 'tester')->count(),
            'total_applicants' => User::where('role', 'applicant')->count(),
            'total_tests' => TestSession::count(),
            'completed_tests' => TestSession::where('status', 'completed')->count(),
            'average_score' => TestSession::where('status', 'completed')->avg('score') ?? 0,
        ];

        $recentUsers = User::orderBy('created_at', 'desc')->limit(10)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }

    /**
     * User Management - List all users
     */
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->has('role') && $request->role != 'all') {
            $query->where('role', $request->role);
        }

        if ($request->has('status') && $request->status != 'all') {
            $query->where('is_active', $request->status == 'active');
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Create User Form
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Store New User
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,tester,applicant',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => $request->has('is_active'),
        ]);

        // If role is applicant, create applicant profile
        if ($user->role === 'applicant') {
            $participant_numb = 'P' . str_pad(Applicant::count() + 1, 6, '0', STR_PAD_LEFT);
            Applicant::create([
                'user_id' => $user->id,
                'participant_numb' => $participant_numb,
                'full_name' => $validated['name'],
                'date_of_birth' => now()->subYears(20),
                'gender' => 'male',
                'address' => '',
                'phone' => '',
                'registration_date' => now(),
                'status' => 'registered',
            ]);
        }

        return redirect()->route('admin.users')->with('success', "User {$user->name} created successfully");
    }

    /**
     * Edit User Form
     */
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update User
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,tester,applicant',
            'is_active' => 'boolean',
        ]);

        $oldRole = $user->role;

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => $request->has('is_active'),
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Handle applicant profile if role changed
        if ($oldRole !== $user->role) {
            if ($user->role === 'applicant' && !$user->applicant) {
                $participant_numb = 'P' . str_pad(Applicant::count() + 1, 6, '0', STR_PAD_LEFT);
                Applicant::create([
                    'user_id' => $user->id,
                    'participant_numb' => $participant_numb,
                    'full_name' => $user->name,
                    'date_of_birth' => now()->subYears(20),
                    'gender' => 'male',
                    'address' => '',
                    'phone' => '',
                    'registration_date' => now(),
                    'status' => 'registered',
                ]);
            } elseif ($oldRole === 'applicant' && $user->applicant) {
                $user->applicant->delete();
            }
        }

        return redirect()->route('admin.users')->with('success', "User {$user->name} updated successfully");
    }

    /**
     * Delete User
     */
    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Cannot delete your own account');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', "User {$user->name} deleted successfully");
    }

    /**
     * Toggle User Status
     */
    public function toggleUserStatus(Request $request, User $user)
    {
        $newStatus = $request->status === 'active';
        $user->update(['is_active' => $newStatus]);

        return response()->json(['success' => true]);
    }

    /**
     * System Settings Page
     */
    public function settings()
    {
        return view('admin.settings.index');
    }

    /**
     * Update System Settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'timezone' => 'required|string',
        ]);

        // Update .env file
        $this->updateEnvFile([
            'APP_NAME' => $validated['app_name'],
            'APP_TIMEZONE' => $validated['timezone'],
        ]);

        return redirect()->back()->with('success', 'Settings updated successfully');
    }

    /**
     * Update .env file
     */
    private function updateEnvFile($values)
    {
        $envFile = base_path('.env');
        $content = File::get($envFile);

        foreach ($values as $key => $value) {
            $content = preg_replace("/^{$key}=.*/m", "{$key}=\"{$value}\"", $content);
        }

        File::put($envFile, $content);

        // Clear config cache
        Artisan::call('config:clear');
    }

    /**
     * System Logs Page
     */
    public function logs()
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];

        if (File::exists($logFile)) {
            $content = File::get($logFile);
            $logLines = explode("\n", $content);
            $logs = array_reverse(array_filter($logLines));
            $logs = array_slice($logs, 0, 500);
        }

        return view('admin.logs.index', compact('logs'));
    }

    /**
     * Clear Logs
     */
    public function clearLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        if (File::exists($logFile)) {
            File::put($logFile, '');
        }

        return redirect()->back()->with('success', 'Logs cleared successfully');
    }

    /**
     * System Information
     */
    public function systemInfo()
    {
        $info = [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'server_os' => PHP_OS,
            'mysql_version' => DB::select('select version() as version')[0]->version ?? 'Unknown',
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
        ];

        return view('admin.system.info', compact('info'));
    }
}
