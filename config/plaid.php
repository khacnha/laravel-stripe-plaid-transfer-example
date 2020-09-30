<?php

return [
    /*
     * Plaid
     * https://plaid.com/docs/
     */
    'client_id' => env("PLAID_CLIENT_ID", ''),
    'client_secret' => env("PLAID_CLIENT_SECRET", ''),
    /*
     * Plaid envitonment:
     * - production
     * - development
     * - sandbox
     */
    'environment' => env("PLAID_ENVIRONMENT", 'sandbox'),
];
