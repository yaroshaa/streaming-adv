<!-- eslint-disable -->
<template>
    <div class="el-grid">
        <div class="el-width-1-4">
            <orders-feed/>
        </div>
        <div class="el-width-3-4">
            <h4>Frequent sales</h4>
            <top-selling-products/>
            <hr>
            <div class="el-grid">
                <div class="el-width-expand">
                    <d3-map/>
                </div>
                <div class="el-width-auto">
                    <fifteen-min-totals/>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- eslint-disable -->
<script>
import OrdersFeed from "@/element/components/Orders/OrdersFeed";
import TopSellingProducts from "@/element/components/Orders/TopSellingProducts";
import AppHeader from "@/element/components/AppHeader";
import FifteenMinTotals from "@/element/components/Orders/FifteenMinTotals";
import GMap from "@/element/components/Orders/GMap";
import D3Map from "@/element/components/Orders/D3Map";

export default {
    name: 'OrdersFeedPage',

    components: {
        FifteenMinTotals,
        AppHeader,
        OrdersFeed,
        TopSellingProducts,
        GMap,
        D3Map,
    },
}
</script>

<style>
.status-dot {
    height: 7px;
    width: 7px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 10px;
}
</style>
