<template>
    <div v-if="typeof this.data.warehouses !== 'undefined' && this.data.warehouses.length > 0" class="container mx-auto bg-container">
        <Warehouse v-for="warehouse in this.data.warehouses" :data="warehouse" :key="`warehouse-${warehouse.id}`"></Warehouse>
    </div>
    <div v-else-if="typeof this.data.warehouses === 'undefined'" class="container mx-auto bg-container text-white font-black text-2xl text-center h-screen">
        Load data...
    </div>
    <div v-else class="container mx-auto bg-container text-white font-black text-2xl text-center h-screen">
        No data found
    </div>
</template>

<script>
import Warehouse from './Warehouse';
import {mapActions, mapGetters} from 'vuex';

export default {
    name: 'WarehousesList',
    components: {
        Warehouse
    },

    mounted() {
        this.update();
    },

    methods: {
        ...mapActions({
            setData: 'marketingOverview/setData',
        }),

        update() {
            this.axios
                .get('/marketing-overview/data', {
                    params: {
                        date_granularity: this.dateGranularity,
                        date: this.storageDate,
                        'currency[id]': this.storageCurrency.id,
                    },
                })
                .then((event) => {
                    if (!('data' in event)) {
                        return;
                    }

                    this.setData(event.data.data);
                });
        },
    },

    computed: {
        ...mapGetters({
            data: 'marketingOverview/data',
            storageCurrency: 'filters/currency',
            storageDate: 'filters/date',
            dateGranularity: 'filters/dateGranularity'
        }),
    },

}
</script>
