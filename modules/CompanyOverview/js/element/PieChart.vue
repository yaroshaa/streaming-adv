<template>
    <div
        id="pie-chart"
        v-loading="loading"
        element-loading-text="Loading..."
        element-loading-spinner="el-icon-loading"
        element-loading-background="rgba(0, 0, 0, 0.4)"
    ></div>
</template>

<script>
import * as d3 from 'd3';
import Markets from '@/data/markets';
import {mapGetters} from 'vuex';
import EventBus from '@/socket/eventbus';
import {ORDER_UPDATED_EVENT} from '@/socket/events';
import styles from '@/../style/element/_variables.scss';
import qs from 'qs';

export default {
    name: 'CompanyOverviewPieChart',
    data() {
        return {
            width: 350,
            height: 350,
            svg: null,
            legend: null,
            legendRectSize: 16,
            legendSpacing: 4,
            keys: [],
            colors: [],
            arc: null,
            outerArc: null,
            radius: null,
            pie: null,
            loading: false,
        };
    },

    mounted() {
        Markets.receive().then((markets) => {
            this.keys = markets.map((d) => d.name);

            this.colors = d3
                .scaleOrdinal()
                .domain(this.keys)
                .range(markets.map((d) => d.color));

            let margin = 22;
            this.radius = Math.min(this.width, this.height) / 2 - margin;

            this.svg = d3
                .select('#pie-chart')
                .append('svg')
                .attr('width', this.width)
                .attr('height', this.height)
                .append('g')
                .attr('transform', 'translate(' + this.width / 2 + ',' + this.height / 2.8 + ')');

            this.pie = d3
                .pie()
                .sort(null)
                .value((d) => d.value);

            this.arc = d3
                .arc()
                .innerRadius(this.radius * 0.5) // This is the size of the donut hole
                .outerRadius(this.radius * 0.8);

            this.outerArc = d3
                .arc()
                .innerRadius(this.radius * 0.9)
                .outerRadius(this.radius * 0.9);

            this.mouseover = function (ev, d) {
                d3.selectAll('.myArea').style('opacity', 0.2);
                d3.selectAll('.chart-area-' + d.index)
                    .style('stroke', 'white')
                    .style('opacity', 1);

                d3.selectAll('.chart-tooltip').text(d.data.name).style('opacity', 1);
            };

            this.mouseleave = function () {
                d3.selectAll('.myArea').style('opacity', 1).style('stroke', 'none');
                d3.selectAll('.chart-tooltip').text('').style('opacity', 0);
            };

            //fix for display only 3 shop
            const legendKeys = this.keys.splice(0, 3);
            const paddingHorizontalLegend = -7;
            const paddingVerticalLegend = 9;
            const legendColors = d3
                .scaleOrdinal()
                .domain(legendKeys)
                .range(markets.map((d) => d.color));

            this.legend = this.svg
                .selectAll('.legend')
                .data(legendColors.domain())
                .enter()
                .append('g')
                .attr('class', 'legend')
                .attr('transform', (d, i) => {
                    let height = this.legendRectSize + this.legendSpacing;
                    let offset = (height * legendColors.domain().length) / 2;
                    let horz = paddingHorizontalLegend * this.legendRectSize;
                    let vert = i * height - offset + paddingVerticalLegend * height;
                    return 'translate(' + horz + ',' + vert + ')';
                });

            this.legend
                .append('text')
                .attr('x', this.legendRectSize + this.legendSpacing)
                .attr('y', this.legendRectSize - this.legendSpacing)
                .text(function (d) {
                    return d;
                });

            this.legend
                .data(markets)
                .append('circle')
                .attr('cy', () => {
                    return this.legendRectSize - this.legendSpacing;
                })
                .attr('r', 5)
                .style('stroke', (d) => {
                    return d.color;
                })
                .style('fill', 'none');

            this.update();
        });

        EventBus.on(
            ORDER_UPDATED_EVENT,
            () => {
                if (this.$route.name === 'company-overview') {
                    this.update();
                }
            },
            this.$options.name
        );
    },

    methods: {
        update() {
            this.loading = true;
            this.axios
                .get('/company-overview/pie-chart-data', {
                    params: {
                        currency: this.currencyFilter,
                        date: this.dateFilter,
                    },
                    paramsSerializer: (params) => qs.stringify(params),
                })
                .then((response) => {
                    let data = response.data;

                    const arcs = this.pie(data);
                    let elements = this.svg
                        .selectAll('allSlices')
                        .data(arcs)
                        .enter()
                        .append('path')
                        .attr('class', (d) => 'myArea chart-area-' + d.index)
                        .attr('d', this.arc)
                        .attr('fill', (d) => this.colors(d.data.name))
                        .attr('stroke', styles.background)
                        .style('stroke-width', '2px')
                        .style('opacity', 0.7)
                        .on('mouseover', this.mouseover)
                        .on('mouseleave', this.mouseleave);

                    elements.selectAll('title').remove();
                    elements.append('title').text((d) => `${d.data.name}: ${d.data.value.toLocaleString()}`);

                    this.legend
                        .data(arcs)
                        .append('text')
                        .attr('x', this.legendRectSize + this.legendSpacing + 150)
                        .attr('y', this.legendRectSize - this.legendSpacing)
                        .text(function (d) {
                            return Math.round(d.value);
                        });

                    this.loading = false;
                    EventBus.unlock(ORDER_UPDATED_EVENT, this.$options.name);
                });
        },
    },

    computed: {
        ...mapGetters({
            currencyFilter: 'filters/currency',
            dateFilter: 'filters/date',
        }),
    },

    watch: {
        currencyFilter() {
            this.update();
        },

        dateFilter() {
            this.update();
        },
    },
};
</script>

<style lang="scss" scoped>
#pie-chart {
    padding: 15px;
    width: 320px;
}
</style>

<style>
#pie-chart text {
    /*fill: #ffffff;*/
    fill: #000000;
}
</style>
