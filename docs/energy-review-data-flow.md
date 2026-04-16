# Energy Review Module — Complete Data Flow Reference

> This document describes the full data architecture, relationships, calculations, and module structure for the Energy Review section. Use this as the authoritative reference when building new modules (e.g., SEU Flagging) that need to interact with existing data.

---

## 1. DATABASE SCHEMA

### 1.1 Energy Data (Meter Configuration)

**Table: `energy_data`** — Defines energy meters/sources (e.g., TNB Meter 1, Solar Panel)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | Auto-increment |
| energy_type | varchar | e.g., "Electricity", "Steam", "Compressed Air" |
| provider | varchar | e.g., "TNB", "Solar" |
| account_no | varchar | Account reference |
| contract_type | varchar | Contract type |
| category | varchar | Default "Industrial" |

**Relationships:** `hasMany(EnergyDataUsage)`

---

**Table: `energy_data_usages`** — Monthly energy consumption records per meter
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | Auto-increment |
| energy_data_id | bigint FK | → energy_data.id |
| month | varchar(7) | Format: "YYYY-MM" (e.g., "2026-01") |
| usage_value | decimal(12,3) | Raw input value in original unit |
| usage_unit | varchar | Original unit (kWh, MWh, etc.) |
| **usage_gj** | **decimal(12,3)** | **Converted to GJ — KEY calculation field** |
| **cost** | **decimal(12,2)** | **Cost in RM — KEY calculation field** |
| notes | text | Optional notes |

**Relationships:** `belongsTo(EnergyData)`

---

### 1.2 Energy Resource Data (Fuel/Resource Configuration)

**Table: `energy_resource_data`** — Defines energy resources (e.g., Natural Gas, Diesel, LPG)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | Auto-increment |
| resource_type | varchar | e.g., "Natural Gas", "Diesel", "LPG" |
| provider | varchar | Supplier name |
| account_no | varchar | Account reference |
| contract_type | varchar | Contract type |
| category | varchar | Default "Industrial" |

**Relationships:** `hasMany(EnergyResourceUsage)`

---

**Table: `energy_resource_usages`** — Monthly resource consumption records per resource
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | Auto-increment |
| energy_resource_data_id | bigint FK | → energy_resource_data.id |
| month | varchar(7) | Format: "YYYY-MM" |
| usage_value | decimal(12,3) | Raw input value |
| usage_unit | varchar | Original unit |
| **usage_gj** | **decimal(12,3)** | **Converted to GJ — KEY calculation field** |
| **cost** | **decimal(12,2)** | **Cost in RM — KEY calculation field** |
| notes | text | Optional notes |

**Relationships:** `belongsTo(EnergyResourceData)`

---

### 1.3 Production Data

**Table: `monthly_productions`** — Defines product types
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | Auto-increment |
| production_type | varchar | e.g., "Product A", "Product B" |
| category | varchar | Default "Industrial" |

**Relationships:** `hasMany(MonthlyProductionUsage)`

---

**Table: `monthly_production_usages`** — Monthly production output per product
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | Auto-increment |
| monthly_production_id | bigint FK | → monthly_productions.id |
| month | varchar(7) | Format: "YYYY-MM" |
| **production_amount** | **decimal** | **Production quantity — KEY for SEC** |
| production_unit | varchar | Unit (tonnes, units, etc.) |
| notes | text | Optional |

**Relationships:** `belongsTo(MonthlyProduction)`

---

### 1.4 Variable Data

**Table: `monthly_variables`** — Defines variable types (for EIP)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | Auto-increment |
| variable_name | varchar | e.g., "Floor Area", "Headcount", "CDD" |
| category | varchar | Default "Industrial" |

**Relationships:** `hasMany(MonthlyVariableUsage)`

---

**Table: `monthly_variable_usages`** — Monthly variable values
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | Auto-increment |
| monthly_variable_id | bigint FK | → monthly_variables.id |
| month | varchar(7) | Format: "YYYY-MM" |
| **variable_value** | **decimal** | **Variable value — KEY for EIP** |
| variable_unit | varchar | Unit (m², persons, etc.) |
| notes | text | Optional |

**Relationships:** `belongsTo(MonthlyVariable)`

---

### 1.5 Load Apportioning Data

**Table: `load_apportioning`** — User-entered load breakdown rows
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | Auto-increment |
| year | smallint | Year (e.g., 2026) |
| approach_id | bigint FK | → load_apportioning_approaches.id |
| energy_type_id | bigint FK | → energy_types.id |
| unit_mode | enum | "energy_gj" or "load_percentage" |
| row_label | varchar(200) | User-typed equipment/building name |
| **energy_consumption_gj** | **decimal(14,4)** | **GJ value (editable in GJ mode)** |
| **load_percentage** | **decimal(8,4)** | **Load % (auto-calc in GJ mode, editable in % mode)** |
| sort_order | int | Display order |
| created_by | bigint FK | → users.id |
| updated_by | bigint FK | → users.id |

**Relationships:** `belongsTo(LoadApportioningApproach)`, `belongsTo(EnergyType)`

---

**Table: `load_apportioning_approaches`** — Approach definitions
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | Auto-increment |
| name | varchar | e.g., "Equipment Types", "Process Plants", "Building/Blocks" |
| is_default | boolean | Default approach flag |
| created_by | bigint FK | → users.id |

---

### 1.6 Supporting Tables

**Table: `energy_types`** — Energy type definitions
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | Auto-increment |
| name | varchar | e.g., "Electricity", "Natural Gas" |
| conversion_coefficient | decimal | Conversion factor |

**Table: `products`** — Product definitions (for SEC)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | Auto-increment |
| name | varchar | Product name |
| unit | varchar | Unit of measurement |
| is_active | boolean | Active flag |

**Table: `sec_poe_allocations`** — POE percentage per product per year
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| product_id | bigint FK | → products.id |
| year | smallint | Year |
| percentage | decimal | POE % value |
| poe_category | varchar | Category |

**Table: `sec_monthly_poes`** — Monthly POE overrides
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| product_id | bigint FK | → products.id |
| month | varchar(7) | YYYY-MM |
| poe_category | varchar | Category |
| percentage | decimal | Monthly POE % |
| created_by | bigint FK | → users.id |

**Table: `eip_filter_presets`** — Saved EIP filter configurations
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| user_id | bigint FK | → users.id |
| name | varchar | Preset name |
| description | text | Description |
| filters | json | Filter configuration |
| is_system | boolean | System preset flag |
| is_favorite | boolean | Favorite flag |
| share_token | varchar | Sharing token |
| usage_count | int | Times used |

**Table: `eip_targets`** — EIP target values
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| year | smallint | Year |
| target_type | varchar | Target type |
| target_value | decimal | Target value |
| seu_threshold | decimal | SEU threshold |
| notes | text | Notes |

**Table: `eip_currency_rates`** — Currency conversion rates
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| currency_code | varchar | e.g., "USD" |
| rate_to_myr | decimal | Rate to MYR |
| effective_date | date | Effective date |

---

## 2. MODULE DATA FLOWS

### 2.1 SEC Analysis (Specific Energy Consumption)

**Route:** `GET /sec-analysis`
**Controller:** `SecAnalysisController`
**Purpose:** Calculate energy consumption per unit of production

**Input:** Start Year, End Year, POE (%), Matrix Type
**Data Sources:**
- `energy_data_usages` → monthly energy GJ per source
- `energy_resource_usages` → monthly resource GJ per source
- `monthly_production_usages` → monthly production quantities per product
- `sec_poe_allocations` → POE % per product per year
- `sec_monthly_poes` → optional monthly POE overrides

**Calculation (client-side JavaScript):**
```
Per month, per product:
  totalEnergy    = Σ (all selected energy source usage_gj for that month)
  totalResource  = Σ (all selected resource source usage_gj for that month)
  totalCombined  = totalEnergy + totalResource
  adjustedEnergy = totalCombined × (POE / 100)
  SEC            = adjustedEnergy / production_amount

Yearly total:
  yearlyTotalEnergy = Σ (all months adjustedEnergy)
  yearlyTotalProd   = Σ (all months production_amount)
  yearlySEC         = yearlyTotalEnergy / yearlyTotalProd
```

**Filter Effect (Data Sources tab):**
When user deselects energy types, the system recalculates:
- `filteredTotalEnergy` = sum of only SELECTED energy sources
- `filteredTotalResource` = sum of only SELECTED resource sources
- SEC is recalculated from filtered totals
- "Filtered" badge appears on the table

**Output:** Matrix table (monthly × products), SEC total table (yearly comparison), bar/line/pie charts, CSV/Excel export

**Key Routes:**
- `GET /sec-analysis/data/matrix` → getMatrixData (fetch all data)
- `POST /sec-analysis/data/energy` → storeEnergyData (save energy usage)
- `POST /sec-analysis/data/production` → storeProductionData (save production)
- `POST /sec-analysis/poe` → storePoe (save POE allocation)
- `POST /sec-analysis/monthly-poe` → storeMonthlyPoe (save monthly POE)

---

### 2.2 EIP Analysis (Energy Intensity Performance)

**Route:** `GET /eip-analysis`
**Controller:** `EIPAnalysisController`
**Purpose:** Calculate energy consumption per unit of a variable (area, headcount, etc.)

**Input:** Start Year, End Year, Variable name, Energy/Resource source selection
**Data Sources:**
- `energy_data_usages` → monthly energy GJ per source
- `energy_resource_usages` → monthly resource GJ per source
- `monthly_variable_usages` → monthly variable values
- `eip_filter_presets` → saved filter configurations
- `eip_targets` → target values for comparison
- `eip_currency_rates` → currency conversion for cost analysis

**Calculation (client-side JavaScript):**
```
Per month:
  totalEnergy    = Σ (selected energy source usage_gj)
  totalResource  = Σ (selected resource source usage_gj)
  totalCombined  = totalEnergy + totalResource
  EIP            = totalCombined / variable_value

Yearly:
  yearlyEIP = Σ(year totalCombined) / Σ(year variable_value)
```

**Key difference from SEC:** Same energy/resource data, but denominator is Variable (area, headcount) instead of Production (quantity).

**Output:** EIP table, baseline vs current comparison, regression analysis, bar/line charts, CSV/Excel export

**Key Routes:**
- `POST /eip-analysis/data/matrix` → getMatrixData
- `POST /eip-analysis/data/insights` → getFilterInsights
- `POST /eip-analysis/data/regression` → getRegressionData
- `GET /eip-analysis/export` → exportData
- `POST /eip-analysis/targets` → storeTarget
- Filter presets: save/load/delete/list/toggleFavorite

---

### 2.3 Load Apportioning

**Route:** `GET /load-apportioning`
**Controller:** `LoadApportioningController`
**Purpose:** Break down energy consumption by equipment/building and calculate load percentages

**Input:** Year, Approach (Equipment Types / Buildings/Blocks / Process Plants), Energy Types (multi-select), Unit Mode (GJ or %)
**Data Sources:**
- `load_apportioning` → user-entered rows (primary data source)
- `load_apportioning_approaches` → approach definitions
- `energy_types` → energy type list
- `energy_resource_usages` → for Monthly Resource Breakdown section only

**Two Unit Modes:**

Mode A — Energy (GJ):
```
User enters: GJ values per row (editable)
System calculates: Load % = Row GJ / Table Total GJ × 100
Each energy type table independently totals to 100%
```

Mode B — Load Percentage:
```
User enters: % values directly (editable)
GJ column: blank/readonly
Validation: each energy type table must sum to exactly 100%
```

**Additional Outputs:**

SEU Summary Rollup Table — aggregates across ALL energy types:
```
Per energy type:
  items     = count of rows for that type
  totalGJ   = Σ (all rows energy_consumption_gj for that type)
  loadPct   = typeGJ / grandTotalGJ × 100

Grand Total row at bottom (should = 100%)
```

Monthly Resource Breakdown — from `energy_resource_usages`:
```
GET /load-apportioning/monthly-resource?year=XXXX
Each resource meter → column
12 monthly rows of GJ values
SUM row at bottom
Apportioning % row = Meter Total / Grand Total × 100
```

**Key Routes:**
- `GET /load-apportioning/data` → getData (fetch load data)
- `POST /load-apportioning/save` → save (persist rows)
- `POST /load-apportioning/approaches` → storeApproach (create new approach)
- `GET /load-apportioning/monthly-resource` → getMonthlyResource

---

### 2.4 Utility Apportioning

**Route:** `GET /utility-apportioning`
**Controller:** `UtilityApportioningController`
**Purpose:** Compare Energy vs Energy Resource consumption and calculate proportional split

**Input:** Year (page reload with `?year=XXXX`)
**Data Sources:**
- `energy_data_usages` → SUM(usage_gj), SUM(cost) GROUP BY month
- `energy_resource_usages` → SUM(usage_gj), SUM(cost) GROUP BY month

**Calculation (server-side PHP):**
```
Build 12-month matrix (Jan-Dec):
  Per month:
    energy_gj     = energy_data_usages total for that month
    resource_gj   = energy_resource_usages total for that month
    energy_cost   = energy_data_usages cost total
    resource_cost = energy_resource_usages cost total

Totals:
  totalEnergyGj    = Σ (12 months energy_gj)
  totalResourceGj  = Σ (12 months resource_gj)
  totalEnergyCost  = Σ (12 months energy_cost)
  totalResourceCost = Σ (12 months resource_cost)

Averages:
  avgEnergyGj   = totalEnergyGj / 12
  avgResourceGj = totalResourceGj / 12
  (same for costs)

Apportioning Percentages:
  totalGj        = totalEnergyGj + totalResourceGj
  pctEnergyGj    = totalEnergyGj / totalGj × 100
  pctResourceGj  = totalResourceGj / totalGj × 100
  (same formula for costs using totalCost denominator)
```

**Note:** This module is 100% auto-generated. No user-entered data. Server-side only (no AJAX, page reload).

**Output:**
- Table View: 4 summary cards + main table (12 months + Average row + Apportioning % row) + CSV/Excel export
- Graph View: 4 summary cards + GJ Doughnut chart + Cost Doughnut chart + Monthly stacked Bar chart

---

## 3. CROSS-MODULE DATA RELATIONSHIP MAP

```
DATABASE TABLE                    → USED BY MODULES
─────────────────────────────────────────────────────────────
energy_data_usages                → SEC, EIP, Utility Apportioning
energy_resource_usages            → SEC, EIP, Load Apportioning (monthly), Utility Apportioning
monthly_production_usages         → SEC only (denominator)
monthly_variable_usages           → EIP only (denominator)
load_apportioning                 → Load Apportioning only (user-entered)
sec_poe_allocations               → SEC only
sec_monthly_poes                  → SEC only
eip_filter_presets                → EIP only
eip_targets                       → EIP only
eip_currency_rates                → EIP only
energy_types                      → Load Apportioning (grouping)
products                          → SEC (production mapping)
load_apportioning_approaches      → Load Apportioning (approach selection)
```

### Key Relationships:
1. **`energy_data_usages`** is the most shared table — used by 3 of 4 modules
2. **`energy_resource_usages`** is shared across ALL 4 modules
3. **Load Apportioning is mostly standalone** — primary data is user-entered in `load_apportioning` table
4. **SEC and EIP read the same energy/resource data** but divide by different denominators (production vs variable)
5. **No data flows between modules** — they all read independently from the same source tables
6. **Utility Apportioning** is the only module with server-side calculation (others use client-side JS via AJAX)
7. **App is single-tenant** — no `organization_id` on any table

---

## 4. COMMON PATTERNS FOR NEW MODULES

When building a new module (like SEU Flagging), follow these patterns:

### Data Access Pattern
```php
// Energy consumption by month
$energyData = EnergyDataUsage::where('month', 'like', "$year-%")
    ->selectRaw('month, SUM(usage_gj) as total_gj, SUM(cost) as total_cost')
    ->groupBy('month')
    ->get()
    ->keyBy('month');

// Resource consumption by month
$resourceData = EnergyResourceUsage::where('month', 'like', "$year-%")
    ->selectRaw('month, SUM(usage_gj) as total_gj, SUM(cost) as total_cost')
    ->groupBy('month')
    ->get()
    ->keyBy('month');

// Per-source breakdown
$perSource = EnergyDataUsage::where('month', 'like', "$year-%")
    ->with('energyData')
    ->get()
    ->groupBy('energy_data_id');
```

### Month Format
All month fields use `varchar(7)` with format `"YYYY-MM"` (e.g., `"2026-01"`).

### Units
- Energy: always converted to **GJ** (stored in `usage_gj`)
- Cost: always in **RM** (stored in `cost`)
- Production: varies by product (stored in `production_amount`)
- Decimals: `.toFixed(2)` for display

### File Structure
```
app/Http/Controllers/Admin/{ModuleName}Controller.php
resources/views/admin/{module-name}/index.blade.php
routes/web.php  (add route inside admin middleware group)
database/seeders/MenuSeeder.php  (add menu item under Energy Review)
```

### Menu Structure (Energy Review children)
1. SEC Analysis (order: 1)
2. EIP Analysis (order: 2)
3. Load Apportioning (order: 3)
4. Utility Apportioning (order: 4)
5. [New module would be order: 5]

---

## 5. EXISTING ENERGY TYPE DEFINITIONS

The `energy_types` table typically contains:
- Electricity
- Natural Gas
- Steam
- Compressed Air
- Chilled Water
- (Others as configured)

The `energy_data.energy_type` field maps to these names. The `energy_resource_data.resource_type` contains similar categorizations for resource fuels.

---

## 6. DATA FLOW SUMMARY DIAGRAM (Text)

```
┌─────────────────┐     ┌──────────────────────┐     ┌─────────────────────┐
│  Energy Data     │────▶│  energy_data_usages   │────▶│  SEC Analysis       │
│  Management      │     │  (monthly GJ + cost)  │────▶│  EIP Analysis       │
│  (Admin CRUD)    │     │                       │────▶│  Utility Apport.    │
└─────────────────┘     └──────────────────────┘     └─────────────────────┘
                                                              │
┌─────────────────┐     ┌──────────────────────┐             │
│  Resource Data   │────▶│ energy_resource_usages│────▶ ALL 4 MODULES
│  Management      │     │  (monthly GJ + cost)  │             │
└─────────────────┘     └──────────────────────┘             │
                                                              │
┌─────────────────┐     ┌──────────────────────┐             │
│  Production      │────▶│ monthly_production_   │────▶ SEC only (÷ denominator)
│  Management      │     │ usages (qty/month)    │             │
└─────────────────┘     └──────────────────────┘             │
                                                              │
┌─────────────────┐     ┌──────────────────────┐             │
│  Variable        │────▶│ monthly_variable_     │────▶ EIP only (÷ denominator)
│  Management      │     │ usages (value/month)  │             │
└─────────────────┘     └──────────────────────┘             │
                                                              │
┌─────────────────┐                                          │
│  Load Apport.    │──── User enters rows directly ──▶ Load Apportioning only
│  (user input)    │                                          │
└─────────────────┘                                          ▼
                                                     ┌─────────────────────┐
                                                     │  NEW MODULE:        │
                                                     │  SEU Flagging       │
                                                     │  (can read from     │
                                                     │  any of the above)  │
                                                     └─────────────────────┘
```
