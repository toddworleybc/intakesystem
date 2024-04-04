<script setup>
    import MainLayout from '@/Layouts/MainLayout.vue';
    import BtnComponent from '@/Components/Button.vue'; 
    import MessageBannerComponent from '@/Components/messageBanner.vue';
    import { computed, ref } from 'vue';
    import { router, usePage } from '@inertiajs/vue3';
    import Loader from '@/Utilities/loader.js';
    import moment from 'moment';


    
    const client = usePage().props.client;
    const payments = usePage().props.payments;
   

    function pendingAmount() {

        let amount = 0.00;

        payments.forEach(payment => {
            
            if(payment.status === 'pending') {

                if(payment.payment_method === 'Credit Card')  amount += parseFloat(payment.card_amount);

                amount = amount + parseFloat(payment.amount);

            }

        });

       return amount.toFixed(2);

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
       return client.status === 'pending' && client.create_quote === 1 ? true : false; 
    }//====


    function sendEmail(clientId) {

       const confirmMessage =  client.welcome_email_sent ? 'Resend welcome email to?' : 'Proceed with sending a Welcome Email to'

        const confirmSendEmail = confirm(`${confirmMessage} ${client.name}`);

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
        
        router.get(route('payments.show', id));
    }//==

    function createPayment() {
        router.get(route('payments.create'), {
            'clientId' : client.id
        });
    }//===

    function showEmailSentMessage() {

       const urlParams = new URLSearchParams(window.location.search);
       
       return urlParams.has('emailSent') ? true : false;

    }//===

    function createdAtDate(createdDate) {
       return moment(createdDate).format('MMMM Do YYYY');
    }

    

</script>


<template>

    <MainLayout>

        <div class="text-white py-2 bg-blue-400 mb-4 flex justify-between items-center px-4">
            <h1 class="text-2xl text-center text-white bg-blue-400">Client {{ client.name }}</h1>
            <BtnComponent @click.prevent="exitShowClient" link="#" type="safe">
                Clients
            </BtnComponent>
        </div>


        <MessageBannerComponent :show="client.created" type="safe">
            Client Successfully Created!
        </MessageBannerComponent>
        <!-- @/client_created  -->

        <MessageBannerComponent :show="client.updated" type="safe">
            Client Updated Successfully!
        </MessageBannerComponent>
        <!-- @/client_created  -->

        <MessageBannerComponent :show="showEmailSentMessage()" type="safe">
            Welcome Email Sent
        </MessageBannerComponent>


        <div class="border border-gray-200 px-4 py-10 rounded-sm shadow-lg relative">

            <div v-if="pendingAmount() > 0"class="text-lg bg-gray-500 text-white inline-block py-2 px-4 rounded">
                <p class="mb-0">Amount Pending: ${{ pendingAmount() }}</p>
            </div>
            
            <div class="border-b-2 border-gray-500 pb-2 mb-10 flex justify-between items-end">

                <div>
                    <div class="flex items-center space-x-4">
                        <span class="bg-green-700 text-sm text-white py-1 px-2 rounded">Id: {{ client.id }}</span>
                        <span :class="statusClassObj" class="text-sm text-white py-1 px-2 rounded">
                            Status: <span class="capitalize">{{ client.status }}</span>
                        </span>
                    </div>
                </div>
                
                

                <div class="flex space-x-2 items-center">

                    
                    <button v-if="showWelcomeEmailBtn()" @click.prevent="sendEmail(client.id)" :class="[client.welcome_email_sent ? 'btn-danger' : 'btn-warning']" class="btn btn-warning">
                        {{ client.welcome_email_sent ? 'Resend' : 'Send' }} Welcome Email
                    </button>

                    <button v-else @click.prevent="createPayment" class="btn btn-info">
                        Create Payment
                    </button>

                    <BtnComponent :link="route('clients.edit', client)">
                        Edit Client
                    </BtnComponent>
                </div>
                
            </div>
            

            <div id="client-info-container" class="flex">

                <div id="client-info" class="w-1/2">
                    <h2 class="mb-2">Client Information</h2>
                    <div class="border-b-2 border-gray-300 w-8/12">
                        <p><span>Name:</span> {{ client.name }}</p>
                        <p><span>Email:</span> {{ client.email }}</p>
                        <p><span>Phone:</span> {{ client.phone }}</p>

                        <p><span>Domain(s):</span></p>
                        <p v-for="domain in client.domains" :key="domain">
                            <a :href="`https://${domain}`">{{ domain }}</a>
                        </p>

                        <p><span>Location:</span> {{ client.location }}</p>
                        <div class="mt-2">
                        <p>Created: {{  client.createdAt }}</p>
                        <p>Updated: {{  client.updatedAt  }}</p>
                    </div>
                    </div>
                    <div :class="hostingClassObj" class="inline-block py-2 px-4 rounded my-4">
                        <h3>Hosting: {{ client.hosting }}</h3>
                    </div>

                    
                </div>
                <!-- #/client-info  -->

                
                
                <div id="client-details" class="w-1/2">

                    <div id="payment-info" class="border-b-2 border-gray-200 mb-4">
                        <h2 class="mb-2">Payment Information</h2>
                        <p>Preferred Method: {{ client.payment_option }}</p>
                        <p>Deposit: {{ client.deposit }}</p>
                        <p>Initial Quote: {{ client.quote }}</p>
                    </div>

                    <div>
                        <h2>Client Details:</h2>
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
                    <table id="pending-payments" class="table-fixed border-collapse border w-full">
                        <thead class="bg-green-300">
                            <tr>
                                <th>Date Issued</th>
                                <th>Invoice Id</th>
                                <th>For</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr v-for="payment in payments" @click.prevent="viewPayment(payment.id)"  class="text-center">
                               
                                <td v-if="payment.status === 'pending'">{{ createdAtDate(payment.created_at) }}</td>
                                <td v-if="payment.status === 'pending'">{{ payment.invoice_id }}</td>
                                <td v-if="payment.status === 'pending'">{{ payment.for }}</td>
                                <td v-if="payment.status === 'pending'"><span :class="statusBg(payment)" class="rounded-lg capitalize py-1 w-1/2 block mx-auto text-white">{{ payment.status }}</span></td>
                               
                                
                            </tr>

                        </tbody>
                    </table>
                </div>
                
                <span class="mt-8 mb-4 border-b-4 border-gray-500 block"></span>

                <div>
                    <h3 class="mb-4">Paid and Void</h3>
                    <table id="paid-void-payments" class="table-fixed border-collapse border w-full">
                        <thead class="bg-green-300">
                            <tr>
                                <th>Date Issued</th>
                                <th>Invoice Id</th>
                                <th>For</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr v-for="payment in payments" @click.prevent="viewPayment(payment.id)"  class="text-center">
                               
                                <td v-if="payment.status !== 'pending'">{{ createdAtDate(payment.created_at) }}</td>
                                <td v-if="payment.status !== 'pending'">{{ payment.invoice_id }}</td>
                                <td v-if="payment.status !== 'pending'">{{ payment.for }}</td>
                                <td v-if="payment.status !== 'pending'"><span :class="statusBg(payment)" class="rounded-lg capitalize py-1 w-1/2 block mx-auto text-white">{{ payment.status }}</span></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <!-- #/Paid and Void  -->
                

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