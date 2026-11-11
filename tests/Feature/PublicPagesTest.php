<?php
namespace Tests\Feature;
use Tests\TestCase;use Illuminate\Foundation\Testing\RefreshDatabase;
class PublicPagesTest extends TestCase {use RefreshDatabase;public function test_home_page_is_accessible():void{$this->get('/')->assertOk();}public function test_login_page_is_accessible():void{$this->get('/login')->assertOk();}public function test_register_page_is_accessible():void{$this->get('/register')->assertOk();}}
