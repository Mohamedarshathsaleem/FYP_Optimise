<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnergyDataUsage;
use App\Models\EnergyResourceUsage;
use Illuminate\Http\Request;

class UtilityApportioningController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = now()->year;
        $years = range($currentYear, $currentYear - 10);
        $year = $request->get('year');

        // If no year selected yet, return view with empty data
        if (!$year) {
            return view('admin.utility-apportioning.index', compact('years', 'year'));
        }

        $monthKeys = ['01','02','03','04','05','06','07','08','09','10','11','12'];
        $monthLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        // Pull monthly energy (GJ + cost) grouped by month
        $energyData = EnergyDataUsage::where('month', 'like', "$year-%")
            ->selectRaw('month, SUM(usage_gj) as total_gj, SUM(cost) as total_cost')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // Pull monthly energy resource (GJ + cost) grouped by month
        $resourceData = EnergyResourceUsage::where('month', 'like', "$year-%")
            ->selectRaw('month, SUM(usage_gj) as total_gj, SUM(cost) as total_cost')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // Build 12-month matrix
        $rows = [];
        $totalEnergyGj = $totalResourceGj = $totalEnergyCost = $totalResourceCost = 0;
        $hasData = false;

        foreach ($monthKeys as $i => $m) {
            $key = "$year-$m";
            $eGj   = round((float)($energyData[$key]->total_gj ?? 0), 2);
            $eCost = round((float)($energyData[$key]->total_cost ?? 0), 2);
            $rGj   = round((float)($resourceData[$key]->total_gj ?? 0), 2);
            $rCost = round((float)($resourceData[$key]->total_cost ?? 0), 2);

            if ($eGj > 0 || $eCost > 0 || $rGj > 0 || $rCost > 0) {
                $hasData = true;
            }

            $rows[] = [
                'month'         => $monthLabels[$i],
                'energy_gj'     => $eGj,
                'resource_gj'   => $rGj,
                'energy_cost'   => $eCost,
                'resource_cost' => $rCost,
            ];

            $totalEnergyGj     += $eGj;
            $totalResourceGj   += $rGj;
            $totalEnergyCost   += $eCost;
            $totalResourceCost += $rCost;
        }

        // Averages
        $avgEnergyGj     = round($totalEnergyGj / 12, 2);
        $avgResourceGj   = round($totalResourceGj / 12, 2);
        $avgEnergyCost   = round($totalEnergyCost / 12, 2);
        $avgResourceCost = round($totalResourceCost / 12, 2);

        // Percentage apportioning
        $totalGj   = $totalEnergyGj + $totalResourceGj;
        $totalCost = $totalEnergyCost + $totalResourceCost;

        $pctEnergyGj     = $totalGj   > 0 ? round($totalEnergyGj / $totalGj * 100, 2) : 0;
        $pctResourceGj   = $totalGj   > 0 ? round($totalResourceGj / $totalGj * 100, 2) : 0;
        $pctEnergyCost   = $totalCost > 0 ? round($totalEnergyCost / $totalCost * 100, 2) : 0;
        $pctResourceCost = $totalCost > 0 ? round($totalResourceCost / $totalCost * 100, 2) : 0;

        return view('admin.utility-apportioning.index', compact(
            'rows', 'year', 'years', 'hasData',
            'avgEnergyGj', 'avgResourceGj', 'avgEnergyCost', 'avgResourceCost',
            'pctEnergyGj', 'pctResourceGj', 'pctEnergyCost', 'pctResourceCost',
            'totalEnergyGj', 'totalResourceGj', 'totalEnergyCost', 'totalResourceCost'
        ));
    }
}
