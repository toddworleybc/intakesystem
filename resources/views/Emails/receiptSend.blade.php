<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Email</title>
    <style>

        h4, p {
            color: black;
        }

    </style>


</head>
<body>

    @if (isset($view))
        
        <div>
            <h2>Subject: EBD - Thank you for your payment</h2>
        </div>

    @endif

    <section>
        <p style="font-size: 20px">Payment Receipt</p>
        <p>Thank you {{ $payment->client['name'] }} for your payment!</p>
        <p>Save or print this email as your receipt</p>
    </section>

    <section style="display:inline-block">
        

        <div style="border-bottom: 2px solid #cbd5e1;">
        
            <h4>Amount Paid: {{ $payment->payment_method === 'Credit Card' ? "$".$payment->card_amount : "$".$payment->amount }}</h4>
            <p>For: {{ $payment->for }}</p>

            <div>
                
                <div>
                    <p>Invoice Id: <strong>{{ $payment->invoice_id }}</strong></p>
                    <p>Payment Frequency: {{ $payment->frequency === 'one_time' ? "One Time Payment" : "$$payment->amount Monthly" }}</p>

                    <p>{{$payment->frequency === 'one_time' ? 'Preferred' : ''}} Payment Method: {{ $payment->payment_method }}</p>

                    @if ($payment->payment_method === 'Credit Card' && $payment->frequency === 'one_time') 
                    <p style="font-style: italic;">Note: Payment with credit card adds a {{ config('services.stripe.processing_fee') * 100 }}% processing fee.</p>
                    <p>Amount Charged: {{ "$".$payment->amount }}</p>
                    @endif

                    <p>{{ config('services.stripe.processing_fee') * 100 }}% Processing Fee:  {{ $payment->processing_fee ? "$".$payment->processing_fee : "- 0.00 free with " . $payment->payment_method }}</p>

                    <p style="font-weight: bold;">Total Payment Amount Due: {{ $payment->card_amount ? "$".$payment->card_amount : "$".$payment->amount}}{{ $payment->frequency === 'recurring' ? "/mo" : "" }}</p>

                    @if($payment->frequency === 'recurring')
                        <p class="italic">This is a <strong>Monthly Subscription Fee</strong> do every month on the date paid.</p>
                    @endif
                    
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