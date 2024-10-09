<template>
    <div class="mo-indicators">
        <doughnut-indicator :index="index" :val="activeUsers" :title="title"></doughnut-indicator>
        <div class="mo-indicator-value secondary-small">
            <span>{{ totalCpo }}</span>
            <span><b>total CPO</b></span>
        </div>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';
import DoughnutIndicator from '../Charts/DoughnutIndicator';

export default {
    name: 'TotalCpo',
    components: {
        DoughnutIndicator,
    },

    data(){
        return {
            index: 'active_users',
            activeUsers: 0,
            totalCpo: 0,
            title: 'Active users \n right now'
        }
    },

    methods:{
        update(){
            this.activeUsers = this.chartData.conversion_indicators.active_users;
            this.totalCpo = this.chartData.conversion_indicators.total_cpo.toFixed(2);
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

    .mo-indicator-value.secondary-small > span:nth-child(1) {
        font-family: Roboto, sans-serif;
        font-weight: bold;
        font-size: 22px;
        color: #A1A1A2;
    }

    .mo-indicator-value.secondary-small > span:nth-child(2) {
        height: 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        b {
            font-family: Roboto, sans-serif;
            font-style: normal;
            font-weight: bold;
            font-size: 10px;
            color: #6A6A9F;
        }
    }
}

</style>

