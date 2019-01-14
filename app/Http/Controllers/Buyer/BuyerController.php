<?php

namespace App\Http\Controllers\Buyer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\User as UserResource;
use App\Buyer;
use App\Transaction;

class BuyerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $buyers = Buyer::has('transactions')->get();
        return ( new UserResource($buyers) )
            ->response()
                ->setStatusCode(200);
    }

  
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Buyer $buyer)
    {
        // $buyer = Buyer::has('transactions')->findOrFail($buyer->id);
        if (!$buyer) {
            return Response::json(['error' => 'Buyer with the specified id does not exist', 'code' => 404], 404);
        }
        return (new UserResource($buyer))
            ->response()
            ->setStatusCode(200);
    }


}
