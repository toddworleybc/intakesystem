<script setup>
    import MainLayout from '@/Layouts/MainLayout.vue';
    import BtnComponent from '@/Components/Button.vue'; 
    import MessageBannerComponent from '@/Components/messageBanner.vue';
    import messageBannerControl from '@/Utilities/messageBannerControl';
    import { computed, onBeforeMount, ref, onMounted } from 'vue';
    import { router, usePage, useForm } from '@inertiajs/vue3';
    import Loader from '@/Utilities/loader.js';
    import currencyFormater from '@/Utilities/currencyFormater';
    

    const payment = usePage().props.payment;
    const enableNoteEditing = ref(false);
    const message = usePage().props.flash.message;
    const form = useForm("PaymentStatus", payment);
    const welcomeEmailPayment = ref(null);
  
    


    function receiptSend() {

        const sendReceipt = confirm(`Do you want to send a receipt to ${payment.client.name}`);

        if(!sendReceipt) return;

        router.post( route('receipt.send'), payment, {

            onStart : function() {
                Loader.action = "Sending Receipt";
                Loader.isLoading = true;
           },

           onFinish : function() {
                Loader.isLoading = false;
           },
           preserveState: false

        } );

    }//

    function cancelSubscription() {

        form.status = 'void';


        if(form.status === 'void') {
            const confirmVoidPayment = confirm('Are you sure you want to cancel this subscription. It cannot be edited once cancelled!');
            
            if(!confirmVoidPayment) return;
        }
        


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



    }//


    function updatePaymentStatus() {

        if(form.status === 'void') {
            const confirmVoidPayment = confirm('Are you sure you want to void this payment. It cannot be edited once voided!');
            
            if(!confirmVoidPayment) return;
        }


        if(form.status === 'paid') {
            const confirmVoidPayment = confirm('Are you sure you want to make this payment paid. It cannot be edited once marked as paid!');
            
            if(!confirmVoidPayment) return;
        }
        


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


    function exitPayment(id) {

        router.get(route('clients.show', id));

    }//==

    function sendPayment() {
        
        const confirmResend = confirm(`Send this payment ${payment.client.name}?`);

        if(!confirmResend) return;

        router.post(route('payment.send'), payment, {
            
            onStart : () => {
                Loader.action = "Sending Payment";
                Loader.isLoading = true;
           },
           onFinish : () => {
                Loader.isLoading = false;
           },
           preserveState: false
            
    });

    }//===
    
    function paymentDatesSent() {
        return payment.payment_sent_count.slice().reverse();
    }//

    function receiptSentDates() {
       
        return payment.receipt_sent_dates.slice().reverse();
     
    }//


    function viewPaymentEmail() {
        router.post(route('view.email'), {
            'view': 'payment_email',
            'client': payment.client,
            'payment': payment
        });
    }

    function viewReceiptEmail() {
        router.post(route('view.email'), {
            'view': 'receipt_email',
            'client': payment.client,
            'payment': payment
        });
    }

    function copyPaymentInvoiceId() {
        var copyText = document.getElementById("copy-invoice");

  // Select the text field
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices

        // Copy the text inside the text field
        navigator.clipboard.writeText(copyText.value);

         
                messageBannerControl.status = 'success';
                messageBannerControl.type = 'safe';
                messageBannerControl.message = "Invoice has been copied!";
    
    }//==


    function hasWelcomeEmailPaymentExists() {

        const payments = payment.client_payments;
        let paymentWelcomeEmailExists = false;
       

        payments.forEach(payment => {

            if(payment.payment_welcome_email) {

                paymentWelcomeEmailExists = true;
                welcomeEmailPayment.value = payment;

            }
        });


        // shows/hides the attach an email option on create payment
        return paymentWelcomeEmailExists ? true : false;

    }//==


    function paymentUsedForWelcomeEmail() {
        
        if(welcomeEmailPayment) {
            if(welcomeEmailPayment.value.id === payment.id) {
                return true;
            }
        }
    }
  
    onBeforeMount( () => {
        const created_at = usePage().props.created_at;
        const updated_at = usePage().props.updated_at;
        payment['created_at'] =  created_at;
        payment['updated_at'] = updated_at;
    } );
   

    onMounted( () => {
        if(message) messageBannerControl.display(message);
    } );

</script>


<template>

    <MainLayout>

        <div class="py-2 border-b-2 border-gray-400 mb-4 flex justify-between items-center px-4">
            <h1 class="text-2xl text-center">Payment to: {{ payment.client.name }}</h1>
            <BtnComponent @click.prevent="exitPayment(payment.client.id)" link="#" type="safe">
                Back To Client
            </BtnComponent>
        </div>


        <MessageBannerComponent :show="messageBannerControl" />


        <div class="border border-gray-200 px-4 py-10 rounded-sm shadow-lg relative">

            

            <div class="border-b-2 border-gray-500 pb-2 mb-2 flex justify-between items-end">
                <div>
                    <div class="text-lg bg-gray-500 text-white inline-block py-2 px-4 rounded">
                        <p class="mb-0">Payment Amount: {{ payment.payment_method === 'Credit Card' ? currencyFormater(payment.card_amount) : currencyFormater(payment.amount) }}</p>
                    </div>
                    <div>
                        <div :class="statusClassObj" class="text-sm mt-4 text-center text-white py-1 px-2 rounded inline-block">
                            Status: <span class="capitalize">{{ payment.status }}</span>
                        </div>
                    </div>
                    
                </div>
                

                <div>
                    <h3 class="mb-4">Payment For: {{ payment.for }}</h3>
                    <div class="flex items-center justify-center space-x-4">
                        <div class="bg-green-700  text-white py-1 px-2 rounded">
                            <label  for="copy-invoice">Invoice Id: </label>
                            <input @click.prevent="copyPaymentInvoiceId" type="text" id="copy-invoice" style="width: 108px;" class="border-none p-0 bg-transparent cursor-pointer" :value="payment.invoice_id">
                        </div>
                        
                    </div>
                </div>
                
                

                <div v-if="payment.status !== 'paid'" class="flex space-x-2 items-center">

                    <div v-if="payment.status === 'void'" class="bg-gray-500 text-white py-2 px-4 rounded">
                        Void Payment
                    </div>


                    <BtnComponent v-else-if="paymentDatesSent().length === 0 && !payment.payment_welcome_email"  link="#" @click.prevent="sendPayment" >
                       Send Payment
                    </BtnComponent>

                    <BtnComponent v-else-if="paymentDatesSent().length !== 0 && !payment.payment_welcome_email" type="danger"  link="#" @click.prevent="sendPayment" >
                       Resend Payment
                    </BtnComponent>

                    <div v-else class="bg-yellow-500 text-white py-2 px-4 rounded">
                       Client Send Welcome Email
                    </div>

                </div>

                <BtnComponent v-else type="warning"  link="#" @click.prevent="receiptSend" >
                       Send Receipt
                </BtnComponent>
                
            </div>

            <div v-if="payment.status === 'pending'" class="mb-10 flex justify-between">
                <div v-if="!hasWelcomeEmailPaymentExists() || paymentUsedForWelcomeEmail()">
                    <input type="checkbox" class="mr-4" v-model="form.payment_welcome_email"  id="payment_welcome_email">
                    <label for="payment_welcome_email">Attach to welcome email</label>
                </div>
                <div v-else></div>
                <div v-if="!payment.payment_welcome_email" class="align-end">
                    <a @click.prevent="viewPaymentEmail" href="#">View Payment Email</a>
                </div>
                
            </div>
            
            <div v-else class="mb-10 flex justify-between">
                <span v-if="payment.payment_welcome_email" class="bg-yellow-600 text-white py-1 px-2 round-sm">Welcome Email Payment</span>
                <span v-else></span>
                <a @click.prevent="viewReceiptEmail" href="#">View Receipt Email</a>
            </div>


            <div id="client-info-container" class="flex">

                <div id="client-info" class="w-1/2">
                    <h2 class="mb-2">Payment Information</h2>
                    <div class="border-b-2 border-gray-300 w-8/12">
                        <p><span>To:</span> {{ payment.client.name }}</p>
                        <p><span>Sent To Email:</span> <a :href="'mailto:'+payment.client.email">{{ payment.client.email }}</a></p>
                        <p><span>Payment Method:</span> {{ payment.payment_method }}</p>
                        <p><span>Amount:</span> {{ currencyFormater(payment.amount) }}</p>
                        

                        <div v-if="payment.payment_method === 'Credit Card'">
                            <p><span>Processing Fee:</span> {{ currencyFormater(payment.processing_fee) }}</p>
                            <p><span>Card Amount:</span> {{ currencyFormater(payment.card_amount) }}</p>
                            <p><span>Payment Link:</span> <a :href="payment.payment_link" target="_blank">{{ payment.payment_link }}</a></p>
                        </div>
                       
                        
                        
                        <div class="mt-2 border-t border-gray-400">
                            <h3 class="my-4">Created and Updated</h3>
                            <p>Created: {{  payment.created_at }}</p>
                            <p>Updated: {{  payment.updated_at  }}</p>
                        </div>
                    </div>
                    <div v-if="receiptSentDates().length === 0">
                        <h3 class="my-4">Payment Sent Count: {{ paymentDatesSent().length }}</h3>

                        <p v-if="paymentDatesSent().length !== 0" v-for="(dateSent, i) in paymentDatesSent()" :key="i">Date Sent: {{ dateSent }}</p>
                        <p v-else class="bg-yellow-500 text-white inline-block text-center py-2 px-4 mt-6">Payment Not Sent Yet</p>
                    </div>

                    <div v-else>
                        <h3 class="my-4">Receipt Sent Count: {{ receiptSentDates().length }}</h3>


                        <p class="border-b-2 pb-6 inline-block" v-if="receiptSentDates().length !== 0" v-for="(dateSent, i) in receiptSentDates()" :key="i">Date Sent: {{ dateSent }}</p>
                        <p v-else class="bg-yellow-500 text-white inline-block text-center py-2 px-4 mt-6 border-b-2 border-gray">Receipt Not Sent Yet</p>
                       
                        <h3 class="my-4">Payment Sent Dates {{ paymentDatesSent().length }}</h3>

                        <p v-if="paymentDatesSent().length !== 0" v-for="(dateSent, i) in paymentDatesSent()" :key="i">Date Sent: {{ dateSent }}</p>
                        <p v-else class="bg-yellow-500 text-white inline-block text-center py-2 px-4 mt-6">Payment Not Sent Yet</p>
                    </div>
                   
                   
                    
                </div>
                <!-- #/payment-info  -->

                <div class="mb-8 w-1/2">
                    <div class="border-b border-gray-400 pb-4 w-full">

                        <label class="mb-4 text-xl block" for="payment-status">Payment Status</label>
                            <div v-if="form.errors.status" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.status }}</p></div>
                        <div class="flex justify-between items-center">
                            
                            <form id="payment-status" class="w-full">
                               
                                <select v-if="payment.status === 'pending'" v-model="form.status" form="payment-status" class="block rounded-lg  shadow-gray-200 shadow-md py-2" id="payment-status">
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="void">Void</option>
                                </select>
                                <div v-else-if="payment.status === 'void'">
                                <p  class="bg-gray-700 text-white inline-block py2 px-4">
                                    Payment Voided
                                </p>
                                <p>{{ payment.updatedAt }}</p>
                            </div>
                            <div v-else>
                                <p v-if="payment.frequency !== 'recurring'"  class="bg-green-500 text-white inline-block py2 px-4">
                                    Payment Paid
                                </p>
                                <div v-else>
                                    <p v-if="payment.frequency !== 'recurring'"  class="bg-green-500 text-white inline-block py2 px-4">
                                        Payment Paid
                                    </p>
                                    <button v-if="payment.frequency === 'recurring'" class="btn btn-danger mt-4" @click.prevent="cancelSubscription">
                                    Cancel Payment
                                    </button>
                                </div>
                                <p>{{ payment.updatedAt }}</p>
                            </div>
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
                        
                    
                        <button v-if="payment.status === 'pending'" class="btn btn-safe mt-4" @click.prevent="updatePaymentStatus">
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