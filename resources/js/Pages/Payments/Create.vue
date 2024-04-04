<script setup>
    import MainLayout from '@/Layouts/MainLayout.vue';
    import { useForm, router, usePage } from '@inertiajs/vue3';
    import Loader from '@/Utilities/loader.js';
    import BtnComponent from '@/Components/Button.vue';



    const client = usePage().props.client;


    const form = useForm("CreatePayment", {
        client: client,
        for: null,
        amount: null,
        frequency: "one_time",
        notes: null
    });

    
// submits the create client form

    function submitPayment() {

        const confirmPayment = confirm(`Are you sure you want to send payment to ${client.name}`);

        if(!confirmPayment) return;

       const $url = route('payments.create.new');

        form.post($url, {
           onStart : function() {
                Loader.action = "Sending Payment";
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
                    
                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="payment-for">For</label>
                        <div v-if="form.errors.for" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.for }}</p></div>
                        <input v-model="form.for" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" type="text" id="payment-for">
                    </div>

                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="amount">Amount</label>
                        <div v-if="form.errors.amount" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.amount }} </p></div>
                        <input v-model="form.amount" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" type="text" name="amount" id="amount" pattern="[0-9]*\.[0-9]{2}" placeholder="100.00">
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
                            <label for="edit-notes" class="mb-1 text-xl block">Edit Note</label>
                            <div v-if="form.errors.notes" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.notes }}</p></div>
                            <textarea  v-model="form.notes" class="w-full" rows="10">{{ form.notes }}</textarea>
                        </div>

                        <button @click.prevent="submitPayment" class="btn btn-warning">
                            Send Payment
                        </button>

                </div>
                <!-- #/left side  -->
                
            </form>

        </div>
        <!-- ./form-container  -->


    </MainLayout>

</template>