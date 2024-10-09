<template>
    <div class="mo-indicators">
        <doughnut-indicator :index="index" :val="orders" :title="title"></doughnut-indicator>
        <div class="mo-indicator-value secondary-small">
            <span>{{ orders45sek }}</span>
            <span><b>Orders /h (45 sek)</b></span>
        </div>
    </div>
</template>

<script>
import DoughnutIndicator from '../Charts/DoughnutIndicator';
import {mapGetters} from 'vuex';

export default {
    name: 'Orders',
    components: {
        DoughnutIndicator,
    },

    data(){
        return {
            index: 'orders_30min',
            orders: 0,
            orders45sek: 0,
            title: 'Orders /h (30 min)'
        }
    },

    methods:{
        update(){
            this.orders = Number(this.chartData.conversion_indicators.orders_30min.toFixed(0));
            this.orders45sek = Number(this.chartData.conversion_indicators.orders_45sek.value.toFixed(0));
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
