<script setup>
    import MainLayout from '@/Layouts/MainLayout.vue';
    import BtnComponent from '@/Components/Button.vue'; 
    import MessageBannerComponent from '@/Components/messageBanner.vue';
    import messageBannerControl from '@/Utilities/messageBannerControl';
    import { computed, onMounted, onBeforeMount } from 'vue';
    import { router, usePage } from '@inertiajs/vue3';
    import Loader from '@/Utilities/loader.js';
    import currencyFormater from '@/Utilities/currencyFormater';
    import phoneNumberFormat from '@/Utilities/phoneNumberFormater';
    



    
    const client = usePage().props.client;
    const payments = usePage().props.payments;
    const message = usePage().props.flash.message;


// return all pending payments
    function paymentsPending() {
    
        const pendingPayments = [];

        payments.forEach(payment => {
            if(payment.status === 'pending' && payment.frequency !== 'recurring') pendingPayments.push(payment);
        });

        return pendingPayments.length === 0 ? false : pendingPayments; 

    }//

// return all paid payments
    function paymentsPaid() {
        const pendingPayments = [];

        payments.forEach(payment => {
            if(payment.status === 'paid') pendingPayments.push(payment);
        });

        return pendingPayments.length === 0 ? false : pendingPayments; 
    }//

// return all void payments
    function paymentsVoid() {

        const pendingPayments = [];

        payments.forEach(payment => {
            if(payment.status === 'void') pendingPayments.push(payment);
        });

        return pendingPayments.length === 0 ? false : pendingPayments; 

    }//

    function recurringPayments() {
        const subscriptions =  payments.filter( payment => {
          return  payment.frequency === 'recurring' && payment.status !== 'void';
        } );


        return subscriptions.length === 0 ?
            [] :
            subscriptions;

    }

    function subscriptionsAmount() {

        let amount = 0;

        payments.forEach(payment => {

            
            if(payment.frequency === 'recurring') {

                if(payment.payment_method === 'Credit Card') {
                    if(payment.status === 'paid') amount = amount + parseFloat(payment.card_amount);
                } else {
                    amount = amount + parseFloat(payment.amount);
                }

            }
        
        });



        return amount;

    }//===
   

    function pendingAmount() {

        let amount = 0;

        payments.forEach(payment => {

            
            if(payment.status === 'pending') {

                if(payment.payment_method === 'Credit Card') {
                    amount = amount + parseFloat(payment.card_amount);
                } else {
                    amount = amount + parseFloat(payment.amount);
                }

            }
           
        });

        

       return amount;

    }//===
    

    function statusBg(payment) {

        let statusColor = null;

        if(payment.status === "pending") statusColor = "bg-yellow-500";

        if(payment.status === "paid") statusColor = "bg-green-500";

        if(payment.status === "void") statusColor = "bg-gray-500";

        return statusColor;

    }
    

    const adCampaignStatusClassObj = computed( () => ({
        "bg-gray-300" : client.ad_campaign_status === "Paused",
        "bg-green-500 text-white" : client.ad_campaign_status === "Active",
        "bg-yellow-500 text-white" : client.ad_campaign_status === "Inactive",
    }));

    const statusClassObj = computed( () => ({
        
        "bg-yellow-500" : client.status === "pending",
        "bg-green-500" : client.status === "active",
        "bg-red-500" : client.status === "cancelled"
    }) );

    

    function showWelcomeEmailBtn() {
       
        let show = false;


        payments.forEach( payment => {
            if(payment.payment_welcome_email) {
                if(payment.status === 'pending') show = true;
            }
        });

        return show;

    }//====


    function sendEmail(clientId) {

       const confirmMessage =  client.welcome_email_sent ? 'Resend welcome email to' : 'Proceed with sending a Welcome Email to'

        const confirmSendEmail = confirm(`${confirmMessage} ${client.name}?`);

        if(!confirmSendEmail) return;

        router.post(route('welcome.email.send'), {
           'id' : clientId,
        },
        {
           onStart : () => {
                Loader.action = "Sending Email";
                Loader.isLoading = true;
           },
           onFinish : () => {
                Loader.isLoading = false;
           },
           preserveState: false
        }
        );
        
    }

    function exitShowClient() {

        router.get(route('clients.index'));

    }//==

    function viewPayment(id) {

        if(clientCancelled()) {
            alert('Payments not editable if client is cancelled.');
            return;
        }

        router.get(route('payments.show', id));

    }//==

    function createPayment() {
        router.get(route('payments.create'), {
            'clientId' : client.id
        });
    }//===


    function clientCancelled() {

        return client.status === 'cancelled' ? true : false;

    }//==




    function restoreClient() {

        const restoreClient = confirm(`Are you sure you want to restore ${client.name}?`);


        if(!restoreClient) return;

        const url = route('clients.update', client.id);

        router.patch(url, client, {
            onStart : function() {
                Loader.action = "Restoring Client";
                Loader.isLoading = true;
           },
            onFinish : function() {
                Loader.isLoading = false;
           },
           preserveState: false
        });

        
    }//==

    function welcomeEmailsDatesSent() {
        return client.welcome_email_sent_count.slice().reverse();
    }//==



    function viewWelcomeEmail() {


        let welcomeEmailPayment = null;

      payments.forEach( payment => {
            if(payment.payment_welcome_email) welcomeEmailPayment = payment;
        } );
        
       

        router.post(route('view.email'), {
            'view': 'welcome_email',
            'client': client,
            'payment': welcomeEmailPayment
        });

    }//

    function welcomeEmailPaymentExists() {

        let emailExists = false;

        payments.forEach( payment => {
            
            if(payment.payment_welcome_email)  emailExists = true;
            
        } );

        return emailExists;
    }

// FIX FOR TIME CHANGE FROM DB ==

    onBeforeMount( () => {
        const created_at = usePage().props.created_at;
        const updated_at = usePage().props.updated_at;
        const payments_created_at = usePage().props.payments_created_at;
        client['created_at'] =  created_at;
        client['updated_at'] = updated_at;
        
        
        payments.forEach( ( payment, i ) => {

            if(payment.id === payments_created_at[i].id) {
                payment.created_at =payments_created_at[i].created_date;
            } 

        } );

    } );
// ==/

    onMounted( () => {
        if(message) messageBannerControl.display(message);
       
    } );

</script>


<template>

    <MainLayout>

        <div class="border-b-2 border-gray py-2 mb-4 flex justify-between items-center px-4">
            <h1 class="text-4xl">{{ client.name }}</h1>
            <BtnComponent @click.prevent="exitShowClient" link="#" type="safe">
               Back To Clients
            </BtnComponent>
        </div>

        <MessageBannerComponent :show="messageBannerControl" />
    


        <div class="border border-gray-200 px-4 py-10 rounded-sm shadow-lg relative">
            <div class="mb-4">
                <p>Total Monthly Subscriptions: {{ currencyFormater(subscriptionsAmount()) }}</p>
                <p>Est. Monthly Payout: {{ currencyFormater( subscriptionsAmount() * 0.965) }}</p>
            </div>
            <div class="text-lg bg-gray-500 text-white inline-block py-2 px-4 rounded">
                <p class="mb-0">Amount Pending: {{ currencyFormater( pendingAmount() ) }}</p>
               
            </div>
            
            <div class="border-b-2 border-gray-500 pb-2 mb-2 flex justify-between items-end">

                <div>
                    <div class="flex items-center space-x-4">
                        <span :class="statusClassObj" class=" text-white py-1 px-2 rounded">
                            Status: <span class="capitalize">{{ client.status }}</span>
                        </span>
                    </div>
                </div>
                
                

                <div class="flex space-x-2 items-center">

                    
                    <button v-if="showWelcomeEmailBtn()" @click.prevent="sendEmail(client.id)" :class="[ welcomeEmailsDatesSent().length > 0 ? 'btn-danger' : 'btn-warning']" class="btn btn-warning">
                        {{ welcomeEmailsDatesSent().length > 0 ? 'Resend' : 'Send' }} Welcome Email
                    </button>

                    <button v-else-if="client.status !== 'cancelled'" @click.prevent="createPayment" class="btn btn-info">
                        Create Payment
                    </button>

                    <div v-else class="bg-yellow-500 text-white text-center px-4 py-2">
                        Client Cancelled
                    </div>

                    <BtnComponent v-if="!clientCancelled()" :link="route('clients.edit', client)">
                        Edit Client
                    </BtnComponent>
                    <button v-else class="btn btn-danger" @click.prevent="restoreClient">
                        Restore Client
                    </button>
                </div>
                
            </div>
            <div v-if="welcomeEmailPaymentExists()" class="mb-10 flex justify-end">
                <a @click.prevent="viewWelcomeEmail" href="#">View Welcome Email</a>
            </div>
            <div v-else class="mb-10 flex justify-end text-white relative"><span class="absolute w-full h-full bg-white z-20"></span>View Welcome Email</div>
            

            <div id="client-info-container" class="flex">
                
                <div id="client-info" class="w-1/2">
                    <h2 class="mb-2">Client Information</h2>
                    <div class="border-b-2 border-gray-300 w-8/12">
                        <p><span>Name:</span> {{ client.name }}</p>
                        <p><span>Email:</span> <a :href="'mailto:'+client.email">{{ client.email }}</a></p>
                        <p><span>Phone:</span> {{ phoneNumberFormat(client.phone) }}</p>
                        <p>Preferred Method: {{ client.payment_option }}</p>
                        <p><span>Domain(s):</span></p>
                        <p v-for="domain in client.domains" :key="domain">
                            <a target="_blank" :href="`https://${domain}`">{{ domain }}</a>
                        </p>

                        <p><span>Location:</span> {{ client.location }}</p>
                        
                    </div>

                    <div class="mt-2 border-b border-gray-400 inline-block">
                        <h3 class="my-4">Created and Updated</h3>
                        <p>Created: {{  client.created_at }}</p>
                        <p>Updated: {{  client.updated_at  }}</p>
                    </div>
                     
                        <div>
                            <h3 class="my-4">Welcome Email Sent Count: {{ welcomeEmailsDatesSent().length }}</h3>
                            <p v-if="welcomeEmailsDatesSent().length !== 0" v-for="(dateSent, i) in welcomeEmailsDatesSent()" :key="i">Date Sent: {{ dateSent }}</p>
                            <p v-else class="bg-yellow-500 text-white inline-block text-center py-2 px-4 mt-6">Welcome Email Not Sent Yet</p>
                        </div>

                    
                </div>
                <!-- #/client-info  -->

                
                
                <div id="client-details" class="w-1/2 flex flex-col justify-between">
                    <div>
                        <div :class="adCampaignStatusClassObj" class="inline-block py-2 px-4 rounded">
                            <h3>Ad Campaign: {{ client.ad_campaign_status }}</h3>
                        </div>
                    </div>

                    <div class="mt-8 mb-12">
                        <h2>Purchased Pro. Emails</h2>
                        <p v-if="client.pro_emails.length" v-for="(pro_email, key) in client.pro_emails" :key="key"> {{ pro_email }}</p>
                        <p v-else><span class="bg-gray-700 text-white py-2 px-4">No Professional Emails Ordered</span></p>
                    </div>
                    
                    <div>
                        <h2>Website Details:</h2>
                        <div class="border p-4 min-h-72">
                            <p>{{ client.details }}</p>
                        </div>
                    </div>
                    
                
                </div>

            </div>
            <!-- #/client-info-container  -->
            

        </div>
        <!-- ./form-container  -->

        <div id="payments-table" class="mt-8">
            
            <h2 class="block border-none">Client Payments</h2>
    
            <section v-if="payments.length !== 0">

                <div>
                    <h3 class="mb-4">Subscriptions</h3>
                    <table v-if="recurringPayments().length !== 0" id="recurring-payments" class="table-fixed border-collapse border w-full">
                        <thead class="bg-green-300">
                            <tr>
                                <th>Date Issued</th>
                                <th>Invoice Id</th>
                                <th>Amount</th>
                                <th>For</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr v-for="payment in recurringPayments().reverse()" @click.prevent="viewPayment(payment.id)"  class="text-center">
                                <td>{{payment.created_at }}</td>
                                <td>{{ payment.invoice_id }}</td>
                                <td>{{ payment.payment_method === 'Credit Card' ? currencyFormater(payment.card_amount) :  currencyFormater(payment.amount) }}</td>
                                <td :class="{'bg-yellow-200' : payment.payment_welcome_email}">{{ payment.for }}</td>
                                <td><span :class="statusBg(payment)" class="rounded-lg capitalize py-1 w-1/2 block mx-auto text-white">{{ payment.status }}</span></td>
                               
                                
                            </tr>

                        </tbody>
                    </table>
                    <div v-else class="bg-yellow-500 text-white text-center py-4">
                        No Active Subscriptions
                    </div>
                </div>
            <!-- subscriptions payments  -->

            <span class="mt-8 mb-4 border-b-4 border-gray-500 block"></span>

                <div>
                    <h3 class="mb-4">Pending</h3>
                    <table v-if="paymentsPending()" id="pending-payments" class="table-fixed border-collapse border w-full">
                        <thead class="bg-green-300">
                            <tr>
                                <th>Date Issued</th>
                                <th>Invoice Id</th>
                                <th>Amount</th>
                                <th>For</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr v-for="payment in paymentsPending().reverse()" @click.prevent="viewPayment(payment.id)"  class="text-center">
                                <td>{{payment.created_at }}</td>
                                <td>{{ payment.invoice_id }}</td>
                                <td>{{ payment.payment_method === 'Credit Card' ? currencyFormater(payment.card_amount) :  currencyFormater(payment.amount) }}</td>
                                <td :class="{'bg-yellow-200' : payment.payment_welcome_email}">{{ payment.for }}</td>
                                <td><span :class="statusBg(payment)" class="rounded-lg capitalize py-1 w-1/2 block mx-auto text-white">{{ payment.status }}</span></td>
                               
                                
                            </tr>

                        </tbody>
                    </table>
                    <div v-else class="bg-yellow-500 text-white text-center py-4">
                        No Pending Payments
                    </div>
                </div>
            <!-- pending payments  -->

                <span class="mt-8 mb-4 border-b-4 border-gray-500 block"></span>

                <h2 class="text-center block mt-8">Payment Archives</h2>
                <div class="mt-8 pb-4">
                    <h3 class="mb-4">Paid</h3>
                    <table v-if="paymentsPaid()" id="-payments" class="table-fixed border-collapse border w-full">
                        <thead class="bg-green-300">
                            <tr>
                               
                                <th>Date Issued</th>
                                <th>Invoice Id</th>
                                <th>Amount</th>
                                <th>For</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr v-for="payment in paymentsPaid().reverse()" @click.prevent="viewPayment(payment.id)"  class="text-center">
                               
                                <td>{{ payment.created_at }}</td>
                                <td>{{ payment.invoice_id }}</td>
                                <td>{{ payment.payment_method === 'Credit Card' ? currencyFormater(payment.card_amount) :  currencyFormater(payment.amount) }}</td>
                                <td :class="{'bg-yellow-200' : payment.payment_welcome_email}">{{ payment.for }}</td>
                                <td><span :class="statusBg(payment)" class="rounded-lg capitalize py-1 w-1/2 block mx-auto text-white">{{ payment.status }}</span></td>
                            </tr>

                        </tbody>
                    </table>
                    <div v-else class="bg-gray-500 text-white text-center py-4">
                        No Paid Payments
                    </div>
                </div>
                <!-- #/Paid   -->


                
                <div class="mt-8 border-t border-gray-400 py-8">
                    <h3 class="mb-4">Void</h3>
                    <table v-if="paymentsVoid()" id="payments" class="table-fixed border-collapse border w-full">
                        <thead class="bg-green-300">
                            <tr>
                                
                                <th>Date Issued</th>
                                <th>Invoice Id</th>
                                <th>Amount</th>
                                <th>For</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr v-for="payment in paymentsVoid().reverse()" @click.prevent="viewPayment(payment.id)"  class="text-center">
                                
                                <td>{{ payment.created_at }}</td>
                                <td>{{ payment.invoice_id }}</td>
                                <td>{{ payment.payment_method === 'Credit Card' ? currencyFormater(payment.card_amount) :  currencyFormater(payment.amount) }}</td>
                                <td :class="{'bg-yellow-200' : payment.payment_welcome_email}">{{ payment.for }}</td>
                                <td><span :class="statusBg(payment)" class="rounded-lg capitalize py-1 w-1/2 block mx-auto text-white">{{ payment.status }}</span></td>
                            </tr>

                        </tbody>
                    </table>
                    <div v-else class="bg-gray-500 text-white text-center py-4">
                        No Void Payments
                    </div>
                </div>
                <!-- #/Paid   -->
                

            </section>

        <section v-else class="bg-yellow-500 text-white text-center py-4">
            No Payments Created
        </section>

        </div>
        <!-- #/ payments-table -->

    </MainLayout>

</template>

<style>
 h2 {
    @apply border-b border-gray-400 mb-4 inline-block pb-2;
 }

</style>