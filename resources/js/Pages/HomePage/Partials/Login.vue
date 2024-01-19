<script setup>
    import { Link } from '@inertiajs/vue3';
    import ApplicationLogo from '@/Components/ApplicationLogo.vue';

</script>
<template>
    <div class="flex justify-center">
        <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />
    </div>

    <h3 class="font-semibold text-gray-600 mt-6" >Please connect to have access to the calendar app</h3>
    <div class="flex flex-row justify-center pt-2">
        <Link
            :href="route('login')"
            class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
            >Log in</Link
        >
        <Link
            :href="route('register')"
            class="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
            >Register</Link
        >
    </div>
</template>