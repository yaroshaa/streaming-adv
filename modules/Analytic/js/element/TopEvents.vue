<template>
    <div id="chart">
        <h1>Top Events</h1>
        <TopEventsChart></TopEventsChart>
    </div>
</template>

<script>
import TopEventsChart from '@/element/components/Analytic/components/TopEventsChart';

export default {
    name: 'TopEvents',
    components: {
        TopEventsChart
    },

    data() {
        return {

        }
    },
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
