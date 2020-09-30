<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Plaid link</title>
</head>
<body>
<button id="link-button">Link Account</button>

<script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
<script type="text/javascript">
    (async function () {
        // Step 1: create link token (có thể tạo để app gọi và truyền link_token vào webview)
        const fetchLinkToken = async () => {
            const response = await fetch('{{ route('payment.create-link-token') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            });
            const responseJSON = await response.json();
            if (response.ok) {
                return responseJSON.link_token;
            } else {
                alert(responseJSON.message);
            }
            return null;
        };

        // Step 2: show website - link to bank (nếu là API thì truyền APP truyền link_token ở bước 1 cho webview)
        const configs = {
            // 1. Pass a new link_token to Link.
            token: await fetchLinkToken(), //token: '{{ request('link_token') }}',
            onSuccess: async function (public_token, metadata) {
                console.log(public_token, metadata);
                // 2a. Send the public_token to your app server.
                // The onSuccess function is called when the user has successfully
                // authenticated and selected an account to use.
                const response = await fetch('{{ route('payment.create-account') }}', { // có thể thêm ?token=.. để xác định user
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({public_token: public_token, account_id: metadata.account_id, account_name: metadata.account.name}),
                });
                const responseJSON = await response.json();
                if (response.ok) {
                    console.log(responseJSON);

                    // redirect success page

                } else {
                    alert(responseJSON.message);
                }
                // request_id: "kiBH0E9WhaAe3ug"
                // stripe_bank_account_token: "btok_1HWewaDcipPMJ2hZVTmHCCYd"
            },
            onExit: async function (err, metadata) {
                // 2b. Gracefully handle the invalid link token error. A link token
                // can become invalidated if it expires, has already been used
                // for a link session, or is associated with too many invalid logins.
                if (err != null && err.error_code === 'INVALID_LINK_TOKEN') {
                    linkHandler.destroy();
                    linkHandler = Plaid.create({
                        ...configs,
                        token: await fetchLinkToken(),
                    });
                }
                if (err != null) {
                    // Handle any other types of errors.
                }
                // metadata contains information about the institution that the
                // user selected and the most recent API request IDs.
                // Storing this information can be helpful for support.
            },
        };
        // bank account test: user_good/pass_good
        var linkHandler = Plaid.create(configs);

        document.getElementById('link-button').onclick = function () {
            linkHandler.open();
        };
    })();
</script>
</body>
</html>
