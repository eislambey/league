<script setup>
import AppNavbar from '@/components/AppNavbar.vue';
import AppTournamentList from '@/components/AppTournamentList.vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const { tournaments } = defineProps({
    tournaments: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Home"> </Head>

    <div class="container bg-white shadow mt-3 p-3 rounded">
        <AppNavbar class="my-3" />

        <div class="d-flex justify-content-between align-items-center border p-3 rounded mb-3">
            <div>
                <h5 class="m-0">Tournaments</h5>
            </div>
            <div>
                <Link :href="route('tournaments.create')" class="btn btn-sm btn-success">&plus; New</Link>
            </div>
        </div>
        <AppTournamentList :tournaments="tournaments" />
    </div>
</template>

<style></style>
