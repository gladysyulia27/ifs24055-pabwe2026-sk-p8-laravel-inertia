<?php
namespace App\Features\Post\Controllers;
use App\Http\Controllers\Controller;use App\Features\Post\Models\Post;use App\Features\Post\Services\PostExcelService;use Inertia\Inertia;
class AdminPostController extends Controller {public function index(){return Inertia::render('Admin/PostList',['posts'=>Post::with('user')->latest()->paginate(15)]);}public function export(PostExcelService $excel){return $excel->export(Post::query()->latest(),'seluruh-artikel-blogverse.xlsx');}}
