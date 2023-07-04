<template>
    <div>
        <bar :data="chart_data" :options="chart_options" :width="100" :height="100" ref="bar" />
    </div>
</template>

<script>
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(BarElement, Title, Tooltip, Legend, CategoryScale, LinearScale)

export default {
    components: { Bar },
    props: [ 'sensors' ],
    data: function () {
        return {
            chart_data: {},
            chart_options: {},
        }
    },
    watch: {
        sensors: function () {
            this.updateData()
        },
    },
    created: function () {
        this.updateData()
    },
    methods: {
        updateData: function () {
            this.chart_data = {
                labels: Object.keys(this.sensors),
                datasets: [
                    {
                        label: 'Sorties / jour / capteur',
                        backgroundColor: '#f87979',
                        data: Object.values(this.sensors)
                    }
                ],
            }
        },
    }
}
</script>