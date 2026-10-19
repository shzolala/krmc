<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Support\FinanceAccess;
use App\Support\FundReport;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $canViewLedger = FinanceAccess::canViewLedger($user);

        return view('panel.finance.index', [
            'summary' => FundReport::summary(),
            'campaigns' => FundReport::campaigns(),
            'supporters' => FundReport::supporters(),
            'memberDues' => FundReport::memberDues($user),
            'canViewLedger' => $canViewLedger,
            'expenses' => $canViewLedger ? FundReport::recentExpenses() : collect(),
            'incomes' => $canViewLedger ? FundReport::recentIncomes() : collect(),
        ]);
    }
}
