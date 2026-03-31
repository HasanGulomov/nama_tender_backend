<?php

namespace Database\Seeders;

use App\Models\Tender;
use App\Models\Region;
use App\Models\Category;
use App\Models\Source;
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
                'region' => 'Tashkent',
                'deadline' => '2025-03-15',
                'source' => 'IT MARKET',
            ],
            [
                'category' => 'Infrastructure',
                'title' => 'IT Infrastructure Modernization Project',
                'description' => 'Upgrade of IT systems and cloud infrastructure for government agencies.',
                'budget' => '85500000',
                'region' => 'Tashkent',
                'deadline' => '2025-03-20',
                'source' => 'UZEX'
            ],
            [
                'category' => 'Supplies',
                'title' => 'Medical Equipment Supply for Hospitals',
                'description' => 'Diagnostic and surgical equipment procurement for regional medical centers.',
                'budget' => '450000000',
                'region' => 'Bukhara',
                'deadline' => '2025-04-01',
                'source' => 'TENDER WEEK'
            ],
            [
                'category' => 'Sustainability',
                'title' => 'Solar Panel Installation for Clinics',
                'description' => 'Installation of solar energy systems for rural healthcare facilities.',
                'budget' => '300000000',
                'region' => 'Samarkand',
                'deadline' => '2025-06-15',
                'source' => 'XT-XARID'
            ]
        ];

        foreach ($tenders as $data) {
            
            $regionId = Region::where('name', $data['region'])->first()?->id ?? 1;
            $categoryId = Category::where('name', $data['category'])->first()?->id ?? 1;
            $sourceId = Source::where('name', $data['source'])->first()?->id ?? 1;

            for ($i = 0; $i < 3; $i++) {
                Tender::create([
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'budget' => $data['budget'],
                    'deadline' => $data['deadline'],
                    'region_id' => $regionId,
                    'category_id' => $categoryId,
                    'source_id' => $sourceId,
                ]);
            }
        }
    }
}