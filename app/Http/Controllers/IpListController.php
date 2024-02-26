<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IpList;


class IpListController extends Controller
{
    public function index()
    {
        return IpList::all();
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'ip_address' => 'required|ip',
                'password' => 'required|min:8',
                'role' => 'required',
            ]);

            $validatedData['password'] = bcrypt($validatedData['password']);

            return IpList::create($validatedData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $ip = IpList::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'string',
            'email' => 'email',
            'ip_address' => 'ip',
            'password' => 'sometimes|min:8',
            'role' => 'sometimes',
        ]);

        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        $ip->update($validatedData);

        return $ip;
    }

    public function destroy($id)
    {
        $ip = IpList::findOrFail($id);
        $ip->delete();

        return response()->json(['message' => 'IP deleted successfully']);
    }

}
