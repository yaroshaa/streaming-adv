<template>
    <div class="events-dates">
        <div v-for="(date, index) in dates" :key="index" class="before-date">
            {{ date.days_before }} {{ date.days_before === 1 ? 'day' : 'days' }} until {{ date.title }}
        </div>
    </div>
</template>

<script>

import {mapGetters} from 'vuex';

export default {
    name: 'EventsDatesPanel',
    components: {

    },

    data() {
        return {
            dates: []
        };
    },

    computed: {
        ...mapGetters({
            marketingData: 'marketingOverview/data',
        }),
    },

    watch: {
        marketingData() {
            if (this.marketingData.events.length) {
                this.dates = this.marketingData.events.splice(0, 4);
            }
        },
    }
}
</script>

<style scoped lang="scss">
.mo-container .mo-panels .mo-col-right {
    .events-dates {
        font-family: Open Sans;
        font-size: 16px;
        line-height: 24px;
        letter-spacing: -0.44px;
        display: flex;
        justify-content: space-around;
        align-items: center;
        flex-wrap: wrap;
        background-color: transparent;
        border: none;
        box-shadow: none;

        .before-date {
            width: 50%;
            text-align: right;
        }
    }
}



</style>
