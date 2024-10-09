<template>
    <div
        id="stream-graph"
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
    name: 'CompanyOverviewStreamGraph',
    data() {
        return {
            keys: [],
            colors: [],
            tooltip: null,
            mouseover: null,
            mouseleave: null,
            mousemove: null,
            svg: null,
            height: null,
            width: null,
            margin: {},
            loading: false,
            area: null,
        };
    },

    mounted() {
        Markets.receive().then((markets) => {
            this.keys = markets.map((d) => d.name);
            this.colors = d3
                .scaleOrdinal()
                .domain(this.keys)
                .range(markets.map((d) => d.color));

            this.margin = {top: 0, right: 20, bottom: 30, left: 20};
            this.width = 1200 - this.margin.left - this.margin.right;
            this.height = 450 - this.margin.top - this.margin.bottom;

            this.svg = d3.select('#stream-graph').append('svg').attr('viewBox', [0, 0, this.width, this.height]);

            // create a tooltip
            this.tooltip = d3
                .select('#stream-graph')
                .append('div')
                .style('opacity', 0)
                .attr('class', 'tooltip')
                .style('background-color', styles.blockColor)
                .style('color', 'white')
                .style('border', 'solid')
                .style('border-color', 'transparent')
                .style('border-width', '2px')
                .style('border-radius', '5px')
                .style('padding', '5px');

            this.update();
        });

        EventBus.on(
            ORDER_UPDATED_EVENT,
            () => {
                if (this.$route.name !== 'company-overview') {
                    return;
                }

                let to = new Date(this.dateFilter[1]);
                let today = new Date();

                if (to.toDateString() === today.toDateString()) {
                    this.socketUpdate();
                }
            },
            this.$options.name
        );
    },

    methods: {
        socketUpdate() {
            this.axios
                .get('/company-overview/stream-graph-data', {
                    params: {
                        currency: this.currencyFilter,
                        date: this.dateFilter,
                    },
                    paramsSerializer: (params) => qs.stringify(params),
                })
                .then((response) => {
                    if (!response.data) {
                        return;
                    }

                    let data = this.receivedData;
                    data[data.length - 1] = response.data[response.data.length - 1];
                    this.keys.forEach((key) => {
                        if (!Object.prototype.hasOwnProperty.call(data[data.length - 1], key)) {
                            data[data.length - 1][key] = 0;
                        }
                    });
                    data[data.length - 1].date = d3.timeParse('%Y-%m-%d')(data[data.length - 1].date);

                    data[data.length - 1].date.setHours(23);
                    data[data.length - 1].date.setMinutes(59);
                    data[data.length - 1].date.setSeconds(59);

                    this.receivedData = data;

                    let x = d3
                        .scaleUtc()
                        .domain(d3.extent(data, (d) => d.date))
                        .range([this.margin.left, this.width - this.margin.right]);

                    let series = d3.stack().offset(d3.stackOffsetWiggle).order(d3.stackOrderInsideOut).keys(this.keys)(
                        data
                    );

                    let y = d3
                        .scaleLinear()
                        .domain([
                            d3.min(series, (d) => d3.min(d, (d) => d[0])),
                            d3.max(series, (d) => d3.max(d, (d) => d[1])),
                        ])
                        .range([this.height - this.margin.bottom, this.margin.top]);

                    let area = d3
                        .area()
                        .x(function (d) {
                            return x(d.data.date);
                        })
                        .y0(function (d) {
                            return y(d[0]);
                        })
                        .y1(function (d) {
                            return y(d[1]);
                        });

                    let path = this.svg.select('g').selectAll('path');

                    path.data(series).transition().duration(500).attr('d', area).end();

                    setTimeout(() => EventBus.unlock(ORDER_UPDATED_EVENT, this.$options.name), 500);
                });
        },

        update() {
            this.axios
                .get('/company-overview/stream-graph-data', {
                    params: {
                        currency: this.currencyFilter,
                        date: this.dateFilter,
                    },
                    paramsSerializer: (params) => qs.stringify(params),
                })
                .then((response) => {
                    if (!response.data.length) {
                        return;
                    }

                    this.receivedData = response.data.map((row) => {
                        this.keys.forEach((key) => {
                            if (!Object.prototype.hasOwnProperty.call(row, key)) {
                                row[key] = 0;
                            }
                        });
                        row.date = d3.timeParse('%Y-%m-%d')(row.date);

                        return row;
                    });

                    this.receivedData[this.receivedData.length - 1].date.setHours(23);
                    this.receivedData[this.receivedData.length - 1].date.setMinutes(59);
                    this.receivedData[this.receivedData.length - 1].date.setSeconds(59);

                    let data = this.receivedData;

                    // let x = d3.scaleBand().rangeRound([0, this.width], .05).padding(0.1);
                    // x.domain(data.map(function (d) {
                    //     return d.date;
                    // }));

                    let x = d3
                        .scaleUtc()
                        .domain(d3.extent(data, (d) => d.date))
                        .range([this.margin.left, this.width - this.margin.right]);

                    this.svg.selectAll('g').remove();
                    this.svg.selectAll('.vertical-line').remove();

                    let xAxis = (g) =>
                        g
                            .attr('transform', `translate(0,${this.height - this.margin.bottom})`)
                            .call(
                                d3
                                    .axisBottom(x)
                                    .ticks(this.width / 80)
                                    .tickSizeOuter(0)
                            )
                            .call((g) => g.select('.domain').remove());

                    let series = d3.stack().offset(d3.stackOffsetWiggle).order(d3.stackOrderInsideOut).keys(this.keys)(
                        data
                    );

                    let y = d3
                        .scaleLinear()
                        .domain([
                            d3.min(series, (d) => d3.min(d, (d) => d[0])),
                            d3.max(series, (d) => d3.max(d, (d) => d[1])),
                        ])
                        .range([this.height - this.margin.bottom, this.margin.top]);

                    this.area = d3
                        .area()
                        .x(function (d) {
                            return x(d.data.date);
                        })
                        .y0(function (d) {
                            return y(d[0]);
                        })
                        .y1(function (d) {
                            return y(d[1]);
                        });

                    let path = this.svg
                        .append('g')
                        .selectAll('path')
                        .data(series)
                        .join('path')
                        .attr('fill', ({key}) => this.colors(key))
                        .attr('d', this.area);

                    let Tooltip = this.tooltip;

                    let $this = this;

                    path.on('mouseover', function () {
                        Tooltip.style('opacity', 1);

                        d3.select(this).style('stroke', 'white').style('opacity', 1);

                        d3.select('.vertical-line').style('opacity', 1);
                    })
                        .on('mousemove', function (ev) {
                            let date = x.invert(d3.pointer(ev)[0]);

                            let rowData = data.find((row) => {
                                return (
                                    row.date.getFullYear() === date.getFullYear() &&
                                    row.date.getMonth() === date.getMonth() &&
                                    row.date.getDate() === date.getDate()
                                );
                            });

                            if (!rowData) {
                                return;
                            }

                            d3.select('.vertical-line').attr('transform', function () {
                                return 'translate(' + (d3.pointer(ev)[0] - 5) + ',0)';
                            });

                            let html = '';
                            for (const [key, value] of Object.entries(rowData)) {
                                if (key !== 'date') {
                                    html += '<p><b>' + key + '</b>: ' + value + '</p>';
                                }
                            }

                            Tooltip.html('<p><b>Date:</b> ' + $this.$moment(date).format('YYYY-MM-DD') + '</p>' + html)
                                .style('left', ev.pageX + 30 + 'px')
                                .style('top', ev.pageY + 'px');
                        })
                        .on('mouseleave', function () {
                            Tooltip.style('opacity', 0);
                            d3.select(this).style('stroke', 'none').style('opacity', 0.8);

                            d3.select('.vertical-line').style('opacity', 0);
                        });

                    this.svg.append('g').call(xAxis);

                    this.svg
                        .append('line')
                        .attr('y', 0)
                        .attr('y2', this.height)
                        .attr('x1', 0)
                        .attr('x2', 0)
                        .attr('stroke', 'white')
                        .attr('class', 'vertical-line')
                        .style('opacity', 0);

                    this.loading = false;
                    this.locked = false;
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

<style lang="scss">
#stream-graph {
    background: $--background-color-palete;
}
#stream-graph text {
    fill: #ffffff;
}

.tooltip {
    position: absolute;
    z-index: 1070;
    margin: 0;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    font-size: 12px;
}

.tooltip p {
    margin: 0;
    margin-bottom: 1px;
}
</style>
