<template>
    <div class="el-grid el-grid-lg">
        <div class="el-width-3-10">
            <div class="el-grid">
                <div class="el-width-1-3">
                    <h1>Holiday Events</h1>
                </div>
                <div class="el-width-2-3 el-text-right">
                    <a class="new-holiday-event-button" @click="addNewDate">+ New Holiday Event</a>
                </div>
            </div>
            <div class="el-flex el-flex-column el-flex-center flex-wrap mt-3">
                <div
                    v-for="(holidaysEvent, index) in holidayEvents"
                    :key="index"
                    class="holiday-event-row"
                    @click="selectHolidayEvent(holidaysEvent)"
                >
                    <button
                        :class="[
                            `holiday-event`,
                            isActive(holidaysEvent) ? `active` : '',
                        ]"
                    >
                        <span>{{ holidaysEvent.title }}: {{ formatDate(holidaysEvent.date) }}</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="el-width-7-10">
            <HolidayEventFormComponent
                ref="form"
                @remove="onRemove"
                @save="onSave"
            ></HolidayEventFormComponent>
        </div>
    </div>
</template>

<script>
import HolidayEventFormComponent from '@/element/components/MarketingOverview/Settings/HolidayEventFormComponent';
import {Notification} from 'element-ui';
import {errorResponseToString} from '@/core/helper';
import {deleteHolidayEvent, getHolidayEvents, postHolidayEvent, putHolidayEvent} from '@/service/request/holidayEvent';
import moment from 'moment';

export default {
    name: 'HolidayEvent',
    components: {
        HolidayEventFormComponent: HolidayEventFormComponent,
    },

    data() {
        return {
            holidayEvents: [],
            currentHolidayEvent: null,
            emptyHolidayEvent: {
                id: 0,
                title: '',
                date: null,
            },
        };
    },

    provide: {
        currentHolidayEvent: () => this.currentHolidayEvent,
    },

    mounted() {
        getHolidayEvents().then((response) => {
            this.holidayEvents = response.data.data;
        });
        this.selectHolidayEvent(this.emptyHolidayEvent);
    },

    methods: {
        formatDate(datetime) {
            return moment(datetime).format('YYYY-MM-DD');
        },

        isActive(holidayEvent) {
            return this.currentHolidayEvent && this.currentHolidayEvent.id === holidayEvent.id;
        },

        selectHolidayEvent(holidayEvent) {
            let currentHolidayEvent = JSON.parse(JSON.stringify(holidayEvent));
            this.currentHolidayEvent = currentHolidayEvent;
            this.$refs.form.setHolidayEvent(currentHolidayEvent);
        },

        addNewDate() {
            this.selectHolidayEvent(this.emptyHolidayEvent);
        },

        onSave(holidayEvent) {
            let index = this.findIndexOfHolidayEvent(holidayEvent);

            if (index === -1) {
                postHolidayEvent(holidayEvent)
                    .then((response) => {
                        let savedHolidayEvent = response.data.data;
                        this.holidayEvents.push(savedHolidayEvent);
                        this.selectHolidayEvent(savedHolidayEvent);
                        Notification.success({
                            title: 'Saved',
                            message: 'Holiday event added',
                        });
                    })
                    .catch((e) => {
                        Notification.error({
                            title: 'Error',
                            message: errorResponseToString(e),
                        });
                    });
            } else {
                putHolidayEvent(holidayEvent.id, holidayEvent)
                    .then((response) => {
                        let savedHolidayEvent = response.data.data;
                        this.holidayEvents.splice(index, 1, savedHolidayEvent);
                        this.selectHolidayEvent(savedHolidayEvent);
                        Notification.success({
                            title: 'Updated',
                            message: 'Holiday event saved',
                        });
                    })
                    .catch((e) => {
                        Notification.error({
                            title: 'Error',
                            message: errorResponseToString(e),
                        });
                    });
            }
        },

        onRemove(holidayEvent) {
            let index = this.findIndexOfHolidayEvent(holidayEvent);

            if (index !== -1) {
                deleteHolidayEvent(holidayEvent.id)
                    .then(() => {
                        this.holidayEvents.splice(index, 1);
                        this.selectHolidayEvent(this.emptyHolidayEvent);
                        Notification.success({
                            title: 'Deleted',
                            message: 'Holiday event deleted',
                        });
                    })
                    .catch((e) => {
                        Notification.success({
                            title: 'Error',
                            message: errorResponseToString(e),
                        });
                    });
            }
        },

        findIndexOfHolidayEvent(holidayEvent) {
            return this.holidayEvents.map((holidayEvent) => holidayEvent.id).indexOf(holidayEvent.id);
        },
    },
};
</script>

<style lang="scss" scoped>
.new-holiday-event-button {
    //color: #991bfa;
    color: #000000;
    font-size: 14px;
    line-height: 24px;
    cursor: pointer;
}

.holiday-event-row {
    text-align: center;
}

.holiday-event {
    border-radius: 50em;
    padding: 5px;
    margin: 7px;
    border: 2px solid #000000;

    &.active {
        //color: $--color-white;
        color: #FFFFFF;
        //background: $--color-base-dark;
        background: #9a9b9d;
    }

    &:hover {
        color: #FFFFFF;
        //background: #191932;
        background: #9a9b9d;
    }
}
</style>
