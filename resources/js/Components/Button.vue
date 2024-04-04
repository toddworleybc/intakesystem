<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps([
    'link', 
    'type', 
    'disabled'
]);


const buttonStatus = computed( () => ({
    'bg-red-600 hover:bg-red-400': props.type === 'danger',
    'bg-yellow-600 hover:bg-yellow-400': props.type === 'warning',
    'bg-green-600 hover:bg-green-400': props.type === 'safe',
    'bg-blue-600 hover:bg-blue-400': !props.type
}) );



</script>


<template>

    <div>
        <Link :class="buttonStatus" class="py-2 inline-block px-4 text-white rounded-md shadow-lg active:shadow-sm transform active:scale-95 active:bg-gray-500 transition-all btn-component" :href="props.link">
            <slot />
        </Link>
    </div>

</template>
