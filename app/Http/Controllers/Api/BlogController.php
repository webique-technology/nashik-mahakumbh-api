<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Blog::query();

        if ($request->has('search') && !empty($request->search)) {

            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->has('category') && !empty($request->category)) {

            $query->where('category', $request->category);
        }

        $query->orderBy('id', 'desc');

        if ($request->filled('limit')) {
            $blogs = $query->take($request->limit)->get();
            $blogs->transform(function ($blog) {

                $blog->image_url = $blog->image
                    ? asset('uploads/blogs/' . $blog->image)
                    : null;

                return $blog;
            });

            return response()->json([
                'status' => true,
                'data' => $blogs
            ]);
        }

        // $perPage = $request->per_page ?? 10;

        $blogs = $query->paginate(8);

        $blogs->getCollection()->transform(function ($blog) {
            $blog->image_url = $blog->image ? asset('uploads/blogs/' . $blog->image) : null;
            return $blog;
        });

        return response()->json([
            'status' => true,
            'message' => 'Blogs fetched successfully',
            'data' => $blogs
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([

            'title' => 'required|string|max:255',
            'description' => 'required',
            'category' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
        ]);

        $blog = new Blog();

        $blog->title = $request->title;

        $blog->description = $request->description;

        $blog->category = $request->category;

        if ($request->hasFile('image')) {

            $file = $request->file('image');

            $imageName = time().'_'.$file->getClientOriginalName();

            $file->move(public_path('uploads/blogs'), $imageName);

            $blog->image = $imageName;
        }

        $blog->save();

        return response()->json([
            'status' => true,
            'message' => 'Blog created successfully',
            'data' => $blog
        ]);
    }

    public function show($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found'
            ], 404);
        }
        
        $blog->image_url = $blog->image
        ? asset('uploads/blogs/' . $blog->image)
        : null;

        return response()->json([
            'status' => true,
            'data' => $blog
        ]);
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found'
            ], 404);
        }

        $request->validate([

            'title' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string',
            'category' => 'sometimes|nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
        ]);

        if ($request->filled('title')) {
            $blog->title = $request->title;
        }

        if ($request->filled('description')) {
            $blog->description = $request->description;
        }

        if ($request->filled('category')) {
            $blog->category = $request->category;
        }

        if ($request->hasFile('image')) {

            if (!empty($blog->image)) {
                $oldPath = public_path('uploads/blogs/' . $blog->image);

                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file('image');

            $imageName = time().'_'.$file->getClientOriginalName();

            $file->move(public_path('uploads/blogs'), $imageName);

            $blog->image = $imageName;
        }

        $blog->save();

        return response()->json([
            'status' => true,
            'message' => 'Blog updated successfully',
            'data' => $blog
        ]);
    }

    public function destroy($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {

            return response()->json([
                'status' => false,
                'message' => 'Blog not found'
            ], 404);
        }

        if (!empty($blog->image)) {

            $path = public_path('uploads/blogs/' . $blog->image);

            if (file_exists($path)) {
                unlink($path);
            }
        }

        $blog->delete();

        return response()->json([
            'status' => true,
            'message' => 'Blog deleted successfully'
        ]);
    }

    public function getBySlug($slug)
    {
        $blog = Blog::all()->first(function ($item) use ($slug) {
            return Str::slug($item->title) === $slug;
        });

        if (!$blog) {
            return response()->json([
                'message' => 'Blog not found'
            ], 404);
        }

        $blog->image_url = $blog->image
            ? asset('uploads/blogs/' . $blog->image)
            : null;

        return response()->json([
            'data' => $blog
        ]);
    }
}
