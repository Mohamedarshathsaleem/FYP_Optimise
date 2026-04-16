<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stakeholder;

class StakeholderSeeder extends Seeder
{
    public function run()
    {
        $stakeholders = [
            [
                'stakeholder_id' => 'ST-001',
                'name' => 'Utility Provider',
                'type' => 'External',
                'role' => 'Energy supply and billing',
                'needs_expectations' => 'Timely payment, forecast updates',
                'influence_level' => 'Low',
                'communication_method' => 'Email, invoices',
                'engagement_frequency' => 'Monthly',
                'responsible_person' => 'Procurement Officer',
                'remarks' => 'Rate structure impacts'
            ],
            [
                'stakeholder_id' => 'ST-002',
                'name' => 'Regulatory Authority',
                'type' => 'External',
                'role' => 'Environmental compliance monitoring',
                'needs_expectations' => 'Compliance reports, environmental data',
                'influence_level' => 'High',
                'communication_method' => 'Official letters, meetings',
                'engagement_frequency' => 'Quarterly',
                'responsible_person' => 'Environmental Manager',
                'remarks' => 'Critical for license renewal'
            ]
        ];

        foreach ($stakeholders as $stakeholder) {
            Stakeholder::updateOrCreate(
                ['stakeholder_id' => $stakeholder['stakeholder_id']],
                $stakeholder
            );
        }
    }
}
