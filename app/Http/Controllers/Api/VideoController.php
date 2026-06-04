<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * Video Listing with Pagination
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $videos = Video::latest()->paginate($perPage);

        $videos->getCollection()->transform(function ($video) {

            $video->image_url = $video->video_image
                ? asset('uploads/videos/' . $video->video_image)
                : null;

            return $video;
        });

        return response()->json($videos);
    }

    /**
     * Store Video
     */
    public function store(Request $request)
    {
        $request->validate([
            'video_link' => 'required|url',
            'video_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'nullable',
            'status' => 'nullable|boolean',
        ]);

        $imageName = null;

        if ($request->hasFile('video_image')) {

            $image = $request->file('video_image');

            $imageName = time() . '_' . $image->getClientOriginalName();

            $image->move(public_path('uploads/videos'), $imageName);
        }

        $video = Video::create([
            'video_link' => $request->video_link,
            'video_image' => $imageName,
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Video created successfully.',
            'data' => $video
        ], 201);
    }

    /**
     * Show Single Video
     */
    public function show($id)
    {
        $video = Video::findOrFail($id);

        $video->image_url = $video->video_image
            ? asset('uploads/videos/' . $video->video_image)
            : null;

        return response()->json($video);
    }

    /**
     * Update Video
     */
    public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);

        $request->validate([
            'video_link' => 'required|url',
            'video_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'nullable',
            'status' => 'nullable|boolean',
        ]);

        if ($request->hasFile('video_image')) {

            if (
                $video->video_image &&
                file_exists(public_path('uploads/videos/' . $video->video_image))
            ) {
                unlink(public_path('uploads/videos/' . $video->video_image));
            }

            $image = $request->file('video_image');

            $imageName = time() . '_' . $image->getClientOriginalName();

            $image->move(public_path('uploads/videos'), $imageName);

            $video->video_image = $imageName;
        }

        $video->video_link = $request->video_link;
        $video->title = $request->title;
        $video->description = $request->description;
        $video->status = $request->status ?? $video->status;

        $video->save();

        return response()->json([
            'success' => true,
            'message' => 'Video updated successfully.',
            'data' => $video
        ]);
    }

    /**
     * Delete Video
     */
    public function destroy($id)
    {
        $video = Video::findOrFail($id);

        if (
            $video->video_image &&
            file_exists(public_path('uploads/videos/' . $video->video_image))
        ) {
            unlink(public_path('uploads/videos/' . $video->video_image));
        }

        $video->delete();

        return response()->json([
            'success' => true,
            'message' => 'Video deleted successfully.'
        ]);
    }
}