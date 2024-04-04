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
        <p style="font-size: 20px">Thank you {{ $client->name }} for choosing Evergreen By Design!</p>
        <p>Follow these steps to start your services</p>
    </section>

    <section style="display:inline-block">
        

        <div style="border-bottom: 2px solid #cbd5e1;">
            <h4>First: Review and Sign Service Agreement</h4>
            <p>by clicking this link: https://www.signwell.com/new_doc/vmVdowuqk8YXTrXK/</p>
        </div>

        <div style="border-bottom: 2px solid #cbd5e1;">

            <h4>Second: Pay Your 50% Deposit Payment</h4>
            <p>Save this email as your invoice</p>

            <div>
                
                <div>
                    <p>Preferred Payment Method: {{ $client->payment_option }}</p>
                    
                    @if ($payment->payment_method === 'Credit Card') 
                    <p style="font-style: italic;">Note: Payment with credit card adds a 3.4% processing fee.</p>
                    @endif

                    <p>Deposit Amount: ${{ $payment->amount }}</p>

                    <p>3.4% Processing Fee:  ${{ $payment->processing_fee ? $payment->processing_fee : "0.00 free with " . $payment->payment_method }}</p>

                    <p style="font-weight: bold;">Total Payment Amount: ${{ $payment->card_amount ? $payment->card_amount : $payment->amount }}</p>

                    @if ($payment->payment_method === 'Credit Card') 
                    <p style="margin-bottom: 8px;">Click Link To Pay With Card: {{ $payment->payment_link }}</p>

                    <p style="font-style: italic;">Avoid future processing fees switch your perferred payment method to Zelle of Venmo.</p>

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
                <p>Confirm that the information below are correct and you received this email by replying "YES" to this email.</p>
                <div>
                    
                    <p>Name: {{ $client->name }}</p>
                    <p>Phone: {{ $client->phone }}</p>
                    <p>Email: {{ $client->email }}</p>
                    <p>Location: {{ $client->location }}</p>
                    <p>Hosting Services: {{ $client->hosting === 'Pending' ? 'EBD Hosting' : 'Self Hosting' }}</p>
                    <p>Initial Quote: {{ $client->quote }}</p>
                    <p>Balance Remaining Upon Completion: {{ $client->deposit }}</p>
                    <p>Details: {{ $client->details }}</p> 

                </div>
            </div>
        </div>

        <div>
            <p>Thank you. We appreciates your business!</p>
        </div>

    </section>

    
</body>
</html>