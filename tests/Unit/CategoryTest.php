<?php
namespace Tests\Unit;
use PHPUnit\Framework\TestCase;use App\Features\Post\Enums\PostCategory;
class CategoryTest extends TestCase {public function test_all_six_categories_are_available():void{$this->assertCount(6,PostCategory::cases());}}
