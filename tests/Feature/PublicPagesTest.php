<?php
namespace Tests\Feature;
use Tests\TestCase;use Illuminate\Foundation\Testing\RefreshDatabase;use App\Models\User;use App\Features\Post\Models\Post;use App\Features\Post\Policies\PostPolicy;use App\Features\Post\Repositories\PostRepository;use App\Features\Ai\Services\OpenAiPostGeneratorService;
class PublicPagesTest extends TestCase {
    use RefreshDatabase;
    public function test_home_page_is_accessible():void{$this->get('/')->assertOk();}
    public function test_login_page_is_accessible():void{$this->get('/login')->assertOk();}
    public function test_register_page_is_accessible():void{$this->get('/register')->assertOk();}
    public function test_dashboard_and_services_work_properly():void{
        $user=User::create(['name'=>'Writer','username'=>'writer','email'=>'writer@example.com','password'=>'password123','role'=>'member']);
        $post=Post::create(['user_id'=>$user->id,'title'=>'Belajar Laravel','slug'=>'belajar-laravel','category'=>'Teknologi','excerpt'=>'Ringkasan','content'=>'Konten lengkap artikel belajar laravel untuk pemula.','is_published'=>true,'published_at'=>now()]);
        $this->actingAs($user)->get('/dashboard/posts')->assertOk();
        $policy=new PostPolicy();
        $this->assertTrue($policy->view($user,$post));
        $this->assertTrue($policy->update($user,$post));
        $this->assertTrue($policy->delete($user,$post));
        $repo=new PostRepository();
        $this->assertCount(1,$repo->publicPosts(['search'=>'laravel','category'=>'Teknologi','status'=>'published'])->items());
        $this->assertCount(1,$repo->userPosts($user,['status'=>'published'])->items());
        $ai=new OpenAiPostGeneratorService();
        $generated=$ai->generate('Teknologi Masa Depan','Teknologi',2);
        $this->assertCount(2,$generated);
    }
}
