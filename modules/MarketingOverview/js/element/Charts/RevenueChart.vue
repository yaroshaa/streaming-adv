<template>
    <div id="revenue-chart" class="revenue-chart"></div>
</template>

<script>
import * as d3 from 'd3';

export default {
    name: 'RevenueChart',
    data() {
        return {
            width: 537,
            height: 156,
            margin: 20,
        };
    },

    props: {
        data: {
            type: Object,
            required: true,
            default: () => {},
        },
    },

    mounted() {
    },

    methods: {},

    computed: {},

    watch: {
        data({stores = [], totals = []}) {
            //clear old graph
            d3.select('#revenue-chart').select('svg').remove();

            const svg = d3.select('#revenue-chart').append('svg')
                .attr('width', this.width + this.margin)
                .attr('height', this.height + this.margin)
                .attr('transform', 'translate(10,-10)')
                .append('g');

            const dataForStack = [];
            stores.forEach((item) => {
                item.forEach((value, index) => {
                    dataForStack[index] = Array.isArray(dataForStack[index]) ? dataForStack[index] : [];
                    dataForStack[index].push(value);
                })
            });

            const stack = d3.stack().keys(Object.keys(stores));
            const stackedData = stack(dataForStack);

            let yMax = d3.max([].concat(...stackedData), function (d) {
                return d[0] + d[1];
            });
            if (!yMax) {
                svg
                    .attr('transform', 'translate(' + (this.width / 2 - 50) + ',' + (this.height / 2 + 50) + ')')
                    .append('text')
                    .text('No Data')
                    .style('fill', '#ffffff')
                    .style('font-size', '40px');

                return;
            }

            const [first] = stores;
            const xScale = d3
                .scaleBand()
                .rangeRound([0, this.width])
                .padding(0.1)
                .domain(Object.keys(first));
            const yScale = d3.scaleLinear().range([this.height, 0]);
            const color = d3.scaleOrdinal(['#ffb843', '#b80000', '#9e09b6', '#5EFF5A']);
            const xAxis = d3.axisBottom(xScale);

            yScale.domain([0, yMax]).nice();

            const layer = svg.selectAll('.layer')
                .data(stackedData)
                .enter().append('g')
                .attr('width', this.width)
                .attr('height', this.height)
                .attr('class', 'layer')
                .style('fill', function (d, i) {
                    return color(i);
                });

            layer.selectAll('rect')
                .data(function (d) {
                    return d;
                })
                .enter().append('rect')
                .attr('x', function (d, i) {
                    return xScale(i);
                })
                .attr('y', function (d) {
                    return yScale(d[1]);
                })
                .attr('height', function (d) {
                    return yScale(d[0]) - yScale(d[1]);
                })
                .attr('width', xScale.bandwidth());

            svg.append('g')
                .attr('class', 'axis axis-x')
                .attr('transform', 'translate(0,' + this.height + ')')
                .call(xAxis);

            if (totals.length) {
                // line chart
                let line = d3
                    .line()
                    .x(function (d, i) {
                        return xScale(i);
                    })
                    .y(function (d) {
                        return yScale(d);
                    })
                    .curve(d3.curveMonotoneX);

                svg.append('path').attr('fill', 'none').attr('stroke', '#979797').attr('stroke-width', 3).attr('d', line(totals));
            }
        }
    }
};
</script>

<style scoped lang="scss">
.revenue-chart {
    background: $--background-color-blackly;
    border-radius: 7px;
    margin: 10px 0 0 35px;
}
</style>
