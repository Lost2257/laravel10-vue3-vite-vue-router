<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IpList;


class IpListController extends Controller
{
    public function index()
{
    $ipLists = IpList::all();
    return response()->json($ipLists);
}

public function store(Request $request)
{
    $ipList = IpList::create($request->all());
    return response()->json($ipList);
}

public function show(IpList $ipList)
{
    return response()->json($ipList);
}

public function update(Request $request, IpList $ipList)
{
    $ipList->update($request->all());
    return response()->json($ipList);
}

public function destroy(IpList $ipList)
{
    $ipList->delete();
    return response()->json(['message' => 'IpList deleted successfully']);
}

}
