<template>
    <div class="mo-indicators-container">
        <div class="mo-indicators-bar-chart">
            <cmam-chart></cmam-chart>
        </div>
        <div class="mo-indicators-charts">
            <div class="mo-indicators-group mo-indicators-group-300">
                <revenue></revenue>
                <cmam></cmam>
            </div>
            <div class="mo-indicators-group mo-indicators-group-300">
                <cm-ratio></cm-ratio>
                <spend-ratio></spend-ratio>
            </div>
            <div class="mo-indicators-group mo-indicators-group-155">
                <profit></profit>
            </div>
            <div class="mo-indicators-group mo-indicators-group-155">
                <marketing-channels></marketing-channels>
            </div>
            <div class="mo-indicators-group justify-content-center align-items-center">
                <total-cpo></total-cpo>
                <span class="ml-2 mr-2 coof">{{ cartsToUsers }}</span>
                <add-to-cart></add-to-cart>
                <span class="ml-2 mr-2 coof">{{ ordersToCarts }}</span>
                <orders></orders>
            </div>
        </div>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';
import CmamChart from '../Charts/CmamChart';
import Revenue from '../Indicators/Revenue';
import Cmam from '../Indicators/Cmam';
import CmRatio from '../Indicators/CmRatio';
import SpendRatio from '../Indicators/SpendRatio';
import Profit from '../Indicators/Profit';
import MarketingChannels from '../Indicators/MarketingChannels';
import TotalCpo from '../Indicators/TotalCpo';
import AddToCart from '../Indicators/AddToCart';
import Orders from '../Indicators/Orders';

export default {
    name: 'IndicatorsPanel',
    components: {
        CmamChart,
        AddToCart,
        Revenue,
        Cmam,
        CmRatio,
        SpendRatio,
        Profit,
        MarketingChannels,
        TotalCpo,
        Orders,
    },

    data(){
        return {
            activeUsers: 0,
            addToCart30min: 0,
            orders30min: 0,
            cartsToUsers:0,
            ordersToCarts:0,
        }
    },

    methods:{
        update(){
            this.activeUsers = this.chartData.conversion_indicators.active_users;
            this.addToCart30min = this.chartData.conversion_indicators.add_to_cart_30min;
            this.orders30min = this.chartData.conversion_indicators.orders_30min;
            this.cartsToUsers = this.getCartsToUsers();
            this.ordersToCarts = this.getOrdersToCarts();
        },

        getCartsToUsers() {
            return ((this.addToCart30min && this.addToCart30min !== 0) &&
                (this.activeUsers && this.activeUsers !== 0))
                ? (this.addToCart30min / this.activeUsers).toFixed(2)
                : 0;
        },

        getOrdersToCarts() {
            return ((this.orders30min && this.orders30min !== 0) &&
                (this.addToCart30min && this.addToCart30min !== 0))
                ? (this.orders30min / this.addToCart30min).toFixed(2)
                : 0;
        }
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
};
</script>

<style lang="scss" scoped>
.mo-indicators-container {
    display: flex;
    flex-direction: column;

    .mo-indicators-bar-chart {
        margin-bottom: 35px;
    }

    .mo-indicators-charts {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        & :last-child {
            border-right: none;
        }
    }

    .mo-indicators-group {
        display: flex;
        flex-direction: row;
        border-right-style: solid;
        border-right-width: 2px;
        border-right-color: #000000;
        justify-content: space-around;
        height: 184px;
        padding: 10px;

        .coof{
            font-weight: bold;
            display: inline-block !important;
            height: auto;
            width: auto;
            margin-top: -70px;
            text-align: center;
            font-size: 0.7rem;

        }
    }

    .mo-indicators-group-300 {
        width: 300px;
    }

    .mo-indicators-group-136 {
        width: 136px;
    }

    .mo-indicators-group-155 {
        width: 155px;
    }
}
</style>
