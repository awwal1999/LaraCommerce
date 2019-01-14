<?php

namespace App\Http\Controllers\User;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Resources\User as UserResource;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $users = User::paginate(20);
        // return (new UserResource($users) )->additional(['meta' => [
        //     'Ok' => '200',
        //     'status' => 'All users returned',
        // ]]);

        $users = User::all();
        return new UserResource($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $validated = $this->validateUser();
        $validated['password'] = bcrypt($validated['password']);
        $validated['verified'] = User::UNVERIFIED;
        $validated['verification_token'] = User::GenerateVerificationCode();
        $validated['admin'] = User::REGULAR_USER;

        $user = User::create($validated);
        return (new UserResource($user) )->additional(['meta' => [
            'Ok' => '201',
        ]]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    public function update(User $user)
    {
        $validate = request()->validate([
            'email' => ['email', 'unique:users', 'email' . $user->id],
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . User::ADMIN_USER . ',' . User::REGULAR_USER,
        ]);

        // Validator::make(request(), [
        //     'zones' => [
        //         'required',
        //         Rule::in(['first-zone', 'second-zone']),
        //     ],
        // ]);

        if (request()->has('name')) {
            $user->name = request()->name;
        }
        if (request()->has('email') && $user->email != request()->email) {
            $user->verified = User::UNVERIFIED;
            $user->verification_token = User::GenerateVerificationCode();
            $user->email = request()->email;
        }
        if (request()->has('password')) {
            $user->password = bcrypt(request()->password);
        }
        if (request()->has('admin')) {
            if (!$user->IsVerified()) {
                return response()->json(['error' => 'Only verified users can modified the admin field', 'code' => 409], 409);
            }
            $user->admin = request()->admin;
        }
        if (!$user->isDirty()) {
            return response()->json(['error' => 'You need to specify a different value to update', 'code' => 422], 422);
        }
        $user->save();
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['data' => $user,], 200);
    }

    /**
     * Validate users
     */
    protected function validateUser()
    {
        return request()->validate([
            'name' => ['required', 'min:3', 'max:190'],
            'email' => ['required', 'email','unique:users'],
            'password' => 'required|min:6|confirmed',
        ]);
    }
}
