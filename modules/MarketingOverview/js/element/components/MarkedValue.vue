<template>
    <span :class="cssClass">{{ formattedValue }}</span>
</template>

<script>
export default {
    name: 'MarkedValue',
    props: {
        value: {
            type: Number,
            default: 0.00
        }
    },

    computed: {
        cssClass() {
            if (this.value == 0.00) {
                return 'white';
            }
            if (this.value > 0.00) {
                return 'green';
            }

            return 'red';
        },

        formattedValue()
        {
            return (this.value > 0 ? '+' : '') + this.value + '%';
        }
    }
}
</script>

<style scoped>
.white {
    color: #fff;
}

.red {
     color: #c10000;
 }

.green {
     color: #00ff0b;
 }
</style>
