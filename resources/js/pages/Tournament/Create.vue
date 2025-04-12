<script setup>
import AppNavbar from '@/components/AppNavbar.vue';
import { Head, router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';
import { route } from 'ziggy-js';
import { toast } from 'vue3-toastify';

const MIN_TEAM_COUNT = 4;
const MAX_TEAM_COUNT = 12;

const team1 = {
    id: 1,
    name: 'Manchester United',
    power: 75,
};

const team2 = {
    id: 2,
    name: 'Barcelona',
    power: 85,
};

const team3 = {
    id: 3,
    name: 'Paris Saint-Germain',
    power: 88,
};

const team4 = {
    id: 4,
    name: 'Bayern Munich',
    power: 90,
};

const isLoading = ref(false);
const randomLetter = String.fromCharCode(65 + Math.floor(Math.random() * 7));
const form = reactive({
    name: `UCL 2024/25 Group ${randomLetter}`,
    teams: [team1, team2, team3, team4],
});

const addTeam = () => {
    if (form.teams.length >= MAX_TEAM_COUNT) {
        toast.error(`Maximum number of teams is ${MAX_TEAM_COUNT}`);
        return;
    }
    form.teams.push({ id: form.teams.length + 1 });
    form.teams.push({ id: form.teams.length + 2 });
};

const removeTeam = () => {
    if (form.teams.length <= MIN_TEAM_COUNT) {
        toast.error(`Minimum number of teams is ${MIN_TEAM_COUNT}`)
        return;
    }
    form.teams.pop();
    form.teams.pop();
};

function submit() {
    isLoading.value = true;
    router.post(route('tournaments.store'), form, {
        onFinish: () => {
            isLoading.value = false;
        },
        onError: (errors) => {
            Object.values(errors).forEach((err) => {
                toast.error(err)
            })
        },
    });
}
</script>

<template>
    <Head title="New Tournament"></Head>
    <div class="container bg-white shadow mt-3 p-3 rounded">
        <AppNavbar class="my-3" />

        <div class="d-flex justify-content-between align-items-center mb-3 border p-3 rounded">
            <div>
                <h5 class="m-0">New Tournament</h5>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form @submit.prevent="submit">
                    <div class="mb-3">
                        <label for="tournament-name" class="form-label">Tournament Name</label>
                        <input type="text" class="form-control" id="tournament-name" v-model="form.name" required />
                        <div class="form-text">Randomly generated when empty</div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex gap-2 align-items-center mb-3">
                            <h6 class="m-0">Teams</h6>
                            <button type="button" class="btn btn-outline-success btn-sm" @click="addTeam">&plus; Add 2 Teams</button>
                            <button
                                type="button"
                                class="btn btn-outline-danger btn-sm"
                                @click="removeTeam"
                                v-show="form.teams.length > MIN_TEAM_COUNT"
                            >
                                &plus; Remove Last 2 Teams
                            </button>
                        </div>

                        <div class="p-3 bg-light rounded-3 shadow-sm mb-3" v-for="(team, i) in form.teams" :key="team.id">
                            <div class="row">
                                <div class="col">
                                    <label :for="`team-name-${i}`" class="form-label">Team Name #{{ i + 1 }}</label>
                                    <input :id="`team-power-${i}`" type="text" class="form-control" v-model="team.name" />
                                </div>
                                <div class="col">
                                    <label :for="`team-power-${i}`" class="form-label">Team Power #{{ i + 1 }}</label>
                                    <input :id="`team-power-${i}`" type="number" min="1" max="100" class="form-control" v-model="team.power" />
                                    <div class="form-text">Power of the team from 1 to 100</div>
                                </div>
                                <div class="col-auto">
                                    <a class="badge bg-danger text-decoration-none fs-6" href> &times; </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</template>
