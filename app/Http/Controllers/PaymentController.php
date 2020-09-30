<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController
{
    /*
     * Services
     */
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Plaid
     * Create link token
     * @return \Illuminate\Http\JsonResponse
     */
    public function plaidCreateLinkToken()
    {
        return response()->json($this->paymentService->plaidCreateLinkToken(), 200);
    }

    /**
     * Plaid link (view)
     * @return \Illuminate\View\View
     */
    public function plaidLink()
    {
        return view('payment.plaid-link');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAccessTokenAndCreateAccount(Request $request)
    {
        $publicToken = $request->get('public_token');
        $accountID = $request->get('account_id');
        $accountName = $request->get('account_name');
        if(empty($publicToken) || empty($accountID)) abort(500, 'public_token and account_id is required');

        return response()->json($this->paymentService->getAccessTokenAndCreateAccount($publicToken, $accountID, $accountName), 200);
    }

    /**
     * Just show success information
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function connectSuccess(){
        return view('payment.connect-success');
    }

    /**
     * Transfer
     * @return \Illuminate\Http\JsonResponse
     */
    public function withdrawal(){
        $data = $this->paymentService->withdrawal();
        return response()->json(['message' => 'Success'], 200);
    }


}
