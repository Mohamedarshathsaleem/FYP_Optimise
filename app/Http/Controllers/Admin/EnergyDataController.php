<?php
// app/Http/Controllers/Admin/EnergyDataController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnergyData;
use App\Models\EnergyDataUsage;
use App\Models\EnergyResourceData;
use App\Models\EnergyResourceUsage;
use App\Models\MonthlyProduction;
use \App\Models\MonthlyProductionUsage;
use App\Models\MonthlyVariable;
use App\Models\ConversionFactor;
use App\Models\EnergyDataConversionFactor;
use App\Models\EnergyResourceConversionFactor;
use App\Models\EnergyUnit;
use \App\Models\MonthlyVariableUsage;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelReader;
use Spatie\SimpleExcel\SimpleExcelWriter;

class EnergyDataController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $category = $request->get('category', 'All');
        
        // Fetch data filtered by category
        if ($category === 'All') {
            // Show all categories
            $energyData = EnergyData::all();
            $energyResourceData = EnergyResourceData::all();
            $monthlyProductions = MonthlyProduction::all();
            $monthlyVariables = MonthlyVariable::all();
        } else {
            // Filter by specific category
            $energyData = EnergyData::where('category', $category)->get();
            $energyResourceData = EnergyResourceData::where('category', $category)->get();
            $monthlyProductions = MonthlyProduction::where('category', $category)->get();
            $monthlyVariables = MonthlyVariable::where('category', $category)->get();
        }

        $energyUnits = EnergyUnit::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.energy-data-management.index', compact(
            'energyData',
            'energyResourceData',
            'monthlyProductions',
            'monthlyVariables',
            'category',
            'energyUnits'
        ));
    }

    /**
    * Show summarize/apportioning page
    */
    public function summarize(Request $request)
    {
        $category = $request->get('category', 'All');
        $startMonth = $request->get('start_month');
        $endMonth = $request->get('end_month');

        // Validate date range
        if (!$startMonth || !$endMonth) {
            return redirect()->back()->with('error', 'Please select both start and end months');
        }

        if ($startMonth > $endMonth) {
            return redirect()->back()->with('error', 'Start month must be before end month');
        }

        // Fetch all energy data for the category
        if ($category === 'All') {
            $energyData = EnergyData::all();
            $resourceData = EnergyResourceData::all();
        } else {
            $energyData = EnergyData::where('category', $category)->get();
            $resourceData = EnergyResourceData::where('category', $category)->get();
        }

        // Calculate total energy usage (GJ) for each month in the range
        $monthlyTotals = [];
        
        // Generate month range
        $currentMonth = $startMonth;
        while ($currentMonth <= $endMonth) {
            $year = substr($currentMonth, 0, 4);
            $month = substr($currentMonth, 5, 2);
            $monthKey = "$year-$month";
            
            $totalGJ = 0;

            // Sum up all energy data usages for this month
            foreach ($energyData as $energy) {
                $usage = EnergyDataUsage::where('energy_data_id', $energy->id)
                    ->where('month', $monthKey)
                    ->first();
                
                if ($usage && $usage->usage_gj) {
                    $totalGJ += floatval($usage->usage_gj);
                }
            }

            // Also add energy resource data
            foreach ($resourceData as $resource) {
                $usage = EnergyResourceUsage::where('energy_resource_data_id', $resource->id)
                    ->where('month', $monthKey)
                    ->first();
                
                if ($usage && $usage->usage_gj) {
                    $totalGJ += floatval($usage->usage_gj);
                }
            }

            $monthlyTotals[] = [
                'month' => $monthKey,
                'total_gj' => round($totalGJ, 2)
            ];

            // Move to next month
            $date = \DateTime::createFromFormat('Y-m', $currentMonth);
            $date->modify('+1 month');
            $currentMonth = $date->format('Y-m');
        }

        return view('admin.energy-data-management.summarize', compact(
            'category',
            'startMonth',
            'endMonth',
            'monthlyTotals'
        ));
    }

    // ==================== ENERGY DATA METHODS ====================
    
   public function storeEnergyData(Request $request)
{
    $request->validate([
        'category'     => 'required|string|max:255', 
        'energytype'   => 'required|string|max:255',
        'provider'     => 'required|string|max:255',
        'accountno'    => 'required|string|max:255',
        'contracttype' => 'nullable|string|max:255'
    ]);

    try {
        $energyData = EnergyData::create([
            'category'      => $request->category,
            'energy_type'   => $request->energytype,
            'provider'      => $request->provider,
            'account_no'    => $request->accountno,
            'contract_type' => $request->contracttype,
        ]);

        // Seed default conversion factors
        EnergyDataConversionFactor::create([
            'energy_data_id' => $energyData->id,
            'from_unit' => 'kWh', 'to_unit' => 'GJ', 'factor' => 0.0036,
        ]);
        EnergyDataConversionFactor::create([
            'energy_data_id' => $energyData->id,
            'from_unit' => 'MWh', 'to_unit' => 'GJ', 'factor' => 3.6,
        ]);

        return redirect()
            ->route('admin.energy-data-management.index', ['category' => $request->category])
            ->with('success', 'Energy data added successfully');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to add energy data');
        }
    }

    public function editEnergyData($id)
    {
        $energyData = EnergyData::findOrFail($id);
        return response()->json($energyData);
    }

    public function updateEnergyData(Request $request, $id)
    {
        $request->validate([
            'category'           => 'required|string|max:255',  
            'energytype'         => 'required|string|max:255',
            'custom_energytype'  => 'nullable|string|max:255',
            'provider'           => 'required|string|max:255',
            'accountno'          => 'required|string|max:255',
            'contracttype'       => 'nullable|string|max:255'
        ]);

        try {
            $energyData = EnergyData::findOrFail($id);
            
            // Use custom energy type if "Others" was selected
            $energyType = $request->energytype === 'Others' ? $request->custom_energytype : $request->energytype;
            
            $energyData->update([
                'category'      => $request->category,    
                'energy_type'   => $energyType,  
                'provider'      => $request->provider,
                'account_no'    => $request->accountno,
                'contract_type' => $request->contracttype,
            ]);
            
            return redirect()
                ->route('admin.energy-data-management.index', ['category' => $request->category]) 
                ->with('success', 'Energy data updated successfully');
                    
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update energy data');
        }
    }

    public function destroyEnergyData($id)
    {
        try {
            EnergyData::findOrFail($id)->delete();
    
            return redirect()->back()->with('success', 'Energy data deleted successfully');
    
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete energy data');
        }
    }
    
    public function getEnergyDataUsage($id)
    {
        try {
            $energyData = EnergyData::findOrFail($id);
            $usages = $energyData->usages()->get()->keyBy('month');
            
            return response()->json([
                'success' => true,
                'data' => $usages,
                'energyData' => $energyData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve usage data'
            ], 500);
        }
    }

        /**
         * Store energy data usage (from calculator modal)
         */
    public function storeEnergyDataUsage(Request $request, $id)
    {
        try {
            $energyData = EnergyData::findOrFail($id);
            
            // Check if file upload
            if ($request->hasFile('upload_file')) {
                $year = $request->input('year');
                
                if (empty($year)) {
                    return redirect()->back()->with('error', 'Year is required when uploading file.');
                }
                
                return $this->processEnergyUsageFile($request->file('upload_file'), $energyData, $year);
            }
            
            // Manual input
            $monthlyData = $request->input('monthly', []);
            $year = $request->input('year');
            
            if (empty($monthlyData)) {
                return redirect()->back()->with('warning', 'Please either upload a file or enter data manually.');
            }
            
            if (empty($year)) {
                return redirect()->back()->with('error', 'Year is required.');
            }
            
            $savedCount = 0;
            
            foreach ($monthlyData as $monthName => $data) {
                if (empty($data['usage'])) continue;
                
                $monthMap = [
                    'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04',
                    'May' => '05', 'Jun' => '06', 'Jul' => '07', 'Aug' => '08',
                    'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12'
                ];
                
                if (!isset($monthMap[$monthName])) continue;
                
                $monthNumber = $monthMap[$monthName];
                $monthKey = $year . '-' . $monthNumber;
                
                $usageValue = floatval(str_replace(',', '', $data['usage']));
                $unit = $data['unit'] ?? 'kWh';
                $cost = !empty($data['cost']) ? floatval(str_replace(',', '', $data['cost'])) : null;
                
                $factor = EnergyDataConversionFactor::resolveForUnit($energyData->id, $unit);
                $gj = round($usageValue * $factor, 3);
                
                EnergyDataUsage::updateOrCreate(
                    [
                        'energy_data_id' => $energyData->id,
                        'month' => $monthKey
                    ],
                    [
                        'usage_value' => $usageValue,
                        'usage_unit' => $unit,
                        'usage_gj' => $gj,
                        'cost' => $cost
                    ]
                );
                
                $savedCount++;
            }
            
            if ($savedCount === 0) {
                return redirect()->back()->with('warning', 'No valid data was entered.');
            }
            
            return redirect()->back()->with('success', "Energy usage data saved successfully. {$savedCount} months saved.");
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save: ' . $e->getMessage());
        }
    }

    /**
     * Process Excel file for energy usage data
     */
    private function processEnergyUsageFile($file, $energyData, $year = null)
    {
        try {
            if (empty($year)) {
                $year = date('Y');
            }
            
            // Move the file to a temporary location with proper extension
            $tempPath = $file->getRealPath();
            $extension = $file->getClientOriginalExtension();
            $tempFile = sys_get_temp_dir() . '/' . uniqid() . '.' . $extension;
            copy($tempPath, $tempFile);
            
            $rows = SimpleExcelReader::create($tempFile)
                ->getRows()
                ->toArray();
            
            // Clean up temp file
            @unlink($tempFile);
            
            if (empty($rows)) {
                return redirect()->back()->with('error', 'File is empty or could not be read.');
            }
            
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $savedCount = 0;
            
            // Skip first row (header)
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // Skip header
                if ($index > 12) break; // Only 12 months
                
                $monthIndex = $index - 1;
                if ($monthIndex < 0 || $monthIndex >= 12) continue;
                
                $monthNumber = str_pad($monthIndex + 1, 2, '0', STR_PAD_LEFT);
                $monthKey = $year . '-' . $monthNumber;
                
                // Try multiple column access methods
                $usageValue = $row['Monthly Energy Usage'] 
                        ?? $row['monthly energy usage'] 
                        ?? $row[1] 
                        ?? null;
                        
                $unit = $row['Units'] 
                    ?? $row['units'] 
                    ?? $row[2] 
                    ?? 'kWh';
                    
                $cost = $row['Monthly Cost (RM)'] 
                    ?? $row['monthly cost (rm)'] 
                    ?? $row[3] 
                    ?? null;
                
                // Skip empty rows
                if (empty($usageValue) || $usageValue === '' || $usageValue === null) {
                    continue;
                }
                
                // Clean and convert
                $usageValue = floatval(str_replace(',', '', $usageValue));
                $cost = !empty($cost) ? floatval(str_replace(',', '', $cost)) : null;
                
                // Validate unit
                $factor = EnergyDataConversionFactor::resolveForUnit($energyData->id, $unit);
                $gj = round($usageValue * $factor, 3);
                
                EnergyDataUsage::updateOrCreate(
                    [
                        'energy_data_id' => $energyData->id,
                        'month' => $monthKey
                    ],
                    [
                        'usage_value' => $usageValue,
                        'usage_unit' => $unit,
                        'usage_gj' => $gj,
                        'cost' => $cost
                    ]
                );
                
                $savedCount++;
            }
            
            if ($savedCount === 0) {
                return redirect()->back()->with('warning', 'No valid data found in file. Please check the template format.');
            }
            
            return redirect()->back()->with('success', "Energy usage data imported successfully. {$savedCount} months saved for year {$year}.");
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to process file: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template for energy usage (using Spatie)
     */
    public function downloadTemplate()
    {
        try {
            $fileName = 'energy_usage_template_' . date('Y-m-d') . '.xlsx';
            $filePath = storage_path('app/public/' . $fileName);
            
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $rows = [];
            
            // Header
            $rows[] = [
                'Month/Year',
                'Monthly Energy Usage',
                'Units',
                'Monthly Cost (RM)',
                'Monthly Energy Usage (GJ)'
            ];
            
            // Data rows
            foreach ($months as $index => $month) {
                if ($index < 3) {
                    $exampleUsage = 1500 + ($index * 100);
                    $exampleCost = $exampleUsage * 0.30;
                    
                    $rows[] = [
                        $month,
                        $exampleUsage,
                        'kWh',
                        $exampleCost,
                        '(Auto-calculated)'
                    ];
                } else {
                    $rows[] = [
                        $month,
                        '',
                        'kWh',
                        '',
                        '(Auto-calculated)'
                    ];
                }
            }
            
            $writer = SimpleExcelWriter::create($filePath);
            
            foreach ($rows as $row) {
                $writer->addRow($row);
            }
            
            $writer->close();
            
            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate template: ' . $e->getMessage());
        }
    }

    public function downloadEnergyDataExcel($id, Request $request)
    {
        try {
            $year = $request->get('year', date('Y'));
            $energyData = EnergyData::findOrFail($id);
            
            $fileName = 'energy_data_' . str_replace(' ', '_', $energyData->provider) . '_' . $year . '.xlsx';
            $filePath = storage_path('app/public/' . $fileName);
            
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $rows = [];
            
            // Header
            $rows[] = [
                'Month/Year',
                'Monthly Energy Usage',
                'Units',
                'Monthly Cost (RM)',
                'Monthly Energy Usage (GJ)'
            ];
            
            // Data rows
            foreach ($months as $index => $month) {
                $monthNumber = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                $monthKey = $year . '-' . $monthNumber;
                
                $usage = EnergyDataUsage::where('energy_data_id', $energyData->id)
                    ->where('month', $monthKey)
                    ->first();
                
                $rows[] = [
                    $month,
                    $usage ? $usage->usage_value : '',
                    $usage ? $usage->usage_unit : 'kWh',
                    $usage ? $usage->cost : '',
                    $usage ? $usage->usage_gj : ''
                ];
            }
            
            $writer = SimpleExcelWriter::create($filePath);
            foreach ($rows as $row) {
                $writer->addRow($row);
            }
            $writer->close();
            
            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate Excel: ' . $e->getMessage());
        }
    }

    // ==================== ENERGY RESOURCE DATA METHODS ====================
    
    public function storeEnergyResourceData(Request $request)
{
    $request->validate([
        'category'     => 'required|string|max:255',  
        'resourcetype' => 'required|string|max:255',
        'provider'     => 'required|string|max:255',
        'accountno'    => 'required|string|max:255',
        'contracttype' => 'nullable|string|max:255'
    ]);

    try {
        $resourceData = EnergyResourceData::create([
            'category'      => $request->category,
            'resource_type' => $request->resourcetype,
            'provider'      => $request->provider,
            'account_no'    => $request->accountno,
            'contract_type' => $request->contracttype,
        ]);

        // Seed default conversion factors
        $defaultFactors = [
            ['from_unit' => 'kWh', 'factor' => 0.0036],
            ['from_unit' => 'MWh', 'factor' => 3.6],
            ['from_unit' => 'L', 'factor' => 0.0347],
            ['from_unit' => 'kg', 'factor' => 0.0464],
            ['from_unit' => 'ton', 'factor' => 46.4],
            ['from_unit' => 'Gallon', 'factor' => 0.131],
            ['from_unit' => 'm3', 'factor' => 0.0378],
        ];
        foreach ($defaultFactors as $df) {
            EnergyResourceConversionFactor::create([
                'energy_resource_data_id' => $resourceData->id,
                'from_unit' => $df['from_unit'],
                'to_unit' => 'GJ',
                'factor' => $df['factor'],
            ]);
        }

        return redirect()
            ->route('admin.energy-data-management.index', ['category' => $request->category])
            ->with('success', 'Energy resource data added successfully');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to add energy resource data');
        }
    }

    public function editEnergyResourceData($id)
    {
        $energyResourceData = EnergyResourceData::findOrFail($id);
        return response()->json($energyResourceData);
    }

    public function updateEnergyResourceData(Request $request, $id)
    {
        $request->validate([
            'category'            => 'required|string|max:255', 
            'resourcetype'        => 'required|string|max:255',
            'custom_resourcetype' => 'nullable|string|max:255',  // NEW
            'provider'            => 'required|string|max:255',
            'accountno'           => 'required|string|max:255',
            'contracttype'        => 'nullable|string|max:255'
        ]);

        try {
            $energyResourceData = EnergyResourceData::findOrFail($id);
            
            // Use custom resource type if "Others" was selected
            $resourceType = $request->resourcetype === 'Others' ? $request->custom_resourcetype : $request->resourcetype;
            
            $energyResourceData->update([
                'category'      => $request->category,     
                'resource_type' => $resourceType,  // CHANGED
                'provider'      => $request->provider,
                'account_no'    => $request->accountno,
                'contract_type' => $request->contracttype,
            ]);
            
            return redirect()
                ->route('admin.energy-data-management.index', ['category' => $request->category])  
                ->with('success', 'Energy resource data updated successfully');
                    
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update energy resource data');
        }
    }

    public function destroyEnergyResourceData($id)
    {
        try {
            EnergyResourceData::findOrFail($id)->delete();
    
            return redirect()->back()->with('success', 'Resource data deleted successfully');
    
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete resource data');
        }
    }

    public function getEnergyResourceUsage($id)
    {
        try {
            $energyResourceData = EnergyResourceData::findOrFail($id);
            $usages = $energyResourceData->usages()->get()->keyBy('month');
            
            return response()->json([
                'success' => true,
                'data' => $usages,
                'energyResourceData' => $energyResourceData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve usage data'
            ], 500);
        }
    }

    public function storeEnergyResourceUsage(Request $request, $id)
    {
        try {
            $energyResourceData = EnergyResourceData::findOrFail($id);
            
            if ($request->hasFile('upload_file')) {
                return $this->processEnergyResourceFile($request->file('upload_file'), $energyResourceData);
            }
            
            $monthlyData = $request->input('monthly', []);
            
            if (empty($monthlyData)) {
                return redirect()->back()->with('warning', 'Please either upload a file or enter data manually.');
            }
            
            $year = $request->input('year', date('Y'));
            $savedCount = 0;

            foreach ($monthlyData as $monthName => $data) {
                if (empty($data['usage'])) continue;

                $monthMap = [
                    'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04',
                    'May' => '05', 'Jun' => '06', 'Jul' => '07', 'Aug' => '08',
                    'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12'
                ];

                if (!isset($monthMap[$monthName])) continue;

                $monthNumber = $monthMap[$monthName];
                $monthKey = $year . '-' . $monthNumber;
                
                $usageValue = floatval(str_replace(',', '', $data['usage']));
                $unit = $data['unit'] ?? 'L';
                $cost = !empty($data['cost']) ? floatval(str_replace(',', '', $data['cost'])) : null;
                $factor = EnergyResourceConversionFactor::resolveForUnit($energyResourceData->id, $unit);
                $gj = round($usageValue * $factor, 3);
                
                \App\Models\EnergyResourceUsage::updateOrCreate(
                    [
                        'energy_resource_data_id' => $energyResourceData->id,
                        'month' => $monthKey
                    ],
                    [
                        'usage_value' => $usageValue,
                        'usage_unit' => $unit,
                        'usage_gj' => $gj,
                        'cost' => $cost
                    ]
                );
                
                $savedCount++;
            }
            
            if ($savedCount === 0) {
                return redirect()->back()->with('warning', 'No valid data was entered.');
            }
            
            return redirect()->back()->with('success', "Energy resource usage saved successfully. {$savedCount} months saved.");
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save: ' . $e->getMessage());
        }
    }

    private function processEnergyResourceFile($file, $energyResourceData, $year = null)
    {
        try {
            if (empty($year)) {
                $year = date('Y');
            }
            
            // Move the file to a temporary location with proper extension
            $tempPath = $file->getRealPath();
            $extension = $file->getClientOriginalExtension();
            $tempFile = sys_get_temp_dir() . '/' . uniqid() . '.' . $extension;
            copy($tempPath, $tempFile);
            
            $rows = SimpleExcelReader::create($tempFile)
                ->getRows()
                ->toArray();
            
            // Clean up temp file
            @unlink($tempFile);
            
            if (empty($rows)) {
                return redirect()->back()->with('error', 'File is empty or could not be read.');
            }
            
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $savedCount = 0;
            
            foreach ($rows as $index => $row) {
                // Skip header row
                if ($index === 0) continue;
                
                // Only process 12 months
                if ($index > 12) break;
                
                $monthIndex = $index - 1;
                if ($monthIndex < 0 || $monthIndex >= 12) continue;
                
                $monthNumber = str_pad($monthIndex + 1, 2, '0', STR_PAD_LEFT);
                $monthKey = $year . '-' . $monthNumber;
                
                // Get values - try both header names and column indices
                $usageValue = $row['Monthly Resource Usage'] ?? $row[1] ?? null;
                $unit = $row['Units'] ?? $row[2] ?? 'L';
                $cost = $row['Monthly Cost (RM)'] ?? $row[3] ?? null;
                
                if (empty($usageValue) || $usageValue === '') continue;
                
                $usageValue = floatval(str_replace(',', '', $usageValue));
                $cost = !empty($cost) ? floatval(str_replace(',', '', $cost)) : null;
                $unit = trim($unit) ?: 'L';
                
                $factor = EnergyResourceConversionFactor::resolveForUnit($energyResourceData->id, $unit);
                $gj = round($usageValue * $factor, 3);
                
                EnergyResourceUsage::updateOrCreate(
                    [
                        'energy_resource_data_id' => $energyResourceData->id,
                        'month' => $monthKey
                    ],
                    [
                        'usage_value' => $usageValue,
                        'usage_unit' => $unit,
                        'usage_gj' => $gj,
                        'cost' => $cost
                    ]
                );
                
                $savedCount++;
            }
            
            if ($savedCount === 0) {
                return redirect()->back()->with('warning', 'No data found in file.');
            }
            
            return redirect()->back()->with('success', "Resource data imported successfully. {$savedCount} months saved for year {$year}.");
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to process file: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template for energy resource usage 
     */
    public function downloadResourceTemplate()
    {
        try {
            $fileName = 'energy_resource_template_' . date('Y-m-d') . '.xlsx';
            $filePath = storage_path('app/public/' . $fileName);
            
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $rows = [];
            
            // Add header row
            $rows[] = [
                'Month/Year',
                'Monthly Resource Usage',
                'Units',
                'Monthly Cost (RM)',
                'Monthly Resource Usage (GJ)'
            ];
            
            // Add data rows with examples for first 3 months
            foreach ($months as $index => $month) {
                if ($index < 3) {
                    $exampleUsage = 500 + ($index * 50);
                    $exampleCost = $exampleUsage * 2.5;
                    
                    $rows[] = [
                        $month,
                        $exampleUsage,
                        'L',
                        $exampleCost,
                        '(Auto-calculated)'
                    ];
                } else {
                    $rows[] = [
                        $month,
                        '',
                        'L',
                        '',
                        '(Auto-calculated)'
                    ];
                }
            }
            
            // Create the Excel file
            $writer = SimpleExcelWriter::create($filePath);
            
            foreach ($rows as $row) {
                $writer->addRow($row);
            }
            
            $writer->close();
            
            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate template: ' . $e->getMessage());
        }
    }

    public function downloadResourceDataExcel($id, Request $request)
{
    try {
        $year = $request->get('year', date('Y'));
        $resourceData = EnergyResourceData::findOrFail($id);
        
        $fileName = 'resource_data_' . str_replace(' ', '_', $resourceData->provider) . '_' . $year . '.xlsx';
        $filePath = storage_path('app/public/' . $fileName);
        
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $rows = [];
        
        $rows[] = [
            'Month/Year',
            'Monthly Resource Usage',
            'Units',
            'Monthly Cost (RM)',
            'Monthly Resource Usage (GJ)'
        ];
        
        foreach ($months as $index => $month) {
            $monthNumber = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $monthKey = $year . '-' . $monthNumber;
            
            $usage = EnergyResourceUsage::where('energy_resource_data_id', $resourceData->id)
                ->where('month', $monthKey)
                ->first();
            
            $rows[] = [
                $month,
                $usage ? $usage->usage_value : '',
                $usage ? $usage->usage_unit : 'L',
                $usage ? $usage->cost : '',
                $usage ? $usage->usage_gj : ''
            ];
        }
        
        $writer = SimpleExcelWriter::create($filePath);
        foreach ($rows as $row) {
            $writer->addRow($row);
        }
        $writer->close();
        
        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to generate Excel: ' . $e->getMessage());
    }
}

    // ==================== MONTHLY PRODUCTION METHODS ====================
    
    public function storeMonthlyProduction(Request $request)
    {
        $request->validate([
            'category'        => 'required|string|max:255',  
            'production_type' => 'required|string|max:255'
        ]);

        try {
            MonthlyProduction::create([
                'category'        => $request->category,    
                'production_type' => $request->production_type
            ]);
            
            return redirect()
                ->route('admin.energy-data-management.index', ['category' => $request->category]) 
                ->with('success', 'Monthly production added successfully');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to add monthly production');
        }
    }

    public function editMonthlyProduction($id)
    {
        $monthlyProduction = MonthlyProduction::findOrFail($id);
        return response()->json($monthlyProduction);
    }

   public function updateMonthlyProduction(Request $request, $id)
    {
        $request->validate([
            'category'        => 'required|string|max:255',  
            'production_type' => 'required|string|max:255'
        ]);

        try {
            $monthlyProduction = MonthlyProduction::findOrFail($id);
            $monthlyProduction->update([
                'category'        => $request->category,    
                'production_type' => $request->production_type
            ]);
            
            return redirect()
                ->route('admin.energy-data-management.index', ['category' => $request->category])  
                ->with('success', 'Monthly production updated successfully');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update monthly production');
        }
    }

    public function destroyMonthlyProduction($id)
    {
        try {
            MonthlyProduction::findOrFail($id)->delete();
            
            return redirect()
                ->back()
                ->with('success', 'Monthly production deleted successfully');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete monthly production');
        }
    }

    public function getMonthlyProductionUsage($id)
    {
        try {
            $monthlyProduction = MonthlyProduction::findOrFail($id);
            $usages = $monthlyProduction->usages()->get()->keyBy('month');
            
            return response()->json([
                'success' => true,
                'data' => $usages,
                'monthlyProduction' => $monthlyProduction
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve production data'
            ], 500);
        }
    }

    /**
     * Store monthly production usage
     */
    public function storeMonthlyProductionUsage(Request $request, $id)
    {
        try {
            $monthlyProduction = MonthlyProduction::findOrFail($id);
            
            // Handle file upload
            if ($request->hasFile('upload_file')) {
                $year = $request->input('year');
                
                if (empty($year)) {
                    return redirect()->back()->with('error', 'Year is required when uploading file.');
                }
                
                return $this->processMonthlyProductionFile($request->file('upload_file'), $monthlyProduction, $year);
            }
            
            // Handle manual input
            $productionData = $request->input('production', []);
            $year = $request->input('year');
            
            // Validate year
            if (empty($year)) {
                return redirect()->back()->with('error', 'Year is required.');
            }
            
            // Validate data exists
            if (empty($productionData)) {
                return redirect()->back()->with('warning', 'Please either upload a file or enter data manually.');
            }
            
            // Get unit from the global selector
            $unit = $request->input('production_unit', 'Gallon');
            
            $savedCount = 0;
            $monthMap = [
                'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04',
                'May' => '05', 'Jun' => '06', 'Jul' => '07', 'Aug' => '08',
                'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12'
            ];

            foreach ($productionData as $monthName => $data) {
                // Skip if no value entered for this month
                if (!isset($data['amount']) || $data['amount'] === '' || $data['amount'] === null) {
                    continue;
                }

                // Validate month name
                if (!isset($monthMap[$monthName])) {
                    continue;
                }

                $monthNumber = $monthMap[$monthName];
                $monthKey = $year . '-' . $monthNumber;

                // Clean and convert amount
                $amount = floatval(str_replace(',', '', $data['amount']));
                
                // Skip if amount is 0 (unless intentionally entered as 0)
                if ($amount == 0 && $data['amount'] !== '0') {
                    continue;
                }

                MonthlyProductionUsage::updateOrCreate(
                    [
                        'monthly_production_id' => $monthlyProduction->id,
                        'month' => $monthKey
                    ],
                    [
                        'production_amount' => $amount,
                        'production_unit' => $unit
                    ]
                );

                $savedCount++;
            }
            
            if ($savedCount === 0) {
                return redirect()->back()->with('warning', 'No valid data was entered. Please enter values for at least one month.');
            }
            
            return redirect()->back()->with('success', "Monthly production saved successfully. {$savedCount} months saved for year {$year}.");
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save: ' . $e->getMessage());
        }
    }

    /**
     * Process Excel file for monthly production
     */
    private function processMonthlyProductionFile($file, $monthlyProduction, $year = null)
    {
        try {
            if (empty($year)) {
                $year = date('Y');
            }
            
            // Move the file to a temporary location with proper extension
            $tempPath = $file->getRealPath();
            $extension = $file->getClientOriginalExtension();
            $tempFile = sys_get_temp_dir() . '/' . uniqid() . '.' . $extension;
            copy($tempPath, $tempFile);
            
            $rows = SimpleExcelReader::create($tempFile)
                ->getRows()
                ->toArray();
            
            // Clean up temp file
            @unlink($tempFile);
            
            if (empty($rows)) {
                return redirect()->back()->with('error', 'File is empty or could not be read.');
            }
            
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $savedCount = 0;
            
            foreach ($rows as $index => $row) {
                // Skip header row
                if ($index === 0) continue;
                
                // Only process 12 months
                if ($index > 12) break;
                
                $monthIndex = $index - 1;
                if ($monthIndex < 0 || $monthIndex >= 12) continue;
                
                $monthNumber = str_pad($monthIndex + 1, 2, '0', STR_PAD_LEFT);
                $monthKey = $year . '-' . $monthNumber;
                
                // Get values
                $amount = $row['Production Amount'] ?? $row[1] ?? null;
                $unit = $row['Units'] ?? $row[2] ?? 'Gallon';
                
                if (empty($amount) || $amount === '') continue;
                
                $amount = floatval(str_replace(',', '', $amount));
                $unit = trim($unit) ?: 'Gallon';
                
                MonthlyProductionUsage::updateOrCreate(
                    [
                        'monthly_production_id' => $monthlyProduction->id,
                        'month' => $monthKey
                    ],
                    [
                        'production_amount' => $amount,
                        'production_unit' => $unit
                    ]
                );
                
                $savedCount++;
            }
            
            if ($savedCount === 0) {
                return redirect()->back()->with('warning', 'No data found in file.');
            }
            
            return redirect()->back()->with('success', "Monthly production imported successfully. {$savedCount} months saved for year {$year}.");
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to process file: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template for monthly production
     */
    public function downloadProductionTemplate()
    {
        try {
            $fileName = 'monthly_production_template_' . date('Y-m-d') . '.xlsx';
            $filePath = storage_path('app/public/' . $fileName);
            
            $months = [
                'Jan-22', 'Feb-22', 'Mar-22', 'Apr-22', 'May-22', 'Jun-22',
                'Jul-22', 'Aug-22', 'Sep-22', 'Oct-22', 'Nov-22', 'Dec-22'
            ];
            $rows = [];
            
            // Header
            $rows[] = [
                'Month / Year',
                'Production Amount',
                'Units'
            ];
            
            // Data rows
            foreach ($months as $index => $month) {
                if ($index < 3) {
                    $exampleAmount = 500 + ($index * 100);
                    
                    $rows[] = [
                        $month,
                        $exampleAmount,
                        'Gallon'
                    ];
                } else {
                    $rows[] = [
                        $month,
                        '',
                        'Gallon'
                    ];
                }
            }
            
            $writer = SimpleExcelWriter::create($filePath);
            
            foreach ($rows as $row) {
                $writer->addRow($row);
            }
            
            $writer->close();
            
            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate template: ' . $e->getMessage());
        }
    }

    public function downloadProductionExcel($id, Request $request)
    {
        try {
            $year = $request->get('year', date('Y'));
            $production = MonthlyProduction::findOrFail($id);
            
            $fileName = 'production_' . str_replace(' ', '_', $production->production_type) . '_' . $year . '.xlsx';
            $filePath = storage_path('app/public/' . $fileName);
            
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $rows = [];
            
            $rows[] = [
                'Month/Year',
                'Production Amount',
                'Units'
            ];
            
            foreach ($months as $index => $month) {
                $monthNumber = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                $monthKey = $year . '-' . $monthNumber;
                
                $usage = MonthlyProductionUsage::where('monthly_production_id', $production->id)
                    ->where('month', $monthKey)
                    ->first();
                
                $rows[] = [
                    $month,
                    $usage ? $usage->production_amount : '',
                    $usage ? $usage->production_unit : 'Gallon'
                ];
            }
            
            $writer = SimpleExcelWriter::create($filePath);
            foreach ($rows as $row) {
                $writer->addRow($row);
            }
            $writer->close();
            
            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate Excel: ' . $e->getMessage());
        }
    }

    // ==================== MONTHLY VARIABLE METHODS ====================
    
    public function storeMonthlyVariable(Request $request)
    {
        $request->validate([
            'category'      => 'required|string|max:255',  
            'variable_name' => 'required|string|max:255'
        ]);

        try {
            MonthlyVariable::create([
                'category'      => $request->category,     
                'variable_name' => $request->variable_name
            ]);
            
            return redirect()
                ->route('admin.energy-data-management.index', ['category' => $request->category])  
                ->with('success', 'Monthly variable added successfully');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to add monthly variable');
        }
    }

    public function editMonthlyVariable($id)
    {
        $monthlyVariable = MonthlyVariable::findOrFail($id);
        return response()->json($monthlyVariable);
    }

    public function updateMonthlyVariable(Request $request, $id)
    {
        $request->validate([
            'category'      => 'required|string|max:255',  
            'variable_name' => 'required|string|max:255'
        ]);

        try {
            $monthlyVariable = MonthlyVariable::findOrFail($id);
            $monthlyVariable->update([
                'category'      => $request->category,    
                'variable_name' => $request->variable_name
            ]);
            
            return redirect()
                ->route('admin.energy-data-management.index', ['category' => $request->category]) 
                ->with('success', 'Monthly variable updated successfully');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update monthly variable');
        }
    }

    public function destroyMonthlyVariable($id)
    {
        try {
            MonthlyVariable::findOrFail($id)->delete();
            
            return redirect()
                ->back()
                ->with('success', 'Monthly variable deleted successfully');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete monthly variable');
        }
    }

    public function getMonthlyVariableUsage($id)
    {
        try {
            $monthlyVariable = MonthlyVariable::findOrFail($id);
            $usages = $monthlyVariable->usages()->get()->keyBy('month');
            
            return response()->json([
                'success' => true,
                'data' => $usages,
                'monthlyVariable' => $monthlyVariable
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve variable data'
            ], 500);
        }
    }

    /**
     * Store monthly variable usage
     */
    public function storeMonthlyVariableUsage(Request $request, $id)
    {
        try {
            $monthlyVariable = MonthlyVariable::findOrFail($id);
            
            if ($request->hasFile('upload_file')) {
                return $this->processMonthlyVariableFile($request->file('upload_file'), $monthlyVariable);
            }
            
            $variableData = $request->input('variable', []);
            
            if (empty($variableData)) {
                return redirect()->back()->with('warning', 'Please either upload a file or enter data manually.');
            }
            
            $year = $request->input('year', date('Y'));
            $savedCount = 0;

            $monthMap = [
                'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04',
                'May' => '05', 'Jun' => '06', 'Jul' => '07', 'Aug' => '08',
                'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12'
            ];

            foreach ($variableData as $monthName => $data) {
                if (empty($data['value'])) continue;

                if (!isset($monthMap[$monthName])) continue;

                $monthNumber = $monthMap[$monthName];
                $monthKey = $year . '-' . $monthNumber;

                $value = floatval(str_replace(',', '', $data['value']));

                // Get unit - use custom unit if "Others" was selected
                $unitSelect = $request->input('variable_unit', '°C');
                $unit = $unitSelect === 'Others' ? $request->input('custom_variable_unit', '°C') : $unitSelect;

                MonthlyVariableUsage::updateOrCreate(
                    [
                        'monthly_variable_id' => $monthlyVariable->id,
                        'month' => $monthKey
                    ],
                    [
                        'variable_value' => $value,
                        'variable_unit' => $unit
                    ]
                );

                $savedCount++;
            }
            
            if ($savedCount === 0) {
                return redirect()->back()->with('warning', 'No valid data was entered.');
            }
            
            return redirect()->back()->with('success', "Monthly variable saved successfully. {$savedCount} months saved.");
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save: ' . $e->getMessage());
        }
    }

    /**
     * Process Excel file for monthly variable
     */
    private function processMonthlyVariableFile($file, $monthlyVariable, $year = null)
    {
        try {
            if (empty($year)) {
                $year = date('Y');
            }
            
            // Move the file to a temporary location with proper extension
            $tempPath = $file->getRealPath();
            $extension = $file->getClientOriginalExtension();
            $tempFile = sys_get_temp_dir() . '/' . uniqid() . '.' . $extension;
            copy($tempPath, $tempFile);
            
            $rows = SimpleExcelReader::create($tempFile)
                ->getRows()
                ->toArray();
            
            // Clean up temp file
            @unlink($tempFile);
            
            if (empty($rows)) {
                return redirect()->back()->with('error', 'File is empty or could not be read.');
            }
            
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $savedCount = 0;
            
            foreach ($rows as $index => $row) {
                if ($index === 0) continue;
                if ($index > 12) break;
                
                $monthIndex = $index - 1;
                if ($monthIndex < 0 || $monthIndex >= 12) continue;
                
                $monthNumber = str_pad($monthIndex + 1, 2, '0', STR_PAD_LEFT);
                $monthKey = $year . '-' . $monthNumber;
                
                $value = $row['Variable Value'] ?? $row[1] ?? null;
                $unit = $row['Units'] ?? $row[2] ?? '°C';
                
                if (empty($value) || $value === '') continue;
                
                $value = floatval(str_replace(',', '', $value));
                $unit = trim($unit) ?: '°C';
                
                MonthlyVariableUsage::updateOrCreate(
                    [
                        'monthly_variable_id' => $monthlyVariable->id,
                        'month' => $monthKey
                    ],
                    [
                        'variable_value' => $value,
                        'variable_unit' => $unit
                    ]
                );
                
                $savedCount++;
            }
            
            if ($savedCount === 0) {
                return redirect()->back()->with('warning', 'No data found in file.');
            }
            
            return redirect()->back()->with('success', "Monthly variable imported successfully. {$savedCount} months saved for year {$year}.");
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to process file: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template for monthly variable
     */
    public function downloadVariableTemplate()
    {
        try {
            $fileName = 'monthly_variable_template_' . date('Y-m-d') . '.xlsx';
            $filePath = storage_path('app/public/' . $fileName);
            
            $months = [
                'Jan-22', 'Feb-22', 'Mar-22', 'Apr-22', 'May-22', 'Jun-22',
                'Jul-22', 'Aug-22', 'Sep-22', 'Oct-22', 'Nov-22', 'Dec-22'
            ];
            $rows = [];
            
            // Header
            $rows[] = [
                'Month / Year',
                'Variable Value',
                'Units'
            ];
            
            // Data rows
            foreach ($months as $index => $month) {
                if ($index < 3) {
                    $exampleValue = 25 + ($index * 2);
                    
                    $rows[] = [
                        $month,
                        $exampleValue,
                        '°C'
                    ];
                } else {
                    $rows[] = [
                        $month,
                        '',
                        '°C'
                    ];
                }
            }
            
            $writer = SimpleExcelWriter::create($filePath);
            
            foreach ($rows as $row) {
                $writer->addRow($row);
            }
            
            $writer->close();
            
            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate template: ' . $e->getMessage());
        }
    }

    // ==================== ENERGY DATA CONVERSION FACTORS ====================

    public function getEnergyDataConversionFactors($id)
    {
        $energyData = EnergyData::findOrFail($id);
        $perSource = $energyData->conversionFactors()->orderBy('from_unit')->get();
        $units = EnergyUnit::where('is_active', true)->orderBy('sort_order')->get();

        // Merge global conversion factors (from Conversion Factor Settings)
        // Per-source factors take priority over global ones
        $perSourceUnits = $perSource->pluck('from_unit')->toArray();
        $globalFactors = ConversionFactor::where('energy_type', $energyData->energy_type)
            ->where(function ($q) {
                $q->where('is_default', true)
                  ->orWhere('organization_id', auth()->id());
            })
            ->orderBy('is_default')
            ->get()
            ->filter(fn($f) => !in_array($f->from_unit, $perSourceUnits))
            ->map(fn($f) => [
                'id' => $f->id,
                'from_unit' => $f->from_unit,
                'to_unit' => $f->to_unit,
                'factor' => $f->factor,
                'notes' => 'Global default',
            ]);

        $factors = $perSource->toArray();
        foreach ($globalFactors as $gf) {
            $factors[] = $gf;
        }

        return response()->json([
            'success' => true,
            'factors' => array_values($factors),
            'units' => $units,
            'energy_data' => $energyData,
        ]);
    }

    public function storeEnergyDataConversionFactor(Request $request, $id)
    {
        $request->validate([
            'from_unit' => 'required|string|max:20',
            'to_unit'   => 'required|string|max:20',
            'factor'    => 'required|numeric|min:0.00000001',
            'notes'     => 'nullable|string|max:255',
        ]);

        $energyData = EnergyData::findOrFail($id);

        $factor = EnergyDataConversionFactor::updateOrCreate(
            ['energy_data_id' => $energyData->id, 'from_unit' => $request->from_unit],
            ['to_unit' => $request->to_unit, 'factor' => $request->factor, 'notes' => $request->notes]
        );

        return response()->json(['success' => true, 'factor' => $factor, 'message' => 'Conversion factor saved.']);
    }

    public function destroyEnergyDataConversionFactor($id, $factorId)
    {
        EnergyDataConversionFactor::where('energy_data_id', $id)->where('id', $factorId)->delete();
        return response()->json(['success' => true, 'message' => 'Conversion factor deleted.']);
    }

    // ==================== ENERGY RESOURCE CONVERSION FACTORS ====================

    public function getEnergyResourceConversionFactors($id)
    {
        $resourceData = EnergyResourceData::findOrFail($id);
        $perSource = $resourceData->conversionFactors()->orderBy('from_unit')->get();
        $units = EnergyUnit::where('is_active', true)->orderBy('sort_order')->get();

        // Merge global conversion factors (from Conversion Factor Settings)
        // Per-source factors take priority over global ones
        $perSourceUnits = $perSource->pluck('from_unit')->toArray();
        $globalFactors = ConversionFactor::where('energy_type', $resourceData->resource_type)
            ->where(function ($q) {
                $q->where('is_default', true)
                  ->orWhere('organization_id', auth()->id());
            })
            ->orderBy('is_default')
            ->get()
            ->filter(fn($f) => !in_array($f->from_unit, $perSourceUnits))
            ->map(fn($f) => [
                'id' => $f->id,
                'from_unit' => $f->from_unit,
                'to_unit' => $f->to_unit,
                'factor' => $f->factor,
                'notes' => 'Global default',
            ]);

        $factors = $perSource->toArray();
        foreach ($globalFactors as $gf) {
            $factors[] = $gf;
        }

        return response()->json([
            'success' => true,
            'factors' => array_values($factors),
            'units' => $units,
            'resource_data' => $resourceData,
        ]);
    }

    public function storeEnergyResourceConversionFactor(Request $request, $id)
    {
        $request->validate([
            'from_unit' => 'required|string|max:20',
            'to_unit'   => 'required|string|max:20',
            'factor'    => 'required|numeric|min:0.00000001',
            'notes'     => 'nullable|string|max:255',
        ]);

        $resourceData = EnergyResourceData::findOrFail($id);

        $factor = EnergyResourceConversionFactor::updateOrCreate(
            ['energy_resource_data_id' => $resourceData->id, 'from_unit' => $request->from_unit],
            ['to_unit' => $request->to_unit, 'factor' => $request->factor, 'notes' => $request->notes]
        );

        return response()->json(['success' => true, 'factor' => $factor, 'message' => 'Conversion factor saved.']);
    }

    public function destroyEnergyResourceConversionFactor($id, $factorId)
    {
        EnergyResourceConversionFactor::where('energy_resource_data_id', $id)->where('id', $factorId)->delete();
        return response()->json(['success' => true, 'message' => 'Conversion factor deleted.']);
    }

    // ==================== MONTHLY VARIABLE USAGE METHODS ====================

    public function downloadVariableExcel($id, Request $request)
    {
        try {
            $year = $request->get('year', date('Y'));
            $variable = MonthlyVariable::findOrFail($id);
            
            $fileName = 'variable_' . str_replace(' ', '_', $variable->variable_name) . '_' . $year . '.xlsx';
            $filePath = storage_path('app/public/' . $fileName);
            
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $rows = [];
            
            $rows[] = [
                'Month/Year',
                'Variable Value',
                'Units'
            ];
            
            foreach ($months as $index => $month) {
                $monthNumber = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                $monthKey = $year . '-' . $monthNumber;
                
                $usage = MonthlyVariableUsage::where('monthly_variable_id', $variable->id)
                    ->where('month', $monthKey)
                    ->first();
                
                $rows[] = [
                    $month,
                    $usage ? $usage->variable_value : '',
                    $usage ? $usage->variable_unit : '°C'
                ];
            }
            
            $writer = SimpleExcelWriter::create($filePath);
            foreach ($rows as $row) {
                $writer->addRow($row);
            }
            $writer->close();
            
            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate Excel: ' . $e->getMessage());
        }
    }

    /**
     * Get available categories
     */
    private function getCategories()
    {
        return ['Industrial', 'Commercial', 'Residential'];
    }
}