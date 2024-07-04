<script setup>
    import MainLayout from '@/Layouts/MainLayout.vue';
    import BtnComponent from '@/Components/Button.vue'; 
    import MessageBannerComponent from '@/Components/messageBanner.vue';
    import { computed, ref } from 'vue';
    import { router, usePage } from '@inertiajs/vue3';
    import Loader from '@/Utilities/loader.js';
    import moment from 'moment';
    import currencyFormater from '@/Utilities/currencyFormater';
    import phoneNumberFormat from '@/Utilities/phoneNumberFormater';


    
    const client = usePage().props.client;
    const payments = usePage().props.payments;

// return all pending payments
    function paymentsPending() {
        

        const pendingPayments = [];

        payments.forEach(payment => {
            if(payment.status === 'pending') pendingPayments.push(payment);
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
   

    function pendingAmount() {

        let amount = 0.00;

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
    

    const hostingClassObj = computed( () => ({
        "bg-gray-300" : client.hosting === "Self Hosting",
        "bg-green-500 text-white" : client.hosting === "Active",
        "bg-yellow-500 text-white" : client.hosting === "Pending",
        
    }));

    const statusClassObj = computed( () => ({
        
        "bg-yellow-500" : client.status === "pending",
        "bg-green-500" : client.status === "active",
        "bg-red-500" : client.status === "cancelled"
    }) );

    

    function showWelcomeEmailBtn() {
       
        let show = false;


        payments.forEach( payment => {
            if(payment.initial_payment) {
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

    } 

   

    function createdAtDate(createdDate) {
       return moment(createdDate).format('MMMM Do YYYY');
    }


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

        
    }//

    function welcomeEmailsDatesSent() {
        return client.welcome_email_sent_count.slice().reverse();
    }//


    function initialPaymentStatus() {

        const payments = client.payments;

        let paymentStatus = {
            'status' : null,
            'updated' : null
        };

        payments.forEach( payment => {

            if(payment.initial_payment) {

              

              paymentStatus.status = payment.status;
              paymentStatus.updated = moment(payment.updated_at).format('MMMM Do YYYY');

            }

        } );


        return paymentStatus;

    }//

    function viewWelcomeEmail() {

        const initialPayment = payments.filter( payment => {
                return payment.initial_payment === 1;
            } );

        router.get(route('view.email'), {
            'view': 'welcome_email',
            'client': client,
            'payment': initialPayment[0]
        });


    }//

</script>


<template>

    <MainLayout>

        <div class="border-b-2 border-gray py-2 mb-4 flex justify-between items-center px-4">
            <h1 class="text-4xl">{{ client.name }}</h1>
            <BtnComponent @click.prevent="exitShowClient" link="#" type="safe">
               Back To Clients
            </BtnComponent>
        </div>

    <!-- Success  -->
        <MessageBannerComponent :show="$page.props.flash.success" type="safe">
            {{ $page.props.flash.success }}
        </MessageBannerComponent>
       
    <!-- Error  -->
        <MessageBannerComponent :show="$page.props.flash.error" type="danger">
            {{ $page.props.flash.error }}
        </MessageBannerComponent>

    


        <div class="border border-gray-200 px-4 py-10 rounded-sm shadow-lg relative">

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

                    
                    <button v-if="showWelcomeEmailBtn()" @click.prevent="sendEmail(client.id)" :class="[client.welcome_email_sent ? 'btn-danger' : 'btn-warning']" class="btn btn-warning">
                        {{ client.welcome_email_sent ? 'Resend' : 'Send' }} Welcome Email
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
            <div class="mb-10 flex justify-end">
                <a @click.prevent="viewWelcomeEmail" href="#">View Welcome Email</a>
            </div>

            <div id="client-info-container" class="flex">

                <div id="client-info" class="w-1/2">
                    <h2 class="mb-2">Client Information</h2>
                    <div class="border-b-2 border-gray-300 w-8/12">
                        <p><span>Name:</span> {{ client.name }}</p>
                        <p><span>Email:</span> <a :href="'mailto:'+client.email">{{ client.email }}</a></p>
                        <p><span>Phone:</span> {{ phoneNumberFormat(client.phone) }}</p>

                        <p><span>Domain(s):</span></p>
                        <p v-for="domain in client.domains" :key="domain">
                            <a target="_blank" :href="`https://${domain}`">{{ domain }}</a>
                        </p>

                        <p><span>Location:</span> {{ client.location }}</p>
                        <div class="mt-2">
                        <p>Created: {{  client.createdAt }}</p>
                        <p>Updated: {{  client.updatedAt  }}</p>
                    </div>
                    </div>
                     

                        <div v-if="initialPaymentStatus().status === 'pending'">
                            <h3 class="my-4">Welcome Email Sent Count: {{ welcomeEmailsDatesSent().length }}</h3>
                            <p v-if="welcomeEmailsDatesSent().length !== 0" v-for="(dateSent, i) in welcomeEmailsDatesSent()" :key="i">Date Sent: {{ dateSent }}</p>
                            <p v-else class="bg-yellow-500 text-white inline-block text-center py-2 px-4 mt-6">Welcome Email Not Sent Yet</p>
                        </div>
                        <div v-else-if="initialPaymentStatus().status === 'paid'">
                            <p class="bg-green-500 text-white inline-block text-center py-2 px-4 mt-6">Initial Payment Paid</p>
                            <p>Date Paid: {{ initialPaymentStatus().updated }}</p>
                        </div>
                        <div v-else-if="initialPaymentStatus() === 'Void'">
                            <p class="bg-gray-700 text-white inline-block text-center py-2 px-4 mt-6">Initial Payment Voided</p>
                            <p>Date Voided: {{ initialPaymentStatus().updated }}</p>
                        </div>
                 

                    
                    

                </div>
                <!-- #/client-info  -->

                
                
                <div id="client-details" class="w-1/2">

                    <div :class="hostingClassObj" class="inline-block py-2 px-4 rounded my-4">
                        <h3>Hosting: {{ client.hosting }}</h3>
                    </div>
                    <div class="mt-4 mb-12">
                        <h2>Purchased Pro. Emails</h2>
                        <p v-if="client.pro_emails.length" v-for="(pro_email, key) in client.pro_emails" :key="key"> {{ pro_email }}</p>
                        <p v-else><span class="bg-gray-700 text-white py-2 px-4">No Professional Emails Ordered</span></p>
                    </div>


                    <div id="payment-info" class="border-b-2 border-gray-200 mb-4">
                        <h2 class="mb-2">Payment Information</h2>
                        <p>Preferred Method: {{ client.payment_option }}</p>
                        <p>Deposit: {{ currencyFormater(client.deposit) }}</p>
                        <div v-if="client.payment_option === 'Credit Card'">
                            <p>Processing Fee: {{ currencyFormater(client.deposit * .034) }}</p>
                        </div>
                        <p>Initial Quote: {{ currencyFormater(client.quote) }}</p>
                    </div>

                    <div>
                        <h2>Website Details:</h2>
                        <div class="border p-4 min-h-52">
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
                    <h3 class="mb-4">Pending</h3>
                    <table v-if="paymentsPending()" id="pending-payments" class="table-fixed border-collapse border w-full">
                        <thead class="bg-green-300">
                            <tr>
                                <th>Name</th>
                                <th>Date Issued</th>
                                <th>Invoice Id</th>
                                <th>Amount</th>
                                <th>For</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr v-for="payment in paymentsPending()" @click.prevent="viewPayment(payment.id)"  class="text-center">
                                <td>{{ client.name }}</td>
                                <td>{{ createdAtDate(payment.created_at) }}</td>
                                <td>{{ payment.invoice_id }}</td>
                                <td>{{ payment.payment_method === 'Credit Card' ? currencyFormater(payment.card_amount) :  currencyFormater(payment.amount) }}</td>
                                <td :class="{'text-white bg-yellow-500': payment.initial_payment}">{{ payment.for }}</td>
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
                <div class="mt-8">
                    <h3 class="mb-4">Paid</h3>
                    <table v-if="paymentsPaid()" id="-payments" class="table-fixed border-collapse border w-full">
                        <thead class="bg-green-300">
                            <tr>
                                <th>Name</th>
                                <th>Date Issued</th>
                                <th>Invoice Id</th>
                                <th>Amount</th>
                                <th>For</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr v-for="payment in paymentsPaid()" @click.prevent="viewPayment(payment.id)"  class="text-center">
                                <td>{{ client.name }}</td>
                                <td>{{ createdAtDate(payment.created_at) }}</td>
                                <td>{{ payment.invoice_id }}</td>
                                <td>{{ payment.payment_method === 'Credit Card' ? currencyFormater(payment.card_amount) :  currencyFormater(payment.amount) }}</td>
                                <td :class="{'text-white bg-yellow-500': payment.initial_payment}">{{ payment.for }}</td>
                                <td><span :class="statusBg(payment)" class="rounded-lg capitalize py-1 w-1/2 block mx-auto text-white">{{ payment.status }}</span></td>
                            </tr>

                        </tbody>
                    </table>
                    <div v-else class="bg-gray-500 text-white text-center py-4">
                        No Paid Payments
                    </div>
                </div>
                <!-- #/Paid   -->


                
                <div class="mt-8">
                    <h3 class="mb-4">Void</h3>
                    <table v-if="paymentsVoid()" id="-payments" class="table-fixed border-collapse border w-full">
                        <thead class="bg-green-300">
                            <tr>
                                <th>Name</th>
                                <th>Date Issued</th>
                                <th>Invoice Id</th>
                                <th>Amount</th>
                                <th>For</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr v-for="payment in paymentsVoid()" @click.prevent="viewPayment(payment.id)"  class="text-center">
                                <td>{{ client.name }}</td>
                                <td>{{ createdAtDate(payment.created_at) }}</td>
                                <td>{{ payment.invoice_id }}</td>
                                <td>{{ payment.payment_method === 'Credit Card' ? currencyFormater(payment.card_amount) :  currencyFormater(payment.amount) }}</td>
                                <td :class="{'text-white bg-yellow-500': payment.initial_payment}">{{ payment.for }}</td>
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