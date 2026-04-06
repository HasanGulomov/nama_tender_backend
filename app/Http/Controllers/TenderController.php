<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\TenderFilterRequest;
use App\Http\Resources\TenderResource;

class TenderController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $tenders = Tender::with(['category', 'region', 'source'])
            ->latest()
            ->paginate($perPage);


        return TenderResource::collection($tenders);
    }

    public function filter(TenderFilterRequest $request)
    {
        $query = Tender::with(['category', 'region', 'source']);

        // IDlar bo'yicha filtr
        if ($request->filled('category_id')) {
            $query->whereIn('category_id', (array)$request->category_id);
        }
        if ($request->filled('region_id')) {
            $query->whereIn('region_id', (array)$request->region_id);
        }
        if ($request->filled('source_id')) {
            $query->whereIn('source_id', (array)$request->source_id);
        }

        // Byudjet bo'yicha (filled ishlatamiz)
        $query->when($request->filled('min_budget'), function ($q) use ($request) {
            $q->where('budget', '>=', (float)$request->min_budget);
        });

        $query->when($request->filled('max_budget'), function ($q) use ($request) {
            $q->where('budget', '<=', (float)$request->max_budget);
        });

        // Deadline bo'yicha
        if ($request->filled('closingDate')) {
            $query->whereDate('deadline', '<=', $request->closingDate);
        }

        $results = $query->latest()->get();

        return TenderResource::collection($results);
    }

    public function getFilterData()
    {
        return response()->json([
            'categories' => \App\Models\Category::all(),
            'regions'    => \App\Models\Region::all(),
            'sources'    => \App\Models\Source::all(), // Endi Source ham bor
            'budgets' => [
                'min_budget' => (float) Tender::min('budget') ?: 0,
                'max_budget' => (float) Tender::max('budget') ?: 0,
            ],
            'deadlines' => Tender::whereNotNull('deadline')
                ->distinct()
                ->pluck('deadline')
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
            'region_id' => 'required|integer|exists:regions,id',
            'source_id' => 'required|integer|exists:sources,id',
            'deadline' => 'required|date',
            'budget' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tender = Tender::create($request->all());
        $tender->load(['category', 'region', 'source']);

        return response()->json([
            'message' => 'Tender muvaffaqiyatli yaratildi',
            'data' => new TenderResource($tender)
        ], 201);
    }

    public function show($id)
    {
        $tender = Tender::with(['category', 'region', 'source'])->find($id);

        if (!$tender) {
            return response()->json(['message' => 'Tender topilmadi'], 404);
        }

        return new TenderResource($tender);
    }

    public function update(Request $request, $id)
    {
        $tender = Tender::find($id);

        if (!$tender) {
            return response()->json(['message' => 'Tender topilmadi'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category_id' => 'sometimes|integer|exists:categories,id',
            'region_id'   => 'sometimes|integer|exists:regions,id',
            'source_id'   => 'sometimes|integer|exists:sources,id',
            'deadline'    => 'sometimes|date',
            'budget'      => 'sometimes|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tender->update($request->all());

        return response()->json([
            'message' => 'Tender yangilandi',
            'data'    => new TenderResource($tender->fresh())
        ]);
    }

    public function destroy($id)
    {
        $tender = Tender::find($id);

        if (!$tender) {
            return response()->json(['message' => 'Tender topilmadi'], 404);
        }

        $tender->delete();

        return response()->json(['message' => 'Tender muvaffaqiyatli o‘chirildi']);
    }

    public function search(Request $request)
    {
        $request->validate(['search' => 'required|string']);

        $perPage = $request->query('per_page', 10);

        $tenders = Tender::with(['category', 'region', 'source'])
            ->where('title', 'like', '%' . $request->search . '%')
            ->latest()
            ->paginate($perPage);

        return TenderResource::collection($tenders);
    }

    public function toggleFavorite(Request $request, $id)
    {
        $user = $request->user();
        $tender = Tender::find($id);

        if (!$tender) {
            return response()->json(['message' => 'Tender topilmadi'], 404);
        }

        $status = $user->favorites()->toggle($id);
        $attached = count($status['attached']) > 0;

        return response()->json([
            'message' => $attached ? 'Tender sevimlilarga qo‘shildi' : 'Tender sevimlilardan olib tashlandi',
            'is_favorite' => $attached
        ]);
    }

    public function getFavorite(Request $request)
    {

        return TenderResource::collection($request->user()->favorites);
    }
}
