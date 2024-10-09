<template>
    <div>
        <portal to="header-filter">
            <company-overview-filter></company-overview-filter>
        </portal>
        <div id="graphs">
            <div class="stream block">
                <stream-graph></stream-graph>
            </div>
            <div class="block">
                <pie-chart></pie-chart>
            </div>
        </div>
        <div class="details">
            <div class="totals">
                <totals></totals>
                <totals-by-market></totals-by-market>
            </div>
            <div class="map-block">
                <company-overview-map></company-overview-map>
            </div>
        </div>
    </div>
</template>

<script>
import CompanyOverviewMap from './Map.vue';
import CompanyOverviewFilter from '@/element/components/CompanyOverview/CompanyOverviewFilter.vue';
import {Portal} from 'portal-vue';
import PieChart from './PieChart';
import StreamGraph from './StreamGraph';
import Totals from './Totals';
import TotalsByMarket from './TotalsByMarket';

export default {
    components: {
        CompanyOverviewMap,
        CompanyOverviewFilter,
        TotalsByMarket,
        Totals,
        PieChart,
        Portal,
        StreamGraph,
    },
};
</script>

<style lang="scss">
#graphs {
    display: flex;
    justify-content: space-between;
    margin-left: 5px;

    .block:first-child {
        width: 69.9%;
    }

    .block:last-child {
        width: 29.9%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
}
.details {
    display: flex;
    margin-top: 30px;
    .totals {
        flex: 4;
    }
}
</style>
