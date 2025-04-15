<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class WeeklyExpenseReport extends Mailable
{
    use Queueable, SerializesModels;

    public $admin;
    public $company;
    public $expenses;
    public $totalAmount;
    public $expensesByCategory;
    public $startDate;
    public $endDate;

    /**
     * Create a new message instance.
     */
    public function __construct(
        User $admin,
        Company $company,
        Collection $expenses,
        float $totalAmount,
        Collection $expensesByCategory,
        \DateTime $startDate,
        \DateTime $endDate
    ) {
        $this->admin = $admin;
        $this->company = $company;
        $this->expenses = $expenses;
        $this->totalAmount = $totalAmount;
        $this->expensesByCategory = $expensesByCategory;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("Weekly Expense Report: {$this->startDate->format('M d')} - {$this->endDate->format('M d')}")
            ->markdown('emails.expenses.weekly-report');
    }
}