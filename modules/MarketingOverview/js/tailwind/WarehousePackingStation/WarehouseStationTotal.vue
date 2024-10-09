<template>
    <div class="bg-container h-52 text-gray-500">
        <p class="font-bold text-center text-2xl">All stations</p>
        <div class="flex flex-row mt-5 mr-2.5">
            <div class="flex flex-col w-1/2 text-center justify-center">
                <div class="text-4xl font-bold">{{ data.total }}</div>
                <div>{{ data.awaiting_stock }} <span class="font-bold">awaiting stock</span></div>
                <div>{{ data.total }} <span class="font-bold">open</span></div>
                <div>{{ data.in_packing }} <span class="font-bold">in packing</span></div>
            </div>
            <div class="flex flex-col w-1/2">
                <div class="h-32 bg-gray-100">
                    <div class="h-32 bg-gray-500" :id="`chart-${this.warehouseId}-total`"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import moment from 'moment';
import * as d3 from 'd3';

export default {
    name: 'WarehouseStationTotal',

    data() {
        return {
            svg: null,
            margin: {top: 10, right: 30, bottom: 30, left: 60},
            width: 0,
            height: 0,
            updated: false,
            dataList: [],
        };
    },

    props: {
        warehouseId: {
            type: String,
            default: ''
        },

        data: {
            type: Object,
            default: () => {}
        },
    },

    mounted() {
        this.render();
    },

    methods: {
        render() {

            const data = this.data.per_hour.map((value, index) => {
                const date = moment().format(`Y-m-d ${index < 10 ? `0${index}` : index }:00`);

                return {
                    date: d3.timeParse('%Y-%m-%d %H:%M')(date),
                    value: value % 1 > 0 ? parseFloat(value.toFixed(2)) : parseInt(value),
                };
            });

            const minMaxByX = d3.extent(data, (d) => d.date);
            const minMaxByY = [d3.extent(data, (d) => d.value)[0], d3.extent(data, (d) => d.value)[1]];

            const margin = {top: 5, right: 8, bottom: 31, left: 13};
            const height = 128;
            const width = 187;

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
                .tickFormat((d) => `${d}`)
                .ticks(5);

            const yAxis = (g) => g.attr('transform', `translate(${margin.left},0)`).call(volumeAxis);

            d3.select(`#chart-${this.warehouseId}-total`).selectAll('*').remove();

            const svg = d3
                .select(`#chart-${this.warehouseId}-total`)
                .append('svg')
                .attr('viewBox', [0, 0, width, height])
                .attr('fill', '#ffffff');

            svg.append('g').call(xAxis);
            svg.append('g').call(yAxis);

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
                .attr('stroke-width', 1);

            // change size and color of ticks labels
            d3.selectAll('.tick text')
                .attr('font-size', '8')
                .attr('color', '#ffffff')
                .attr('style', 'writing-mode: tb; glyph-orientation-vertical: 90;');

            d3.selectAll('.xAxis .tick text').attr('transform', 'translate(0, 5)');
            d3.selectAll('.xAxis .tick line').attr('display', 'none');

            // change color of axis lines
            d3.selectAll('.domain').attr('stroke', '#6A6A9F');
        },
    },

    computed: {
        id: () => `chart-${this.station}`
    }
};
</script>
