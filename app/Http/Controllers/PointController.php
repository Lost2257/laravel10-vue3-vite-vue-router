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
        ]);

        return Point::create($data);
    }

    public function destroy(Point $point)
    {
        $point->delete();

        return response()->json(['message' => 'Location deleted successfully']);
    }
}
