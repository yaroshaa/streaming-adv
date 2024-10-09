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
    name: 'PieChart',

    data() {
        return {
            id: 'pieChart-' + this.index,
            width: 107,
            height: 60,
            svg: null,
            tooltip: null,
            updated: false,
            arc: null,
            pie: null,
            color: null,
        };
    },

    props: {
        index: {
            type: String,
            required: true
        },
    },

    mounted() {

        this.svg = d3.select('#pieChart-' + this.index)
            .append('svg')
            .attr('width', this.width)
            .attr('height', this.height)
            .append('svg:g')
            .attr('width', '100%')
            .attr('transform', 'translate(' + (this.width / 2) + ',' + (this.height) + ')');


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
            .style('color', '#fff')
            .text('');
    },

    methods: {

        update() {
            this.renderChart();
        },

        renderChart() {

            const Tooltip = this.tooltip;

            this.svg
                .data([this.chartData])
                .enter();

            this.arc = d3.arc()
                .innerRadius(0)
                .outerRadius(Math.min(this.width, this.width) / 2);

            this.pie = d3.pie()
                .startAngle(-90 * (Math.PI / 180))
                .endAngle(90 * (Math.PI / 180))
                .padAngle(.0)
                .sort(null)
                .value(function (d) {
                    return d.value;
                });


            const arcs = this.svg.selectAll('g.slice')
                .data(this.pie)
                .enter()
                .append('g')
                .attr('class', 'slice');

            arcs.append('path')
                .attr('fill', function (d) {
                    return d.data.color;
                })
                .attr('d', this.arc).on('mouseover', function (e, i) {
                    Tooltip
                        .html(
                            `<div>${i.data.name}:</div><div>${i.value}</div>`
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
        },

    },

    computed: {
        ...mapGetters({
            storageData: 'marketingOverview/data',
        }),

        chartData() {
            const pieData = [];
            for (let item of this.storageData.stores) {
                pieData[item.id] = {
                    id: Number(item.id),
                    name: item.name,
                    value: Number(item[this.index].total.toFixed(2)),
                    color: item.color
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

