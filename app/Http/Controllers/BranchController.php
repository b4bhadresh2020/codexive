<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branches = Branch::with('user')->get();

        return ApiResponse::create([
            "branches" => $branches
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                      => 'required|string|unique:branches,name',
            'email'                     => 'required|email|unique:users,email',
            'password'                  => 'required'

        ], [
            'name.unique'               => 'username already exist',
            'email.unique'              => 'email already exist',
        ]);

        if ($validator->fails()) return ApiResponse::createValidationResponse($validator->errors());

        try{
            DB::beginTransaction();

            $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            ]);

            $user->save();

            $branch = Branch::create([
                'user_id' => $user->id,
                'name' => $request->name
                ]);
            DB::commit();


        }catch(\Exception $e){
            return ApiResponse::createServerError($e);
        }

        return ApiResponse::__create( $branch->name . " Branch created successfully");

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show(Branch $branch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function edit(Branch $branch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Branch $branch)
    {
        $validator = Validator::make($request->all(), [
            'name'                      => 'required|string|unique:branches,name',
        ], [
            'name.unique'               => 'username already exist',
        ]);

        if ($validator->fails()) return ApiResponse::createValidationResponse($validator->errors());

        $branch->name = $request->name;
        $branch->save();
        return ApiResponse::__create( $branch->name . " Branch updated successfully");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Branch $branch)
    {
        $branch->delete();
        return ApiResponse::__create("Branch deleted successfully");

    }
}
