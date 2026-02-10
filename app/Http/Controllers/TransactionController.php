<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Order::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('pages.user.transactions', compact('transactions'));
    }
}
