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

        <canvas id="map" :height="height" :width="width" class="map" @mousemove="trackPoints"></canvas>
        <canvas id="tooltip" :width="tooltipWidth" :height="tooltipHeight" v-show="isTooltipShow"></canvas>
    </div>
</template>

<script>
import * as d3 from 'd3';
import * as d3cluster from '@/assets/d3-cluster.js';
import europeGeoJson from '@/assets/geoJson/europe.json';
import {mapActions, mapGetters} from 'vuex';
import EventBus from '@/socket/eventbus';
import {ORDER_UPDATED_EVENT} from '@/socket/events';
import styles from '@/../style/element/_variables.scss';
import qs from 'qs';

export default {
    name: 'CompanyOverviewMap',

    data() {
        return {
            isTooltipShow: false,
            width: 760,
            height: 500,
            tooltipWidth: 100,
            tooltipHeight: 25,
            center: [18, 58],
            geoJson: europeGeoJson,
            $canvas: null,
            canvas: null,
            ctx: null,
            $tooltipCanvas: null,
            tooltipCtx: null,
            cluster: [],
        };
    },

    mounted() {
        this.$canvas = document.getElementById('map');
        this.canvas = d3.select(this.$canvas);
        this.ctx = this.canvas.node().getContext('2d');
        this.$tooltipCanvas = document.getElementById('tooltip');
        this.tooltipCtx = this.$tooltipCanvas.getContext('2d');

        EventBus.on(
            ORDER_UPDATED_EVENT,
            (event) => {
                if (this.$route.name === 'company-overview') {
                    const orders = [event.order];
                    this.addOrders(orders);
                    EventBus.unlock(ORDER_UPDATED_EVENT, this.$options.name);
                }
            },
            this.$options.name
        );

        this.update();
    },

    methods: {
        ...mapActions({
            filterOrders: 'orders/filter',
            addOrders: 'orders/addOrders',
            setOrders: 'orders/setOrders',
        }),

        trackPoints($event) {
            const canvasOffset = $('#map').offset();
            const offsetX = canvasOffset.left - $(window).scrollLeft();
            const offsetY = canvasOffset.top - $(window).scrollTop();

            const mouseX = $event.clientX - offsetX;
            const mouseY = $event.clientY - offsetY;
            this.isTooltipShow = false;
            for (let i = 0; i < this.orders.length; i++) {
                const dot = this.orders[i];
                const dx = mouseX -  dot.x;
                const dy = mouseY - dot.y;
                if (dx * dx + dy * dy < dot.r * dot.r && dot.profit !== 0) {
                    this.$tooltipCanvas.style.left = dot.x + 'px';
                    this.$tooltipCanvas.style.top = dot.y - 40 + 'px';
                    this.tooltipCtx.clearRect(0, 0, this.tooltipWidth, this.tooltipHeight);
                    this.tooltipCtx.fillText(dot.profit.toFixed(2), 5, 15);
                    this.isTooltipShow = true;
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

        ticked() {
            this.clear();
            this.cluster.nodes(this.orders.filter((d) => d.r !== 0));
            this.orders.filter((d) => d.r !== 0).forEach(this.drawCircle);
        },

        orderSizeConstructor(nodes) {
            const maxProfit = Math.max(...nodes);

            return d3.scaleLinear().domain([0, maxProfit]).range([2, 16]);
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

        update() {
            this.axios
                .get('/company-overview/orders', {
                    params: {
                        currency: this.storageCurrency,
                        date: this.storageDate,
                    },
                    paramsSerializer: (params) => qs.stringify(params),
                })
                .then((event) => {
                    if (!('data' in event)) {
                        return;
                    }

                    let items = Object.values(event.data);

                    this.setOrders(items);
                    EventBus.unlock(ORDER_UPDATED_EVENT, this.$options.name);

                    // Set up nodes
                    this.resetNodes(this.orders);

                    this.cluster = d3cluster.cluster().nodes(this.orders).on('tick', this.ticked);
                });
        },
    },

    computed: {
        ...mapGetters({
            orders: 'orders/orders',
            storageCurrency: 'filters/currency',
            storageDate: 'filters/date',
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
            return this.geoJson ? this.geoJson.features.map((feature) => ({feature})) : [];
        },

        profitData() {
            return this.orders.map((point) => point.product_profit);
        },
    },

    watch: {
        orders(newVal) {
            if (newVal.length > 0 && this.$route.name === 'company-overview') {

                // Set up nodes
                this.resetNodes(newVal);

                const orders = newVal.filter((d) => d.r !== 0);

                this.cluster.stop();
                this.cluster.nodes(orders);
                this.cluster.restart();
            }
        },

        storageCurrency() {
            this.update();
        },

        storageDate() {
            this.update();
        },
    },
};
</script>

<style>
.map-container {
    position: relative;
}
.map {
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    padding: 0;
    margin: 0 auto;
}

#tooltip {
    background-color: white;
    border: 1px solid blue;
    position: absolute;
    left: -200px;
    top: 100px;
}
</style>
