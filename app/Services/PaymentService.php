<?php

namespace App\Services;

use Stripe\Account;
use Stripe\Stripe;
use TomorrowIdeas\Plaid\Plaid;

class PaymentService
{
    private $plaid;

    public function __construct()
    {
        // init Plaid SDK
        $this->plaid = new Plaid(
            config('plaid.client_id'),
            config('plaid.client_secret'),
            null,
            config('plaid.environment')
        );

        // Stripe Set Key
        Stripe::setApiKey(config('cashier.secret'));
    }

    /**
     * Get link_token from plaid
     * @return object
     */
    public function plaidCreateLinkToken()
    {
        try {
            $clientName = config('app.name');
            $language = 'en';
            $countryCodes = ['US'];
            $clientUserId = 1;// TODO: auth()->id();
            $products = ['auth', 'transactions'];
            return $this->plaid->createLinkToken($clientName, $language, $countryCodes, $clientUserId, $products);
//            [
//            expiration: "2020-09-29T13:15:48Z"
//            link_token: "link-sandbox-f3f67ee1-b925-4df9-9549-9ef8a6f22921"
//            request_id: "3R8Hce2udaORWRy
//            ]
        } catch (\Exception $exception) {
            abort(500, $exception->getMessage());
        }
    }

    /**
     * Plaid - Get access token and create Stripe connect account
     * @param $publicToken
     * @param $accountID - bank account id
     * @param $accountName - bank account name
     * @return object
     */
    public function getAccessTokenAndCreateAccount($publicToken, $accountID, $accountName)
    {
        try {
            $jsonParsed = $this->plaid->exchangeToken($publicToken);

            // Plaid: need enable Select Account https://dashboard.plaid.com/link/account-select
            $data = $this->plaid->createStripeToken($jsonParsed->access_token, $accountID);

            // Creat Stripe account
            $account = Account::create([
                'country' => 'US',
                'type' => 'custom',
                'email' => 'jenny.rosen@example.com', // TODO: \auth()->user()->email,
                'capabilities' => [
                    'transfers' => ['requested' => true],
                ],
                'external_account' => $data->stripe_bank_account_token // "btok_1HWwHiDcipPMJ2hZ7vV2bOPx",
                // TODO: need last name, website link, terms accept (gui kem ip la dc)
            ]);
            // Full example
//            $account = Account::create([
//                'country' => 'US',
//                'type' => 'custom',
//                'business_type' => 'individual', //require
//                'individual' => [ //require
//                    'email' => Auth::user()->email,
//                    'first_name' => Auth::user()->fullname,
//                    'last_name' => Auth::user()->fullname,
//                    // 'business_website' => 'quichef.com'
//                ],
//                'business_profile'=>[ //require
//                    'url'=>'quichef.com'
//                ],
//                'requested_capabilities' => [ 'transfers'], //require
//                'email' => Auth::user()->email, //require
//                'external_account' => $data->stripe_bank_account_token,
//                'tos_acceptance' => [ //require
//                    'date' => time(),
//                    'ip' => $_SERVER['REMOTE_ADDR'],
//                ],
//                'default_currency'=> Auth::user()->currency
//            ]);

            // Save account_id ($account->id) and $accountName to user (chef)
            \auth()->user()->update([
                'bank_account_name' => $accountName, // optional
                'stripe_account_id' => $account->id // required
            ]);

            return $account;
            // [
            // request_id: "kiBH0E9WhaAe3ug"
            // stripe_bank_account_token: "btok_1HWewaDcipPMJ2hZVTmHCCYd"
            // ]
        } catch (\Exception $exception) {
            abort(500, $exception->getMessage());
        }
    }

    /**
     * Rút tiền về tài khoản ngân hàng thông qua plaid - withdrawal
     */
    public function withdrawal()
    {
        $accountID = "acct_1HVDTrIo3OQqINzl";// TODO: \auth()->user()->stripe_account_id;
        if(!$accountID) abort(500, 'User not connected to the bank!');

        try {
            $amount = 50; // 50 USD - TODO: amount

            // transfer
            $data = \Stripe\Transfer::create([
                'amount' => $amount * 100,
                'currency' => 'usd',
                'destination' => $accountID,
                'transfer_group' => 'TRANSFER_CHEF',
            ]);

            // save transfer to db

            // return
            return $data;
        }catch (\Exception $exception) {
            abort(500, $exception->getMessage());
        }
    }
}
