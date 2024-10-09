<!-- eslint-disable -->
<template>
    <GmapMap
        :center="center"
        :zoom="6"
        map-type-id="terrain"
        class="g-map-container"
        ref="mapRef"
    >
        <GmapMarker
            :key="index"
            v-for="(order, index) in orders"
            :position="{lat: order.address_lat, lng: order.address_lng}"
            :clickable="true"
            :draggable="false"
            @click="markerClick(order)"
        />
        <gmap-info-window
            :position="infoWindowPosition"
            :opened="infoWindowOpened"
            @closeclick="infoWindowOpened = false"
        >
            Order <a class="info-window-link" @click="filterOrders(order => order.id === currentOrderId)">#{{ currentOrderId }}</a>
        </gmap-info-window>
    </GmapMap>
</template>

<!-- eslint-disable -->
<script>
import {mapGetters, mapActions} from 'vuex'

export default {
    name: 'GMap',
    data() {
        return {
            center: {lat:62, lng:10},
            infoWindowOpened: false,
            infoWindowPosition: {
                lat: 0,
                lng: 0
            },
            currentOrderId: null
        }
    },
    methods: {
        ...mapActions({
            filterOrders: "orders/filter"
        }),
        markerClick(order) {
            this.center = {lat: order.address_lat, lng: order.address_lng}
            this.infoWindowPosition = {lat: order.address_lat, lng: order.address_lng}
            this.currentOrderId = order.order_id
            this.infoWindowOpened = true;
        },
    },
    computed: {
        ...mapGetters({
            orders: "orders/orders"
        })
    },
}
</script>

<style lang="scss" scoped>
.g-map-container {
    width: 96%;
    height: 370px;
    margin: 0 2%;
    .info-window-link {
        cursor: pointer;
    }
}
</style>
