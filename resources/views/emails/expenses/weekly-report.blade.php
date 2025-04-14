<!-- resources/views/emails/expenses/weekly-report.blade.php -->
@component('mail::message')
# Weekly Expense Report

Dear {{ $admin->name }},

Here is the weekly expense report for **{{ $company->name }}** from **{{ $startDate->format('M d, Y') }}** to **{{ $endDate->format('M d, Y') }}**.

## Summary
- Total Expenses: {{ $expenses->count() }}
- Total Amount: ${{ number_format($totalAmount, 2) }}

## Breakdown by Category
@component('mail::table')
| Category | Count | Total Amount |
|:--------|:------:|------------:|
@foreach($expensesByCategory as $category => $data)
| {{ $category }} | {{ $data['count'] }} | ${{ number_format($data['total'], 2) }} |
@endforeach
@endcomponent

## Recent Expenses
@component('mail::table')
| Date | Employee | Title | Category | Amount |
|:-----|:---------|:------|:---------|-------:|
@foreach($expenses->take(10) as $expense)
| {{ $expense->created_at->format('M d') }} | {{ $expense->user->name }} | {{ $expense->title }} | {{ $expense->category }} | ${{ number_format($expense->amount, 2) }} |
@endforeach
@endcomponent

@if($expenses->count() > 10)
*...and {{ $expenses->count() - 10 }} more expenses*
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent