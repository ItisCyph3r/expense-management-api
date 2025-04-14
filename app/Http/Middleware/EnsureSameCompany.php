namespace App\Http\Middleware;

use App\Models\Expense;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSameCompany
{
    /**
     * Ensure users can only access data from their own company
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // For expense routes that contain an ID parameter
        if ($request->route('expense')) {
            $expense = Expense::findOrFail($request->route('expense'));
            
            if ($expense->company_id !== $user->company_id) {
                return response()->json([
                    'message' => 'Unauthorized access to another company\'s data'
                ], 403);
            }
        }
        
        // For user routes that contain an ID parameter
        if ($request->route('user')) {
            $targetUser = \App\Models\User::findOrFail($request->route('user'));
            
            if ($targetUser->company_id !== $user->company_id) {
                return response()->json([
                    'message' => 'Unauthorized access to another company\'s data'
                ], 403);
            }
        }

        return $next($request);
    }
}