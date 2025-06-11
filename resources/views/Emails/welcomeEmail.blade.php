
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Email</title>
    <style>

        h4, p {
            color: black;
        }

        .italic {
            font-style: italic;
        }

    </style>


</head>
<body>

    @if (isset($view))
    
        <div>
            <h2>Subject: Welcome To Evergreen By Design</h2>
        </div>

    @endif
    


    <section>
        <p style="font-size: 20px">Thank you {{ $client->name }} for choosing Evergreen By Design!</p>
        <p>--- follow these steps to start your services ---</p>
    </section>

    <section style="display:inline-block">
        

        <div style="border-bottom: 2px solid #cbd5e1;">
            <h4>First: Review and Sign Online Marketing Services Agreement</h4>
            <p>Click this link to begin signing service agreement: <a href="{{ config('services.signwell.link') }}">Sign Agreement</a></p>
        </div>

        <div style="border-bottom: 2px solid #cbd5e1;">

            <h4>Second: Payment Invoice</h4>
            <p>Save this email as your invoice</p>
            <p>Invoice Id: <strong>{{ $payment->invoice_id }}</strong></p>
            <div>
                
                <div>
                    <p>Preferred Payment Method: {{ $client->payment_option }}</p>
                    
                    @if ($payment->payment_method === 'Credit Card') 
                    <p style="font-style: italic;">Note: Payment with credit card adds a {{config("services.stripe.processing_fee") * 100}}% processing fee.</p>
                    @endif

                    <p>Amount: {{ $payment->amount }}</p>

                    <p>{{ config('services.stripe.processing_fee') * 100 }}% Processing Fee:  {{ $payment->processing_fee ? $payment->processing_fee : "$0.00 - free with " . $payment->payment_method }}</p>

                    <p style="font-weight: bold;">Total Payment Amount Due: {{ $payment->card_amount ? $payment->card_amount : $payment->amount}}{{ $payment->frequency === 'recurring' ? "/mo" : "" }}</p>

                    @if($payment->frequency === 'recurring')
                        <p class="italic">This is a <strong>Monthly Subscription Fee</strong> do every month on the date paid.</p>
                    @endif

                    @if ($payment->payment_method === 'Credit Card') 
                    <p style="margin-bottom: 8px;">Click Link To Pay With Card: <a href="{{ $payment->payment_link }}">Proceed with Purchase</a></p>

                    @elseif ($payment->payment_method === 'Zelle') 
                    <p style="margin-bottom: 8px;">Send Payment by Zelle to: todd@evergreenbydesign.com</p>

                    @elseif ($payment->payment_method === 'Venmo') 
                    <p style="margin-bottom: 8px;">Send Payment by Venmo to: @evergreenbydesign</p>

                    @endif
                    
                </div>
            </div>

        </div>
        
        <div style="border-bottom: 2px solid #cbd5e1;">
            <h4>Third: Review Your Information</h4>
            <div>
                <p>Confirm that the information below is correct and you received this email by replying "YES".</p>
                <div>
                    
                    <p>Name: {{ $client->name }}</p>
                    <p>Phone: {{ $client->phone }}</p>
                    <p>Email: {{ $client->email }}</p>
                    <p>Location: {{ $client->location }}</p>
                    <p>Payment Amount: {{ $client->payment_option === 'Credit Card' ? $payment->card_amount : $payment->amount }}</p>
                    <p>Details: {{ $client->details }}</p> 

                </div>
            </div>
        </div>


        <div>
            <p>Thank you. We appreciates your business!</p>
        </div>
        <div style="border-bottom: 2px solid #cbd5e1;">

            <p>Appreciate our work and want to show you some extra love! We always enjoy a good cup of coffee ☕ at Evergreen By Design. We also love great reviews!</p> 

            <p>Coffee Donation Link: <a href="https://buy.stripe.com/14k9D61M9d5j50cdRT">Buy us a cup of coffee.</a></p>

            <p>Leave Us a Review Here: <a href="https://g.page/r/CTAVc79GDT9HEB0/review">Leave Review</a></p>

        </div>
        <div>

            <p>Owner: Todd Worley</p>
            <p>Phone: <a href="tel:+15413785563">(541) 378.5563</a></p>
            <p>Email: <a href="mailto:todd@evergreenbydesign.com">todd@evergreenbydesign.com</a></p>
            <p>Website: <a href="https://evergreenbydesign.com">https://evergreenbydesign.com</a></p>
            <p>Location: Roseburg, Oregon</p>


        </div>

    </section>

    
</body>
</html>
