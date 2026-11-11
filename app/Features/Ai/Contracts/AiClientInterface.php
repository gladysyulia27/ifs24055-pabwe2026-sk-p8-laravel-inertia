<?php
namespace App\Features\Ai\Contracts;
interface AiClientInterface {public function generate(string $topic,string $category,int $count):array;}
