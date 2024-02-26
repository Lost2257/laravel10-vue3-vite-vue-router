<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\IpList;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    private function validateIpAddress($ip)
    {
        $allowedIpAddresses = IpList::pluck('ip_address')->toArray();

        \Log::info("Provided IP: $ip");
        \Log::info("Allowed IPs: " . implode(', ', $allowedIpAddresses));

        return in_array($ip, $allowedIpAddresses);
    }

    public function autoLogin(Request $request)
    {
        $ipAddress = $request->getClientIp();

        if ($this->validateIpAddress($ipAddress)) {
            $userIpAddress = $request->ip();
            auth()->loginUsingId(auth()->id());
            $name = IpList::where('ip_address', $userIpAddress)->value('name');
            $role = IpList::where('ip_address', $userIpAddress)->value('role');

            return response()->json(['success' => true, 'name'=>$name, 'roles'=>$role]);
        }

        return response()->json(['error' => 'Invalid IP address'], 401);
    }

    public function updateSession(Request $request)
    {
        $userIpAddress = $request->ip();
        $name = IpList::where('ip_address', $userIpAddress)->value('name');
        $request->session()->put('loggedIn', $request->input('loggedIn', false));
        $request->session()->put('name', $name);

        return response()->json(['success' => true]);
    }

    public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (auth()->attempt($credentials)) {
        $user = auth()->user();
        return response()->json(['message' => 'Login successful', 'user' => $user]);
    }

    return response()->json(['error' => 'Invalid credentials'], 401);
}

}
