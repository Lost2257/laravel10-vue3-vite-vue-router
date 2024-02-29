<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Point;

class PointController extends Controller
{
    public function index()
    {
        return Point::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'image' => '',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('images'), $imageName); // Store image in the public/images directory
            $data['image'] = 'images/' . $imageName; // Save image path in the database
        }

        return Point::create($data);
    }

    public function destroy(Point $point)
    {
        // Delete the associated image when deleting the point
        if ($point->image) {
            $imagePath = public_path($point->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $point->delete();

        return response()->json(['message' => 'Location deleted successfully']);
    }
}
