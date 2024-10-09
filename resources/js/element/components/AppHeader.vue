<!-- eslint-disable -->
<template>
    <el-menu mode="horizontal" class="nav-menu-header">
        <div class="header-buttons">
            <portal-target name="header-filter"></portal-target>
            <order-filters
                v-if="$route.name === 'orders'"
                currency-filter-enabled
                market-filter-enabled
                order-status-filter-enabled
                percentile-filter-enabled
                weight-filter-enabled
            />
            <order-filters
                v-if="$route.name === 'kpi-overview'"
                date-filter-enabled
                currency-filter-enabled
                date-granularity-filter-enabled
                kpiOverviewFilterEnabled
            />
            <order-filters
                v-if="$route.name === 'store-today'"
                single-date-filter-enabled
                market-filter-enabled
                currency-filter-enabled
                date-filter-type="date"
            />
            <order-filters
                v-if="$route.name === 'product-statistic'"
                search-filter-enabled
                date-filter-enabled
                market-filter-enabled
                currency-filter-enabled
            />
        </div>
        <el-button type="text" size="mini" icon="el-icon-monitor" @click="toggleFullScreen" class="button-header-menu"></el-button>
    </el-menu>
</template>

<script>
import {PortalTarget} from 'portal-vue';
import OrderFilters from './OrderFilters';

export default {
    name: 'AppHeader',

    components: {
        OrderFilters,
        PortalTarget,
    },

    methods: {
        toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }
    }
};
</script>

<style lang="scss"></style>
