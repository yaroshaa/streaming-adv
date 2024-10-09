<template>
    <div class="flex flex-col">
        <div class="text-gray-200 font-bold uppercase pl-10 pt-2.5 pb-2.5 border-b border-gray-500">
            {{ data.name }}
        </div>
        <div v-for="(row, index) in stationsRows" :key="`${data.id}-${index}`" class="grid grid-cols-4 divide-x divide-gray-200 text-gray-200 mt-2.5 pb-2.5 border-b border-gray-500">
            <WarehouseStation v-for="(station, index) in row"
                :key="`${data.id} - ${data.station} - ${index}`"
                :warehouse-id="data.id"
                :station="station.station"
                :data="station"
            ></WarehouseStation>

            <WarehouseStationTotal v-if="stationsRows.length - 1 === index"
                :key="`${data.id} - all - ${data.id}`"
                :warehouse-id="data.id"
                :data="{
                    id: data.id,
                    awaiting_stock: data.awaiting_stock,
                    in_packing: data.in_packing,
                    open: data.open,
                    total: data.total,
                    per_hour: data.per_hour
                }"
            ></WarehouseStationTotal>
        </div>
    </div>
</template>

<script>

import WarehouseStation from './WarehouseStation';
import WarehouseStationTotal from './WarehouseStationTotal';

export default {
    name: 'Warehouse',

    components: {
        WarehouseStation,
        WarehouseStationTotal
    },

    data() {
        return {
            countLines: 0,
            stations: [],
            stationsRows: []
        }
    },

    props: {
        data: {
            type: Object,
            default: () => {
                return {
                    id: 0,
                    name: '',
                    by_station: []
                }
            }
        }
    },

    mounted() {
        this.countLines = Math.ceil(this.data.by_station.length / 4);
        this.stations = this.data.by_station;

        const result = [];
        const countCells = 4;

        for (let i = 0; i < this.countLines; i++) {
            result[i] = [];
            for (let m = i * countCells; m < i * 4 + countCells; m++) {

                if (this.stations.length - 1 > m) {
                    result[i].push({
                        ...this.stations[m]
                    })
                }
            }
        }

        this.stationsRows = result;
    },

};
</script>
