<script>
import { Line } from 'vue-chartjs';
import ChartJsPluginDataLabels from 'chartjs-plugin-datalabels';
import axios from 'axios';
import moment from 'moment';
import EventBus from '@/socket/eventbus';
import {ANALYTIC_TRACK_EVENT} from '@/socket/events';


export default {
    data() {
        return {
            chartData: {
                labels: [],
                datasets: [],
            },

            dataSets: [],
            options: {
                maintainAspectRatio: false,
                animation: {
                    duration: 0
                }
            },

            lastHour: moment().format('H')
        }
    },


    extends: Line,
    mounted() {
        this.addPlugin(ChartJsPluginDataLabels)
        this.syncData()

        EventBus.on(
            ANALYTIC_TRACK_EVENT,
            (data) => {

                for (let i = 0; i < this.chartData.datasets.length; i++) {
                    const dataItem = data.data.find(item => item.name === this.chartData.datasets[i].label)

                    if (dataItem) {
                        const lastIndex = this.chartData.datasets[i].data.length - 1
                        this.chartData.datasets[i].data[lastIndex] = dataItem.last15MinutesConversionRate
                    }
                }

                if (this.lastHour !== moment().format('H')) {
                    this.syncData()
                    this.lastHour = moment().format('H')
                } else {
                    this.renderChart(this.chartData, this.options);
                }

                EventBus.unlock(ANALYTIC_TRACK_EVENT, 'ConversionChart')
            },
            'ConversionChart'
        );
    },

    methods: {
        syncData: function() {
            const startOfMonth = moment().startOf('month').format('DD.MM.YYYY');
            const endOfMonth   = moment().endOf('month').format('DD.MM.YYYY');

            axios.get('/analytic/conversations/by-hours', {
                params: {
                    date_from: startOfMonth,
                    date_to: endOfMonth
                }
            }).then((data) => {
                const colors = [
                    '#ffd700',
                    '#ffb14e',
                    '#fa8775',
                    '#ea5f94',
                    '#cd34b5',
                    '#9d02d7',
                    '#0000ff'
                ];
                this.chartData.labels = data.data[0].hour
                data.data.forEach((value, key) => {
                    const displayLabels = value.conversionRate.map(() => false)
                    displayLabels[displayLabels.length - 1] = true

                    this.chartData.datasets.push({
                        label: value.name,
                        data: value.conversionRate,
                        lineTension: 0,
                        backgroundColor: colors[key],
                        borderColor: colors[key],
                        pointBackgroundColor: colors[key],
                        fill: false,
                        datalabels: {
                            align: 'top',
                            anchor: 'end',
                            clamp: true,
                            backgroundColor: colors[key],
                            color: 'white',
                            display: displayLabels
                        }
                    });
                });


                this.renderChart(this.chartData, this.options);
            });
        }
    }
};
</script>
