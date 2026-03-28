<?php

namespace Database\Seeders;

use App\Models\Tender;
use Illuminate\Database\Seeder;

class TenderSeeder extends Seeder
{
    public function run(): void
    {
        $tenders = [
            [
                'category' => 'Technology',
                'title' => 'Construction of New Government Office Complex',
                'description' => 'Modern administrative offices with sustainable design in Tashkent.',
                'budget' => '250000000',
                'location' => 'Tashkent',
                'deadline' => '2025-03-15',
                'source'      => 'IT MARKET',
            ],
            [
                'category' => 'Infrastructure',
                'title' => 'IT Infrastructure Modernization Project',
                'description' => 'Upgrade of IT systems and cloud infrastructure for government agencies.',
                'budget' => '85500000',
                'location' => 'Tashkent',
                'deadline' => '2025-03-20',
                'source'   => 'UZEX'
            ],
            [
                'category' => 'Supplies',
                'title' => 'Medical Equipment Supply for Hospitals',
                'description' => 'Diagnostic and surgical equipment procurement for regional medical centers.',
                'budget' => '450000000',
                'location' => 'Bukhara',
                'deadline' => '2025-04-01',
                'source'   => 'TENDER WEEK'
            ],
            
        ];

        foreach ($tenders as $tender) {
            
            for ($i = 0; $i < 3; $i++) {
                Tender::create($tender);
            }
        }
    }
}
