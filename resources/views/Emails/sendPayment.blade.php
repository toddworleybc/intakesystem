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

    </style>


</head>
<body>

       
    <section>
        <p style="font-size: 20px">Hi, {{ $payment->client->name }}</p>
        <p>This a payment request from Evergreen By Design! Save this email as your invoice.</p>
    </section>

    <section style="display:inline-block">
        

        

        <div style="border-bottom: 2px solid #cbd5e1;">

            <h4>Amount Due: {{  $payment->amount }}</h4>
            <p>For: {{ $payment->for }}</p>

            <div>
                
                <div>
                    <p>Invoice Id: {{ $payment->invoice_id }}</p>
                    <p>Payment Frequency: {{ $payment->frequency === 'one_time' ? "One Time Payment" : "$$payment->amount Monthly" }}</p>

                    <p>{{$payment->frequency === 'one_time' ? 'Preferred' : ''}} Payment Method: {{ $payment->payment_method }}</p>

                    @if ($payment->payment_method === 'Credit Card' && $payment->frequency === 'one_time') 
                    <p style="font-style: italic;">Note: Payment with credit card adds a 3.4% processing fee.</p>
                    @endif

                    <p>3.4% Processing Fee:  ${{ $payment->processing_fee ? $payment->processing_fee : "0.00 free with " . $payment->payment_method }}</p>

                    <p style="font-weight: bold;">Total {{ $payment->frequency === "recurring" ? 'Monthly' : '' }} Payment Amount: ${{ $payment->card_amount ? $payment->card_amount : $payment->amount }}</p>

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

    </section>

    
</body>
</html>