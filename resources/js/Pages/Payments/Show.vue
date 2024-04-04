<script setup>
    import MainLayout from '@/Layouts/MainLayout.vue';
    import BtnComponent from '@/Components/Button.vue'; 
    import MessageBannerComponent from '@/Components/messageBanner.vue';
    import { computed, watch, ref } from 'vue';
    import { router, usePage, useForm } from '@inertiajs/vue3';
    import Loader from '@/Utilities/loader.js';



    
    const payment = usePage().props.payment;
    const enableNoteEditing = ref(false);
    const showPaymentSendingMessage = ref(null);
    

    const form = useForm("PaymentStatus", payment);


   

    function updatePaymentStatus() {


        form.patch(route('payments.update', payment.id), {

            onStart : function() {
                Loader.action = "Updating Payment";
                Loader.isLoading = true;
           },

           onFinish : function() {
                Loader.isLoading = false;
           },
           preserveState: false

        });
    }//===


    function statusBg(payment) {

        let statusColor = null;

        if(payment.status === "pending") statusColor = "bg-yellow-500";

        if(payment.status === "paid") statusColor = "bg-green-500";

        if(payment.status === "cancelled") statusColor = "bg-red-500";

        return statusColor;

    }
    

    const statusClassObj = computed( () => ({
        "bg-yellow-500" : payment.status === "pending",
        "bg-green-500" : payment.status === "paid",
        "bg-gray-500" : payment.status === "void"
    }) );


    const allowPaymentCreating = computed(() => {
        return payment.initial_payment && payment.status === "pending" ? false : true;
    })//====


    function exitPayment(id) {

        router.get(route('clients.show', id));

    }//==

    function resendPayment() {
        
        const confirmResend = confirm('Resend This Payment?');

        if(!confirmResend) return;

        router.post(route('payments.create.new'), {
            'resendPayment' : true,
            'paymentId' : payment.id,
    }, {
            
            onStart : () => {
                Loader.action = "Resending Payment";
                Loader.isLoading = true;
           },
           onSuccess: () => {
               showPaymentSendingMessage.value = 'resend';
           },
           onFinish : () => {
                Loader.isLoading = false;
           }
          
          
    });

    }//===

    function paymentUpdated() {
       const urlParams = new URLSearchParams(window.location.search);
       
       return urlParams.has('paymentUpdated') ? true : false;
    }

    function createdPayment() {
       const urlParams = new URLSearchParams(window.location.search);
       
       return urlParams.get('createdPayment') === '1' ? true : false;
    }
    


</script>


<template>

    <MainLayout>

        <div class="text-white py-2 bg-blue-400 mb-4 flex justify-between items-center px-4">
            <h1 class="text-2xl text-center text-white bg-blue-400">Payment to {{ payment.client.name }}</h1>
            <BtnComponent @click.prevent="exitPayment(payment.client.id)" link="#" type="safe">
                Back To Client
            </BtnComponent>
        </div>


        <MessageBannerComponent :show="paymentUpdated()" type="safe">
            Payment Updated!
        </MessageBannerComponent>

        <MessageBannerComponent :show="createdPayment()" type="safe">
             Payment Successfully Sent To {{ payment.client.name }}!
        </MessageBannerComponent>

        <MessageBannerComponent :show="showPaymentSendingMessage === 'resend'" type="warning">
            Payment Resent!
        </MessageBannerComponent>


        <div class="border border-gray-200 px-4 py-10 rounded-sm shadow-lg relative">

            <div class="border-b-2 border-gray-500 pb-2 mb-10 flex justify-between items-end">

                <div>
                    <h3 class="mb-4">{{ payment.for }}</h3>
                    <div class="flex items-center space-x-4">
                        <span class="bg-green-700 text-sm text-white py-1 px-2 rounded">Invoice Id: {{ payment.invoice_id }}</span>
                        <span :class="statusClassObj" class="text-sm text-white py-1 px-2 rounded">
                            Status: <span class="capitalize">{{ payment.status }}</span>
                        </span>
                    </div>
                </div>
                
                

                <div v-if="payment.status !== 'paid'" class="flex space-x-2 items-center">

                    <BtnComponent v-if="allowPaymentCreating" type="danger" link="#" @click.prevent="resendPayment" >
                       Resend Payment
                    </BtnComponent>

                    <div v-else class="bg-yellow-500 text-white py-2 px-4 rounded">
                        Initial Payment Required
                    </div>

                </div>

                <div v-else class="bg-green-500 text-white py-2 px-4 rounded">
                   Payment Paid
                </div>
                
            </div>
            

            <div id="client-info-container" class="flex">

                <div id="client-info" class="w-1/2">
                    <h2 class="mb-2">Payment Information</h2>
                    <div class="border-b-2 border-gray-300 w-8/12">
                        <p><span>To:</span> {{ payment.client.name }}</p>
                        <p><span>Sent To Email:</span> <a :href="'mailto:'+payment.client.email">{{ payment.client.email }}</a></p>
                        <p><span>Payment Method:</span> {{ payment.payment_method }}</p>
                        <p><span>Amount:</span> {{ payment.amount }}</p>
                        

                        <div v-if="payment.payment_method === 'Credit Card'">
                            <p><span>Processing Fee:</span> {{ payment.processing_fee }}</p>
                            <p><span>Card Amount:</span> {{ payment.card_amount }}</p>
                            <p><span>Payment Link:</span> {{ payment.payment_link }}</p>
                        </div>
                       
                        
                        
                        <div class="mt-2">
                            <p>Created: {{  payment.createdAt }}</p>
                            <p>Updated: {{  payment.updatedAt  }}</p>
                        </div>
                    </div>
                   
                    
                </div>
                <!-- #/payment-info  -->

                <div class="mb-8 w-1/2">
                    <div class="border-b border-gray-400 pb-4 w-full">

                        <label class="mb-4 text-xl block" for="payment-status">Payment Status</label>
                            <div v-if="form.errors.status" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.status }}</p></div>
                        <div class="flex justify-between items-center">
                            
                            <form id="payment-status" class="w-full">
                               
                                <select v-model="form.status" form="payment-status" class="block rounded-lg  shadow-gray-200 shadow-md py-2" id="payment-status">
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="void">Void</option>
                                </select>
                                <p class="mt-4"><span>Frequency:</span> {{ payment.frequency === 'one_time' ? 'One Time' : 'Recurring'  }}</p>
                                <div class="w-full border-t border-gray-400 mt-4 ">

                                   
                                    <h3 class="py-4">Notes:</h3>


                                    <div class="mb-4">
                                        <label for="edit-notes">Edit Note</label>
                                        <input v-model="enableNoteEditing" type="checkbox" class="ml-2 rounded-full">
                                    </div>
                                    <div v-if="form.errors.notes" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.notes }}</p></div>
                                    <div v-if="!enableNoteEditing" class="border p-4 min-h-52">
                                        <p v-if="form.notes">{{ form.notes }}</p>
                                        <p v-else class="text-gray-400">No Notes</p>
                                    </div>
                                    <textarea v-else v-model="form.notes" class="w-full" rows="10">{{ form.notes }}</textarea>
                                </div>
                            </form>

                            
                        </div>
                        
                        

                    </div>

                    
                        <button class="btn btn-safe mt-4" @click.prevent="updatePaymentStatus">
                                Update Payment
                            </button>
                </div>
                <!-- #/ payment status -->

                

            </div>
            <!-- #/client-info-container  -->
            

        </div>
        <!-- ./form-container  -->

    </MainLayout>

</template>

<style>
 h2 {
    @apply border-b border-gray-400 mb-4 inline-block pb-2;
 }

</style>