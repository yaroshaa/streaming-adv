<template>
    <div class="map-container">
        <svg ref="svg" :height="height" :width="width">
            <path
                v-for="country in countryData"
                :key="country.feature.id"
                :d="pathGenerator(country.feature)"
                :style="{stroke: 'darkslategray'}"
            ></path>
        </svg>
        <canvas
            id="map"
            :height="height"
            :width="width"
            class="map"
            @mousemove="trackPoints"
            @mouseup="clickPoint"
        ></canvas>
        <canvas
            id="tooltip"
            :width="tooltipWidth"
            :height="tooltipHeight"
            v-show="isTooltipShow"
        ></canvas>
    </div>
</template>

<script>
import * as d3 from 'd3';
import europeGeoJson from '@/assets/geoJson/europe.json';
import {mapActions, mapGetters} from 'vuex';
import styles from '@/../style/element/_variables.scss';

export default {
    name: 'D3Map',

    data() {
        return {
            isTooltipShow: false,
            width: 960,
            height: 440,
            tooltipWidth: 100,
            tooltipHeight: 25,
            center: [13, 52],
            geoJson: europeGeoJson,
            $canvas: null,
            canvas: null,
            ctx: null,
            $tooltipCanvas: null,
            tooltipCtx: null,
            currentOrderId: null,
        };
    },

    mounted() {
        this.$canvas = document.getElementById('map');
        this.canvas = d3.select(this.$canvas);
        this.ctx = this.canvas.node().getContext('2d');
        this.$tooltipCanvas = document.getElementById('tooltip');
        this.tooltipCtx = this.$tooltipCanvas.getContext('2d');
    },

    methods: {
        ...mapActions({
            filterOrders: 'orders/filter',
        }),

        clickPoint() {
            if (this.currentOrderId) {
                this.filterOrders(
                    (order) => order.order_id === this.currentOrderId
                );
            }
        },

        trackPoints($event) {
            const canvasOffset = $('#map').offset();
            const offsetX = canvasOffset.left - $(window).scrollLeft();
            const offsetY = canvasOffset.top - $(window).scrollTop();

            const mouseX = $event.clientX - offsetX;
            const mouseY = $event.clientY - offsetY;
            this.isTooltipShow = false;
            this.currentOrderId = null;
            for (let i = 0; i < this.orders.length; i++) {
                const dot = this.orders[i];
                const dx = mouseX - dot.x;
                const dy = mouseY - dot.y;
                //for clustering "&& dot.profit !== 0"
                if (dx * dx + dy * dy < dot.r * dot.r) {
                    this.$tooltipCanvas.style.left = dot.x + 'px';
                    this.$tooltipCanvas.style.top = dot.y - 40 + 'px';
                    this.tooltipCtx.clearRect(
                        0,
                        0,
                        this.tooltipWidth,
                        this.tooltipHeight
                    );
                    this.tooltipCtx.fillText(dot.profit.toFixed(2), 5, 15);
                    this.isTooltipShow = true;
                    this.currentOrderId = dot.order_id;
                }
            }
        },

        clear() {
            this.ctx.clearRect(0, 0, this.width, this.height);
        },

        drawCircle(d) {
            this.ctx.fillStyle = styles.colorPrimary;
            this.ctx.beginPath();
            this.ctx.moveTo(d.x, d.y);
            this.ctx.arc(d.x, d.y, d.r, 0, this.doublePi);
            this.ctx.fill();
        },

        orderSizeConstructor(nodes) {
            const maxProfit = Math.max(...nodes);

            return d3.scaleLinear().domain([0, maxProfit]).range([2, 10]);
        },

        resetNodes(nodes) {
            const orderSize = this.orderSizeConstructor(this.profitData);

            nodes.forEach((node, index) => {
                const p = this.projection([
                    +node.address_lng,
                    +node.address_lat,
                ]);
                node.x = p[0];
                node.y = p[1];
                node.collided = false;
                node.count = 1;
                node.profit = this.profitData[index];
                node.r = orderSize(node.profit);
                node.a = Math.PI * node.r * node.r;
                node.orderSizeConstructor = this.orderSizeConstructor;
            });
        },
    },

    computed: {
        ...mapGetters({
            orders: 'orders/orders',
        }),

        doublePi() {
            return 2 * Math.PI;
        },

        defaultScale() {
            return this.width / this.doublePi;
        },

        tileCenter() {
            return this.projection(this.center);
        },

        geoProjection() {
            return d3
                .geoMercator()
                .scale(this.defaultScale)
                .center(this.projection.invert([0, 0]))
                .translate([0, 0]);
        },

        projection() {
            return d3
                .geoMercator()
                .center(this.center)
                .translate([this.width / 2, this.height / 2])
                .scale([this.width / 1.5]);
        },

        pathGenerator() {
            return d3.geoPath().projection(this.projection);
        },

        countryData() {
            return this.geoJson
                ? this.geoJson.features.map((feature) => ({feature}))
                : [];
        },

        profitData() {
            return this.orders.map((point) => point.product_profit);
        },
    },

    watch: {
        orders(newVal) {
            if (newVal.length > 0 && this.$route.name === 'orders') {
                // Set up nodes
                this.resetNodes(newVal);

                //redraw actual orders
                this.clear();
                newVal.filter((d) => d.r !== 0).forEach(this.drawCircle);
            }
        },
    },
};
</script>

<style lang="scss">
.map-container {
    width: 100%;
    height: 440px;
    position: relative;
    border-radius: $--border-radius-base;
    overflow: hidden;

    rect {
        fill: white;
    }
}

.map {
    position: absolute;
    left: 0;
    top: 0;
}

#tooltip {
    background-color: white;
    border: 1px solid blue;
    position: absolute;
    left: -200px;
    top: 100px;
}
</style>
