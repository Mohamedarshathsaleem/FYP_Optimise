<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Legal;
use App\Models\LegalItem;

class LegalItemSeeder extends Seeder
{
    public function run()
    {
        // Create the parent Legal record if it doesn't exist
        $legal = Legal::firstOrCreate(
            ['legal_id' => 'LR-ENMS-001'],
            [
                'title' => 'Energy Efficiency and Conservation Act',
                'authority' => 'Energy Commission',
                'relevant_clause' => 'Section 4, 5, 6',
                'reference_others' => 'ISO 50001:2018',
                'category' => 'Legal',
                'effective_date' => '2023-01-01',
                'relevant' => 'Y',
                'description' => 'Act governing energy efficiency and conservation requirements for industries',
                'what_affected' => 'Energy management systems and processes',
                'action_required' => 'Implement and maintain energy management system',
                'responsible_person' => 'Energy Manager',
                'last_review_date' => '2024-01-15',
                'review_frequency' => 'Annually',
                'further_action_bool' => 'No',
                'further_action' => null,
                'compliance_status' => 'Compliant',
                'evidence_compliance' => 'ISO 50001 certification, energy audit reports',
                'remarks' => 'Primary regulatory requirement for energy management'
            ]
        );

        $items = [
            [
                'item_id' => 'LR-EECA-001',
                'description' => 'Energy consumption monitoring requirements',
            ],
            [
                'item_id' => 'LR-EECA-002',
                'description' => 'Energy efficiency reporting standards',
            ],
            [
                'item_id' => 'LR-EECA-003',
                'description' => 'Energy management system implementation',
            ],
            [
                'item_id' => 'LR-EECA-004',
                'description' => 'Energy audit compliance requirements',
            ],
        ];

        foreach ($items as $item) {
            LegalItem::updateOrCreate(
                ['legal_id' => $legal->id, 'item_id' => $item['item_id']],
                [
                    'description' => $item['description'],
                    'is_active' => true
                ]
            );
        }
    }
}
