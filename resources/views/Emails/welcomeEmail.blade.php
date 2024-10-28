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

                    <p>Amount: {{ "$".$payment->amount }}</p>

                    <p>{{ config('services.stripe.processing_fee') * 100 }}% Processing Fee:  {{ $payment->processing_fee ? "$".$payment->processing_fee : "$0.00 - free with " . $payment->payment_method }}</p>

                    <p style="font-weight: bold;">Total Payment Amount Due: {{ $payment->card_amount ? "$".$payment->card_amount : "$".$payment->amount}}{{ $payment->frequency === 'recurring' ? "/mo" : "" }}</p>

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
                    <p>Payment Amount: {{ $client->payment_option === 'Credit Card' ? "$".$payment->card_amount : "$".$payment->amount }}</p>
                    <p>Details: {{ $client->details }}</p> 

                </div>
            </div>
        </div>

        <div style="border-bottom: 2px solid #cbd5e1;">
            <h2>Services Instructions</h2>
            <h4>Goolge Ads Services</h4>
            <div>
                <p>1. Create a <a href="https://www.google.com/intl/en_us/business/">Google Business Profile Page</a>. (<span class="italic">This will help with Google Ads setup.</span>)</p>
                <p>2. Next, <a href="https://ads.google.com/">Create your Google Ads Account</a>. When you begin Google Ads setup just do the first 2 pages <span>(Business Info and Google Profile Link) and <strong>SKIP</strong> the campaign setup and the bottom of the next few pages. When prompted enter your billing information.</span> Make sure to complete your <strong>Advertiser Verification.</strong></p>
                <p>3. Once your Google Ads has been completed go to "Admin" menu option in the left side panel and click on "Access and Security".</p>
                <p>4. Under the "Users" tab click on the BLUE circle icon on the top left of the table to add Evergreen By Design to your account.</p>
                <p>5. Use "evergreenbydesignwebsites@gmail.com" to invite us to your Google Ad Account. Make sure to set our privleges to "Admin" so we can interconnect your data streams with Google Analytics and your Sales/Landing page!</p>
                <p>6. Email us: <a href="mailto:todd@evergreenbydesign.com">todd@evergreenbydesign.com</a>, information to about the product or service you want to market so we can start creating your Ad Campaign and Marketing Assets.</p>
                <p>7. Optional Domain Branding: We use sub-domains for Sales/Landing page creation (https://yourbusiness.evergreenbydesign.com). But, if you would like to use a custom domain <span class="italic">(recommended)</span> for better branding you may purchase one for ~$15/yr. We will need access to your domain register so we can link the nameservers to our hosting.</p>
                <p>We recommend  <a href="https://www.luckyregister.com/">luckyregister.com</a> if you don't already have a domain register.</p>
                <p>8. Ecommerce only: If you are selling products directly on your Sales/Landing page you will need a Payment Gateway. Please refer below to payment gateway services for additional setup.</p>
            </div>
        </div>

        <div style="border-bottom: 2px solid #cbd5e1;">
            <h4>Website Design Services</h4>
            <div>
                <p>1. Purchase a domain at the domain register of your choosing. Domains cost ~$15/yr per year depending on the domain register. We will need access to your domain register (where you purchased the domain) so we can link the nameservers to our hosting.</p>
                <p>We recommend  <a href="https://www.luckyregister.com/">luckyregister.com</a> if you don't already have a domain register.</p>
                <p>2. Email <a href="mailto:todd@evergreenbydesign.com">todd@evergreenbydesign.com</a> all the information that you want on your website. This includes images, and written content. Written content can be a sort description or business/mission statement so we can create content for your website. Also send login information to your domain register</p>
                <p>3. <span class="italic">(Optional)</span> Tell me 3 of your favorite colors (if you have a logo then colors from logo will be used), and any websites that are in your industry or examples of the type of website you want or like.</p>
                <p>4. (Recommended) Create a Google Business Profile Page</p>
                <p>5. (Ecommerce Website Only) You will need to setup a payment gateway service. Please refer below to payment gateway services for additional setup.</p>
            </div>
        </div>

        <div style="border-bottom: 2px solid #cbd5e1;">
            <h4>Payment Gateway Service (Stripe)</h4>
            <div>
                <p>If you are using Ecommerce features for your website and/or sales/landing page (i.e you are making direct sales through your website/landing page) then you will need a payment gateway.</p>
                <p>Evergreen By Design highly-recommends <a href="https://stripe.com">stripe.com</a> for all your payment gateway needs.</p>
                
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