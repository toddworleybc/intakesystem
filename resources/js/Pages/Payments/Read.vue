<script setup>
    import MainLayout from '@/Layouts/MainLayout.vue';
    import { usePage, router } from '@inertiajs/vue3';
    import { onBeforeMount } from 'vue';
    import currencyFormater from '@/Utilities/currencyFormater';

    const clients = usePage().props.clients;
    const payments = usePage().props.payments;
    const processing_fee = usePage().props.processing_fee;

 
   


    
    function recurringPayments() {
        const subscriptions =  payments.filter( payment => {
          return  payment.frequency === 'recurring' && payment.status !== 'void';
        } );


        return subscriptions.length === 0 ?
            [] :
            subscriptions;

    }
    
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
    

    function statusBgClient(client) {

        let statusColor = null;

        if(client.status === "pending") statusColor = "bg-yellow-500";

        if(client.status === "active") statusColor = "bg-green-500";

        if(client.status === "cancelled") statusColor = "bg-red-500";

        return statusColor;


    }
    
    function statusBgPayments(payment) {

        let statusColor = null;

        if(payment.status === "pending") statusColor = "bg-yellow-500";

        if(payment.status === "paid") statusColor = "bg-green-500";

        if(payment.status === "void") statusColor = "bg-gray-500";

        return statusColor;

    }

    function editUser(id) {
        router.visit(route('clients.show', id));
    }

    function clientNamePaymentsShow(payment) {

        let clientName = null;

        clients.forEach( client => {

            if(payment.clients_id === client.id) {

                clientName = client.name;

            } 

        } );

        return clientName;

    }//

    function viewPayment(id) {
        
        router.get(route('payments.show', id));
    }//==

    function createPayment() {
        router.get(route('payments.create'), {
            'clientId' : client.id
        });
    }//===

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

// FIX FOR TIME CHANGE FROM DB ==

    onBeforeMount( () => {
        const created_at = usePage().props.created_at;
        const updated_at = usePage().props.updated_at;
        const payments_created_at = usePage().props.payments_created_at;
        payments['created_at'] =  created_at;
        payments['updated_at'] = updated_at;
        
        
        payments.forEach( ( payment, i ) => {

            if(payment.id === payments_created_at[i].id) {
                payment.created_at = payments_created_at[i].created_date;
            } 

        } );

    } );
// ==/


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


    function estSubscriptionsPayout() {

        let amount = 0;

        payments.forEach(payment => {

            
            if(payment.frequency === 'recurring') {

                if(payment.payment_method === 'Credit Card') {
                    if(payment.status === 'paid') amount = (amount + parseFloat(payment.card_amount)) * (1 - processing_fee);
                } else {
                    amount = amount + parseFloat(payment.amount);
                }

            }

        });


        return amount;

    }//===

</script>


<template>

    <MainLayout>


        <div class="text-white py-2 bg-blue-600 mb-4 text-center px-4">
            <h1 class="text-2xl">Payments</h1>
        </div>

       
       

        <section class="flex justify-between items-center my-6">
            <div class="mb-4">
                <p>Monthly Subscriptions: {{ currencyFormater(subscriptionsAmount()) }}</p>
                <p>Est. Monthly Subscription Payout: {{ currencyFormater( estSubscriptionsPayout() ) }}</p>
            </div>
            <div>
                <p class="text-lg font-semibold">Total Pending Amount: {{ currencyFormater(pendingAmount()) }}</p>
            </div>
            
        </section>

        <div id="payments-table" class="mt-8">
            
          
    
            <section v-if="payments.length !== 0">

                
                <div>
                    <h3 class="mb-4">Subscriptions</h3>
                    <table v-if="recurringPayments().length !== 0" id="recurring-payments" class="table-fixed border-collapse border w-full">
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

                            <tr v-for="payment in recurringPayments().reverse()" @click.prevent="viewPayment(payment.id)"  class="text-center">
                                <td>{{ clientNamePaymentsShow(payment) }}</td>
                                <td>{{payment.created_at }}</td>
                                <td>{{ payment.invoice_id }}</td>
                                <td>{{ payment.payment_method === 'Credit Card' ? currencyFormater(payment.card_amount) :  currencyFormater(payment.amount) }}</td>
                                <td :class="{'bg-yellow-200' : payment.payment_welcome_email}">{{ payment.for }}</td>
                                <td><span :class="statusBgPayments(payment)" class="rounded-lg capitalize py-1 w-1/2 block mx-auto text-white">{{ payment.status }}</span></td>
                               
                                
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
                                <th>Name</th>
                                <th>Date Issued</th>
                                <th>Invoice Id</th>
                                <th>Amount</th>
                                <th>For</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr v-for="payment in paymentsPending().reverse()" @click.prevent="viewPayment(payment.id)"  class="text-center">
                                <td>{{ clientNamePaymentsShow(payment) }}</td>
                                <td>{{ payment.created_at }}</td>
                                <td>{{ payment.invoice_id }}</td>
                                <td>{{ payment.payment_method === 'Credit Card' ? currencyFormater(payment.card_amount) :  currencyFormater(payment.amount) }}</td>
                                <td :class="{'text-white bg-yellow-500': payment.initial_payment}">{{ payment.for }}</td>
                                <td><span :class="statusBgPayments(payment)" class="rounded-lg capitalize py-1 w-1/2 block mx-auto text-white">{{ payment.status }}</span></td>
                               
                                
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

                            <tr v-for="payment in paymentsPaid().reverse()" @click.prevent="viewPayment(payment.id)"  class="text-center">
                                <td>{{ clientNamePaymentsShow(payment) }}</td>
                                <td>{{ payment.created_at }}</td>
                                <td>{{ payment.invoice_id }}</td>
                                <td>{{ payment.payment_method === 'Credit Card' ? currencyFormater(payment.card_amount) :  currencyFormater(payment.amount) }}</td>
                                <td :class="{'text-white bg-yellow-500': payment.initial_payment}">{{ payment.for }}</td>
                                <td><span :class="statusBgPayments(payment)" class="rounded-lg capitalize py-1 w-1/2 block mx-auto text-white">{{ payment.status }}</span></td>
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

                            <tr v-for="payment in paymentsVoid().reverse()" @click.prevent="viewPayment(payment.id)"  class="text-center">
                                <td>{{ clientNamePaymentsShow(payment) }}</td>
                                <td>{{ payment.created_at }}</td>
                                <td>{{ payment.invoice_id }}</td>
                                <td>{{ payment.payment_method === 'Credit Card' ? currencyFormater(payment.card_amount) :  currencyFormater(payment.amount) }}</td>
                                <td :class="{'text-white bg-yellow-500': payment.initial_payment}">{{ payment.for }}</td>
                                <td><span :class="statusBgPayments(payment)" class="rounded-lg capitalize py-1 w-1/2 block mx-auto text-white">{{ payment.status }}</span></td>
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


    th,
    td {
        @apply border border-gray-600 py-2 px-1;
    }

    tbody tr {
        @apply cursor-pointer;
    }

    tbody tr:hover {
        @apply bg-blue-200;
    }

    tbody tr:active {
        @apply bg-gray-200;
    }



</style>