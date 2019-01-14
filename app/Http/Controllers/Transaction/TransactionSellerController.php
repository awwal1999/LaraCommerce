<?php

namespace App\Http\Controllers\Transaction;

use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Transaction as TransactionResource;

class TransactionSellerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(Transaction $transaction)
    {
        $seller = $transaction->product->seller;
        return new TransactionResource($seller);
    }

}
