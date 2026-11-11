<?php
namespace App\Features\Admin\Controllers;
use App\Http\Controllers\Controller;use App\Models\User;use App\Features\Post\Services\PostExcelService;use Inertia\Inertia;
class AdminUserController extends Controller {public function index(){return Inertia::render('Admin/UserList',['users'=>User::withCount('posts')->latest()->paginate(15)]);}public function posts(User $user){return Inertia::render('Admin/UserPostList',['author'=>$user->only('id','name','username','email'),'posts'=>$user->posts()->with('user')->latest()->paginate(15)]);}public function export(User $user,PostExcelService $excel){return $excel->export($user->posts()->latest(),'artikel-'.$user->username.'.xlsx');}}
