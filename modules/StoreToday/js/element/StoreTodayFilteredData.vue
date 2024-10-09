<template>
    <div class="filtered-data">
        <div class="filtered-data-row">
            <div class="filtered-data-row-label">
                <div>Profit {{ this.currencySymbol }}{{ this.profit.singleDay.value.toFixed(2) }}</div>
                <div>Forecast {{ this.currencySymbol }}{{ this.profit.singleDay.forecast.toFixed(2) }}</div>
            </div>
            <div class="filtered-data-items">
                <div
                    class="filtered-data-item"
                    v-for="item in profitProgress.active"
                    :key="`profile-progress-active-${item}`"
                ></div>
                <div
                    class="filtered-data-item no-active"
                    v-for="item in profitProgress.notActive"
                    :key="`profile-progress-not-active-${item}`"
                ></div>
            </div>
        </div>
        <div class="filtered-data-row">
            <div class="filtered-data-row-label">
                <div>Revenue {{ this.currencySymbol }}{{ this.revenue.singleDay.value.toFixed(2) }}</div>
                <div>Forecast {{ this.currencySymbol }}{{ this.revenue.singleDay.forecast.toFixed(2) }}</div>
            </div>
            <div class="filtered-data-items">
                <div
                    class="filtered-data-item purple"
                    v-for="item in revenueProgress.active"
                    :key="`revenue-progress-active-${item}`"
                ></div>
                <div
                    class="filtered-data-item no-active"
                    v-for="item in revenueProgress.notActive"
                    :key="`revenue-progress-not-active-${item}`"
                ></div>
            </div>
        </div>
        <div class="filtered-legend">
            <div class="filtered-legend-item">
                <div class="legend-item-icon">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="10" cy="10" r="9" stroke="url(#paint0_linear)" stroke-width="2"></circle>
                        <defs>
                            <linearGradient
                                id="paint0_linear"
                                x1="20"
                                y1="4.99995"
                                x2="0.49859"
                                y2="5.01662"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#FFD422"></stop>
                                <stop offset="1" stop-color="#FF7D05"></stop>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="legend-item-label">Profit</div>
            </div>
            <div class="filtered-legend-item">
                <div class="legend-item-icon">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="10" cy="10" r="9" stroke="url(#paint1_linear)" stroke-width="2"></circle>
                        <defs>
                            <linearGradient
                                id="paint1_linear"
                                x1="20"
                                y1="0"
                                x2="0.729167"
                                y2="0"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#4DFFDF"></stop>
                                <stop offset="1" stop-color="#4DA1FF"></stop>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="legend-item-label">Revenue</div>
            </div>
            <div class="filtered-legend-item">
                <div class="legend-item-icon">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="10" cy="10" r="9" stroke="#05050F" stroke-width="2"></circle>
                    </svg>
                </div>
                <div class="legend-item-label">Forecast</div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'StoreTodayFilteredData',
    props: {
        profit: {
            type: Object,
            required: true,
            default: () => {
                return {
                    singleDay: {
                        value: 0,
                        forecast: 0,
                    },

                    lastDays: {
                        countOfDays: 30,
                        value: 0,
                        forecast: 0,
                    },
                };
            },
        },

        revenue: {
            type: Object,
            required: true,
            default: () => {
                return {
                    singleDay: {
                        value: 0,
                        forecast: 0,
                    },

                    lastDays: {
                        countOfDays: 30,
                        value: 0,
                        forecast: 0,
                    },
                };
            },
        },

        currencySymbol: {
            type: String,
            required: true
        }
    },

    computed: {
        profitProgress: function () {

            if (this.profit.singleDay.forecast === 0) {
                return {
                    active: 0,
                    notActive: 5,
                };
            }


            if (this.profit.singleDay.value < this.profit.singleDay.forecast) {
                const result = Math.floor(this.profit.singleDay.value / (this.profit.singleDay.forecast / 5));
                const resultValue = isNaN(result) ? 0 : result;

                return {
                    active: resultValue,
                    notActive: 5 - resultValue,
                };
            }
            return {
                active: 5,
                notActive: 0,
            };

        },

        revenueProgress: function () {

            if (this.revenue.singleDay.forecast === 0) {
                return {
                    active: 0,
                    notActive: 5,
                };
            }

            if (this.revenue.singleDay.value < this.revenue.singleDay.forecast) {
                const result = Math.floor(this.revenue.singleDay.value / (this.revenue.singleDay.forecast / 5));
                const resultValue = isNaN(result) ? 0 : result;

                return {
                    active: resultValue,
                    notActive: 5 - resultValue,
                };
            }
            return {
                active: 5,
                notActive: 0,
            };

        },
    },
};
</script>

<style lang="scss" scoped>
.filtered-data {
    display: flex;
    flex-direction: column;
    padding-left: 79px;
    padding-right: 79px;
    //color: #ffffff;
    color: #000000;

    border-left-style: solid;
    border-right-style: solid;
    border-left-width: 1px;
    border-right-width: 1px;
    //border-color: rgba(255, 255, 255, 0.5);

    .filtered-data-row {
        .filtered-data-row-label {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .filtered-data-items {
            display: flex;
            flex-direction: row;

            .filtered-data-item {
                background: linear-gradient(269.95deg, #ffd422 0.02%, #ff7d05 97.45%);
                width: 94px;
                height: 12px;
                border-radius: 100px;
                margin-right: 3px;
            }

            .filtered-data-item.purple {
                background: linear-gradient(270deg, #4dffdf 0%, #4da1ff 96.35%);
            }

            .filtered-data-item.no-active {
                background: #05050f;
            }
        }
    }

    .filtered-data-row:nth-child(2) {
        margin-top: 41px;
    }

    .filtered-legend {
        display: flex;
        flex-direction: row;
        margin-top: 56px;
        .filtered-legend-item {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            .legend-item-icon {
                margin-right: 10px;
            }
        }

        .filtered-legend-item:nth-child(2) {
            margin-left: 49px;
        }
        .filtered-legend-item:nth-child(3) {
            margin-left: 49px;
        }
    }
}
</style>
