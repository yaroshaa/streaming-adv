<template>
    <div class="mo-cities">
        <div>
            <div class="mo-city">Vestby</div>
            <doughnut-chart></doughnut-chart>
            <div class="mo-city-indicator">
                <span>920</span>
                <span>In packing</span>
            </div>
            <div class="mo-city-indicator">
                <span>4,512</span>
                <span>Open</span>
            </div>
            <div class="mo-city-indicator">
                <span>7,074</span>
                <span>Awaiting stock</span>
            </div>
        </div>
        <div>
            <div class="mo-city">BORÅS</div>
            <doughnut-chart></doughnut-chart>
            <div class="mo-city-indicator">
                <span>120</span>
                <span>In packing</span>
            </div>
            <div class="mo-city-indicator">
                <span>2,386</span>
                <span>Open</span>
            </div>
            <div class="mo-city-indicator">
                <span>0</span>
                <span>Awaiting stock</span>
            </div>
        </div>
    </div>
</template>

<script>
import DoughnutChart from '../Charts/DoughnutChart';

export default {
    name: 'CitiesIndicatorPanel',
    components: {
        DoughnutChart
    }
}
</script>

<style lang="scss" scoped>
    .mo-cities {
        display: flex;
        flex-direction: row;
        .mo-city {
            text-transform: uppercase;
            font-family: Roboto,sans-serif;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
        }
        .mo-city-indicator {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .mo-city-indicator > span:nth-child(1) {
            font-family: Open Sans,serif;
            font-weight: 600;
            font-size: 20px;
        }

        .mo-city-indicator > span:nth-child(2) {
            font-family: Open Sans,serif;
            font-size: 14px;
            color: #6A6A9F;
        }
    }

    .mo-cities > div {
        padding: 21px;
    }
</style>
