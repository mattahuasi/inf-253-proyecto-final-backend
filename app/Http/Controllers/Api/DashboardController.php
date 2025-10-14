<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StateResource;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Menu;
use App\Models\Order;
use App\Models\State;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index']),
        ];
    }

    public function index()
    {
        $ordersToday = Order::whereDate('ordered_at', Carbon::today())->count();
        $totalCustomers = Customer::count();
        $totalEmployees = Employee::count();
        $totalMenus = Menu::count();
        $totalUsers = User::count();

        $states = StateResource::collection(State::withCount(['orders'])->get());

        return response()->json([
            'orders_today' => $ordersToday,
            'total_customers' => $totalCustomers,
            'total_employees' => $totalEmployees,
            'total_menus' => $totalMenus,
            'total_users' => $totalUsers,
            'states' => $states,
        ]);
    }
}
