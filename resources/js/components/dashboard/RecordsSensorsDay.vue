<template>
    <div class="records-sensors-day">
        <div class="tab-header">Sorties / jour / capteur</div>

        <div class="page-wrapper">
            <div class="page-dates">
                <div v-for="d in dates" :key="d" @click="selectDate(d)" role="a">{{ d }}</div>
            </div>

            <div class="page-values">
                <sensors-chart v-if="display_date !== null" :sensors="sensors[ display_date ]" />
            </div>
        </div>
    </div>
</template>

<script>
import SensorsChart from './records_sensors_day/SensorsChart.vue'

export default {
    components: { SensorsChart },
    data: function () {
        return {
            display_date: null,

            dates: [],
            sensors: {},
        }
    },
    created: function () {
        this.getData()
    },
    methods: {
        selectDate: function (date) {
            this.display_date = date
        },
        getData: function () {
            axios.post('/dashboard/records_sensors_day')
                .then((res) => {
                    this.sensors = res.data.data
                    this.dates = Object.keys(this.sensors)
                })
        },
    }
}
</script>