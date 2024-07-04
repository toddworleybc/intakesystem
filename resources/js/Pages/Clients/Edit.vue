<script setup>
    import MainLayout from '@/Layouts/MainLayout.vue';
    import { useForm, Link, router } from '@inertiajs/vue3';
    import { CheckCircleIcon } from '@heroicons/vue/24/outline';
    import { useAttrs, ref } from 'vue';
    import BtnComponent from '@/Components/Button.vue';
    import MessageBanner from '@/Components/messageBanner.vue';
    import Loader from '@/Utilities/loader.js';


    const editSuccessful = ref(false);


    const client = useAttrs();


    const form = useForm(`EditClient:${client.id}`, client);

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

    
// submits the update client form

    function submitForm() {

        if(form.status === 'cancelled') {

            const cancelClient = confirm(`Are you sure you want to cancel ${client.name}? This will void all pending payments!`);

            if(!cancelClient) return; 
        }

       const $url = route('clients.update', client.id);

        form.patch($url, {
            onSuccess: function() {
                editSuccessful.value = true;
            },
            onStart : function() {
                Loader.action = "Updating Client";
                Loader.isLoading = true;
                },
            onFinish : function() {
                    Loader.isLoading = false;
                }
        });
    }//===
    
    function deleteClient(client) {

        const confirmDelete = confirm(`Are you sure you want to delete client ${client.name}. This action can not be undone!`);

        if(confirmDelete) {
            router.delete(route('clients.destroy', client),{
                onSubmit : function() {
                    Loader.action = "Deleting Client";
                    Loader.isLoading = true;
                },
                onFinish : function() {
                    Loader.isLoading = false;
                }
            } );
        }
        
    }//==


    function exitEditClient() {

        router.get(route('clients.show', client.id));

    }//==

</script>


<template>

    <MainLayout>

        <div class="text-white py-2 bg-blue-400 mb-4 flex justify-between items-center px-4">
            <h1 class="text-2xl">Edit Client {{ client.name }}</h1>
            <BtnComponent @click.prevent="exitEditClient" link="#" type="safe">
                Exit
            </BtnComponent>
        </div>
        

        <MessageBanner :show="editSuccessful" type="safe">
            Client Updated!
        </MessageBanner>



        <div class="border border-gray-200 px-4 py-10 rounded-sm shadow-lg">

            <form @submit.prevent="submitForm" id="client-update" class="flex justify-between items-start">

                <div id="left-side" class="w-1/2">

                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="first-name">Full Name</label>
                        <div v-if="form.errors.name" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.name }}</p></div>
                        <input v-model="form.name" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" type="text" name="name" id="name">
                    </div>

                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="email">Email</label>
                        <div v-if="form.errors.email" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.email }}</p></div>
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

                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="hosting">Hosting</label>
                        <div v-if="form.errors.hosting" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.hosting }}</p></div>
                        <select v-model="form.hosting" form="create-client" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" name="hosting" id="hosting">
                            <option value="Self Hosting">Self Hosting</option>
                            <option value="Pending">Pending</option>
                            <option value="Active">Active</option>
                        </select>
                    </div>
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
                <!-- #/left side  -->

                <div id="right-side" class="w-1/2">

                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="status">Status</label>
                        <div v-if="form.errors.status" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.status }}</p></div>
                        <select v-model="form.status" form="client-update" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" name="status" id="status">
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="payment_option">Preferred Payment Method</label>
                        <div v-if="form.errors.payment_option" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.payment_option }}</p></div>
                        <select v-model="form.payment_option" form="client-update" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" name="payment_option" id="payment_option">
                            <option value="Credit Card">Credit Card</option>
                            <option value="Zelle">Zelle</option>
                            <option value="Venmo">Venmo</option>
                        </select>
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
                        <label class="mb-1 text-xl block" for="website-details">Website Details</label>
                        <div v-if="form.errors.details" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p class="mb-0">{{ form.errors.details }}</p></div>
                        <textarea v-model="form.details" class="block rounded-lg w-full border-gray-400 shadow-gray-200 shadow-md py-2" rows="10" name="details" id="details"></textarea>
                    </div>

                    <div>
                        <button type="submit" :disabled="form.processing" class="btn btn-safe disabled:bg-green-100">
                            Update Client
                        </button>
                    </div>
                    
                </div>
                <!-- #/right-side  -->
                
                
            </form>





        </div>
        <!-- ./form-container  -->


    </MainLayout>

</template>