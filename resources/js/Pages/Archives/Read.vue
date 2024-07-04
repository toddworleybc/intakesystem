<script setup>
    import MainLayout from '@/Layouts/MainLayout.vue';
    import BtnComponent from '@/Components/Button.vue';
    import { PlusCircleIcon } from '@heroicons/vue/24/solid';
    import { usePage, Link, router } from '@inertiajs/vue3';
    import phoneNumberFormat from '@/Utilities/phoneNumberFormater';
    import MessageBanner from '@/Components/messageBanner.vue';
    import moment from 'moment';
    import { ref } from 'vue';

    const clients = usePage().props.clients;
    const payments = usePage().props.payments;
    const filter = ref('payments');


    function cancelledClients(client) {
        return client.status === 'cancelled';
    }//

    
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

    function createdAtDate(createdDate) {
       return moment(createdDate).format('MMMM Do YYYY');
    }
    

   
    
    function statusBgPayments(payment) {

        let statusColor = null;

        if(payment.status === "pending") statusColor = "bg-yellow-500";

        if(payment.status === "paid") statusColor = "bg-green-500";

        if(payment.status === "void") statusColor = "bg-gray-500";

        return statusColor;

    }

    function statusBgClients(client) {

        let statusColor = null;

        if(client.status === "pending") statusColor = "bg-yellow-500";

        if(client.status === "active") statusColor = "bg-green-500";

        if(client.status === "cancelled") statusColor = "bg-red-500";

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

            } else {
                clientName = "Client Deleted";
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


</script>


<template>

    <MainLayout>


        <div class="text-white py-2 bg-blue-600 mb-4 text-center px-4">
            <h1 class="text-2xl">The Archives</h1>
        </div>

        <MessageBanner :show="$page.props.flash.success" type="warning">
            {{ $page.props.flash.success }}
        </MessageBanner>

        <section class="flex justify-between items-center my-6">
            
            <div>
                <h3>Select Archive</h3>
                <select v-model="filter" class="cursor-pointer">
                    <option value="clients">Clients</option>
                    <option value="payments">Payments</option>
                </select>
            </div>
            
        </section>

        <div v-if="filter === 'clients'" id="clients-table">
            <h2 class="block border-b pb-2 mb-6">Clients</h2>
            <section v-if="clients.filter(cancelledClients).length !== 0">
                <table class="table-fixed border-collapse border w-full">
                    <thead class="bg-green-300">
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr v-for="client in clients.filter(cancelledClients)" @click.prevent="editUser(client.id)" :key="client.id" class="text-center">
                            <td>{{ client.name }}</td>
                            <td>{{ phoneNumberFormat(client.phone) }}</td>
                            <td>{{ client.email }}</td>
                            <td><span :class="statusBgClients(client)" class="rounded-lg capitalize py-1 w-1/2 block mx-auto text-white">{{ client.status }}</span></td>
                        </tr>

                    </tbody>
                </table>
            </section>

            <section v-else class="bg-yellow-500 text-white text-center py-4">
                No Deleted Clients
            </section>


        </div>
        <!-- #/clients-table  -->




<!-- ------------PAYMENT ARCHIVES-----------  -->
        <div v-if="filter === 'payments'" id="payments-table" class="mt-8">
            
            <h2 class="block border-b pb-2 mb-6">Payments</h2>
    
            <section v-if="payments.length !== 0">
               
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
                                <td>{{ clientNamePaymentsShow(payment) }}</td>
                                <td>{{ createdAtDate(payment.created_at) }}</td>
                                <td>{{ payment.invoice_id }}</td>
                                <td>${{ payment.amount }}</td>
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

                            <tr v-for="payment in paymentsVoid()" @click.prevent="viewPayment(payment.id)"  class="text-center">
                                <td>{{ clientNamePaymentsShow(payment) }}</td>
                                <td>{{ createdAtDate(payment.created_at) }}</td>
                                <td>{{ payment.invoice_id }}</td>
                                <td>${{ payment.amount }}</td>
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

        <!-- <section v-else class="bg-yellow-500 text-white text-center py-4">
            No Clients Created
        </section> -->

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