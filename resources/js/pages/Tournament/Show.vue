<script setup>
import AppButton from '@/components/AppButton.vue';
import AppNavbar from '@/components/AppNavbar.vue';
import AppTournamentState from '@/components/AppTournamentState.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { toast } from 'vue3-toastify';
import { route } from 'ziggy-js';

const { tournament, fixtureGroups } = defineProps({
    tournament: {
        type: Object,
        required: true,
    },
    fixtureGroups: {
        type: Object,
        required: true,
    },
    teams: {
        type: Array,
        required: true,
    },
    predictions: {
        type: Array,
        required: true,
    },
});
const page = usePage();
const fixtureRef = ref({});
const activeFixtureWeek = ref(null);
const allPlaying = ref(false);
const nextFixture = computed(() => {
    return tournament.fixtures.find((fixture) => fixture.state === 'created');
});

const isFinished = computed(() => {
    return tournament.state === 'finished';
});

const isPlayable = computed(() => {
    return !isFinished.value && nextFixture.value;
});

const disablePlayButtons = computed(() => {
    return activeFixtureWeek.value !== null || !isPlayable.value || allPlaying.value;
});

const playFixture = async () => {
    if (!nextFixture.value) {
        toast.error('No fixtures to play');
        return;
    }

    activeFixtureWeek.value = nextFixture.value.week_number;
    scrollToWeek(activeFixtureWeek.value);

    await toast.promise(
        new Promise((resolve, reject) => {
            const params = {
                tournament: tournament.id,
                week: nextFixture.value.week_number,
            };
            const url = route('fixtures.play', params);
            router.post(
                url,
                {},
                {
                    onSuccess: resolve,
                    onError: reject,
                },
            );
        }),
        {
            pending: `Playing week #${nextFixture.value.week_number}...`,
            success: `Week ${nextFixture.value.week_number} played successfully`,
            error: `Error playing week ${nextFixture.value.week_number}`,
        },
        {
            autoClose: 1000,
            closeButton: true,
        },
    );

    activeFixtureWeek.value = null;
};

const scrollToWeek = (weekNumber) => {
    if (!weekNumber) {
        return;
    }
    const el = fixtureRef.value[weekNumber];
    if (!el) {
        return;
    }
    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
};

const playAllFixtures = async () => {
    if (!isPlayable.value) {
        toast.error('No fixtures to play');
        return;
    }
    allPlaying.value = true;
    toast.info('Playing all remaining fixtures...');

    try {
        while (nextFixture.value) {
            await new Promise((resolve) => setTimeout(resolve, 750)); // Add a delay to see changes
            await playFixture();
        }
    } catch (error) {
        toast.error('Error playing fixtures');
        console.error('Error playing fixtures:', error);
    } finally {
        activeFixtureWeek.value = null;
        allPlaying.value = false;
    }
};
const playAll = async () => {
    if (!isPlayable.value) {
        toast.error('No fixtures to play');
        return;
    }
    try {
        await toast.promise(
            new Promise((resolve, reject) => {
                const params = {
                    tournament: tournament.id,
                };
                const url = route('fixtures.playAll', params);
                router.post(
                    url,
                    {},
                    {
                        onSuccess: resolve,
                        onError: reject,
                    },
                );
            }),
            {
                pending: 'Playing all fixtures...',
                success: 'All fixtures played successfully',
                error: 'Error playing all fixtures',
            },
        );
    } catch (error) {
        console.error('Error playing all fixtures:', error);
    } finally {
        activeFixtureWeek.value = null;
    }
};

const resetTournament = async () => {
    if (!confirm('Are you sure you want to reset the tournament? Fixtures will be re-generated.')) {
        return;
    }

    const params = {
        tournament: tournament.id,
    };
    const url = route('tournament.reset', params);
    router.post(
        url,
        {},
        {
            onSuccess: () => {
                if (page.props.flash.error) {
                    toast.error(page.props.flash.error);
                } else {
                    toast.success('Tournament reset successfully');
                }
            },
            onError: (e) => {
                toast.error('Error resetting tournament');
                console.log('e', e);
            },
        },
    );
};

onMounted(() => {
    scrollToWeek(nextFixture.value?.week_number);
});
</script>

<template>
    <Head :title="tournament.name" />

    <div class="container bg-white shadow mt-3 p-3 rounded">
        <AppNavbar class="my-3" />

        <div class="d-flex justify-content-between align-items-center mb-3 border p-3 rounded">
            <div>
                <h5 class="m-0">Tournament: {{ tournament.name }}</h5>
            </div>
            <div>
                <AppTournamentState :state="tournament.state" />
            </div>
        </div>

        <div class="row h-100">
            <div class="col-md mb-3 d-flex">
                <div class="card w-100">
                    <div class="card-header">
                        <div class="card-title mb-0">Standings</div>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover table-borderless" style="table-layout: fixed">
                            <thead>
                                <tr>
                                    <th colspan="4">Team</th>
                                    <th>
                                        <span v-tooltip data-bs-placement="top" title="Played Matches"> P </span>
                                    </th>
                                    <th>
                                        <span v-tooltip data-bs-placement="top" title="Wins"> W </span>
                                    </th>
                                    <th>
                                        <span v-tooltip data-bs-placement="top" title="Draws"> D </span>
                                    </th>
                                    <th>
                                        <span v-tooltip data-bs-placement="top" title="Losses"> L </span>
                                    </th>
                                    <th>
                                        <span v-tooltip data-bs-placement="top" title="Goal Difference"> GD </span>
                                    </th>
                                    <th>
                                        <span v-tooltip data-bs-placement="top" title="Points"> PTS </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="team in teams" :key="team.id">
                                    <td colspan="4">{{ team.name }}</td>
                                    <td>{{ team.fixtures_played }}</td>
                                    <td>{{ team.wins }}</td>
                                    <td>{{ team.draws }}</td>
                                    <td>{{ team.losses }}</td>
                                    <td>{{ team.goal_diff }}</td>
                                    <td>{{ team.points }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md mb-3 d-flex">
                <div class="card w-100">
                    <div class="card-header">
                        <div class="card-title mb-0">Fixtures</div>
                    </div>
                    <div class="card-body overflow-auto" style="max-height: 300px">
                        <template v-for="(fixtures, index) in fixtureGroups" :key="index">
                            <table
                                class="table table-hover table-borderless"
                                style="table-layout: fixed"
                                :ref="(el) => (fixtureRef[index] = el)"
                                :class="{ 'highlight-fixture': activeFixtureWeek === parseInt(index) }"
                            >
                                <thead>
                                    <tr>
                                        <th colspan="5">Week #{{ index }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="fixture in fixtures" :key="fixture.id">
                                        <td colspan="2">{{ fixture.home_team.name }}</td>
                                        <td colspan="2">{{ fixture.away_team.name }}</td>
                                        <td>
                                            <span v-if="fixture.state === 'finished'">
                                                {{ fixture.home_team_score }}-{{ fixture.away_team_score }}
                                            </span>
                                            <span v-else-if="activeFixtureWeek === parseInt(index)">
                                                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                                                <span class="visually-hidden" role="status">Loading...</span>
                                            </span>
                                            <span v-else> N/A </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </template>
                    </div>
                </div>
            </div>
            <div class="col-md col-md-3 mb-3 d-flex">
                <div class="card w-100">
                    <div class="card-header">
                        <div class="card-title mb-0">Win Probabilities</div>
                    </div>
                    <div class="card-body overflow-auto" style="max-height: 300px">
                        <table class="table table-hover table-borderless" style="table-layout: fixed">
                            <thead>
                                <tr>
                                    <th colspan="3">Team</th>
                                    <th>Chance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(prediction, i) in predictions" :key="i">
                                    <td colspan="3">{{ prediction.team.name }}</td>
                                    <td class="font-monospace">%{{ prediction.probability }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <template v-if="!allPlaying && !activeFixtureWeek">
                <AppButton @click="playFixture" :tooltip="!isFinished ? 'Play next fixture' : 'Tournament finished'" :disabled="disablePlayButtons">
                    Play &rarr;
                </AppButton>

                <AppButton
                    @click="playAllFixtures"
                    :tooltip="isPlayable ? 'Play all fixtures week by week' : 'Tournament finished'"
                    :disabled="disablePlayButtons"
                >
                    Play All (Weekly)
                </AppButton>

                <AppButton
                    :tooltip="isPlayable ? 'Play all fixtures instantly' : 'Tournament finished'"
                    :disabled="disablePlayButtons"
                    @click="playAll"
                >
                    Play All (Instantly)
                </AppButton>

                <AppButton v-if="tournament.state !== 'created'" kind="btn-danger" tooltip="Reset tournament" @click="resetTournament">
                    Reset
                </AppButton>
            </template>
            <template v-else>
                <button class="btn btn-secondary" type="button" disabled>
                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                    <span class="visually-hidden" role="status">Loading...</span>
                </button>
            </template>
        </div>
    </div>
</template>

<style>
.highlight-fixture td {
    background-color: rgba(25, 135, 84, 0.1) !important;
}
</style>
