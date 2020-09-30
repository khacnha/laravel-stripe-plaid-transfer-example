## About project
Stripe + plaid: transfer money from stripe to Bank account (via plaid)

## Document
- Connect Plaid: https://plaid.com/docs/stripe/, https://stripe.com/docs/ach
- Create custom account: https://stripe.com/docs/connect/custom-accounts, https://stripe.com/docs/api/accounts/create
- Transfer money: https://stripe.com/docs/api/transfers/create 

## Note:
- Plaid: need enable Select Account https://dashboard.plaid.com/link/account-select
- Stripe: You must "Verify your account" in Stripe to be able to demo the Transfer function 

## Config:
- Install Stripe SDK ([laravel/cashier](https://laravel.com/docs/8.x/billing)) and Plaid SDK ([tomorrow-ideas/plaid-sdk-php](https://github.com/TomorrowIdeas/plaid-sdk-php))
- Update .env file:
```
STRIPE_KEY=your-stripe-key
STRIPE_SECRET=your-stripe-secret

PLAID_CLIENT_ID=
PLAID_CLIENT_SECRET=
PLAID_ENVIRONMENT=sandbox
```
