<template>
    <div id="chart">
        <h1>Profit</h1>
        <ProfitChart></ProfitChart>
    </div>
</template>

<script>
import ProfitChart from '@/element/components/Analytic/components/ProfitChart';

export default {
    name: 'Profit',
    components: {
        ProfitChart
    },

    data() {
        return {

        }
    },

    methods: {

    }
}
</script>

<style scoped>
#chart {
    font-family: "Avenir", Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
    margin-top: 60px;
}
</style>
