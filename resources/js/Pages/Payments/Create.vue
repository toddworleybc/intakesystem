<script setup>
    import MainLayout from '@/Layouts/MainLayout.vue';
    import { useForm, router, usePage } from '@inertiajs/vue3';
    import Loader from '@/Utilities/loader.js';
    import BtnComponent from '@/Components/Button.vue';
    import inputCurrencyFormat from '@/Utilities/inputCurrencyFormat';



    const client = usePage().props.client;
    const initialPayment = usePage().props.initialPayment;


    const form = useForm("CreatePayment", {
        client: client,
        for: null,
        amount: null,
        frequency: "one_time",
        notes: null
    });

// set quote input
function setQuoteInput(e) {
        form.amount = inputCurrencyFormat(e.target.value);
    }

    
// submits the create client form

    function submitPayment() {

       const $url = route('payments.create.new');

        form.post($url, {
           onStart : function() {
                Loader.action = "Creating Payment";
                Loader.isLoading = true;
           },
           onFinish : function() {
                Loader.isLoading = false;
           },
           preserveState: false
        });
    } 

    function exitEditClient() {

        router.get(route('clients.show', client.id));

    }//==


    function createFinalPayment(e) {

        const setFinalPayment = e.target.checked;
        

        if(setFinalPayment) {
            form.for = "EBD Final Website Payment Balance";
            form.amount = initialPayment.amount;
            form.notes = "Final Payment for Completed Website"
        } else {
            form.for = null;
            form.amount = null;
            form.notes = null;
        }

    }


</script>


<template>

    <MainLayout>

        <div class="text-white py-2 bg-blue-400 mb-4 flex justify-between items-center px-4">
            <h1 class="text-2xl">Create Payment for {{ client.name }}</h1>
            <BtnComponent @click.prevent="exitEditClient" link="#" type="safe">
                Exit
            </BtnComponent>
        </div>


        <div class="border border-gray-200 px-4 py-10 rounded-sm shadow-lg">
            <div class="text-center">
                <h2>Payment to: {{ client.name }}</h2>
                <p>Sending to: {{ client.email }}</p>
            </div>
            

            <form @submit.prevent="submitForm" id="client-create" class="flex justify-center items-start">

                <div id="left-side" class="w-1/2">

                    <div class="mb-2 flex space-x-5 items-center">
                        <label class="mb-1 text-xl" for="final-payment">Final Payment</label>
                        <input @change.prevent="createFinalPayment" type="checkbox">
                        
                    </div>
                    
                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="payment-for">For <small class="italic">(*Note client will see)</small></label>
                        <div v-if="form.errors.for" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.for }}</p></div>
                        <input v-model="form.for" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" type="text" id="payment-for" placeholder="EBD Website and Graphic Services">
                    </div>

                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="amount">Amount</label>
                        <div v-if="form.errors.amount" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.amount }} </p></div>
                        <input @change.prevent="setQuoteInput" :value="form.amount" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" type="text" name="amount" id="amount">
                    </div>

                    <div class="mb-8">
                        <label class="mb-4 text-xl block" for="frequency">Frequency</label>
                        <div v-if="form.errors.frequency" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.frequency }}</p></div>
                        <select v-model="form.frequency" form="payment-status" class="block rounded-lg  shadow-gray-200 shadow-md py-2" id="frequency">
                            <option value="one_time">One Time</option>
                            <option value="recurring">Recurring</option>
                        </select>
                    </div>
                    

                        <div class="mb-4">
                            <label for="edit-notes" class="mb-1 text-xl block">Edit Note <small>(Your information)</small></label>
                            <div v-if="form.errors.notes" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.notes }}</p></div>
                            <textarea  v-model="form.notes" class="w-full" rows="10">{{ form.notes }}</textarea>
                        </div>

                        <button @click.prevent="submitPayment" class="btn btn-info">
                            Create Payment
                        </button>

                </div>
                <!-- #/left side  -->
                
            </form>

        </div>
        <!-- ./form-container  -->


    </MainLayout>

</template>