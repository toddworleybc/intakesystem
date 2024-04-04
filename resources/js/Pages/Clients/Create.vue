<script setup>
    import MainLayout from '@/Layouts/MainLayout.vue';
    import { useForm } from '@inertiajs/vue3';
    import Loader from '@/Utilities/loader.js';
    import { ref } from 'vue';
    import MessageBannerComponent from '@/Components/messageBanner.vue';


    const formError = ref(null);
   

    const form = useForm("CreateClient", {
        name: null,
        email: null,
        phone: null,
        location: null,
        quote: null,
        domains: null,
        create_quote: false,
        welcome_email_sent: false,
        hosting: "Self Hosting",
        payment_option: "Credit Card",
        details: null
    });

    
// submits the create client form

    function submitForm() {
       const $url = route('clients.store');

        form.post($url, {
           onStart : function() {
                Loader.action = "Creating Client";
                Loader.isLoading = true;
           },
           onFinish : function() {
                Loader.isLoading = false;
           },
           onError : error => {
               formError.value = "An error has occured";
           }
        });
    } 




</script>


<template>

    <MainLayout>

        <h1 class="text-2xl text-center py-2 text-white bg-blue-400 mb-8">New Client Intake</h1>


        <MessageBannerComponent :show="formError" type="danger">
            {{ formError }}
        </MessageBannerComponent>




        <div class="border border-gray-200 px-4 py-10 rounded-sm shadow-lg">

            

            <form @submit.prevent="submitForm" id="client-create" class="flex justify-between items-start">

                <div id="left-side" class="w-1/2">
                    <h2>Client Informaion</h2>
                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="first-name">Full Name</label>
                        <div v-if="form.errors.name" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.name }}</p></div>
                        <input v-model="form.name" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" type="text" name="name" id="name">
                    </div>

                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="email">Email</label>
                        <div v-if="form.errors.email" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.email }} </p></div>
                        <input v-model="form.email" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" type="email" name="email" id="email">
                    </div>

                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="phone">Phone</label>
                        <div v-if="form.errors.phone" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.phone }}</p></div>
                        <input v-model="form.phone" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" type="tel" name="phone" id="phone" pattern="[0-9]{10}">
                    </div>

                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="location">Location</label>
                        <div v-if="form.errors.location" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.location }}</p></div>
                        <input v-model="form.location" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" type="text" name="location" id="location">
                    </div>

                    <div>
                        <h2>Hosting Services</h2>
                        <div class="mb-8">
                            <label class="mb-1 text-xl block" for="hosting">Hosting</label>
                            <div v-if="form.errors.hosting" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.hosting }}</p></div>
                            <select v-model="form.hosting" form="client-create" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" name="hosting" id="hosting">
                                <option value="Self Hosting">Self Hosting</option>
                                <option value="Pending">Pending</option>
                                <option value="Active">Active</option>
                            </select>
                        </div>
                    </div>
                    

                </div>
                <!-- #/left side  -->

                <div id="right-side" class="w-1/2">
                    <div class="flex space-x-4 items-center mb-4">
                        <h2 class="mb-0">Create Quote</h2>
                        <input v-model="form.create_quote" class="rounded-full border-gray-400 shadow-gray-200 shadow-md p-2" type="checkbox" >
                    </div>
                    <div v-if="form.create_quote || form.errors.quote" id="hide-quote">
                    
                        <div class="mb-8">
                            <label class="mb-1 text-xl block" for="Quote">Quote</label>
                            <div v-if="form.errors.quote" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.quote }}</p></div>
                            <input v-model="form.quote" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" type="text" name="quote" id="quote" pattern="[0-9]*\.[0-9]{2}" placeholder="100.00">
                        </div>

                        <div class="mb-8">
                            <div v-if="form.errors.welcome_email_sent" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.welcome_email_sent }}</p></div>
                            <div class="flex items-center space-x-4">
                                <label class="mb-1 text-xl" for="welcome-email">Don't Send Welcome Email</label>
                            
                                <input v-model="form.welcome_email_sent" class="rounded-full border-gray-400 shadow-gray-200 shadow-md p-2" type="checkbox" id="welcome-email">
                            </div>
                            
                        </div>
                    </div>
                    <!-- #hide-quote -->
                    

                    <div class="mb-8">
                        <h3 class="mb-2 text-xl" >Payment Option</h3>
                        <div v-if="form.errors.payment_option" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.payment_option }}</p></div>
                        <div class="space-y-2">
                            <div class="space-x-4">
                                    <select v-model="form.payment_option" id="payment-option" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2">
                                        <option value="Credit Card">Credit Card</option>
                                        <option value="Zelle">Zelle</option>
                                        <option value="Venmo">Venmo</option>
                                    </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="domains">Domain(s)</label>
                        <p class="italic">comma seperated</p>
                        <div v-if="form.errors.domains" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.domains }}</p></div>
                        <input v-model="form.domains" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" type="text" name="domains" id="domains">
                    </div>

                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="website-details">Website Details</label>
                        <div v-if="form.errors.details" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.details }}</p></div>
                        <textarea v-model="form.details" class="block rounded-lg w-full border-gray-400 shadow-gray-200 shadow-md py-2" rows="10" name="details" id="details"></textarea>
                    </div>

                    <div>
                        <button type="submit" :disabled="form.processing" class="btn btn-safe disabled:bg-green-100">
                            Create Client
                        </button>
                    </div>
                    
                </div>
                <!-- #/right-side  -->
                
                
            </form>





        </div>
        <!-- ./form-container  -->


    </MainLayout>

</template>

<style>
  h2 {
    @apply mb-4;
  }
</style>