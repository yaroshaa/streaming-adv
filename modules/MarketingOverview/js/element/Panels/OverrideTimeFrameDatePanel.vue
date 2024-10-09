<template>
    <div class="mo-override-tf-date">
        <div class="mo-override-tf-date-title">Override timeframe (y to date)</div>
        <div class="mo-override-tf-date-title secondary">
            <span>123.123,323</span>
            <span>Revenue ytd</span>
        </div>

        <div class="mo-override-tf-date-indicator-list">
            <div class="mo-override-tf-date-indicator-row">
                <div class="mo-override-tf-date-indicator-col">
                    <span>676m</span>
                    <span>Run rate (flat)</span>
                </div>
                <div class="mo-override-tf-date-indicator-col align-end value-up">
                    <span>239%</span>
                    <span>2021 vs. 2020</span>
                </div>
            </div>
            <div class="mo-override-tf-date-indicator-row">
                <div class="mo-override-tf-date-indicator-col">
                    <span>1076m</span>
                    <span>Est year (index)</span>
                </div>
                <div class="mo-override-tf-date-indicator-col align-end value-up">
                    <span>134%</span>
                    <span>2021 vs. goal</span>
                </div>
            </div>
            <div class="mo-override-tf-date-indicator-row">
                <div class="mo-override-tf-date-indicator-col">
                    <span>129m</span>
                    <span>Est spend year (index)</span>
                </div>
                <div class="mo-override-tf-date-indicator-col align-end value-up">
                    <span>12%</span>
                    <span>Est spend %</span>
                </div>
            </div>
        </div>
        <div class="mo-override-tf-date-indicator-row bottom">
            <div class="mo-override-tf-date-indicator-col">
                <span>32m</span>
                <span>Current month</span>
            </div>
            <div class="mo-override-tf-date-indicator-col align-end value-down">
                <span>89%</span>
                <span>vs. goal</span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'OverrideTimeFrameDatePanel'
}
</script>


<style lang="scss">
    .mo-override-tf-date {
        display: flex;
        flex-direction: column;
        width: 320px;
        padding: 22px;

        .mo-override-tf-date-title {
            font-family: Open Sans,sans-serif;
            font-size: 12px;
            color: #505050;
            text-align: end;
        }

        .mo-override-tf-date-title.secondary {
            display: flex;
            flex-direction: column;
            text-align: end;
        }

        .mo-override-tf-date-title.secondary > span:nth-child(1) {
            font-family: Open Sans,sans-serif;
            font-weight: 600;
            font-size: 30px;
            //color: #ffffff;
        }

        .mo-override-tf-date-title.secondary > span:nth-child(2) {
            font-family: Open Sans,sans-serif;
            font-weight: 600;
            font-size: 14px;
            //color: #ffffff;
        }

        .mo-override-tf-date-indicator-list {
            margin-top: 13px;
        }
        .mo-override-tf-date-indicator-row {
            display: flex;
            flex-direction: row;
            justify-content: space-between;

            .mo-override-tf-date-indicator-col {
                display: flex;
                flex-direction: column;
            }

            .mo-override-tf-date-indicator-col.align-end {
                text-align: end;
            }

            .mo-override-tf-date-indicator-col.value-up {
                //color: #5EFF5A;
                color: #909399;
            }

            .mo-override-tf-date-indicator-col.value-down {
                //color: #F01D1D;
                color: #909399;
            }

            .mo-override-tf-date-indicator-col > span:nth-child(1) {
                font-family: Open Sans,sans-serif;
                font-weight: 600;
                font-size: 20px;
            }

            .mo-override-tf-date-indicator-col > span:nth-child(2) {
                font-family: Open Sans,sans-serif;
                font-size: 14px;
                color: #6A6A9F;
            }
        }

        .mo-override-tf-date-indicator-row.bottom {

            border-top-style: solid;
            border-top-color: #000000;
            border-top-width: 1px;
            margin-top: 19px;

        }

    }
</style>
