<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClassroomRequest;
use App\Http\Requests\UpdateClassroomRequest;
use App\Models\Classroom;
use App\Models\Grade;
use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::get();
        return response()->json(['message' => 'Classrooms retrieved successfully', 'Classrooms' => $classrooms], 200);
    }

    public function store(CreateClassroomRequest $request)
    {
        try {
            $translator = new GoogleTranslate('ar');
            $validated = $request->validated();
            $namesInRequest = array_column($validated, 'name');
            $duplicateNamesInRequest = array_filter(array_count_values($namesInRequest), function ($count) {
                return $count > 1;
            });
            if (!empty($duplicateNamesInRequest)) {
                return response()->json(['message' => 'There are duplicate row names in the transmitted data:' . implode(', ', array_keys($duplicateNamesInRequest))], 422);
            }


            foreach($validated as $classroom){
                $classrooms[] = Classroom::create([
                    'grade_id' => $classroom['grade_id'],
                    'name' => ['en' => $classroom['name'], 'ar' => $translator->translate($classroom['name'])],
                ]);
            }
            return response()->json(['message' => 'Classroom created successfully', 'Classrooms' => $classrooms], 201);
        } catch (\Throwable $throwable) {
            return response()->json(['message' => ['file' => $throwable->getFile(), 'line' => $throwable->getLine(), 'error' => $throwable->getMessage()]], 500);
        }
    }

    public function update(UpdateClassroomRequest $request, $id)
    {
        try {
            $translator = new GoogleTranslate('ar');
            $validated = $request->validated();
            $classroom = Classroom::findOrFail($id);
            $classroom->update([
                'grade_id' => $validated['grade_id'],
                'name' => ['en' => $validated['name'], 'ar' => $translator->translate($validated['name'])],
            ]);
            return response()->json(['message' => 'Classroom updated successfully', 'Classroom' => $classroom], 200);
        } catch (\Throwable $throwable) {
            return response()->json(['message' => ['file' => $throwable->getFile(), 'line' => $throwable->getLine(), 'error' => $throwable->getMessage()]], 500);
        }
    }
    public function destroy(Request $request)
    {
        try {
//            foreach ($request->all() as $classroom) {
//                Classroom::findOrFail($classroom['id'])->delete();
//            }
            $delete_all_id = explode(",", $request->delete_all_id);
            Classroom::whereIn('id', $delete_all_id)->Delete();
            return response()->json(['message' => 'Classroom deleted successfully'], 200);
        }
        catch (\Throwable $throwable) {
            return response()->json(['message' => ['file' => $throwable->getFile(), 'line' => $throwable->getLine(), 'error' => $throwable->getMessage()]], 500);
        }
    }

    public function search($name) {

        try {
            $id = Grade::where('name', 'like', '%' . $name . '%')->first()->id;
            $classrooms = Classroom::where('grade_id', $id)->get();
//            $classrooms = Classroom::select('*')->where('Grade_id','=',$request->Grade_id)->get();
            return response()->json(['message' => 'Classrooms retrieved successfully', 'classrooms' => $classrooms], 200);
        }
        catch (\Throwable $throwable) {
            return response()->json(['message' => ['file' => $throwable->getFile(), 'line' => $throwable->getLine(), 'error' => $throwable->getMessage()]], 500);
        }
    }

}
