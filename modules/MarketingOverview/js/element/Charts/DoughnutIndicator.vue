<template>
    <div
        :id="id"
        @data:updated="update"
    ></div>
</template>

<script>
import * as d3 from 'd3';
import {mapGetters} from 'vuex';

export default {
    name: 'DoughnutIndicator',

    data() {
        return {
            id: 'doughnut-indicator-' + this.index,
            width: 100,
            height: 100,
            svg: null,
            tooltip: null,
            updated: false,
            arc: null,
            pie: null,
            color: null,
            legend:null
        };
    },

    props: {
        index: {
            type: String,
            required: true
        },

        val: {
            type: Number,
            required: true,
            default: 0
        },

        title: {
            type: String,
            required: true,
            default: 'Title'
        }

    },

    mounted() {
        this.svg = d3.select('#doughnut-indicator-' + this.index)
            .append('svg')
            .attr('width', this.width)
            .attr('height', this.height)
            .append('svg:g')
            .attr('width', '100%')
            .attr('transform', 'translate(' + (this.width / 2) + ',' + (this.height/2) + ')');


        this.tooltip = d3
            .select('body')
            .append('div')
            .attr('class', 'd3-tooltip')
            .attr('id', 'tooltip-' + this.index)
            .style('position', 'absolute')
            .style('z-index', '10')
            .style('display', 'none')
            .style('padding', '10px')
            .style('background', 'rgba(0,0,0,0.6)')
            .style('border-radius', '4px')
            .style('color', '#ffffff')
            .text('');
    },

    methods: {

        update() {
            this.renderChart();
        },

        renderChart() {
            const Tooltip = this.tooltip;

            this.svg.selectAll('*').remove();
            this.svg
                .data([this.chartData])
                .enter();

            this.arc = d3.arc()
                .innerRadius(Math.min(this.width, this.width) / 2.5)
                .outerRadius(Math.min(this.width, this.width) / 2);

            this.pie = d3.pie()
                .padAngle(0.005)
                .sort(null)
                .value(function (d) {
                    return d.value;
                });

            this.svg.append('g')
                .call(g => g.append('text')
                    .attr('text-anchor', 'middle')
                    .attr('font-size', '1.5em')
                    .attr('font-weight', 'bold')
                    .attr('y', -11)
                    .attr('fill', 'white')
                    .attr('dy', '0.35em')
                    .text(this.val));

            this.svg.append('g')
                .call(g => g.append('foreignObject')
                    .attr('width', 70)
                    .attr('height', 70)
                    .attr('x', -35)
                    .attr('y', 3)
                    .append('xhtml:body')
                    .style('font-size', '0.6em')
                    .style('line-height', '1em')
                    .style('text-align', 'center')
                    .style('font-weight', 'bold')
                    .style('background', 'transparent')
                    .style('color', '#A1A1A2')
                    .html(this.title));

            const arcs = this.svg.selectAll('g.slice')
                .data(this.pie)
                .enter()
                .append('g')
                .attr('class', 'slice');

            arcs.append('path')
                .attr('fill', (d) =>  d.data.color)
                .attr('d', this.arc).on('mouseover', function (e, i) {
                    if (i.data.name !== 'Empty') {
                        Tooltip
                            .html(
                                `<div>${i.data.name}:</div><div>${i.value}</div>`
                            )
                            .style('display', 'inline-block');
                    }
                })
                .on('mousemove', function (e) {
                    Tooltip
                        .style('top', e.pageY - 15 + 'px')
                        .style('left', e.pageX + 15 + 'px');
                })
                .on('mouseout', function () {
                    Tooltip.html('').style('display', 'none');
                });
        },
    },

    computed: {
        ...mapGetters({
            storageData: 'marketingOverview/data',
        }),

        chartData() {
            let pieData = [];
            let arr = [];
            for (let item of this.storageData.stores) {
                if (item[this.index] > 0) {
                    arr[item.id] = {
                        id: Number(item.id),
                        name: item.name,
                        value: Number(item[this.index].toFixed(2)),
                        color: item.color
                    };
                }
            }

            if (arr.length) {
                pieData = arr;
            } else {
                pieData[0] = {
                    id: 1,
                    name: 'Empty',
                    value: 1,
                    color: '#A1A1A2'
                };
            }

            return pieData.filter(Boolean);
        },
    },

    watch: {
        storageData() {
            this.update();
        },
    },
};
</script>
