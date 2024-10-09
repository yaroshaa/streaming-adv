<template>
    <div class="mo-indicators">
        <gauge :index="index" @custom="customHandler"></gauge>
        <div class="mo-indicator-value">
            <span :class="setColor">{{ spendRatio }}%</span>
            <span>Spend Ratio</span>
        </div>
        <div class="mo-indicator-value secondary">
            <span>{{ spendRatioLastHour }}% <span class="percent"><span v-if="spendRatioLast30Minutes > 0">+</span>{{ spendRatioLast30Minutes }}</span></span>
            <span>last hour</span>
        </div>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';
import Gauge from '../Charts/Gauge';

export default {
    name: 'SpendRatio',
    components: {
        Gauge,
    },

    data(){
        return {
            index:'spend_ratio',
            spendRatio: 0,
            spendRatioLastHour: 0,
            spendRatioLast30Minutes: 0,
            textGreen: 'text-green',
            textRed: 'text-red',
            danger: null
        }
    },

    methods:{
        update(){
            this.spendRatio = this.chartData.totals.spend_ratio.value.toFixed(0);
            this.threshold = this.chartData.totals.spend_ratio.value.toFixed(0);
            this.spendRatioLastHour = this.chartData.totals.spend_ratio.last_hour.toFixed(0);
            this.spendRatioLast30Minutes = this.chartData.totals.spend_ratio.last_30_minutes.toFixed(0);
        },

        customHandler (event) {
            this.danger = event.danger;
        }
    },

    computed: {
        ...mapGetters({
            storageData: 'marketingOverview/data',
        }),

        chartData() {
            return this.storageData;
        },

        setColor() {
            return this.danger ? this.textRed : this.textGreen;
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
        justify-content: space-between;
        height: 184px;
        padding: 10px;

        .mo-indicators {
            display: flex;
            flex-direction: column;
            justify-content: space-between;

            .mo-source-icon {
                width: 16px;
                height: 16px;
                background-color: #fff6a1;
            }

            .mo-indicators-row {
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }

            .mo-indicator-value {
                display: flex;
                flex-direction: column;
                align-items: center;

                .text-green {
                    //color: #5EFF5A;
                    color: #909399;
                }

                .text-red {
                    color: #FF4343;
                }
            }

            .mo-indicator-value > span:nth-child(1) {
                font-family: Roboto,sans-serif;
                font-weight: bold;
                font-size: 26px;
            }

            .mo-indicator-value > span:nth-child(2) {
                font-family: Open Sans,sans-serif;
                font-size: 14px;
                color: #6A6A9F;
            }

            .mo-indicator-value.secondary > span:nth-child(1) {
                font-family: Roboto,sans-serif;
                font-weight: bold;
                font-size: 22px;
                color: #A1A1A2;

                .percent {
                    font-family: Roboto,sans-serif;
                    font-weight: bold;
                    font-size: 11px;
                    //color: #5EFF5A;
                    color: #909399;
                }
            }

            .mo-indicator-value.secondary > span:nth-child(2) {
                font-family: Open Sans,sans-serif;
                font-size: 14px;
                color: #6A6A9F;
            }

            .mo-indicator-value.secondary-small > span:nth-child(1) {
                font-family: Roboto,sans-serif;
                font-weight: bold;
                font-size: 22px;
                color: #A1A1A2;
            }

            .mo-indicator-value.secondary-small > span:nth-child(2) {
                font-family: Roboto,sans-serif;
                font-style: normal;
                font-weight: bold;
                font-size: 8px;
                color: #6A6A9F;
            }

            .mo-indicator-value.profit-rank > span:nth-child(1) {
                font-family: Roboto,sans-serif;
                font-weight: bold;
                font-size: 50px;
                //color: #5EFF5A;
                color: #909399;
            }

            .mo-indicator-value.profit-rank > span:nth-child(2) {
                font-family: Open Sans,sans-serif;
                font-size: 14px;
                color: #6A6A9F;
            }
        }

        .coof{
            font-weight: bold;
            font-size: 14px;
            display: inline-block !important;
            height: auto;
            margin-top: -40px;
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
