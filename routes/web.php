<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\SeuFlaggingController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'showLandingPage'])->name('landing');
Route::get('/features', function () { return view('pages.features'); })->name('features');
Route::get('/solution', function () { return view('pages.solution'); })->name('solution');
Route::get('/pricing', function () { return view('pages.pricing'); })->name('pricing');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login-process', [AuthController::class, 'login'])->name('login.process');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/register-process', [AuthController::class, 'register'])->name('register.process');
Route::get('/logout', function() {
    if (auth()->check()) {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
    return redirect('/')->with('success', 'You have been logged out!');
})->name('logout');

Route::get('/fix-users', function () {
    $fixed = 0;
    \App\Models\User::whereDoesntHave('roles')->each(function ($user) use (&$fixed) {
        $role = \App\Models\Role::where('name', $user->role)->first();
        if ($role) {
            $user->roles()->sync([$role->id]);
            $user->update(['default_role_id' => $role->id]);
            $fixed++;
        }
    });
    return "Done! Fixed {$fixed} users.";
});

/*
|--------------------------------------------------------------------------
| Protected Routes - All authenticated users
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Main Dashboard
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/internal-external-issues/approval', [Admin\RiskOpportunityController::class, 'approvalIndex'])->name('admin.risks.approval');
    Route::post('/internal-external-issues/{id}/approve', [Admin\RiskOpportunityController::class, 'approve'])->name('admin.risks.approve');
    Route::post('/internal-external-issues/{id}/reject', [Admin\RiskOpportunityController::class, 'reject'])->name('admin.risks.reject');
    // Organizational Context Resources
    Route::resource('internal-external-issues', Admin\RiskOpportunityController::class);
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('swot-analysis', Admin\SwotAnalysisController::class);
    Route::post('swot-analysis/{id}/approve', [Admin\SwotAnalysisController::class, 'approve'])->name('swot-analysis.approve');
    Route::post('swot-analysis/{id}/archive', [Admin\SwotAnalysisController::class, 'archive'])->name('swot-analysis.archive');
    Route::get('/scope-boundaries/approval', [Admin\ScopeController::class, 'approvalIndex'])->name('admin.scopes.approval');
    Route::post('/scope-boundaries/{id}/approve', [Admin\ScopeController::class, 'approve'])->name('admin.scopes.approve');
    Route::post('/scope-boundaries/{id}/reject', [Admin\ScopeController::class, 'reject'])->name('admin.scopes.reject');
    Route::resource('scope-boundaries', Admin\ScopeController::class);
    Route::get('/legal/approval', [Admin\LegalController::class, 'approvalIndex'])->name('admin.legals.approval');
    Route::post('/legal/{id}/approve', [Admin\LegalController::class, 'approve'])->name('admin.legals.approve');
    Route::post('/legal/{id}/reject', [Admin\LegalController::class, 'reject'])->name('admin.legals.reject');
    Route::resource('legals', Admin\LegalController::class);
    Route::get('legals/{id}/detail', [Admin\LegalController::class, 'detail'])->name('legals.detail');
    Route::get('legals/{id}/items', [Admin\LegalController::class, 'getItems'])->name('legals.items');
    Route::get('/stakeholders/approval', [Admin\StakeholderController::class, 'approvalIndex'])->name('admin.stakeholders.approval');
    Route::post('/stakeholders/{id}/approve', [Admin\StakeholderController::class, 'approve'])->name('admin.stakeholders.approve');
    Route::post('/stakeholders/{id}/reject', [Admin\StakeholderController::class, 'reject'])->name('admin.stakeholders.reject');
    Route::resource('stakeholders', Admin\StakeholderController::class);

    // Energy Management Committee
    Route::resource('committees', Admin\CommitteeController::class);
    Route::get('committees/{id}/appointment-letter', [Admin\CommitteeController::class, 'appointmentLetter'])->name('committees.appointment-letter');
    Route::get('committees-statistics', [Admin\CommitteeController::class, 'statistics'])->name('committees.statistics');

    // Energy Policy
    Route::get('/energy-policy', [Admin\EnergyPolicyController::class, 'index'])->name('energy-policy.index');
    Route::post('/energy-policy', [Admin\EnergyPolicyController::class, 'store'])->name('energy-policy.store');
    Route::get('/energy-policy/{id}', [Admin\EnergyPolicyController::class, 'show'])->name('energy-policy.show');
    Route::get('/energy-policy/{id}/edit', [Admin\EnergyPolicyController::class, 'edit'])->name('energy-policy.edit');
    Route::put('/energy-policy/{id}', [Admin\EnergyPolicyController::class, 'update'])->name('energy-policy.update');
    Route::delete('/energy-policy/{id}', [Admin\EnergyPolicyController::class, 'destroy'])->name('energy-policy.destroy');
    Route::post('/energy-policy/{id}/approve', [Admin\EnergyPolicyController::class, 'approve'])->name('energy-policy.approve');
    Route::post('/energy-policy/{id}/reject', [Admin\EnergyPolicyController::class, 'reject'])->name('energy-policy.reject');
    Route::post('/energy-policy/upload-document', [Admin\EnergyPolicyController::class, 'uploadDocument'])->name('energy-policy.upload-document');


    // SEC Analysis
    Route::get('/sec-analysis', [Admin\SecAnalysisController::class, 'index'])->name('sec-analysis.index');
    Route::get('/sec-analysis/data/matrix', [Admin\SecAnalysisController::class, 'getMatrixData'])->name('sec-analysis.matrix-data');
    Route::post('/sec-analysis/data/energy', [Admin\SecAnalysisController::class, 'storeEnergyData'])->name('sec-analysis.store-energy');
    Route::post('/sec-analysis/data/production', [Admin\SecAnalysisController::class, 'storeProductionData'])->name('sec-analysis.store-production');
    Route::post('/sec-analysis/poe', [Admin\SecAnalysisController::class, 'storePoe'])->name('sec-analysis.store-poe');
    Route::post('/sec-analysis', [Admin\SecAnalysisController::class, 'store'])->name('sec-analysis.store');
    Route::post('/sec-analysis/monthly-poe', [Admin\SecAnalysisController::class, 'storeMonthlyPoe'])->name('sec-analysis.store-monthly-poe');
    Route::get('/sec-analysis/monthly-poe', [Admin\SecAnalysisController::class, 'getMonthlyPoe'])->name('sec-analysis.get-monthly-poe');
    Route::get('/sec-analysis/{id}', [Admin\SecAnalysisController::class, 'show'])->name('sec-analysis.show');
    Route::put('/sec-analysis/{id}', [Admin\SecAnalysisController::class, 'update'])->name('sec-analysis.update');
    Route::delete('/sec-analysis/{id}', [Admin\SecAnalysisController::class, 'destroy'])->name('sec-analysis.destroy');
    
    Route::get('/eip-analysis', [Admin\EIPAnalysisController::class, 'index'])->name('eip-analysis.index');
    Route::post('/eip-analysis/data/matrix', [Admin\EIPAnalysisController::class, 'getMatrixData'])->name('eip-analysis.matrix-data');
    Route::post('/eip-analysis/data/energy', [Admin\EIPAnalysisController::class, 'storeEnergyData'])->name('eip-analysis.store-energy');
    Route::post('/eip-analysis/data/variable', [Admin\EIPAnalysisController::class, 'storeVariableData'])->name('eip-analysis.store-variable');

    // EIP Analysis - Enhanced Filter Endpoints
    Route::post('/eip-analysis/data/insights', [Admin\EIPAnalysisController::class, 'getFilterInsights'])->name('eip-analysis.insights');
    Route::get('/eip-analysis/export', [Admin\EIPAnalysisController::class, 'exportData'])->name('eip-analysis.export');

    // EIP Filter Presets
    Route::post('/eip-analysis/presets', [Admin\EIPAnalysisController::class, 'savePreset'])->name('eip-analysis.presets.store');
    Route::get('/eip-analysis/presets', [Admin\EIPAnalysisController::class, 'listPresets'])->name('eip-analysis.presets.index');
    Route::get('/eip-analysis/presets/{id}', [Admin\EIPAnalysisController::class, 'loadPreset'])->name('eip-analysis.presets.show');
    Route::delete('/eip-analysis/presets/{id}', [Admin\EIPAnalysisController::class, 'deletePreset'])->name('eip-analysis.presets.destroy');
    Route::post('/eip-analysis/presets/{id}/favorite', [Admin\EIPAnalysisController::class, 'toggleFavorite'])->name('eip-analysis.presets.favorite');

    // EIP Targets & Normalization
    Route::get('/eip-analysis/targets', [Admin\EIPAnalysisController::class, 'getTargets'])->name('eip-analysis.targets');
    Route::post('/eip-analysis/targets', [Admin\EIPAnalysisController::class, 'storeTarget'])->name('eip-analysis.targets.store');
    Route::get('/eip-analysis/currency-rates', [Admin\EIPAnalysisController::class, 'getCurrencyRates'])->name('eip-analysis.currency-rates');

    Route::get('/load-apportioning-energy', [Admin\EnergyApportioningController::class, 'index'])->name('load-apportioning-energy.index');
    // SEU Flagging Routes
    Route::prefix('seu-flagging')->name('seu-flagging.')->group(function () {
        Route::get('/', [Admin\SeuFlaggingController::class, 'index'])->name('index');
        Route::post('/criteria', [Admin\SeuFlaggingController::class, 'updateCriteria'])->name('criteria.update');
        Route::post('/generate', [Admin\SeuFlaggingController::class, 'generate'])->name('generate');
        Route::post('/{id}/toggle-flag', [Admin\SeuFlaggingController::class, 'toggleFlag'])->name('toggle-flag');
        Route::get('/data', [Admin\SeuFlaggingController::class, 'getData'])->name('data');
        Route::get('/chart-data', [Admin\SeuFlaggingController::class, 'getChartData'])->name('chart-data');
        Route::get('/export', [Admin\SeuFlaggingController::class, 'export'])->name('export');
    });
    Route::get('/settings', fn() => view('admin.settings.index'))->name('settings.index');

    Route::get('/energy-type-settings', [Admin\EnergyTypeSettingsController::class, 'index'])->name('energy-type-settings.index');

    Route::prefix('/energy-type-settings')->name('energy-type-settings.')->group(function () {
        Route::post('/', [Admin\EnergyTypeSettingsController::class, 'store']);
        Route::get('/{id}/edit', [Admin\EnergyTypeSettingsController::class, 'edit']);
        Route::put('/{id}', [Admin\EnergyTypeSettingsController::class, 'update']);
        Route::delete('/{id}', [Admin\EnergyTypeSettingsController::class, 'destroy']);
    });

    // Load Apportioning (Energy Review module)
    Route::prefix('load-apportioning')->name('load-apportioning.')->group(function () {
        Route::get('/', [Admin\LoadApportioningController::class, 'index'])->name('index');
        Route::get('/data', [Admin\LoadApportioningController::class, 'getData'])->name('data');
        Route::post('/save', [Admin\LoadApportioningController::class, 'save'])->name('save');
        Route::get('/seu-summary', [Admin\LoadApportioningController::class, 'getSeuSummary'])->name('seu-summary');
        Route::get('/monthly-ng', [Admin\LoadApportioningController::class, 'getMonthlyNgBreakdown'])->name('monthly-ng');
        Route::get('/monthly-resource', [Admin\LoadApportioningController::class, 'getMonthlyNgBreakdown'])->name('monthly-resource');
        Route::post('/approaches', [Admin\LoadApportioningController::class, 'storeApproach'])->name('approaches.store');
        Route::get('/equipment-counts', [Admin\LoadApportioningController::class, 'getEquipmentCounts'])->name('equipment-counts');
    });

    // SEC Analysis - Monthly POE

    // EIP Analysis - Regression Data
    Route::post('/eip-analysis/data/regression', [Admin\EIPAnalysisController::class, 'getRegressionData'])->name('eip-analysis.regression-data');
    // Action Plan - Overview
    Route::get('/action-plan/overview', [Admin\ActionPlanController::class, 'overview'])->name('action-plan.overview');

    // Action Plan - Yearly
    Route::get('/action-plan/yearly', [Admin\ActionPlanController::class, 'yearly'])->name('action-plan.yearly');
    Route::post('/action-plan/yearly', [Admin\ActionPlanController::class, 'storeYearly'])->name('action-plan.yearly.store');

    // Action Plan Development - Motivation Strategy
    Route::get('/action-plan/motivation-strategy', [Admin\MotivationStrategyController::class, 'index'])->name('action-plan.motivation-strategy');
    Route::post('/action-plan/motivation-strategy', [Admin\MotivationStrategyController::class, 'store'])->name('action-plan.motivation-strategy.store');
    Route::get('action-plan/motivation-strategy/{id}/edit', [Admin\MotivationStrategyController::class, 'edit'])->name('admin.motivation-strategy.edit');
    Route::put('/action-plan/motivation-strategy/{id}', [Admin\MotivationStrategyController::class, 'update'])->name('action-plan.motivation-strategy.update');
    Route::delete('/action-plan/motivation-strategy/{id}', [Admin\MotivationStrategyController::class, 'destroy'])->name('action-plan.motivation-strategy.destroy');

    // Action Plan Development - Communication & Awareness
    Route::prefix('action-plan/communication-awareness')
    ->group(function () {
        Route::get('/', [Admin\CommunicationAwarenessController::class, 'index'])
            ->name('action-plan.communication-awarness');

        Route::post('/', [Admin\CommunicationAwarenessController::class, 'store'])
            ->name('action-plan.communication-awarness.store');

        Route::get('/{id}/edit', [Admin\CommunicationAwarenessController::class, 'edit'])
            ->name('admin.communication-awarness.edit');

        Route::put('/{id}', [Admin\CommunicationAwarenessController::class, 'update'])
            ->name('action-plan.communication-awarness.update');

        Route::delete('/{id}', [Admin\CommunicationAwarenessController::class, 'destroy'])
            ->name('action-plan.communication-awarness.destroy');
    });

    Route::get('action-plan/training-plan', [Admin\TrainingPlanController::class, 'index'])->name('admin.training-plans.index');
    Route::post('action-plan/training-plan', [Admin\TrainingPlanController::class, 'store'])->name('admin.training-plans.store');
    Route::delete('action-plan/training-plan/{trainingPlan}', [Admin\TrainingPlanController::class, 'destroy'])->name('admin.training-plans.destroy');

    // EnPI & Baseline Management
    // Route::resource('enpi-baseline-management', App\Http\Controllers\BaselineModelController::class);

    Route::get('/enpi-baseline-management/get-variables', [App\Http\Controllers\BaselineModelController::class, 'getVariables'])
        ->name('enpi-baseline-management.get-variables');

    Route::get('/enpi-baseline-management/dependent-options', [App\Http\Controllers\BaselineModelController::class, 'getDependentOptions'])
        ->name('enpi-baseline-management.dependent-options');

    Route::get('/enpi-baseline-management/independent-options', [App\Http\Controllers\BaselineModelController::class, 'getIndependentOptions'])
        ->name('enpi-baseline-management.independent-options');

    Route::get('/enpi-baseline-management', [App\Http\Controllers\BaselineModelController::class, 'index'])
        ->name('enpi-baseline-management.index');

    Route::post('/enpi-baseline-management', [App\Http\Controllers\BaselineModelController::class, 'store'])
        ->name('enpi-baseline-management.store');

    Route::get('/enpi-baseline-management/{id}/calculate', [App\Http\Controllers\BaselineModelController::class, 'calculate'])
        ->name('enpi-baseline-management.calculate');

    Route::get('/enpi-baseline-management/{id}/export-excel', [App\Http\Controllers\BaselineModelController::class, 'exportExcel'])
        ->name('enpi-baseline-management.export-excel');

    Route::put('/enpi-baseline-management/{id}', [App\Http\Controllers\BaselineModelController::class, 'update'])
        ->name('enpi-baseline-management.update');

    Route::delete('/enpi-baseline-management/{id}', [App\Http\Controllers\BaselineModelController::class, 'destroy'])
        ->name('enpi-baseline-management.destroy');

    Route::patch('/enpi-baseline-management/{id}/approve', [App\Http\Controllers\BaselineModelController::class, 'approve'])
        ->name('enpi-baseline-management.approve');

    Route::patch('/enpi-baseline-management/{id}/disapprove', [App\Http\Controllers\BaselineModelController::class, 'disapprove'])
        ->name('enpi-baseline-management.disapprove');

});

/*
|--------------------------------------------------------------------------
| Superadmin Only Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {
    // User Management
    Route::resource('users', Admin\UserController::class);
    Route::resource('permissions', Admin\PermissionController::class);
    Route::resource('roles', Admin\RoleController::class);

    // User Permission Management
    Route::resource('user-permissions', Admin\RolePermissionController::class)
        ->only(['index', 'edit', 'update'])
        ->parameters(['user-permissions' => 'user']);
    Route::post('/user-permissions/bulk-update', [Admin\RolePermissionController::class, 'bulkUpdate'])->name('user-permissions.bulk-update');
});

/*
|--------------------------------------------------------------------------
| Energy Data Management — EMT, Superadmin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:energy-data-management,view'])->prefix('admin')->name('admin.')->group(function () {
    // Energy Data Management
    Route::get('/energy-data-management', [Admin\EnergyDataController::class, 'index'])
        ->name('energy-data-management.index');
    Route::get('/energy-data-management/summarize', [Admin\EnergyDataController::class, 'summarize'])
        ->name('energy-data-management.summarize');

    // ==================== ENERGY DATA ROUTES ====================
    Route::post('/energy-data-management/store-data', [Admin\EnergyDataController::class, 'storeEnergyData'])
        ->name('energy-data-management.store-data');
    
    Route::get('/energy-data/{id}/edit', [Admin\EnergyDataController::class, 'editEnergyData'])
        ->name('energy-data.edit');
    
    Route::put('/energy-data/{id}', [Admin\EnergyDataController::class, 'updateEnergyData'])
        ->name('energy-data.update');
    
    Route::delete('/energy-data/{id}', [Admin\EnergyDataController::class, 'destroyEnergyData'])
        ->name('energy-data.destroy');
        
    Route::get('/energy-data/{id}/download-excel', [Admin\EnergyDataController::class, 'downloadEnergyDataExcel'])
        ->name('admin.energy-data.download-excel');

    // Energy Data Usage routes
    Route::post('/energy-data/{id}/calculate', [Admin\EnergyDataController::class, 'storeEnergyDataUsage'])
        ->name('energy-data-management.store-usage');
    
    Route::get('/energy-data/{id}/usage', [Admin\EnergyDataController::class, 'getEnergyDataUsage'])
        ->name('energy-data-management.get-usage');

    Route::get('/energy-data/download-template', [Admin\EnergyDataController::class, 'downloadTemplate'])
        ->name('energy-data-management.download-template');

    // Energy Data Conversion Factors
    Route::get('/energy-data/{id}/conversion-factors', [Admin\EnergyDataController::class, 'getEnergyDataConversionFactors'])
        ->name('energy-data.conversion-factors.index');
    Route::post('/energy-data/{id}/conversion-factors', [Admin\EnergyDataController::class, 'storeEnergyDataConversionFactor'])
        ->name('energy-data.conversion-factors.store');
    Route::delete('/energy-data/{id}/conversion-factors/{factorId}', [Admin\EnergyDataController::class, 'destroyEnergyDataConversionFactor'])
        ->name('energy-data.conversion-factors.destroy');

    // ==================== ENERGY RESOURCE DATA ROUTES ====================
    Route::get('/energy-resource-data/download-template', [Admin\EnergyDataController::class, 'downloadResourceTemplate'])
        ->name('energy-resource-data.download-template');
        
    Route::post('/energy-data-management/store-resource', [Admin\EnergyDataController::class, 'storeEnergyResourceData'])
        ->name('energy-data-management.store-resource');
    
    Route::get('/energy-resource-data/{id}/edit', [Admin\EnergyDataController::class, 'editEnergyResourceData'])
        ->name('energy-resource-data.edit');
    
    Route::put('/energy-resource-data/{id}', [Admin\EnergyDataController::class, 'updateEnergyResourceData'])
        ->name('energy-resource-data.update');
    
    Route::delete('/energy-resource-data/{id}', [Admin\EnergyDataController::class, 'destroyEnergyResourceData'])
        ->name('energy-resource-data.destroy');

    Route::get('/energy-resource-data/{id}/download-excel', [Admin\EnergyDataController::class, 'downloadResourceDataExcel'])
        ->name('admin.energy-resource-data.download-excel');

    // Energy Resource Usage routes
    Route::post('/energy-resource-data/{id}/calculate', [Admin\EnergyDataController::class, 'storeEnergyResourceUsage'])
        ->name('energy-resource-data.store-usage');
    
    Route::get('/energy-resource-data/{id}/usage', [Admin\EnergyDataController::class, 'getEnergyResourceUsage'])
        ->name('energy-resource-data.get-usage');

    // Energy Resource Conversion Factors
    Route::get('/energy-resource-data/{id}/conversion-factors', [Admin\EnergyDataController::class, 'getEnergyResourceConversionFactors'])
        ->name('energy-resource-data.conversion-factors.index');
    Route::post('/energy-resource-data/{id}/conversion-factors', [Admin\EnergyDataController::class, 'storeEnergyResourceConversionFactor'])
        ->name('energy-resource-data.conversion-factors.store');
    Route::delete('/energy-resource-data/{id}/conversion-factors/{factorId}', [Admin\EnergyDataController::class, 'destroyEnergyResourceConversionFactor'])
        ->name('energy-resource-data.conversion-factors.destroy');

    // ==================== MONTHLY PRODUCTION ROUTES ====================
    Route::get('/monthly-production/download-template', [Admin\EnergyDataController::class, 'downloadProductionTemplate'])
        ->name('monthly-production.download-template');

    Route::post('/energy-data-management/store-production', [Admin\EnergyDataController::class, 'storeMonthlyProduction'])
        ->name('energy-data-management.store-production');
    
    Route::get('/monthly-production/{id}/edit', [Admin\EnergyDataController::class, 'editMonthlyProduction'])
        ->name('monthly-production.edit');
    
    Route::put('/monthly-production/{id}', [Admin\EnergyDataController::class, 'updateMonthlyProduction'])
        ->name('monthly-production.update');
    
    Route::delete('/monthly-production/{id}', [Admin\EnergyDataController::class, 'destroyMonthlyProduction'])
        ->name('monthly-production.destroy');

    // Monthly Production Usage routes
    Route::post('/monthly-production/{id}/calculate', [Admin\EnergyDataController::class, 'storeMonthlyProductionUsage'])
        ->name('monthly-production.store-usage');
    
    Route::get('/monthly-production/{id}/usage', [Admin\EnergyDataController::class, 'getMonthlyProductionUsage'])
        ->name('monthly-production.get-usage');

    Route::get('/monthly-production/{id}/download-excel', [Admin\EnergyDataController::class, 'downloadProductionExcel'])
        ->name('admin.monthly-production.download-excel');

    // ==================== MONTHLY VARIABLE ROUTES ====================
    Route::get('/monthly-variable/download-template', [Admin\EnergyDataController::class, 'downloadVariableTemplate'])
        ->name('monthly-variable.download-template');

    Route::post('/energy-data-management/store-variable', [Admin\EnergyDataController::class, 'storeMonthlyVariable'])
        ->name('energy-data-management.store-variable');
    
    Route::get('/monthly-variable/{id}/edit', [Admin\EnergyDataController::class, 'editMonthlyVariable'])
        ->name('monthly-variable.edit');
    
    Route::put('/monthly-variable/{id}', [Admin\EnergyDataController::class, 'updateMonthlyVariable'])
        ->name('monthly-variable.update');
    
    Route::delete('/monthly-variable/{id}', [Admin\EnergyDataController::class, 'destroyMonthlyVariable'])
        ->name('monthly-variable.destroy');

    Route::get('/monthly-variable/{id}/usage', [Admin\EnergyDataController::class, 'getMonthlyVariableUsage'])
        ->name('monthly-variable.get-usage');

    Route::post('/monthly-variable/{id}/calculate', [Admin\EnergyDataController::class, 'storeMonthlyVariableUsage'])
        ->name('monthly-variable.store-usage');

    Route::get('/monthly-variable/{id}/download-excel', [Admin\EnergyDataController::class, 'downloadVariableExcel'])
        ->name('admin.monthly-variable.download-excel');

});


