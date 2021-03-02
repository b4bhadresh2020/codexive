<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function getExpenseTypes()
    {
        $expenseType = ExpenseType::all();
        return ApiResponse::create(["expenseType" => $expenseType]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $expenses = Expense::select()->with('account.masterAccount')->get();
        return ApiResponse::create([
            'expenses' => $expenses
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
        $invoice_path = ($request->hasFile('invoice_img')) ? Storage::disk()->put('images', $request->file('invoice_img')) : "";

        try {
            $expense = Expense::create(array_merge($request->except('invoice_img'), [
                "invoice_img" => $invoice_path,
            ]));
        } catch (\Exception $e) {
            return ApiResponse::createServerError($e);
        }

        return ApiResponse::create($expense);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $expense = Expense::find($id);
            $invoice_path = ($request->hasFile('invoice_img')) ? Storage::disk()->put('images', $request->file('invoice_img')) : "";
            if ($invoice_path != "") {
                if ($expense->invoice_img && Storage::disk()->exists($expense->invoice_img)) {
                    @Storage::disk()->delete($expense->invoice_img);
                }
            }
            Expense::where('id', $id)->update(
                array_merge($request->except('invoice_img', '_method'), [
                    "invoice_img" => $invoice_path,
                ])
            );
        } catch (\Exception $e) {
            return ApiResponse::createServerError($e);
        }

        return ApiResponse::__create("Expense update successfully.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $expense = Expense::find($id);
            $expense->delete();
        } catch (\Exception $e) {
            return ApiResponse::createServerError($e);
        }
        return ApiResponse::__create("Expenes deleted successfully.");
    }
}
