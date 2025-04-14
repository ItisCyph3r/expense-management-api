<?php
// app/Jobs/SendWeeklyExpenseReport.php
namespace App\Jobs;

use App\Models\User;
use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyExpenseReport;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all companies
        $companies = \App\Models\Company::all();
        
        foreach ($companies as $company) {
            // Get all admins for this company
            $admins = User::where('company_id', $company->id)
                ->where('role', 'Admin')
                ->get();
            
            if ($admins->isEmpty()) {
                continue;
            }
            
            // Get expenses for this company from the past week
            $startDate = now()->subWeek();
            $endDate = now();
            
            $expenses = Expense::where('company_id', $company->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->with('user')
                ->get();
            
            // Calculate totals
            $totalAmount = $expenses->sum('amount');
            $expensesByCategory = $expenses->groupBy('category')
                ->map(function ($items) {
                    return [
                        'count' => $items->count(),
                        'total' => $items->sum('amount')
                    ];
                });
            
            // Send email to each admin
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new WeeklyExpenseReport(
                    $admin,
                    $company,
                    $expenses,
                    $totalAmount,
                    $expensesByCategory,
                    $startDate,
                    $endDate
                ));
            }
        }
    }
}