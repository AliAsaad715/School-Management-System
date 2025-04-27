<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Section;
use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate;

class SectionController extends Controller
{

    public function index()
    {
        $grades = Grade::with(['sections'])->get();
        return $grades;
    }

    public function store(SectionRequest $request)
    {
        try {
            $translator = new GoogleTranslate('ar');
            $validated = $request->validated();
            $section = Section::create([
                'name' => ['en' => $validated['name'], 'ar' => $translator->translate($validated['name'])],
                'classroom_id' => $validated['classroom_id'],
                'grade_id' => $validated['grade_id'],
            ]);
            return response()->json(['message' => 'Section created successfully', 'Section' => $section], 201);
        } catch (\Throwable $throwable) {
            return response()->json(['message' => ['file' => $throwable->getFile(), 'line' => $throwable->getLine(), 'error' => $throwable->getMessage()]], 500);
        }
    }
}
