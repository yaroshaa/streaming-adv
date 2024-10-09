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
    name: 'GaugeIndicator',

    data() {
        return {
            id: 'gaugeIndicator-' + this.index,
            width: 107,
            height: 60,
            svg: null,
            updated: false,
            arc: null,
            pie: null,

            scale: null,
            arcs: null,
            needle: null,
            current: 10,
            previous: 5,
            red: '#FF4343',
            green: '#38c172',
            threshold_position: '',
            threshold_value: 0,
            minValue: 0,
            maxValue: 100,
            panel: {
                startAngle: -90,
                endAngle: 90,
                width: 10
            }
        };
    },

    props: {
        index: {
            type: String,
            required: true
        },
    },

    mounted() {
        this.svg = d3.select('#gaugeIndicator-' + this.index)
            .append('svg')
            .attr('width', this.width)
            .attr('height', this.height)
            .append('g')
            .attr('width', '100%')
            .attr('height', this.height + 2)
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
            this.threshold_value = ('threshold_value' in this.chartData[this.index])
                ? this.chartData[this.index].threshold_value.value
                : 10;

            this.threshold_position = ('threshold_value' in this.chartData[this.index])
                ? this.chartData[this.index].threshold_value.position
                : 'start';

            const scale = d3.scaleLinear()
                .domain([this.minValue, this.maxValue])
                .range([
                    this.panel.startAngle,
                    this.panel.endAngle
                ]);

            const arc = d3.arc()
                .innerRadius(this.panel.width)
                .outerRadius(Math.floor(Math.max(this.width, this.height) / 2));


            const inner = this.svg
                .append('g');

            inner
                .selectAll('path.arc')
                .data(this.sections())
                .join('path')
                .attr('class', 'arc')
                .attr('d', d => arc({
                    startAngle: this.deg2rad(scale(d.from)),
                    endAngle: this.deg2rad(scale(d.to))
                }))
                .attr('fill', d => d.color)
                .on('mouseover', function (e, i) {
                    Tooltip
                        .html(
                            `<div>Threshold Value:</div><div>${i.threshold}</div>`
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

            inner
                .append('g')
                .attr('class', 'needle')
                .attr('fill', '#fff')
                .call(s => s
                    .append('circle')
                    .attr('r', 4)
                )
                .call(s => s
                    .append('rect')
                    .attr('x', -1)
                    .attr('y', - this.height)
                    .attr('width', 2)
                    .attr('height', this.height)
                )
                .attr('transform', 'rotate('+ scale(0)+')')
                .transition()
                .ease(d3.easeElastic.amplitude(.25).period(1))
                .duration(1000)
                .attr('transform', 'rotate('+ scale(this.chartData[this.index].value) +')');

            this.handlePositionValue();
        },

        deg2rad(deg) {
            return Math.PI / 180 * deg;
        },

        sections() {
            return [
                {
                    threshold:this.threshold_value,
                    from: this.minValue,
                    to: this.threshold_value,
                    color: (this.threshold_position === 'start') ? this.red : this.green
                },
                {
                    threshold:this.threshold_value,
                    from: this.threshold_value,
                    to: this.maxValue,
                    color: (this.threshold_position === 'end') ? this.red : this.green
                }]
        },

        handlePositionValue() {
            if (this.threshold_position === 'start' && this.threshold_value > this.chartData[this.index].value) {
                this.$emit('custom', {danger: true});
            } else if (this.threshold_position === 'end' && this.threshold_value < this.chartData[this.index].value) {
                this.$emit('custom', {danger: true});
            }
        }
    },

    computed: {
        ...mapGetters({
            storageData: 'marketingOverview/data',
        }),

        chartData() {
            return this.storageData.totals;
        },
    },

    watch: {
        storageData() {
            this.update();
        },
    },

};
</script>

<style scoped>

</style>
