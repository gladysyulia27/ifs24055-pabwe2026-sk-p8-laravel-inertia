<?php
namespace App\Features\Post\Enums;
enum PostCategory:string {case Teknologi='Teknologi';case Pendidikan='Pendidikan';case GayaHidup='Gaya Hidup';case Bisnis='Bisnis';case Opini='Opini';case Umum='Umum';public static function options():array{return array_map(fn($c)=>['value'=>$c->value,'label'=>$c->value],self::cases());}}
