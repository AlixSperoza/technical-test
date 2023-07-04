<template>
    <div class="geofencing">
        <div class="tab-header">
            Geofencing

            <button v-if="display_without_sensor" @click="displayWithoutSensor(false)">Masquer les jours sans senseurs</button>
            <button v-else @click="displayWithoutSensor(true)">Afficher les jours sans senseurs</button>
        </div>

        <div class="page-wrapper">
            <div class="page-dates">
                <template v-for="d in dates" :key="d">
                    <div v-if="display_without_sensor || (!display_without_sensor && sensors[ d ].length > 0)" @click="selectDate(d)" role="a">
                        {{ d }}
                    </div>
                </template>
            </div>

            <div class="page-values" v-if="display_date !== null">
                <template v-if="sensors[ display_date ].length === 0">Aucun senseur à proximité à la fin de cette journée</template>

                <template v-else>
                    <p>Liste des senseurs à proximité à la fin du {{ display_date }}</p>
                    <div v-for="s in sensors[ display_date ]" :key="s">{{ s }}</div>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            display_date: null,
            display_without_sensor: true,

            dates: [],
            sensors: {},
        }
    },
    created: function () {
        this.getData()
    },
    methods: {
        displayWithoutSensor: function (display) {
            this.display_without_sensor = display
        },
        selectDate: function (date) {
            this.display_date = date
        },
        getData: function () {
            axios.post('/dashboard/geofencing')
                .then((res) => {
                    this.sensors = res.data.data
                    this.dates = Object.keys(this.sensors)
                })
        },
    }
}
</script>