<template>
    <div id="chart">
        <h1>Promo Clicks</h1>
        <AreaChart></AreaChart>
    </div>
</template>

<script>
import AreaChart from '@/element/components/Analytic/components/AreaChart';


export default {
    name: 'Analytic',
    components: {
        AreaChart,
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
