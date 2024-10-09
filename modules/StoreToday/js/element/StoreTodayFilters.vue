<template>
    <div class="el-grid store-today-filters">
        <div v-for="(column, index) in columns" :key="index">
            <store-today-filter-row
                v-for="filter in column"
                :key="filter.key"
                :index="filter.key"
                :label="filter.name"
                :value="filter.value"
                :slug="filter.slug"
                :slug-position="filter.slugPosition"
                :active="filter.active"
            ></store-today-filter-row>
        </div>
    </div>
</template>

<script>
import StoreTodayFilterRow from '@/element/components/StoreToday/StoreTodayFilterRow';

export default {
    name: 'StoreTodayFilters',
    components: {StoreTodayFilterRow},

    props: {
        data: {
            type: Array,
            required: true,
            default: () => [],
        }
    },

    computed: {
        columns() {
            const columns = {};

            const countColumns = Math.ceil(this.data.length / 6);
            for (let i = 0; i < countColumns; i++) {
                columns[`col${i}`] = this.data.slice(
                    i * 6,
                    (i + 1) * 6
                );
            }

            return columns;
        },
    },
};
</script>

<style lang="scss" scoped>
.store-today-filters {
    //color: #ffffff;
    color: #000000;
    width: 40%;
    margin-right: 14px;
    &>div {
        width: 50%;
    }
}
</style>
