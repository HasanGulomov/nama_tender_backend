<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TenderController extends Controller
{
    public function index(Request $request)
    {
        $query = Tender::query();

        $query->when($request->category, fn($q) => 
            $q->where('category', $request->category)
        )
        ->when($request->search, fn($q) => 
            $q->where('title', 'like', '%' . $request->search . '%')
        )
        ->when($request->location, fn($q) => 
            $q->where('location', 'like', '%' . $request->location . '%')
        )
        ->when($request->deadline, fn($q) => 
            $q->whereDate('deadline', '>=', $request->deadline)
        )
        ->when($request->min_budget, fn($q) => 
            $q->where('budget', '>=', $request->min_budget)
        )
        ->when($request->max_budget, fn($q) => 
            $q->where('budget', '<=', $request->max_budget)
        );

        $tenders = $query->latest()->paginate(10)->appends($request->query());

        return response()->json($tenders);
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
            'source'      => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tender = Tender::create($request->all());

        return response()->json([
            'message' => 'Tender muvaffaqiyatli yaratildi',
            'data' => $tender
        ], 201);
    }

    public function show($id)
    {
        $tender = Tender::find($id);
        
        if (!$tender) {
            return response()->json(['message' => 'Tender topilmadi'], 404);
        }

        return response()->json($tender);
    }

    public function update(Request $request, $id)
    {
        $tender = Tender::find($id);

        if (!$tender) {
            return response()->json(['message' => 'Tender topilmadi'], 404);
        }

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

        return response()->json([
            'message' => 'Tender yangilandi',
            'data' => $tender
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
    public function toggleFavorite(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'Avtorizatsiyadan oting'], 401);
        }

        $tender = Tender::findOrFail($id);
        $user->favorites()->toggle($tender->id);

        return response()->json([   
            'message' => 'Muvaffaqiyatli bajarildi',
            'is_favorite' => $user->favorites()->where('tender_id', $id)->exists()
        ]);
    }

   
    public function getFavorite(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Avtorizatsiyadan oting'], 401);
        }

      
        return response()->json($user->favorites);
    }
}