<template>
    <div class="filters-block">
        <el-date-picker
            v-model="date"
            type="daterange"
            :picker-options="pickerOptions"
            range-separator="To"
            start-placeholder="Start date"
            end-placeholder="End date"
            class="filters-block-element"
        >
        </el-date-picker>

        <el-select
            class="filters-block-element"
            v-model="currency"
            placeholder="Currency"
            value-key="id"
            @change="loadCurrencies"
            key="key-currency"
        >
            <!-- eslint-disable -->
            <el-option v-for="currency in currencies" :key="currency.id" :label="currency.name" :value="currency">
            </el-option>
        </el-select>

        <div class="filters-block-element">
            <span v-for="rate in rates" :key="rate.to.code" class="rates">
                {{ currency.code }}/{{ rate.to.code }} = {{ rate.rate }}
            </span>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';
import {mapActions, mapGetters} from 'vuex';
import {DatePicker, Option, Select} from 'element-ui';

export default {
    name: 'CompanyOverviewFilters',

    components: {
        ElDatePicker: DatePicker,
        ElOption: Option,
        ElSelect: Select,
    },

    data() {
        return {
            currencies: [],
            rates: [],
            pickerOptions: {
                shortcuts: [
                    {
                        text: 'Last week',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                            picker.$emit('pick', [start, end]);
                        },
                    },
                    {
                        text: 'Last month',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                            picker.$emit('pick', [start, end]);
                        },
                    },
                    {
                        text: 'This month',
                        onClick(picker) {
                            const end = new Date();
                            const start = moment().startOf('month').toDate();
                            picker.$emit('pick', [start, end]);
                        },
                    },
                    {
                        text: 'Last 3 months',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                            picker.$emit('pick', [start, end]);
                        },
                    },
                    {
                        text: 'Year to date',
                        onClick(picker) {
                            const end = new Date();
                            const start = moment().subtract(1, 'year').toDate();
                            picker.$emit('pick', [start, end]);
                        },
                    },
                    {
                        text: 'This year',
                        onClick(picker) {
                            const end = new Date();
                            const start = moment().startOf('year').toDate();
                            picker.$emit('pick', [start, end]);
                        },
                    },
                ],
            },
        };
    },

    mounted() {
        axios.get('/currencies').then((data) => {
            this.currencies = data.data;
        });
        this.loadCurrencies();
    },

    methods: {
        ...mapActions({
            updateCurrency: 'filters/updateCurrency',
            resetOrdersFilter: 'orders/resetFilters',
            updateDate: 'filters/updateDate',
        }),

        loadCurrencies() {
            axios.get('/exchange-rates/' + this.currency.id).then((data) => {
                this.rates = data.data
                    .filter((x) => x.to.id !== this.currency.id)
                    .map((x) => {
                        x.rate = x.rate.toFixed(2);
                        return x;
                    });
            });
        },
    },

    computed: {
        ...mapGetters({
            storageCurrency: 'filters/currency',
            storageDate: 'filters/date',
        }),

        currency: {
            get() {
                return this.storageCurrency;
            },

            set(value) {
                this.resetOrdersFilter();
                this.updateCurrency(value);
            },
        },

        date: {
            get() {
                return this.storageDate;
            },

            set(value) {
                this.updateDate(value);
            },
        },
    },
};
</script>

<style lang="scss" scoped>
.filters-block {
    .filters-block-element {
        .rates {
            &::after {
                content: ' | ';
            }

            &:last-child {
                &::after {
                    content: none;
                }
            }
        }
    }
}
</style>
