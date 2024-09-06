<script setup>
    import MainLayout from '@/Layouts/MainLayout.vue';
    import { useForm } from '@inertiajs/vue3';
    import Loader from '@/Utilities/loader.js';
    import { ref, watch, watchEffect, watchPostEffect } from 'vue';
    import currencyFormater from '@/Utilities/currencyFormater';
    
    import MessageBannerComponent from '@/Components/messageBanner.vue';
    import inputCurrencyFormat from '@/Utilities/inputCurrencyFormat';


    const formError = ref(null);
   

    const form = useForm("CreateClient", {
        name: null,
        email: null,
        phone: null,
        location: null,
        domains:[],
        pro_emails: [],
        welcome_email_sent_count: [],
        payment_option: "Credit Card",
        details: null
    });



// add empty domain
    function addDomain() {
        form.domains.push("");
    }

    function removeDomain(i) {
        form.domains.splice(i, 1);
    }

    // add empty domain
    function addProEmail() {
        form.pro_emails.push("");
    }

    function removeProEmail(i) {
        form.pro_emails.splice(i, 1);
    }
    
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
                       
                        <div class="mb-8">
                            <label class="mb-1 text-xl block" for="pro-emails">Professional Email(s)</label>
                        
                            <div v-if="form.errors.pro_emails" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.pro_emails }}</p></div>

                            <div v-for="(pro_emails, i) in form.pro_emails" :key="i" class="flex space-x-4 mb-6">
                                <input  v-model="form.pro_emails[i]" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" type="email"  href="#"name="pro_emails" id="pro_emails">
                                <span @click.prevent="removeProEmail(i)" class="self-center text-blue-600 cursor-pointer hover:text-blue-200 active:text-gray-400">remove</span>
                            </div>
                            

                            <button @click.prevent="addProEmail" class="btn btn-info">
                                Add Pro.Email
                            </button>
                        
                        </div>
                    </div>
                    

                </div>
                <!-- #/left side  -->

                <div id="right-side" class="w-1/2">  

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
                      
                        <div v-if="form.errors.domains" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.domains }}</p></div>

                        <div v-for="(domains, i) in form.domains" :key="i" class="flex space-x-4 mb-6">
                            <input  v-model="form.domains[i]" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" type="text"  href="#"name="domains" id="domains">
                            <span @click.prevent="removeDomain(i)" class="self-center text-blue-600 cursor-pointer hover:text-blue-200 active:text-gray-400">remove</span>
                        </div>
                        

                        <button @click.prevent="addDomain" class="btn btn-info">
                            Add Domain
                        </button>
                        
                    </div>

                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="website-details">Website Details <span class="italic ml-2">*note client will see this</span></label>
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