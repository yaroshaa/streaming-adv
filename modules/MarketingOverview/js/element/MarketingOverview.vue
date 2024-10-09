<template>
    <div class="mo-container">
        <portal to="header-filter">
            <filters-panel></filters-panel>
        </portal>
        <div class="mo-panels">
            <div class="mo-col-left">
                <div class="mo-panel-row">
                    <div>
                        <main-panel></main-panel>
                    </div>
                    <div>
                        <indicators-panel></indicators-panel>
                    </div>
                </div>
                <div class="mo-bottom-indicators">
                    <stores-ratio-panel></stores-ratio-panel>
                    <cities-indicator-panel></cities-indicator-panel>
                    <override-time-frame-date-panel></override-time-frame-date-panel>
                </div>
            </div>
            <div class="mo-col-right">
                <override-time-frame-today-panel :data="data"></override-time-frame-today-panel>
                <events-dates-panel></events-dates-panel>
            </div>
        </div>
    </div>
</template>

<script>

import {Portal} from 'portal-vue';
import CitiesIndicatorPanel from './Panels/CitiesIndicatorPanel';
import FiltersPanel from './Panels/FiltersPanel';
import IndicatorsPanel from './Panels/IndicatorsPanel';
import MainPanel from './Panels/MainPanel';
import OverrideTimeFrameDatePanel from './Panels/OverrideTimeFrameDatePanel';
import OverrideTimeFrameTodayPanel from './Panels/OverrideTimeFrameTodayPanel';
import EventsDatesPanel from './Panels/EventsDatesPanel';
import {mapActions, mapGetters} from 'vuex';
import StoresRatioPanel from './Panels/StoresRatioPanel';
import qs from 'qs';
import EventBus from '@/socket/eventbus';
import {MARKETING_OVERVIEW_UPDATED_EVENT} from '@/element/components/MarketingOverview/events';

export default {
    name: 'MarketingOverview',
    components: {
        Portal,
        StoresRatioPanel,
        CitiesIndicatorPanel,
        FiltersPanel,
        IndicatorsPanel,
        MainPanel,
        OverrideTimeFrameDatePanel,
        OverrideTimeFrameTodayPanel,
        EventsDatesPanel
    },

    mounted() {
        EventBus.on(
            MARKETING_OVERVIEW_UPDATED_EVENT,
            () => {
                console.log('Emit received');
                this.update();
            },
            this.$options.name
        );
        this.update();
    },

    beforeDestroy() {
        EventBus.off(MARKETING_OVERVIEW_UPDATED_EVENT);
    },

    methods: {
        ...mapActions({
            setData: 'marketingOverview/setData',
        }),

        update() {

            this.axios
                // .get('/marketing-overview/deprecated/data', {
                //         params: {
                //             filter:{
                //                 currency: this.storageCurrency,
                //                     date: this.storageDate,
                //                     date_granularity: 'Daily'
                //             }
                //         },
                .get('/marketing-overview/data', {
                    params: {
                        currency: this.storageCurrency,
                        date: this.storageDate,
                        date_granularity: 'Daily'
                    },
                    paramsSerializer: params => {
                        return qs.stringify(params)
                    }
                })
                .then((event) => {
                    this.setData(event.data.data);
                }).finally(() => {
                    EventBus.unlock(MARKETING_OVERVIEW_UPDATED_EVENT, this.$options.name);
                });
        },
    },

    computed: {
        ...mapGetters({
            storageCurrency: 'filters/currency',
            storageDate: 'filters/date',
            data: 'marketingOverview/data',
        }),
    },

    watch: {
        storageCurrency() {
            this.update();
        },

        storageDate() {
            this.update();
        },
    },

};
</script>

<style lang="scss" scoped>
.mo-container {
    //color: white;
    display: flex;
    flex-direction: column;
    .mo-panels {
        display: flex;
        flex-direction: row;
        justify-content: space-between;

        .mo-col-left {
            display: flex;
            flex-direction: column;
            width: 1338px;

            .mo-bottom-indicators {
                display: flex;
                flex-direction: row;
                justify-content: space-between;
            }

            .mo-bottom-indicators > div {
                //background-color: #191932;
                border: 1px solid #000000;
                box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
                border-radius: 8px;
            }

            .mo-bottom-indicators > div:nth-child(1) {
                width: 759px;
            }
        }

        .mo-col-left > div:nth-child(1) {
            //background-color: #191932;
            border: 1px solid #000000;
            box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
            border-radius: 8px;
        }

        .mo-col-right > div {
            //background-color: #191932;
            border: 1px solid #000000;
            box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
            border-radius: 8px;
        }
    }

    .mo-panel-row > div {
        margin-bottom: 15px;
    }
}
</style>
