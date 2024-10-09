<!-- eslint-disable -->
<template>
    <div class="mo-indicators">
        <div>
            <div v-for="item of spendMarketingChannels"
                 class="mo-indicators-row"
                 :key="item.id"
            >
                <div class="mo-source-icon"><img :src="item.icon_link" :alt="item.name" rel="icon"/></div>
                <div>{{ item.last_15_minutes_index.toFixed(2) }} <span>/h</span></div>
            </div>
            <div v-if="other" >
                    <span class="text-center other"
                          @mouseover="toggle = false"
                          @mousemove="onMousemove"
                          @mouseout="toggle = true"
                         >Other</span>
                <div class="customTooltip" ref="customTooltip" v-if="!toggle">{{other}}</div>
            </div>
        </div>
        <div class="mo-indicator-value secondary">
            <span>{{ spendTotal }}</span>
            <span>total spend /h</span>
        </div>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';

export default {
    name: 'MarketingChannels',
    data(){
        return {
            spendMarketingChannels: [],
            arrOther: [],
            spendTotal: 0,
            other: false,
            toggle: true
        }
    },

    mounted() {

    },

    methods:{
        update(){
            this.allList = this.chartData.spend.marketing_channels.sort(this.sLastHour);
            this.spendMarketingChannels = this.allList.slice(0,3);
            if (this.allList.length > 3){
                this.arrOther = this.allList.slice(3);
                this.other = this.arrOther.map(item => Number(item.last_15_minutes_index).toFixed(2)).reduce((a, b) => Number(a) + Number(b), 0) + '/h';
            }
            this.spendTotal = this.allList.map(item => Number(item.last_15_minutes_index).toFixed(2)).reduce((a, b) => (Number(a) + Number(b)).toFixed(2), 0) + '/h';
        },

        sLastHour(a, b) {
            if (b.last_15_minutes_index > a.last_15_minutes_index) return 1;
            else if (b.last_15_minutes_index < a.last_15_minutes_index) return -1;
            return 0;
        },

        onMousemove(event) {
            this.$refs.customTooltip.style.left = `${event.pageX + 15}px`;
            this.$refs.customTooltip.style.top = `${event.pageY - 15}px`;
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

    .mo-source-icon {
        width: 16px;
        height: 16px;

        img {
            width: 100%;
            height: auto;
            margin-top: -9px;
        }
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
}

.other {
    font-family: Roboto, sans-serif;
    font-weight: bold;
    font-size: 16px;
    color: #6A6A9F;
    padding-top: 10px;
    display: inline-block;
    cursor: default;
}

.right {
    clear: both;
    text-align: left;
}

.customTooltip {
    position: absolute;
    z-index: 9999;
    border-radius: 3px;
    background: rgba(0, 0, 0, 0.6) !important;
    color: #fff !important;
    padding: 10px;
    width: auto;
}
</style>
