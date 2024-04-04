<script setup>
    import { computed } from 'vue';
    import { CheckCircleIcon } from '@heroicons/vue/24/outline';

    const props = defineProps(['type', 'show']);


    const bannerType = computed( () => ({
        'bg-green-500' : props.type === 'safe',
        'bg-red-500' : props.type === 'danger',
        'bg-blue-500' : props.type === 'info',
        'bg-yellow-500' : props.type === 'warning'
    }) );


</script>
<template>

    <div v-if="props.show" class="flex justify-center items-center space-x-4 mb-4">
               
        <div id="message-banner" :class="bannerType" class="flex item-center space-x-2 text-white justify-center py-2 w-1/2 rounded-lg">

            <div>
                <p class="mb-0"><slot /></p>
            </div>
            
            
            <CheckCircleIcon v-if="type !== 'danger'" class="w-5 h-5 mt-0.5"/>
            
        </div>
            <!-- #/message-banner  -->
    </div>
    <!-- @/client_created  -->

</template>
<style>
</style>