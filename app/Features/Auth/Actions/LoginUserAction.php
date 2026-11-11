<?php
namespace App\Features\Auth\Actions;
use Illuminate\Support\Facades\Auth;use Illuminate\Validation\ValidationException;
class LoginUserAction {public function execute(array $data):void{if(!Auth::attempt(['username'=>$data['username'],'password'=>$data['password']],$data['remember']??false))throw ValidationException::withMessages(['username'=>'Username atau password tidak sesuai.']);request()->session()->regenerate();}}
