<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\TenderFilterRequest;

class TenderController extends Controller
{
    
    public function index(Request $request)
    {
        
        $perPage = $request->query('per_page', 10);
        
        $tenders = Tender::latest()->paginate($perPage);
        
        return response()->json($tenders);
    }

    
    public function search(Request $request)
    {
        $request->validate(['search' => 'required|string']);
        
        $perPage = $request->query('per_page', 10);
        
        $tenders = Tender::where('title', 'like', '%' . $request->search . '%')
            ->latest()
            ->paginate($perPage)
            ->appends($request->query()); 

        return response()->json($tenders);
    }

    
    
 public function filter(TenderFilterRequest $request) 
{
    $query = Tender::query(); 

 
    $query->when($request->category_id, fn($q, $v) => $q->where('category_id', $v));
    $query->when($request->region_id, fn($q, $v) => $q->where('region_id', $v));
    $query->when($request->source_id, fn($q, $v) => $q->where('source_id', $v));

  
    $query->when($request->min_budget, fn($q, $v) => $q->where('budget', '>=', $v));
    $query->when($request->max_budget, fn($q, $v) => $q->where('budget', '<=', $v));
    $query->when($request->closingDate, fn($q, $v) => $q->whereDate('deadline', $v));

   
    $tenders = $query->with(['category', 'region', 'source'])->latest()->get();

    return response()->json($tenders);
}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'region' => 'required|string',
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
            'region' => 'sometimes|string',
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