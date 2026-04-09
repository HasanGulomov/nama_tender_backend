<?php

namespace App\Repositories;

use App\Models\{Tender, Category, Region, Source};

class TenderRepository
{
    public function getFiltered($params, $perPage = 10)
    {
        $query = Tender::with(['category', 'region', 'source']);
        $query->when(!empty($params['search']), function ($q) use ($params) {
            $search = $params['search'];
            $q->where(function ($sub) use ($search) {
                $sub->where('title', 'like', '%' . $search . '%');
            });
        });

        $query->when(!empty($params['category_id']), fn($q) => $q->whereIn('category_id', (array)$params['category_id']));
        $query->when(!empty($params['region_id']), function ($q) use ($params) {
            $q->whereIn('region_id', (array)$params['region_id']);
        });

        $query->when(isset($params['min_budget']), fn($q) => $q->where('budget', '>=', (float)$params['min_budget']));
        $query->when(isset($params['max_budget']), fn($q) => $q->where('budget', '<=', (float)$params['max_budget']));

        $query->when(!empty($params['closingDate']), fn($q) => $q->whereDate('deadline', $params['closingDate']));

        return $query->latest()->paginate($perPage);
    }

    public function getMetaData()
    {
        return [
            'categories' => Category::all(),
            'regions'    => Region::all(),
            'sources'    => Source::all(),
            'budgets'    => [
                'min_budget' => (float) Tender::min('budget') ?: 0,
                'max_budget' => (float) Tender::max('budget') ?: 0,
            ],
            'deadlines'  => Tender::whereNotNull('deadline')->distinct()->pluck('deadline')
        ];
    }

    public function findById($id)
    {
        return Tender::with(['category', 'region', 'source'])->find($id);
    }

    public function create($data)
    {
        return Tender::create($data)->load(['category', 'region', 'source']);
    }
}
