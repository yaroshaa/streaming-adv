<template>
    <div class="percentages">
        <div class="item" v-for="(store, index) in data" :key="index">
            <div class="icon">
                <img :src="store.icon_link" rel="icon" :title="store.name" :alt="store.name" />
            </div>
            <div class="value">{{ store.total.toFixed(1) }}%</div>
            <div :class="[
                'profit',
                store.last_30_minutes >= 0 ? 'positive' : 'negative'
            ]"
            >{{ Math.abs(store.last_30_minutes).toFixed(2) }}%</div>
        </div>
    </div>
</template>

<script>

export default {
    name: 'PercentagesPanel',
    components: {

    },

    data() {
        return {
            conversionRate: 'conversion_rate',
        }
    },

    props: {
        stores: {
            type: Array,
            required: true,
            default: () => [],
        },
    },

    computed: {
        data() {
            return this.stores.map((store) => {
                const result = store[this.conversionRate];
                result.icon_link = store.icon_link;
                result.name = store.name;

                return result;
            })
        }
    }
}
</script>

<style scoped lang="scss">

.item {
    display: flex;
    flex-direction: row;
    margin: 11px 0;
    padding: 1px;
}

.value {
    padding: 0 5px;
    font-family: Roboto;
    font-style: normal;
    font-weight: normal;
    font-size: 20px;
    line-height: 23px;
    display: flex;
    align-items: flex-end;
    text-align: center;
}

.profit {
    font-family: Roboto;
    font-style: normal;
    font-weight: 200;
    font-size: 12px;
    line-height: 14px;
    display: flex;
    align-items: flex-end;
    text-align: center;
}

.icon img {
    width: 25px;
    height: 25px;
}

.percentages {
    margin: 11px auto;
}

.positive {
    color: #5EFF5A;
}

.negative {
    color: #F01D1D;
}
</style>
