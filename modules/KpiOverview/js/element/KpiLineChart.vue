<template>
    <div
        id="line-chart"
        class="chart-container"
        @data:updated="update"
        v-show="!(this.dataList.length === 2 && this.isCompared)"
        v-loading="loading"
        element-loading-text="Loading..."
        element-loading-spinner="el-icon-loading"
        element-loading-background="rgba(0, 0, 0, 0.4)"
    ></div>
</template>

<script>
import * as d3 from 'd3';
import {mapGetters} from 'vuex';

export default {
    name: 'KpiLineChart',

    data() {
        return {
            svg: null,
            margin: {top: 50, right: 50, bottom: 80, left: 85},
            width: 0,
            height: 0,
            updated: false,
        };
    },

    props: {
        loading: {
            type: Boolean,
            required: true,
            default: false,
        },
    },

    mounted() {
        const container = document.getElementById('line-chart');
        this.width =
            container.clientWidth - this.margin.left - this.margin.right;
        this.height = 400 - this.margin.top - this.margin.bottom;

        this.svg = d3
            .select('#line-chart')
            .append('svg')
            .attr('width', '100%')
            .attr('height', this.height + this.margin.top + this.margin.bottom)
            .append('g')
            .attr('width', '100%')
            .attr(
                'transform',
                `translate(${this.margin.left}, ${this.margin.top})`
            );
    },

    methods: {
        socketUpdate() {
            d3.selectAll('.text-label').transition().duration(200).remove();

            this.svg.selectAll('g.xAxis').call(d3.axisBottom(this.rangeX));

            this.svg.selectAll('g.yAxis').call(
                d3
                    .axisLeft(this.rangeY)
                    .tickFormat(
                        (value) => `${this.currencyFilter.code}${value}`
                    )
                    .ticks(5)
            );

            // draw vertical lines for grid
            d3.selectAll('g.xAxis g.tick')
                .append('line')
                .attr('class', 'gridline')
                .attr('x1', 0)
                .attr('y1', -this.height)
                .attr('x2', 0)
                .attr('y2', 0)
                .attr('stroke', '#6A6A9F');

            this.svg
                .selectAll('.line')
                .datum(this.chartData)
                .transition()
                .duration(500)
                .attr(
                    'd',
                    d3
                        .line()
                        .x((row) => this.rangeX(row.date))
                        .y((row) => this.rangeY(row.value))
                )
                .end();

            // draw points labels
            for (let i = 0; i < this.chartData.length; i++) {
                let labelY = this.rangeY(this.chartData[i].value);

                if (i + 1 < this.chartData.length) {
                    labelY =
                        this.rangeY(this.chartData[i + 1].value) >=
                        this.rangeY(this.chartData[i].value)
                            ? this.rangeY(this.chartData[i].value) - 10
                            : this.rangeY(this.chartData[i].value - 35);
                }
                this.svg
                    .append('text')
                    .datum(this.chartData)
                    .attr('class', 'text-label')
                    .attr('font-style', 'Open Sans')
                    .attr('font-weight', '600')
                    .attr('font-size', '14px')
                    .attr('x', this.rangeX(this.chartData[i].date))
                    .attr('y', labelY)
                    .attr('fill', '#ffffff')
                    .text(() => {
                        return `${this.currencyFilter.code}${this.chartData[i].value}`;
                    });
            }
        },

        update() {
            if (this.updated) {
                this.socketUpdate();
                return;
            }

            d3.selectAll('.text-label').transition().duration(200).remove();

            this.svg
                .append('g')
                .attr('class', 'xAxis')
                .attr('transform', `translate(0, ${this.height})`)
                .call(d3.axisBottom(this.rangeX));

            this.svg
                .append('g')
                .attr('class', 'yAxis')
                .call(
                    d3
                        .axisLeft(this.rangeY)
                        .tickFormat(
                            (value) => `${this.currencyFilter.code}${value}`
                        )
                        .ticks(5)
                );

            // draw vertical lines for grid
            d3.selectAll('g.xAxis g.tick')
                .append('line')
                .attr('class', 'gridline')
                .attr('x1', 0)
                .attr('y1', -this.height)
                .attr('x2', 0)
                .attr('y2', 0)
                .attr('stroke', '#6A6A9F');

            // draw top horizontal line
            d3.select('.xAxis')
                .append('line')
                .attr('x1', 0)
                .attr('y1', -this.height)
                .attr('x2', this.width - this.margin.right)
                .attr('y2', -this.height)
                .attr('stroke', '#6A6A9F');

            // Add the line
            this.svg
                .append('path')
                .datum(this.chartData)
                .attr('fill', 'none')
                .attr('stroke', '#01F1E3')
                .attr('stroke-width', 3)
                .attr('class', 'line')
                .attr(
                    'd',
                    d3
                        .line()
                        .x((row) => this.rangeX(row.date))
                        .y((row) => this.rangeY(row.value))
                );

            // draw points labels
            for (let i = 0; i < this.chartData.length; i++) {
                let labelY = this.rangeY(this.chartData[i].value);

                if (i + 1 < this.chartData.length) {
                    labelY =
                        this.rangeY(this.chartData[i + 1].value) >=
                        this.rangeY(this.chartData[i].value)
                            ? this.rangeY(this.chartData[i].value) - 10
                            : this.rangeY(this.chartData[i].value - 35);
                }
                this.svg
                    .append('text')
                    .datum(this.chartData)
                    .attr('font-style', 'Open Sans')
                    .attr('font-weight', '600')
                    .attr('font-size', '14px')
                    .attr('x', this.rangeX(this.chartData[i].date))
                    .attr('y', labelY)
                    .attr('fill', '#ffffff')
                    .text(() => {
                        return `${this.currencyFilter.code}${this.chartData[i].value}`;
                    });
            }

            this.updated = true;
        },
    },

    computed: {
        ...mapGetters({
            currencyFilter: 'filters/currency',
            dateFilter: 'filters/date',
            activeFieldsFilter: 'filters/kpiOverviewActiveFields',
            dataList: 'kpiOverview/data',
            isCompared: 'kpiOverview/isCompared',
        }),

        chartData() {
            return this.dataList.map((row) => {
                return {
                    date: new Date(row.date),
                    value: row[this.activeFieldsFilter ?? 'avg_total'],
                };
            });
        },

        rangeX() {
            return d3
                .scaleUtc()
                .domain(d3.extent(this.chartData, (row) => row.date))
                .range([0, this.width - this.margin.right]);
        },

        rangeY() {
            return d3
                .scaleLinear()
                .domain([0, d3.max(this.chartData, (row) => row.value)])
                .range([this.height, 0]);
        },
    },

    watch: {
        dataList() {
            this.update();
        },

        activeFieldsFilter() {
            this.update();
        },
    },
};
</script>

<style lang="scss">
.chart-container {
    background-color: $--background-color-palete;
    .tick {
        font-size: 14px;
        color: #92929d;
    }
}
.graph-label {
    display: flex;
    flex-direction: row;
    align-items: center;
    padding: 6px 10px;

    width: 177px;
    height: 60px;

    background: #000000;
    border-radius: 16px;
}
</style>
