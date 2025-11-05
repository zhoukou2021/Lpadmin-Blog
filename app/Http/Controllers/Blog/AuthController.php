<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\LPadmin\User;

class AuthController extends Controller
{
    /**
     * 用户登录
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'username.required' => '请输入用户名或邮箱',
            'password.required' => '请输入密码',
            'password.min' => '密码至少6个字符',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $username = $request->input('username');
        $password = $request->input('password');
        $remember = $request->boolean('remember', false);

        // 查找用户（支持用户名、邮箱、手机号登录）
        $user = User::where(function($query) use ($username) {
            $query->where('username', $username)
                  ->orWhere('email', $username)
                  ->orWhere('phone', $username);
        })->first();

        if (!$user) {
            return response()->json([
                'code' => 401,
                'message' => '用户名或密码错误',
            ], 401);
        }

        // 检查用户状态
        if ($user->status !== User::STATUS_ENABLED) {
            return response()->json([
                'code' => 403,
                'message' => '账户已被禁用',
            ], 403);
        }

        // 验证密码
        if (!Hash::check($password, $user->password)) {
            return response()->json([
                'code' => 401,
                'message' => '用户名或密码错误',
            ], 401);
        }

        // 更新最后登录信息
        $user->last_login_at = now();
        $user->last_login_ip = $request->ip();
        $user->save();

        // 登录用户（使用 web guard）
        Auth::guard('web')->login($user, $remember);

        return response()->json([
            'code' => 0,
            'message' => '登录成功',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'nickname' => $user->nickname,
                    'email' => $user->email,
                    'avatar' => $user->avatar_url,
                ],
            ],
        ]);
    }

    /**
     * 用户注册
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'min:3', 'max:255', 'unique:users,username'],
            'nickname' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'username.required' => '请输入用户名',
            'username.min' => '用户名至少3个字符',
            'username.unique' => '用户名已被使用',
            'email.required' => '请输入邮箱',
            'email.email' => '邮箱格式不正确',
            'email.unique' => '邮箱已被注册',
            'password.required' => '请输入密码',
            'password.min' => '密码至少6个字符',
            'password.confirmed' => '两次输入的密码不一致',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        // 创建用户
        $user = User::create([
            'username' => $request->input('username'),
            'nickname' => $request->input('nickname') ?: $request->input('username'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'status' => User::STATUS_ENABLED,
            'gender' => User::GENDER_UNKNOWN,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // 自动登录
        Auth::guard('web')->login($user);

        return response()->json([
            'code' => 0,
            'message' => '注册成功',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'nickname' => $user->nickname,
                    'email' => $user->email,
                    'avatar' => $user->avatar_url,
                ],
            ],
        ]);
    }

    /**
     * 用户登出
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'code' => 0,
            'message' => '登出成功',
        ]);
    }

    /**
     * 获取当前登录用户信息
     */
    public function user(Request $request)
    {
        $user = Auth::guard('web')->user();
        
        if (!$user) {
            return response()->json([
                'code' => 401,
                'message' => '未登录',
            ], 401);
        }

        return response()->json([
            'code' => 0,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'nickname' => $user->nickname,
                    'email' => $user->email,
                    'avatar' => $user->avatar_url,
                ],
            ],
        ]);
    }
}

