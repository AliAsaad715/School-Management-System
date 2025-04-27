<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradeRequest;
use App\Models\Grade;
use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::get();
        return response()->json(['message' => 'Grades retrieved successfully', 'Grades' => $grades], 200);
    }

    public function store(GradeRequest $request)
    {
//        if (Grade::where('name->en', $request->name)->exists()) {
//            return redirect()->back()->withErrors(trans('Grades_trans.exists'));
//        }
        try {
            $translator = new GoogleTranslate('ar');
            $validated = $request->validated();
            $grade = Grade::create([
                'name' => ['en' => $validated['name'], 'ar' => $translator->translate($validated['name'])],
                'notes' => isset($validated['notes']) ? ['en' => $validated['notes'], 'ar' => $translator->translate($validated['notes'])] : null,
            ]);
            return response()->json(['message' => 'Grade created successfully', 'Grade' => $grade], 201);
        } catch (\Throwable $throwable) {
            return response()->json(['message' => ['file' => $throwable->getFile(), 'line' => $throwable->getLine(), 'error' => $throwable->getMessage()]], 500);
        }
    }

    public function update(GradeRequest $request, $id)
    {
        try {
            $translator = new GoogleTranslate('ar');
            $validated = $request->validated();
            $grade = Grade::findOrFail($id);
            $grade->update([
                'name' => ['en' => $validated['name'], 'ar' => $translator->translate($validated['name'])],
                'notes' => isset($validated['notes']) ? ['en' => $validated['notes'], 'ar' => $translator->translate($validated['notes'])] : null,
            ]);
            return response()->json(['message' => 'Grade updated successfully', 'Grade' => $grade], 200);
        } catch (\Throwable $throwable) {
            return response()->json(['message' => ['file' => $throwable->getFile(), 'line' => $throwable->getLine(), 'error' => $throwable->getMessage()]], 500);
        }
    }
    public function destroy($id)
    {
        try {
            $grade = Grade::findOrFail($id);
            if($grade->classrooms()->exists()) { //$grade->classrooms->count() > 0
                return response()->json(['message' => 'You can not delete a grade because it has classes. You must delete the classes first!'], 422);
            }
            /*
              $MyClass_id = Classroom::where('Grade_id',$request->id)->pluck('Grade_id');
              if($MyClass_id->count() == 0){
                $Grades = Grade::findOrFail($request->id)->delete();
             */
            else {

                $grade->delete();
                return response()->json(['message' => 'Grade deleted successfully'], 200);
            }
        }
        catch (\Throwable $throwable) {
            return response()->json(['message' => ['file' => $throwable->getFile(), 'line' => $throwable->getLine(), 'error' => $throwable->getMessage()]], 500);
        }
    }

}
