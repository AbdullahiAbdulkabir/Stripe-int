<?php 
// require_once('vendor/autoload.php'); Load via composer
// include_once("index.html");
define('BASE_URL', 'http://localhost/stripe-pay');
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_.....');

 ?>

<!doctype html>
<html>

<head>
    
</head>

<body id="body">
<center >

    <?php
    if (isset($_GET['success'])){?>
    <!-- Show success message on index page and Redirect  -->
        <div class="alert alert-success" role="alert">
            Payment Successful.
        </div>
   <?php
        header('refresh:4;url= '. BASE_URL);
    } ?>
</center>
<section class="unfull">
    <div class="container">
       
        

        <div id="homeRight" class="fullShow">
           

            


            <div id="quoteDiv">
                <div style="height: 300px; width: 100%;">
                    <form id="quoteForm" onsubmit="event.preventDefault(); pay();">
                        <!-- <div id="card-element">
                            Stripe.js injects the Card Element
                        </div> -->
                        <div id="paymentResponse">
                        <!-- If there is an error show the error here -->
                        </div>
                        <div class="putBlock">
                            You can request a quote here. Just fill in the details below and tell us a bit about
                            your project. :-)
                        </div>
                        <div class="putBlock">
                            <input type="text" placeholder="Your Full name" class="putted" name="organisation"
                                   id="payment-name">
                        </div>
                        <div class="putBlock">
                            <input type="text" placeholder="Your email" class="putted" name="email"
                                   id="payment-email">
                        </div>
                        <div class="putBlock">
                            <input type="number" placeholder="Amount" class="putted" name="amount"
                                   id="payment-amount">
                        </div>
                        <div class="putBlock">
                                <textarea type="text" placeholder="Service description" class="putted"
                                          name="description" id="payment-description"></textarea>
                        </div>
                        <div class="putBlock">
                            <button type="submit" id="payment-button">Pay Bill</button>
                        </div>
                    </form>
                </div>
            </div>

          

        </div>
    </div>
   

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
    <script src="https://checkout.stripe.com/checkout.js"></script>

    <script>

        function pay() {
            // Open Checkout with further options:
            const amountRegex = /^\d+(\.\d{0,2})?$/;
            const amount = document.getElementById('payment-amount').value;
            const email = document.getElementById('payment-email').value;
            const name = document.getElementById('payment-name').value;
            const description = document.getElementById('payment-description').value;

            if (!amountRegex.test(amount)) return alert('NaN');
            if (!validateEmail(email)) return alert('Invalid Email');

            var buyBtn = document.getElementById('payment-button');
            var responseContainer = document.getElementById('paymentResponse');

        // Create a Checkout Session with the selected product
            var createCheckoutSession = function (stripe) {
                return fetch("payment.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        // Parmemeter to be passed to payment file
                        checkoutSession: 1,
                        description: description,
                        name: name,
                        amount: amount,
                        email: email
                    }),
                }).then(function (result) {
                    return result.json();
                });
            };

// Handle any errors returned from Checkout
            var handleResult = function (result) {
                if (result.error) {
                    responseContainer.innerHTML = '<p>'+result.error.message+'</p>';
                }
                buyBtn.disabled = false;
                buyBtn.textContent = 'Buy Now';
            };

// Specify Stripe publishable key to initialize Stripe.js
            var stripe = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY; ?>');

            buyBtn.addEventListener("click", function (evt) {
                buyBtn.disabled = true;
                buyBtn.textContent = 'Please wait..';

                createCheckoutSession().then(function (data) {
                    if(data.sessionId){
                        stripe.redirectToCheckout({
                            sessionId: data.sessionId,
                        }).then(handleResult);
                    }else{
                        handleResult(data);
                    }
                });
            });
        }

   

        function validateEmail(email) {
            const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }

    </script>
</section>

</body>

</html>