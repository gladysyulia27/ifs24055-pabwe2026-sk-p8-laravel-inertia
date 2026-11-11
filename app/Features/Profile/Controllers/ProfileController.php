<?php
namespace App\Features\Profile\Controllers;
use App\Http\Controllers\Controller;use Illuminate\Http\Request;use Illuminate\Validation\Rule;use Inertia\Inertia;
class ProfileController extends Controller {public function edit(Request $r){return Inertia::render('Profile/Edit',['user'=>$r->user()->only('name','username','email','bio')]);}public function update(Request $r){$user=$r->user();$data=$r->validate(['name'=>'required|string|max:120','username'=>['required','alpha_dash','min:3','max:40',Rule::unique('users')->ignore($user->id)],'email'=>['required','email',Rule::unique('users')->ignore($user->id)],'bio'=>'nullable|string|max:500']);$user->update($data);return back()->with('success','Profil berhasil diperbarui.');}}
