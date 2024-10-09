<template>
    <div class="filter-row">
        <div class="filter-label">
            <div class="filter-radio-button" @click="(event) => handleChangeRadioButton(event, this.index, this.slug)">
                <svg
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    v-if="active"
                >
                    <circle cx="12" cy="12" r="7.5" :stroke="colorPrimary" stroke-width="9"></circle>
                </svg>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" v-else>
                    <circle cx="12" cy="12" r="11" stroke="#24244B" stroke-width="2"></circle>
                </svg>
            </div>
            <div class="filter-label-text">{{ label }}:</div>
        </div>
        <div class="filter-value" v-if="slugPosition === 'left'">{{ slug }}{{ value }}</div>
        <div class="filter-value" v-else-if="slugPosition === 'right'">{{ value }}{{ slug }}</div>
        <div class="filter-value" v-else>
            {{ value }}
        </div>
    </div>
</template>

<script>
import * as d3 from 'd3';
import {mapActions, mapGetters} from 'vuex';
import styles from '@/../style/element/_variables.scss';

export default {
    name: 'StoreTodayFilterRow',
    props: {
        index: {
            type: String,
            required: true,
        },

        label: {
            type: String,
            required: true,
        },

        value: {
            type: [String, Number],
            required: true,
        },

        slug: {
            type: String,
            required: false,
            default: null,
        },

        slugPosition: {
            type: String,
            required: false,
            default: null,
        },

        active: {
            type: Boolean,
            required: false,
            default: false,
        },
    },

    methods: {
        ...mapActions({
            updateData: 'storeToday/updateData',
        }),

        handleChangeRadioButton(event, index, symbol) {

            const data = [];
            this.srcData.map((item) => {
                data.push({
                    date: item.date,
                    value: item[index],
                    symbol
                })
            });

            this.updateData(data);

            d3.selectAll('.filter-radio-button')
                .select('svg')
                .select('circle')
                .attr('r', '11')
                .attr('stroke', '#24244B')
                .attr('stroke-width', '2');

            d3.select(event.currentTarget)
                .select('svg')
                .select('circle')
                .attr('r', '7.5')
                .attr('stroke', styles.colorPrimary)
                .attr('stroke-width', '9');
        },
    },

    computed: {
        colorPrimary: () => styles.colorPrimary,
        ...mapGetters({
            getSrcData: 'storeToday/getSrcData',
        }),

        srcData: {
            get() {
                return this.getSrcData;
            }
        }
    }
};
</script>

<style lang="scss" scoped>
.filter-row {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    font-family: Open Sans, serif;
    align-items: center;

    font-size: 16px;
    line-height: 18px;
    letter-spacing: -0.44px;

    margin-bottom: 33px;
    .filter-label {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        .filter-radio-button {
            width: 24px;
            height: 24px;
            margin-right: 13px;
            cursor: pointer;
            /*border-style: solid;*/
            /*border-width: 1px;*/
        }
        /*border-style: solid;*/
        /*border-width: 1px;*/
    }
    .filter-value {
        font-weight: bold;
        /*border-style: solid;*/
        /*border-width: 1px;*/
    }
}
</style>
