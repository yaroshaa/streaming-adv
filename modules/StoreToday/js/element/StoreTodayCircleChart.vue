<template>
    <div class="filtered-chart">
        <div class="filtered-circle-chart-container"></div>
        <div class="filtered-chart-rank">
            <div class="filtered-chart-data-label">
                RANK {{ this.profit.count_days }} DAYS
            </div>
            <div class="filtered-chart-data-value">
                {{ this.rank }}
            </div>
        </div>
        <div class="filtered-chart-legend">
            <div class="chart-legend-row">
                <div class="legend-profit-icon">
                    <svg
                        width="13"
                        height="14"
                        viewBox="0 0 13 14"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <circle
                            cx="6.46533"
                            cy="6.63769"
                            r="5.46533"
                            stroke="url(#icon_profile_linear)"
                            stroke-width="2"
                        ></circle>
                        <defs>
                            <linearGradient
                                id="icon_profile_linear"
                                x1="12.9307"
                                y1="3.405"
                                x2="0.322355"
                                y2="3.41577"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#FFD422"></stop>
                                <stop offset="1" stop-color="#FF7D05"></stop>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="legend-label">
                    Profit
                </div>
                <div class="legend-value">
                    {{ this.profit.value.toFixed(2) }}
                </div>
            </div>
            <div class="chart-legend-row">
                <div>
                    <svg
                        width="13"
                        height="14"
                        viewBox="0 0 13 14"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <circle
                            cx="6.46533"
                            cy="6.63818"
                            r="5.46533"
                            stroke="url(#icon_revenue)"
                            stroke-width="2"
                        ></circle>
                        <defs>
                            <linearGradient
                                id="icon_revenue"
                                x1="12.9307"
                                y1="0.172852"
                                x2="0.47143"
                                y2="0.172852"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#4DFFDF"></stop>
                                <stop offset="1" stop-color="#4DA1FF"></stop>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="legend-label">
                    Revenue
                </div>
                <div class="legend-value">
                    {{ this.revenue.value.toFixed(2) }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import * as d3 from 'd3';

export default {
    name: 'StoreTodayCircleChart',

    props: {

        profit: {
            type: Object,
            required: true,
            default: () => {
                return {
                    count_days: 30,
                    value: 0,
                    forecast: 0,
                }
            }
        },

        revenue: {
            type: Object,
            required: true,
            default: () => {
                return {
                    count_days: 30,
                    value: 0,
                    forecast: 0,
                }
            }
        }
    },

    methods: {
        render() {
            const width = 236;
            const height = 236;
            d3.select('.filtered-circle-chart-container').selectAll('*').remove();

            let svg = d3
                .select('.filtered-circle-chart-container')
                .append('svg')
                .attr('width', width)
                .attr('height', height);

            const profitChartValueInRadians =
                (this.profitChartValue * 3.6 * Math.PI) / 180;

            const revenueChartValueInRadians =
                (this.revenueChartValue * 3.6 * Math.PI) / 180;

            // add an arc
            svg.append('path')
                .attr('transform', 'translate(118, 118)')
                .attr(
                    'd',
                    d3
                        .arc()
                        .innerRadius(110)
                        .outerRadius(118)
                        .startAngle(Math.PI)
                        .endAngle(Math.PI + revenueChartValueInRadians)
                )
                .attr('stroke', 'black')
                .attr('fill', '#4da2ff');

            svg.append('path')
                .attr('transform', 'translate(118, 118)')
                .attr(
                    'd',
                    d3
                        .arc()
                        .innerRadius(92)
                        .outerRadius(100)
                        .startAngle(Math.PI)
                        .endAngle(Math.PI + profitChartValueInRadians)
                )
                .attr('stroke', 'black')
                .attr('fill', '#ff7d05');
        }
    },

    computed: {
        rank: function() {
            if (this.profit.forecast !== 0) {
                return (this.profit.value / this.profit.forecast).toFixed(2)
            }

            return 0;
        },

        profitChartValue: function () {

            if (this.profit.value !== 0 && this.profit.forecast !== 0) {
                return this.profit.value / (this.profit.forecast / 100)
            }

            return 0;
        },

        revenueChartValue: function () {

            if (this.revenue.value !== 0 && this.revenue.forecast !== 0) {
                return this.revenue.value / (this.revenue.forecast / 100)
            }

            return 0;
        }
    },

    watch: {
        profit: function () {
            this.render();
        },

        revenue: function () {
            this.render();
        }
    }

};
</script>

<style lang="scss" scoped>
.filtered-chart {
    padding-left: 62px;
    .filtered-circle-chart-container {
        width: 228px;
        height: 228px;
    }

    .filtered-chart-rank {
        position: relative;
        top: -114px - 34px;
        text-align: center;
        .filtered-chart-data-label {
            font-family: Open Sans, serif;
            font-weight: bold;
            font-size: 10px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #5a5a89;
        }
        .filtered-chart-data-value {
            font-family: Open Sans, serif;
            font-weight: bold;
            font-size: 32px;
            letter-spacing: -0.75px;
            //color: #ffffff;
            color: #000000;
        }
    }

    .filtered-chart-legend {
        display: flex;
        flex-direction: column;
        color: #ffffff;
        position: relative;
        bottom: 114px;
        left: 114px + 34px;
        background-color:rgba(0, 0, 0, 0.5);
        padding: 5px;
        .chart-legend-row {
            display: flex;
            flex-direction: row;
            div:nth-child(2) {
                margin-left: 7px;
                width: 60px;
            }
            div:nth-child(3) {
                margin-left: 50px;
            }
        }
    }
}
</style>
