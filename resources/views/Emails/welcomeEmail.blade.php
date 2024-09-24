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
        <p>--- Follow these steps to start your services ---</p>
    </section>

    <section style="display:inline-block">
        

        <div style="border-bottom: 2px solid #cbd5e1;">
            <h4>First: Review and Sign Service Agreement</h4>
            <p>Click this link to begin signing service agreement: <a href="{{ config('services.signwell.link') }}">Sign Agreement</a></p>
        </div>

        <div style="border-bottom: 2px solid #cbd5e1;">

            <h4>Second: Pay to Start Your Services</h4>
            <p>Save this email as your invoice</p>
            <p>Invoice Id: <strong>{{ $payment->invoice_id }}</strong></p>
            <div>
                
                <div>
                    <p>Preferred Payment Method: {{ $client->payment_option }}</p>
                    
                    @if ($payment->payment_method === 'Credit Card') 
                    <p style="font-style: italic;">Note: Payment with credit card adds a {{config("services.stripe.processing_fee") * 100}}% processing fee.</p>
                    @endif

                    <p>Amount: {{ "$".$payment->amount }}</p>

                    <p>{{ config('services.stripe.processing_fee') * 100 }}% Processing Fee:  {{ $payment->processing_fee ? "$".$payment->processing_fee : "$0.00 - free with " . $payment->payment_method }}</p>

                    <p style="font-weight: bold;">Total Payment Amount Due: {{ $payment->card_amount ? "$".$payment->card_amount : "$".$payment->amount }}</p>

                    @if ($payment->payment_method === 'Credit Card') 
                    <p style="margin-bottom: 8px;">Click Link To Pay With Card: <a href="{{ $payment->payment_link }}">Proceed with Purchase</a></p>

                    <p style="font-style: italic;">Avoid future processing fees switch your perferred payment method to Zelle.</p>

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
                    <p>Payment Amount: {{ $client->payment_option === 'Credit Card' ? "$".$payment->card_amount : "$".$payment->amount }}</p>
                    <p>Details: {{ $client->details }}</p> 

                </div>
            </div>
        </div>

        <div style="border-bottom: 2px solid #cbd5e1;">
            <h4>For Goolge Ads Services</h4>
            <div>
                <p>1. If you already haven't create your Google Ads account create your Google Ads. Make sure to complete your Advertiser Verification.</p>
                <p>2. Once your Google Ads has been completed go to admin in the left menu side panel and click on "Access and Security".</p>
                <p>3. Under the "Users" tab click on the BLUE circle icon on the top left of the table to add Evergreen By Design to your account.</p>
                <p>4. Use "evergreenbydesignwebsites@gmail.com" to invite me to your Google Ad Account. Make sure to set our privleges to "Admin" so we can interconnect your data streams!</p>
                <p>5. Send us information about the product or service you want to market so we can start creating your Ad Campaign and Marketing Assets.</p>
                <p>6. <span class="italic">(Optional)</span> We use sub-domains for Sales page creating (https://yourbusiness/evergreenbydesign.com). If you would like to use a custom domain for better branding you may purchase one for ~$12/yr. We will need access to your domain register (where you purchased the domain) so we can link the nameservers to our hosting.</p>
                <p>We recommend  <a href="https://www.luckyregister.com/">luckyregister.com</a> if you don't already have a domain register.</p>
            </div>
        </div>

        <div style="border-bottom: 2px solid #cbd5e1;">
            <h4>For Website Design Services</h4>
            <div>
                <p>1. Purchase a domain at a domain register of your choosing. Domains cost ~$12/yr per year depending on the domain register. We will need access to your domain register (where you purchased the domain) so we can link the nameservers to our hosting.</p>
                <p>We recommend  <a href="https://www.luckyregister.com/">luckyregister.com</a> if you don't already have a domain register.</p>
                <p>2. Email <a href="mailto:todd@evergreenbydesign.com">todd@evergreenbydesign.com</a> all the information that you want on your website. This includes images, and written content. Written content can be a sort description or Business/Mission statement. Also send login information to your domain register</p>
                <p>3. <span class="italic">(Optional)</span> Tell me 3 of your favorite colors (if you have a logo then colors from logo will be used), and any websites that are in your industry or examples of the type of website you want or like.</p>
            </div>
        </div>

        <div style="border-bottom: 2px solid #cbd5e1;">
            <h4>Payment Gateways for websites</h4>
            <div>
                <p>If you are using Ecommerce feature for your website (i.e you are making direct sales on your website) then you will need a payment gateway.</p>
                <p>Evergreen By Design recommends <a href="https://stripe.com">stripe.com</a> for all your payment gateway needs.</p>
                
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