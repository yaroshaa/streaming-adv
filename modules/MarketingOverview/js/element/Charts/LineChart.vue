<template>
    <div :id="chartName" class="line-chart"></div>
</template>

<script>
import * as d3 from 'd3';

export default {
    name: 'LineChart',
    data() {
        return {
            width: 537,
            height: 156,
            margin: 20,
        };
    },

    props: {
        data: {
            type: Array,
            required: true,
            default: () => [],
        },

        chartName: {
            type: String,
            required: true,
            default: () => 'line',
        },
    },

    mounted() {},

    computed: {
        fullWidth: function() {
            return this.width + this.margin
        },

        fullHeight: function() {
            return this.height + this.margin
        }
    },

    watch: {
        data(data) {
            //clear old graph
            d3.select('#' + this.chartName).select('svg').remove();

            const svg = d3.select('#' + this.chartName)
                .append('svg')
                .attr('width', this.fullWidth)
                .attr('height', this.fullHeight)
                .attr('transform', 'translate(10,-10)')
                .append('g');

            let yMax = d3.max([].concat(...data));
            if (!yMax) {
                svg
                    .attr('transform', 'translate(' + (this.width / 2 - 50) + ',' + (this.height / 2 + 50) + ')')
                    .append('text')
                    .text('No Data')
                    .style('fill', '#ffffff')
                    .style('font-size', '40px');

                return;
            }

            //fix moving position
            yMax += yMax * 0.1;

            const xMax = d3.max(data.map((store) => store.length - 1));
            const color = d3.scaleOrdinal(d3.schemeCategory10);
            const xScale = d3
                .scaleLinear()
                .rangeRound([0, this.width])
                .domain([0, xMax]);

            let yScale = d3.scaleLinear()
                .range([ this.height, 0 ])
                .domain([0, yMax + 500]);

            let line = d3
                .line()
                .x((d, i) => xScale(i))
                .y((d) => yScale(d || 0));

            data.forEach((lineData, index) => {
                svg
                    .append('path')
                    .attr('fill', 'none')
                    .style('stroke', () => color(index))
                    .attr('stroke-width', 3)
                    .attr('d', line(lineData));
            })

            let xAxis = d3.axisBottom().scale(xScale).ticks(5);
            svg.append('g')
                .attr('class', 'axis axis-x')
                .attr('transform', 'translate(0,' + this.height + ')')
                .call(xAxis);
        }
    }
};
</script>

<style lang="scss" scoped>
.line-chart {
    background: $--background-color-blackly;
    border-radius: 0 7px 7px 7px;
    display: inline-block;
    margin: 10px 0 0 35px;
}
</style>
