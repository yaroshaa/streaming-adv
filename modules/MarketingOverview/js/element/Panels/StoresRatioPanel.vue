<template>
    <div class="stores-ratio-panel">
        <div class="charts">
            <el-tabs v-model="activeTab" class="ratio-area">
                <el-tab-pane label="%SPEND" name="SPEND">
                    <line-chart :chart-name="spendRatio" :data="spendRatioData"></line-chart>
                </el-tab-pane>
                <el-tab-pane label="%CM" name="CM">
                    <line-chart :chart-name="contributionMargin" :data="contributionMarginData"></line-chart>
                </el-tab-pane>
                <el-tab-pane label="CR" name="CR">
                    <line-chart :chart-name="conversionRate" :data="conversionRateData"></line-chart>
                </el-tab-pane>
            </el-tabs>
            <div class="revenue-area">
                <div class="title">Revenue</div>
                <revenue-chart :data="revenueData"></revenue-chart>
            </div>
        </div>
        <percentages-panel :stores="stores"></percentages-panel>
    </div>
</template>

<script>
import {Tabs, TabPane} from 'element-ui';
import LineChart from '@/element/components/MarketingOverview/Charts/LineChart';
import RevenueChart from '@/element/components/MarketingOverview/Charts/RevenueChart';
import PercentagesPanel from '@/element/components/MarketingOverview/Panels/PercentagesPanel';
import {mapGetters} from 'vuex';

export default {
    name: 'StoresRatioPanel',

    components: {
        LineChart,
        RevenueChart,
        PercentagesPanel,
        ElTabs: Tabs,
        ElTabPane: TabPane,
    },

    data() {
        return {
            activeTab: 'CR',
            spendRatio: 'spend_ratio',
            contributionMargin: 'contribution_margin',
            conversionRate: 'conversion_rate',
            revenue: 'revenue',
        }
    },

    computed: {
        ...mapGetters({
            marketingData: 'marketingOverview/data',
        }),

        stores() {
            const {stores = []} = this.marketingData;

            return stores;
        },

        spendRatioData() {
            return this.stores.map((store) => {
                return store[this.spendRatio].per_hour || [];
            })
        },

        contributionMarginData() {
            return this.stores.map((store) => {
                return store[this.contributionMargin].per_hour || [];
            })
        },

        conversionRateData() {
            return this.stores.map((store) => {
                return store[this.conversionRate].per_hour || [];
            })
        },

        revenueData() {
            let {stores = [], totals = {}} = this.marketingData;

            stores = stores.map((store) => {
                return store[this.revenue].per_hour || [];
            });

            totals = totals[this.revenue] ? totals[this.revenue].per_hour_estimate_prev_week_day : [];

            return {stores, totals}
        },
    },
}
</script>

<style lang="scss">
.stores-ratio-panel {
    display: flex;
    justify-content: space-between;

    .revenue-area {
        position: relative;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;

        .title {
            position: absolute;
            left: 0;
            transform: rotate(-90deg);
        }
    }

    .ratio-area {
        .el-tabs__nav-wrap.is-top {
            -webkit-transform-origin: left top;
            -webkit-transform: rotate(
                    -90deg
            ) translateX(-28.5%);
        }
    }

    .el-tabs__active-bar.is-top,
    .el-tabs--top .el-tabs__nav-wrap.is-top::after {
        display: none;
    }

    .el-tabs__header {
        height: 0;
        margin: 0;
        z-index: 100;
    }

    .el-tabs__active-bar {
        display: none;
    }

    .el-tabs__item {
        color: $--color-white;
        padding: 0 10px;

        &:hover {
            color: $--color-white;
        }

        &.is-active {
            color: $--color-white;
            background-color: $--background-color-blackly;
            border-radius: 7px 7px 0 0;
        }
    }

    .el-tabs--top .el-tabs__item.is-top:last-child {
        padding: 0 10px;
    }
}
</style>
