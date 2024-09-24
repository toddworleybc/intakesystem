<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Email</title>
    <style>

        h4, p {
            color: black;
        }

    </style>


</head>
<body>

    @if (isset($view))
        
        <div>
            <h2>Subject: EBD Payment Request - Invoice {{ $payment->invoice_id }}</h2>
        </div>

    @endif

    <section>
        <p style="font-size: 20px">Hi, {{ $payment->client['name'] }}</p>
        <p>This a payment request from Evergreen By Design! Save this email as your invoice.</p>
    </section>

    <section style="display:inline-block">
        

        

        <div style="border-bottom: 2px solid #cbd5e1;">

            <h4>Amount Due: {{ $payment->payment_method === 'Credit Card' ? $payment->card_amount : $payment->amount }}</h4>
            <p>For: {{ $payment->for }}</p>

            <div>
                
                <div>
                    <p>Invoice Id: {{ $payment->invoice_id }}</p>
                    <p>Payment Frequency: {{ $payment->frequency === 'one_time' ? "One Time Payment" : "$payment->amount Monthly" }}</p>

                    <p>{{$payment->frequency === 'one_time' ? 'Preferred' : ''}} Payment Method: {{ $payment->payment_method }}</p>

                    @if ($payment->payment_method === 'Credit Card' && $payment->frequency === 'one_time') 
                    <p style="font-style: italic;">Note: Payment with credit card adds a 3.4% processing fee.</p>
                    <p>Amount Charged: {{ $payment->amount }}</p>
                    @endif

                    <p>{{ config('services.stripe.processing_fee') * 100 }}% Processing Fee:  {{ $payment->processing_fee ? $payment->processing_fee : "0.00 free with " . $payment->payment_method }}</p>

                    <p style="font-weight: bold;">Total {{ $payment->frequency === "recurring" ? 'Monthly' : '' }} Payment Amount: {{ $payment->card_amount ? $payment->card_amount : $payment->amount }}</p>

                    @if ($payment->payment_method === 'Credit Card') 
                    <p style="margin-bottom: 8px;">Click Link To Pay With Card: {{ $payment->payment_link }}</p>

                     @if ($payment->frequency === 'one_time')
                     <p style="font-style: italic;">Avoid future processing fees switch your perferred payment method to Zelle of Venmo.</p>
                     @endif

                    @elseif ($payment->payment_method === 'Zelle') 

                    <p style="margin-bottom: 8px;">Send Payment by Zelle to: todd@evergreenbydesign.com</p>

                    @elseif ($payment->payment_method === 'Venmo') 

                    <p style="margin-bottom: 8px;">Send Payment by Venmo to: @evergreenbydesign</p>

                    @endif 
                    
                </div>
            </div>

        </div>

        <div>
            <p>Thank you. We appreciates your business!</p>
        </div>
        <div style="border-bottom: 2px solid #cbd5e1;">

            <p>Appreciate our work and want to show us some extra love! We always appreciate a good cup of coffee ☕ at or review at Evergreen By Design.</p> 

            <p>Coffee Donation Link: <a href="https://buy.stripe.com/14k9D61M9d5j50cdRT">https://buy.stripe.com/14k9D61M9d5j50cdRT</a></p>

            <p>Leave Us a Review Here: <a href="https://g.page/r/CTAVc79GDT9HEB0/review">https://g.page/r/CTAVc79GDT9HEB0/review</a></p>

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