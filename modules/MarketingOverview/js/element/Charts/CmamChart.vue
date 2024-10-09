<template>
    <div>
        <div
            id="cmam-graph"
            v-loading="loading"
            element-loading-text="Loading..."
            element-loading-spinner="el-icon-loading"
            element-loading-background="rgba(0, 0, 0, 0.4)"
        ></div>
    </div>
</template>

<script>
import * as d3 from 'd3';
import {mapGetters} from 'vuex';

export default {
    name: 'CmamChart',
    data() {
        return {
            symbolSize: 1,
            height: 123,
            width: 1290,
            margin: {top: 20, right: 20, bottom: 35, left: 20},
            loading: false,
            firstLineColor: '#005faa',
            secondLineColor: '#211c3f',
        };
    },

    mounted() {},

    methods: {
        line(xScale, yScale) {
            return d3
                .line()
                .x(function (d, i) {
                    return xScale(i) + xScale.bandwidth() / 2;
                })
                .y(function (d) {
                    return yScale(d);
                })
                .curve(d3.curveMonotoneX);
        },

        xScale(data) {
            return d3
                .scaleBand()
                .rangeRound([0, this.width])
                .padding(0.1)
                .domain(
                    data.map(function (d, i) {
                        return i;
                    })
                );
        },

        yScale(data) {
            return d3
                .scaleLinear()
                .rangeRound([this.height, 0])
                .domain([
                    0,
                    d3.max(data, function (d) {
                        return d;
                    }),
                ]);
        },


    },

    computed: {
        ...mapGetters({
            marketingData: 'marketingOverview/data',
        }),
    },

    watch: {
        marketingData() {
            if ('totals' in this.marketingData) {
                const {totals} = this.marketingData;

                const {
                    cmam: {per_day: cmamData = []} = {},
                    cm_ratio: {per_date: cmRatioData = []} = {},
                    spend_ratio: {per_date: spendRatioData = []} = {}
                } = totals;

                const xScaleCmam = this.xScale(cmamData);
                const yScaleCmam = this.yScale(cmamData);

                const xCmRatioData = cmRatioData.map((item) => item.date);
                const yCmRatioData = cmRatioData.map((item) => item.value);
                const xScaleCmRatio = this.xScale(xCmRatioData);
                const yScaleCmRatio = this.yScale(yCmRatioData);

                const xSpendRatioData = spendRatioData.map((item) => item.date);
                const ySpendRatioData = spendRatioData.map((item) => item.value);
                const xScaleSpendRatio = this.xScale(xSpendRatioData);
                const yScaleSpendRatio = this.yScale(ySpendRatioData);

                //clear old graph
                d3.select('#cmam-graph').select('svg').remove();

                let svg = d3
                    .select('#cmam-graph')
                    .append('svg')
                    .attr('width', this.width + this.margin.left + this.margin.right)
                    .attr('height', this.height + this.margin.top + this.margin.bottom);

                let g = svg.append('g').attr('transform', 'translate(' + this.margin.left + ',' + this.margin.top + ')');
                let bar = g.selectAll('rect').data(cmamData).enter().append('g');

                // bar chart
                bar.append('rect')
                    .attr('x', function (d, i) {
                        return xScaleCmam(i);
                    })
                    .attr('y', function (d) {
                        return yScaleCmam(d);
                    })
                    .attr('width', xScaleCmam.bandwidth())
                    .attr('height', (d) => {
                        return this.height - yScaleCmam(d);
                    })
                    .attr('class', 'bar');

                // labels on the bar chart
                bar.append('text')
                    .attr('dy', '1.3em')
                    .attr('x', function (d, i) {
                        return xScaleCmam(i) + xScaleCmam.bandwidth() / 2;
                    })
                    .attr('y', function (d) {
                        return yScaleCmam(d);
                    })
                    .attr('text-anchor', 'middle')
                    .attr('font-family', 'sans-serif')
                    .attr('font-size', '11px')
                    .attr('fill', 'black')
                    .text(function (d) {
                        return Math.round(d);
                    });

                //tooltip
                const div = d3
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

                //first line
                const cmRatioLine = this.line(xScaleCmRatio, yScaleCmRatio);
                bar.append('path').attr('fill', 'none').attr('stroke', this.firstLineColor).attr('d', cmRatioLine(yCmRatioData));
                bar.append('path')
                    .attr('d', d3.symbol().size(this.symbolSize).type(d3.symbolTriangle))
                    .attr('fill', this.firstLineColor)
                    .attr('stroke', this.firstLineColor)
                    .attr('stroke-width', 3)
                    .attr('transform', function (d, i) {
                        return 'translate(' + (xScaleCmRatio(i) + xScaleCmRatio.bandwidth() / 2) + ',' + yScaleCmRatio(yCmRatioData[i]) + ')';
                    })
                    .on('mouseover', function(event, d) {
                        const index = cmamData.indexOf(d);
                        div.transition()
                            .duration(200)
                            .style('display', 'inline-block');
                        div
                            .html(
                                `<div>${xCmRatioData[index]}:</div><div>${yCmRatioData[index]}</div>`
                            )
                            .style('top', event.pageY - 15 + 'px')
                            .style('left', event.pageX + 15 + 'px');
                    })
                    .on('mouseout', function() {
                        div.transition()
                            .duration(500)
                            .style('display', 'none');
                    });

                //second line
                const spendRatioLine = this.line(xScaleSpendRatio, yScaleSpendRatio);
                bar.append('path').attr('fill', 'none').attr('stroke', this.secondLineColor).attr('d', spendRatioLine(ySpendRatioData));
                bar.append('path')
                    .attr('d', d3.symbol().size(this.symbolSize).type(d3.symbolSquare))
                    .attr('fill', this.secondLineColor)
                    .attr('stroke', this.secondLineColor)
                    .attr('stroke-width', 3)
                    .attr('transform', function (d, i) {
                        return 'translate(' + (xScaleSpendRatio(i) + xScaleSpendRatio.bandwidth() / 2) + ',' + yScaleSpendRatio(ySpendRatioData[i]) + ')';
                    })
                    .on('mouseover', function(event, d) {
                        const index = cmamData.indexOf(d);

                        div.transition()
                            .duration(200)
                            .style('display', 'inline-block');
                        div
                            .html(
                                `<div>${xSpendRatioData[index]}:</div><div>${ySpendRatioData[index]}</div>`
                            )
                            .style('top', event.pageY - 15 + 'px')
                            .style('left', event.pageX + 15 + 'px');
                    })
                    .on('mouseout', function() {
                        div.transition()
                            .duration(500)
                            .style('display', 'none');
                    });
            }
        },
    }
};
</script>

<style lang="scss">
.bar {
    fill: #42918d;
}
.axis--y path {
    display: none;
}

.axis--x path {
    color: #1c2b3d;
}

#cmam-graph {
    background: $--background-color-palete;
    border-radius: 5px;
    margin: 10px;
}

#cmam-graph text {
    fill: #ffffff;
}

.line {
    fill: none;
    stroke: steelblue;
    stroke-width: 2px;
}

div.tooltip {
    position: absolute;
    text-align: center;
    width: 60px;
    height: 28px;
    padding: 2px;
    font: 12px sans-serif;
    background: lightsteelblue;
    color: #000;
    border: 0;
    border-radius: 8px;
    pointer-events: none;
}
</style>
