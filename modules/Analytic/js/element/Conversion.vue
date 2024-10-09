<template>
    <div id="chart">
        <h1>Conversion</h1>
        <ConversionChart></ConversionChart>
    </div>
</template>

<script>
import ConversionChart from '@/element/components/Analytic/components/ConversionChart';

export default {
    name: 'Conversion',
    components: {
        ConversionChart
    },

    data() {
        return {
            number: 0
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
