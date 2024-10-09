<script>
import { Line } from 'vue-chartjs';
import axios from 'axios';

export default {
    extends: Line,
    mounted() {
        axios.get('/analytic').then((data) => {
            const analytic = [];
            data.data.forEach(function (value, key) {
                analytic.push({
                    label: value.name,
                    data: value.totalVisitors,
                    lineTension: 0,
                    backgroundColor: 'transparent',
                    borderColor: (key === 0)  ? 'rgba(1, 116, 188, 0.50)' : 'rgba(44,168,102,0.5)',
                    pointBackgroundColor: (key === 0)  ? 'rgba(171, 71, 188, 1)' : 'rgba(236,24,112,0.5)',
                });
            });
            this.renderChart(

                {
                    labels:data.data[0].date,
                    datasets: analytic,
                },
                {
                    responsive: true,
                    maintainAspectRatio: false,
                    title: {
                        display: true,
                        text: 'My Data'
                    }
                }
            );
        });
    }
};
</script>
