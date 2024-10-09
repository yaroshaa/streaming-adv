<template>
    <span
        class="circle-color"
        :class="[{ 'active' : active }, 'background-color-' + colorClass]"
    ></span>
</template>

<script>
export default {
    name: 'CircleColorComponent',
    props: {
        'colorClass': {
            type: String,
            default: ''
        },

        'active': {
            type: Boolean,
            default: false,
        }
    }
}
</script>

<style lang="scss" scoped>

.circle-color {
    display: inline-block;
    vertical-align: middle;
    width: 32px;
    height: 32px;
    border-radius: 50em;

    &.active {
        border: 4px solid $--background-color-blackly;
    }
}

</style>
