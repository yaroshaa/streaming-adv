<script>
import { Line } from 'vue-chartjs';

export default {
    extends: Line,
    mounted() {
        this.renderChart(
            {
                labels: [
                    'January',
                    'February',
                    'March',
                    'April',
                    'May',
                    'June',
                    'July'
                ],
                datasets: [
                    {
                        label: 'Data 1',
                        data: [2, 10, 5, 9, 0, 6, 20],
                        backgroundColor: 'transparent',
                        borderColor: 'rgba(145,64,126,0.5)',
                        pointBackgroundColor: 'rgb(96,188,71)'
                    }
                ]
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
    }
};
</script>
