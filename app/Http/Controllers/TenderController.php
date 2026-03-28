<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\TenderFilterRequest;

class TenderController extends Controller
{
  
    public function index()
    {
        $tenders = Tender::latest()->paginate(10);
        return response()->json($tenders);
    }


    public function search(Request $request)
    {
        $request->validate(['search' => 'required|string']);
        
        $tenders = Tender::where('title', 'like', '%' . $request->search . '%')
            ->latest()
            ->paginate(10);

        return response()->json($tenders);
    }

public function filter(TenderFilterRequest $request) 
{
    return response()->json(
        Tender::query()
            ->when($request->category, fn($q, $v) => $q->where('category', $v))
            ->when($request->region, fn($q, $v) => $q->where('location', 'like', "%$v%"))
            ->when($request->source, fn($q, $v) => $q->where('source', $v))
            ->when($request->budgetRange, fn($q, $v) => match ($v) {
                'under-100k' => $q->where('budget', '<', 100000),
                '100k-500k'  => $q->whereBetween('budget', [100000, 500000]),
                'over-500k'  => $q->where('budget', '>', 500000),
                default      => $q
            })
            ->when($request->closingDate, fn($q, $v) => match ($v) {
                'today'      => $q->whereDate('deadline', now()),
                'this-week'  => $q->whereBetween('deadline', [now()->startOfWeek(), now()->endOfWeek()]),
                'this-month' => $q->whereMonth('deadline', now()->month),
                default      => $q
            })
            ->when($request->sortOrder, 
                fn($q, $v) => $q->orderBy('budget', $v === 'highest' ? 'desc' : 'asc'),
                fn($q) => $q->latest()
            )
            ->paginate(10)
            ->appends($request->query())
    );
}


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'location' => 'required|string',
            'deadline' => 'required|date',
            'budget' => 'required|numeric',
            'source' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tender = Tender::create($request->all());

        return response()->json(['message' => 'Tender muvaffaqiyatli yaratildi', 'data' => $tender], 201);
    }


    public function show($id)
    {
        $tender = Tender::find($id);
        if (!$tender) return response()->json(['message' => 'Tender topilmadi'], 404);
        return response()->json($tender);
    }

 
    public function update(Request $request, $id)
    {
        $tender = Tender::find($id);
        if (!$tender) return response()->json(['message' => 'Tender topilmadi'], 404);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category' => 'sometimes|string',
            'location' => 'sometimes|string',
            'deadline' => 'sometimes|date',
            'budget' => 'sometimes|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tender->update($request->all());
        return response()->json(['message' => 'Tender yangilandi', 'data' => $tender]);
    }


    public function destroy($id)
    {
        $tender = Tender::find($id);
        if (!$tender) return response()->json(['message' => 'Tender topilmadi'], 404);
        
        $tender->delete();
        return response()->json(['message' => 'Tender muvaffaqiyatli o‘chirildi']);
    }

    public function toggleFavorite(Request $request, $id)
    {
        $user = $request->user();
        $tender = Tender::findOrFail($id);
        $user->favorites()->toggle($tender->id);

        return response()->json([   
            'message' => 'Muvaffaqiyatli bajarildi',
            'is_favorite' => $user->favorites()->where('tender_id', $id)->exists()
        ]);
    }

    
    public function getFavorite(Request $request)
    {
        return response()->json($request->user()->favorites);
    }
}