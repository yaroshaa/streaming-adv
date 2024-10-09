<template>
    <div class="mo-indicators">
        <pie-chart :index="index"></pie-chart>
        <div class="mo-indicator-value">
            <span v-text="revenueEst"></span>
            <span>Est. Revenue</span>
        </div>
        <div class="mo-indicator-value secondary">
            <span v-text="revenueVal"></span>
            <span>Revenue</span>
        </div>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';
import PieChart from '../Charts/PieChart';

export default {
    name: 'Revenue',
    components: {
        PieChart,
    },

    data(){
        return {
            index:'revenue',
            revenueEst: 0,
            revenueVal: 0,
        }
    },

    methods:{
        update(){
            this.revenueEst = this.chartData.totals.revenue.estimate.toFixed(0);
            this.revenueVal = this.chartData.totals.revenue.value.toFixed(0);
        },
    },

    computed: {
        ...mapGetters({
            storageData: 'marketingOverview/data',
        }),

        chartData() {
            return this.storageData;
        },
    },

    watch:{
        storageData(){
            this.update();
        }
    },
}
</script>

<style lang="scss" scoped>
.mo-indicators {
    display: flex;
    flex-direction: column;
    justify-content: space-between;

    .mo-indicator-value {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .mo-indicator-value > span:nth-child(1) {
        font-family: Roboto, sans-serif;
        font-weight: bold;
        font-size: 26px;
    }

    .mo-indicator-value > span:nth-child(2) {
        font-family: Open Sans, sans-serif;
        font-size: 14px;
        color: #6A6A9F;
    }

    .mo-indicator-value.secondary > span:nth-child(1) {
        font-family: Roboto, sans-serif;
        font-weight: bold;
        font-size: 22px;
        color: #A1A1A2;
    }

    .mo-indicator-value.secondary > span:nth-child(2) {
        font-family: Open Sans, sans-serif;
        font-size: 14px;
        color: #6A6A9F;
    }

    .mo-indicator-value.secondary-small > span:nth-child(1) {
        font-family: Roboto, sans-serif;
        font-weight: bold;
        font-size: 22px;
        color: #A1A1A2;
    }

    .mo-indicator-value.secondary-small > span:nth-child(2) {
        font-family: Roboto, sans-serif;
        font-style: normal;
        font-weight: bold;
        font-size: 8px;
        color: #6A6A9F;
    }
}
</style>
