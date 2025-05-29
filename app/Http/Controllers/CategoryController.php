<?php

namespace App\Http\Controllers;

use App\Http\Resources\AnnouncementResource;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return CategoryResource::collection(Category::all());
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:500',
            'description'=>'required|string|max:600'
        ]);
        $category=Category::create([
            'name'=>$request['name'],
            'description'=>$request['description']
        ]);

        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // $category= Category:: findOrFail($id);
        // return new CategoryResource($category);
        try{
            $category = Category::find($id);
            if (!$category) {
                return response()->json([
                    "message" => "La catégorie n'existe pas"
                ]);
            }
            $announcements = $category->announcements()->where('is_completed', false)->where('is_cancelled', false)->orderBy('created_at', 'desc')->get();
            return AnnouncementResource::collection($announcements);
        }catch(\Exception $e){
            return response()->json([
                "message" => "Une erreur est survenue",
                "error" => $e->getMessage()
            ]);
        }

    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
            $request->validate([
            'name'=>'required|string|max:500',
            'description'=>'required|string|max:600'
        ]);
        $category->update([
            'name'=>$request['name'],
            'description'=>$request['description']
        ]);
        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( Category $category)
    {

        $category->delete();
        return response()->json([

            'Message' => "[]"
        ]);
    }
}
