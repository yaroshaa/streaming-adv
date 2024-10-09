<template>
    <form class="form">
        <h1>Title</h1>
        <div class="form-group">
            <el-input
                class="text-input"
                placeholder="Holiday event title"
                v-model="title"
            ></el-input>
        </div>

        <h2>Holiday event</h2>
        <div class="form-group">
            <div class="date-wrapper">
                <el-date-picker v-model="date"></el-date-picker>
            </div>
        </div>

        <el-button
            type="primary-submit"
            size="large"
            class="el-width-sm rounded-sm"
            @click="saveHolidayEvent"
        >
            Save changes
        </el-button>
        <el-button
            v-show="id !== 0"
            type="danger-submit"
            size="large"
            class="el-width-xs rounded-sm remove-button"
            icon="el-icon-delete"
            @click="removeHolidayEvent"
        >
            Delete
        </el-button>
    </form>
</template>

<script>

import {Button, Input, DatePicker} from 'element-ui';

export default {
    name: 'HolidayEventFormComponent',
    components: {
        ElInput: Input,
        ElDatePicker: DatePicker,
        ElButton: Button
    },

    data() {
        return {
            id: 0,
            title: '',
            date: null,
        }
    },

    inject: [
        'currentHolidayEvent',
    ],

    methods: {
        getData() {
            return {
                'id': this.id,
                'title': this.title,
                'date': this.date,
            };
        },

        saveHolidayEvent() {
            this.$emit('save', this.getData());
        },

        removeHolidayEvent() {
            this.$emit('remove', this.getData())
        },

        setHolidayEvent(event) {
            this.id = event.id;
            this.title = event.title;
            this.date = event.date;
        }
    },
}
</script>


<style lang="scss" scoped>

.form-group {
    margin-bottom: 35px;
}

.text-input {
    width: 234px;
}

.date-wrapper {
    margin-top: 20px;
    margin-bottom: 75px;
}

.remove-button {
    margin-left: 12px;
}

</style>
