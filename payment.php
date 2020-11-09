<?php
// 1. Install https://github.com/stripe/stripe-php to use setApiKey
// 2.
require_once('vendor/stripe/stripe-php/init.php');
// Include Stripe PHP library

define('BASE_URL', 'http://localhost/stripe-pay');
define('STRIPE_SUCCESS_URL', BASE_URL.'?success=true');
define('STRIPE_CANCEL_URL', BASE_URL.'/cancel.html');
define('STRIPE_API_KEY', 'sk_test_.............');
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_.........');

// Set API key

// Install https://github.com/stripe/stripe-php to use setApiKey
\Stripe\Stripe::setApiKey(STRIPE_API_KEY);


// Set Invalid request response
$response = array(
    'status' => 0,
    'error' => array(
        'message' => 'Invalid Request!'
    )
);

// CHECK IF METHOD TO ACCESS THIS FILE IS POST REQUEST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = file_get_contents('php://input');
    $request = json_decode($input);
}

// If there is an error
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode($response);
    exit;
}

// Items to be passed to the stripe checkout data
$productName =  $request->description; //description
$productID =  $request->name; //meta data
$productPrice = $request->amount; //amount to be charged from card
$currency = "usd"; //currency
$stripeAmount = round($productPrice*100, 2); //stripe calculation

if(!empty($request->checkoutSession)){
    // Creating a new Checkout Session for the order
    try {
        $session = \Stripe\Checkout\Session::create([ //Stripe Php library has to be installed first
            'payment_method_types' => ['card'],
            'customer_email' => $request->email,
            'line_items' => [[
                'price_data' => [
                    'product_data' => [
                        'name' => $productName,
                        'metadata' => [
                            'pro_id' => $productID
                        ]
                    ],
                    'unit_amount' => $stripeAmount,
                    'currency' => $currency,
                ],
                'quantity' => 1,

                'description' => $productName,
            ]],
            'mode' => 'payment',
            'success_url' => STRIPE_SUCCESS_URL,
            'cancel_url' => STRIPE_CANCEL_URL,
        ]);
    }catch(Exception $e) { //Throws error if there is an error
        $api_error = $e->getMessage();
    }

    // Success message
    if(empty($api_error) && $session){
        $response = array(
            'status' => 1,
            'message' => 'Checkout Session created successfully!',
            'sessionId' => $session['id']
        );
    }else{
        // Error message
        $response = array(
            'status' => 0,
            'error' => array(
                'message' => 'Checkout Session creation failed! '.$api_error
            )
        );
    }
}

// Return response
echo json_encode($response);


?>