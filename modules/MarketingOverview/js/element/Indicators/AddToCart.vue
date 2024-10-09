<template>
    <div class="mo-indicators el-width-10">
        <doughnut-indicator :index="index" :val="addToCart30min" :title="title"></doughnut-indicator>
        <div class="mo-indicator-value secondary-small">
            <span>{{ addToCart45sek }}</span>
            <span><b>Add to cart /h</b><b>(45 sek)</b></span>
        </div>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';
import DoughnutIndicator from '../Charts/DoughnutIndicator';

export default {
    name: 'AddToCart',
    components: {
        DoughnutIndicator,
    },

    data(){
        return {
            index: 'add_to_cart',
            addToCart30min: 0,
            addToCart45sek: 0,
            title: 'Add to cart /h (30 min)',
        }
    },

    methods:{
        update(){
            this.addToCart30min = this.chartData.conversion_indicators.add_to_cart_30min;
            this.addToCart45sek = this.chartData.conversion_indicators.add_to_cart_45sek;
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
