<?php

namespace App\Http\Controllers\Panel\Admin;

use App\Http\Controllers\Controller;
use App\Support\FundReport;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function __invoke(): View
    {
        return view('panel.admin.finance', [
            'summary' => FundReport::summary(),
            'campaigns' => FundReport::campaigns(),
            'supporters' => FundReport::supporters(),
            'expenses' => FundReport::recentExpenses(),
            'incomes' => FundReport::recentIncomes(),
        ]);
    }
}
