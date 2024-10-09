<template>
    <div id="store-today-line-chart">
        <div class="no-data">No data...</div>
    </div>
</template>

<script>
import * as d3 from 'd3';
import {mapGetters} from 'vuex';

export default {
    name: 'StoreTodayLineChart',
    data() {
        return {
            svg: null,
            margin: {top: 10, right: 30, bottom: 30, left: 60},
            width: 0,
            height: 0,
            updated: false,
        };
    },

    methods: {
        render() {
            const symbol = this.dataList.length > 0 && this.dataList[0].symbol ? this.dataList[0].symbol : '';

            const data = this.dataList.map((item) => {
                return {
                    date: d3.timeParse('%Y-%m-%d %H:%M')(item.date),
                    value: item.value % 1 > 0 ? parseFloat(item.value.toFixed(2)) : parseInt(item.value),
                };
            });

            const minMaxByX = d3.extent(data, (d) => d.date);
            const minMaxByY = [d3.extent(data, (d) => d.value)[0], d3.extent(data, (d) => d.value)[1]];

            const margin = {top: 69, right: 85, bottom: 77, left: 72};
            const height = 488;
            const width = 1632;

            const x = d3
                .scaleTime()
                .domain(minMaxByX)
                .range([margin.left, width - margin.right]);

            const y = d3
                .scaleLinear()
                .domain(minMaxByY)
                .range([height - margin.bottom, margin.top]);

            // draw x axis
            const timeAxis = d3.axisBottom(x).ticks(d3.timeHour.every(1), '%H:%M');

            const xAxis = (g) =>
                g
                    .attr('transform', `translate(0,${height - margin.bottom})`)
                    .attr('class', 'xAxis')
                    .call(timeAxis);

            // draw y axis
            const volumeAxis = d3
                .axisLeft(y)
                .tickFormat((d) => `${symbol}${d}`)
                .ticks(5);

            const yAxis = (g) => g.attr('transform', `translate(${margin.left},0)`).call(volumeAxis);

            d3.select('#store-today-line-chart').selectAll('*').remove();

            const svg = d3
                .select('#store-today-line-chart')
                .append('svg')
                .attr('viewBox', [0, 0, width, height])
                .attr('fill', '#ffffff');

            svg.append('g').call(xAxis);
            svg.append('g').call(yAxis);

            // draw vertical lines for grid
            d3.selectAll('g.xAxis g.tick')
                .append('line')
                .attr('class', 'gridline')
                .attr('x1', 0)
                .attr('y1', -height + margin.top + margin.bottom)
                .attr('x2', 0)
                .attr('y2', 0)
                .attr('stroke', '#6A6A9F');

            // draw top horizontal line
            d3.select('.xAxis')
                .append('line')
                .attr('x1', margin.left)
                .attr('y1', -height + margin.top + margin.bottom + 0.5)
                .attr('x2', 1548)
                .attr('y2', -height + margin.top + margin.bottom + 0.5)
                .attr('stroke', '#6A6A9F');

            // draw chart curve
            svg.append('path')
                .datum(data)
                .attr('fill', 'none')
                .attr('stroke', '#01F1E3')
                .attr('stroke-width', 1)
                .attr(
                    'd',
                    d3
                        .line()
                        .x((d) => {
                            return x(d.date);
                        })
                        .y((d) => {
                            return y(d.value);
                        })
                )
                .attr('stroke-width', 3);

            // draw points labels
            for (let i = 0; i < data.length; i++) {
                let labelY = y(data[i].value);

                if (i + 1 < data.length) {
                    labelY = y(data[i + 1].value) >= y(data[i].value) ? y(data[i].value) - 10 : y(data[i].value - 35);
                }
                svg.append('text')
                    .datum(data)
                    .attr('font-style', 'Open Sans')
                    .attr('font-weight', '600')
                    .attr('font-size', '14px')
                    .attr('x', x(data[i].date))
                    .attr('y', labelY)
                    .attr('fill', '#ffffff')
                    .text(() => {
                        return `${symbol}${data[i].value}`;
                    });
            }

            // change size and color of ticks labels
            d3.selectAll('.tick text').attr('font-size', '14').attr('color', '#92929D');

            // change color of axis lines
            d3.selectAll('.domain').attr('stroke', '#6A6A9F');
        },
    },

    computed: {
        ...mapGetters({
            dataList: 'storeToday/getData',
            getSrcData: 'storeToday/getSrcData',
        }),
    },

    watch: {
        dataList: function () {
            if (this.dataList.length > 0) {
                this.render();
            }
        },

        getSrcData() {
            this.render();
        },
    },
};
</script>

<style lang="scss" scoped>
#store-today-line-chart {
    width: 100%;
    height: 488px;
    background-color: #090916;
    border-radius: 8px;
    margin-bottom: 60px;

    .xAxis {
        font: 5px sans-serif;
    }

    .no-data {
        color: #ffffff;
        font-weight: bold;
        font-size: 22px;
        text-align: center;
    }
}
</style>
