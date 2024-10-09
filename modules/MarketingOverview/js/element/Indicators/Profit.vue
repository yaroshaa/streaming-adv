<template>
    <div class="mo-indicators">
        <div class="mo-indicator-value big-value">
            <span>{{ profitRank }}</span>
            <span>Profit rank</span>
        </div>
        <div class="mo-indicator-value secondary">
            <span>{{ profitRankEstimate }}</span>
            <span>Est.profit</span>
        </div>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';

export default {
    name: 'Profit',
    data(){
        return {
            profitRank: 0,
            profitRankEstimate: 0,
        }
    },

    methods:{
        update(){
            this.profitRank = Number(this.chartData.totals.profit.value) !== 0
                // eslint-disable-next-line vue/max-len
                ? (Number(this.chartData.totals.profit.estimate) / Number(this.chartData.totals.profit.value)).toFixed(2)
                : 0;
            this.profitRankEstimate = Number(this.chartData.totals.profit.estimate).toFixed(2);
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

    .mo-indicator-value.big-value > span:nth-child(1) {
        font-family: Roboto, sans-serif;
        font-weight: bold;
        font-size: 50px;
        //color: #5EFF5A;
        color: #909399;
    }
}

</style>
