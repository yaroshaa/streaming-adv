<template>
    <div class="mo-area-chart">
        <div id="store-area-chart">
            <div class="no-data">No data...</div>
        </div>
    </div>
</template>

<script>
import * as d3 from 'd3';
import {mapGetters} from 'vuex';

export default {
    name: 'AreaChart',

    data() {
        return {
            svg: null,
            margin: {top: 69, right: 85, bottom: 0, left: 72},
            width: 1632,
            height: 190,
            indexXScale: 10,
            dataName: 'revenue',
            color: {
                list: {
                    stores: {color: '#331929', strColor: '#98387b'},
                    totals: {color: '#211d1f', strColor: '#9c8670'},
                },
            },
        };
    },

    mounted() {},

    methods: {},

    computed: {
        ...mapGetters({
            marketingData: 'marketingOverview/data',
        }),
    },

    watch: {
        marketingData: function (data) {
            const stores = data.stores.map((store) => {
                return store[this.dataName].per_day
            }).sort(function(a, b) { return d3.max(a) - d3.max(b); });

            const xMax = d3.max(stores.map((store) => store.length - 1));
            const yMax = d3.max([].concat(...stores));
            const lineColor = d3.scaleOrdinal(d3.schemeCategory10);
            const areaColor = d3.scaleOrdinal(['#191f23', '#171321', '#33431e', '#a36629', '#92462f', '#b63e36', '#b74a70', '#946943']);

            const x = d3
                .scaleTime()
                .domain([0, xMax])
                .range([this.margin.left, this.width - this.margin.right]);
            const y = d3
                .scaleLinear()
                .domain([yMax, 0])
                .range([this.height - 5, this.margin.top]);
            const xAxis = (g) => g.attr('class', 'axis');

            d3.select('#store-area-chart').selectAll('*').remove();

            const svg = d3
                .select('#store-area-chart')
                .append('svg')
                .attr('viewBox', [0, 10, this.width, this.height])
                .attr('fill', '#ffffff');

            const area = d3
                .area()
                .x(function (d, index) {
                    return x(index);
                })
                .y0(this.height)
                .y1(function (d) {
                    return y(d);
                });
            const line = d3
                .line()
                .x(function (d, index) {
                    return x(index);
                })
                .y(function (d) {
                    return y(d);
                });

            stores.forEach((store, index) => {
                svg.append('path')
                    .attr('class', 'area')
                    .attr('d', area(store))
                    .attr('fill', areaColor(index))
                    .attr('stroke', 'none')
                    .attr('stroke-width', 3)
                    .style('opacity', 1);

                svg.append('path')
                    .attr('class', 'line')
                    .attr('d', line(store))
                    .attr('fill', 'none')
                    .attr('stroke', lineColor(index))
                    .attr('stroke-width', 3)
                    .style('opacity', 1);
            })

            svg.append('g').call(xAxis);

            // draw points labels
            const dataStore = stores[0];
            for (let i = 0; i < dataStore.length; i++) {
                let labelY = y(dataStore[i]);

                if (i + 1 < dataStore.length) {
                    labelY = y(dataStore[i]) - 5;
                }
                svg.append('text')
                    .datum(dataStore.series)
                    .attr('font-style', 'Open Sans')
                    .attr('font-weight', '600')
                    .attr('font-size', '14px')
                    .attr('x', x(i))
                    .attr('y', labelY)
                    .attr('fill', '#e3daf5')
                    .style('opacity', 1)
                    .text(() => `${dataStore[i]}`)
                    .transition()
                    .delay(100)
                    .duration(400)
                    .style('opacity', 1);
            }
        },

    },
};
</script>

<style lang="scss" scoped>
#store-area-chart {
    background: $--background-color-palete;
    border-radius: 5px;
    margin: 10px 25px 10px 10px;
}

.mo-area-chart {
    rect {
        fill: url(#pattern2) !important;
    }
}
</style>
