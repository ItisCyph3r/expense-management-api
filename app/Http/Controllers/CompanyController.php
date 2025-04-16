<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:Super_Admin');
    }

    public function index(Request $request)
    {
        return Company::with('users')->latest()->paginate(15);
    }

    public function show(Request $request, Company $company)
    {
        return response()->json([
            'company' => $company->load('users')
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:companies',
                'admin_name' => 'required|string|max:255',
                'admin_email' => 'required|email|unique:users,email',
                'admin_password' => 'required|min:8|confirmed',
                'admin_password_confirmation' => 'required'
            ]);

            return DB::transaction(function () use ($validated) {
                $company = Company::create([
                    'name' => $validated['name'],
                    'email' => $validated['email']
                ]);

                $admin = User::create([
                    'name' => $validated['admin_name'],
                    'email' => $validated['admin_email'],
                    'password' => Hash::make($validated['admin_password']),
                    'role' => 'Admin',
                    'company_id' => $company->id
                ]);

                return response()->json([
                    'message' => 'Company and admin created successfully',
                    'company' => $company,
                    'admin' => $admin
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create company',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:companies,email,' . $company->id,
        ]);

        $company->update($validated);

        return response()->json([
            'message' => 'Company updated successfully',
            'company' => $company
        ]);
    }
}