<template>
    <div class="dashboard">
        <div class="dashboard-header">
            <button @click="updateDisplay('records_sensors_day')">Sorties / jour / capteur</button>
            <button @click="updateDisplay('geofencing')">Geofencing</button>
        </div>

        <records-sensors-day v-show="display === 'records_sensors_day'" />
        <geofencing v-show="display === 'geofencing'" />
    </div>
</template>

<script>
import Geofencing from './dashboard/Geofencing.vue'
import RecordsSensorsDay from './dashboard/RecordsSensorsDay.vue'

export default {
    components: { Geofencing, RecordsSensorsDay },
    data: function () {
        return {
            display: 'records_sensors_day',
        }
    },
    methods: {
        updateDisplay: function (display) {
            this.display = display
        },
    }
}
</script>

<style lang="sass" scoped>
@import '@sass/pages/dashboard.sass'
</style>