<template>
    <div id="price-dynamic-container">
        <h3 class="mt-2 text-left pl-5">Prices</h3>
        <div
            id="price-dynamic-chart"
            class="price-chart-chart  m-3"
            @data:updated="update"
            ref="price-dynamic"
        ></div>
    </div>
</template>

<script>
import * as d3 from 'd3';
import {mapGetters} from 'vuex';

export default {
    name: 'PriceDynamicChart',

    data() {
        return {
            svg: null,
            chart: null,
            margin: {top: 15, right: 15, bottom: 30, left: 50},
            width: 0,
            height: 0,
            updated: false,
            tooltip: null,
        };
    },

    mounted() {

        const container = document.getElementById('price-dynamic-chart');
        this.width = container.clientWidth - this.margin.left - this.margin.right;
        this.height = 200 - this.margin.top - this.margin.bottom;

        this.svg = d3
            .select('#price-dynamic-chart')
            .append('svg')
            .attr('width', '100%')
            .attr('height', this.height + this.margin.top + this.margin.bottom);

        this.chart = this.svg
            .append('g')
            .attr('id', 'wrap')
            .attr('width', '100%')
            .attr(
                'transform',
                `translate(${this.margin.left}, ${this.margin.top})`
            );

        this.tooltip = d3
            .select('body')
            .append('div')
            .attr('class', 'd3-tooltip')
            .attr('id', 'tooltip-price')
            .style('position', 'absolute')
            .style('z-index', '10')
            .style('display', 'none')
            .style('padding', '10px')
            .style('background', 'rgba(0,0,0,0.6)')
            .style('border-radius', '4px')
            .style('color', '#fff')
            .text('');
    },

    methods: {
        update() {
            this.chart.selectAll('*').remove();
            this.renderChart();
            this.chart
                .append('g')
                .attr('class', 'xAxis')
                .attr('transform', `translate(0, ${this.height})`)
                .call(d3.axisBottom(this.rangeX));

            this.chart
                .append('g')
                .attr('class', 'yAxis')
                .call(d3.axisLeft(this.rangeY)
                    .tickFormat((value) => (value > 0) ? `${value / 1000}K` : `${value / 1000}`)
                    .ticks(5)
                );

        },

        renderChart() {
            const barGroups = this.chart
                .selectAll('rect')
                .data(this.chartData)
                .enter();

            const Tooltip = this.tooltip;

            barGroups
                .append('rect')
                .attr('class', 'bar')
                .attr('x', (g,i) => this.rangeX(i))
                .attr('y', (d) => this.rangeY(d.value))
                .attr('height',(d) => this.rangeY(0) - this.rangeY(d.value))
                .attr('width', this.rangeX.bandwidth())
                .on('mouseover', function (e, i) {
                    Tooltip
                        .html(
                            `<div>Date: ${i.date}</div><div>Price: ${i.value}</div>`
                        )
                        .style('display', 'inline-block');
                })
                .on('mousemove', function (e) {
                    Tooltip
                        .style('top', e.pageY - 15 + 'px')
                        .style('left', e.pageX + 15 + 'px');
                })
                .on('mouseout', function () {
                    Tooltip.html('').style('display', 'none');
                });
        }
    },

    computed: {
        ...mapGetters({
            dataList: 'products/dynamic',
        }),

        chartData() {
            return this.dataList.price_dynamic;
        },

        rangeX() {
            return d3
                .scaleBand()
                .domain(d3.range(this.chartData.length))
                .range([0, this.width - this.margin.right])
                .padding(0.1);
        },

        rangeY() {
            return d3
                .scaleLinear()
                .domain([0, d3.max(this.chartData, (row) => Number(row.value))]).nice()
                .range([this.height, 0]);
        },
    },

    watch: {
        dataList() {
            this.update();
        },
    },
}
</script>

<style lang="scss">
.d3-tooltip{
    position: absolute;
}
.price-chart-chart {
    rect {
        fill: #4dc0b5;
    }

    rect:hover {
        fill: #fff;
    }
}
</style>

