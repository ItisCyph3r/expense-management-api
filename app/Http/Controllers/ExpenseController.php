<?php
// app/Http/Controllers/ExpenseController.php
namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use App\Jobs\SendWeeklyExpenseReport;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses for the user's company with eager loading 
     * and caching to optimize performance.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;
        
        // Get search and filter parameters
        $search = $request->get('search');
        $category = $request->get('category');
        $perPage = $request->get('per_page', 15);
        
        // Cache key based on request parameters and company ID
        $cacheKey = "expenses_{$companyId}_{$search}_{$category}_{$perPage}_" . $request->get('page', 1);
        
        // Get expenses from cache or database with eager loading
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($companyId, $search, $category, $perPage, $request) {
            $query = Expense::with('user') // Eager load user relationship
                ->where('company_id', $companyId);
            
            // Apply search filter if provided
            if ($search) {
                $query->where('title', 'like', "%{$search}%");
            }
            
            // Apply category filter if provided
            if ($category) {
                $query->where('category', $category);
            }
            
            return $query->latest()->paginate($perPage);
        });
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
        ]);
        
        $user = $request->user();
        
        $expense = Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'category' => $request->category,
            'user_id' => $user->id,
            'company_id' => $user->company_id,
        ]);
        
        // Clear relevant cache
        $this->clearExpenseCache($user->company_id);
        
        return response()->json([
            'message' => 'Expense created successfully',
            'expense' => $expense
        ], 201);
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'category' => 'sometimes|required|string|max:255',
        ]);
        
        $user = $request->user();
        
        // Store old values for audit log
        $oldValues = $expense->toArray();
        
        // Update expense
        $expense->update($request->only(['title', 'amount', 'category']));
        
        // Create audit log
        AuditLog::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'action' => 'update',
            'changes' => [
                'old' => $oldValues,
                'new' => $expense->toArray()
            ]
        ]);
        
        // Clear relevant cache
        $this->clearExpenseCache($user->company_id);
        
        return response()->json([
            'message' => 'Expense updated successfully',
            'expense' => $expense
        ]);
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Request $request, Expense $expense)
    {
        $user = $request->user();
        
        // Store expense data for audit log
        $expenseData = $expense->toArray();
        
        // Delete expense
        $expense->delete();
        
        // Create audit log
        AuditLog::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'action' => 'delete',
            'changes' => [
                'old' => $expenseData,
                'new' => null
            ]
        ]);
        
        // Clear relevant cache
        $this->clearExpenseCache($user->company_id);
        
        return response()->json([
            'message' => 'Expense deleted successfully'
        ]);
    }
    
    /**
     * Clear expense-related cache for a company
     */
    private function clearExpenseCache($companyId)
    {
        Cache::forget("expenses_{$companyId}_*");
    }
}