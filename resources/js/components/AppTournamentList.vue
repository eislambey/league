<script setup>
import AppTournamentState from '@/components/AppTournamentState.vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

defineProps({
    tournaments: {
        type: Object,
        default: () => null,
    },
});
</script>

<template>
    <div v-if="tournaments.data.length > 0" class="table-responsive">
        <table class="table table-hover" style="min-width: 500px; table-layout: fixed">
            <thead>
                <tr>
                    <th colspan="1">ID</th>
                    <th colspan="4">Tournament</th>
                    <th colspan="4">Leader</th>
                    <th colspan="3">State</th>
                    <th colspan="3">Created at</th>
                    <th colspan="1">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="tournament in tournaments.data" :key="tournament.id" class="align-middle">
                    <td colspan="1">{{ tournament.id }}</td>
                    <td colspan="4">
                        <Link :href="route('tournaments.show', tournament)">{{ tournament.name }}</Link>
                    </td>
                    <td colspan="4">
                        {{ tournament.winner.name }}
                    </td>
                    <td colspan="3">
                        <AppTournamentState :state="tournament.state" />
                    </td>
                    <td colspan="3">
                        <span v-tooltip :title="tournament.created_at">
                            {{ tournament.created_at_human }}
                        </span>
                    </td>
                    <td colspan="1">
                        <Link :href="route('tournaments.show', tournament)" class="btn btn-primary btn-sm">View</Link>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div v-else class="alert alert-info">No tournaments found.</div>
    <nav class="mt-3">
        <ul class="pagination">
            <li class="page-item" :class="{ disabled: !tournaments.prev_page_url }">
                <Link class="page-link" :href="tournaments.prev_page_url ? tournaments.prev_page_url : '#'"> Previous </Link>
            </li>
            <li class="page-item" :class="{ disabled: !tournaments.next_page_url }">
                <Link class="page-link" :href="tournaments.next_page_url ? tournaments.next_page_url : '#'"> Next </Link>
            </li>
        </ul>
    </nav>
</template>
