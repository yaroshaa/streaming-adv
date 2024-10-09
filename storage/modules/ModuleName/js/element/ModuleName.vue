<template>
    <div>
        <h1>ModuleName</h1>
        <input v-model="number" />
        <el-button @click="calc">Calculate</el-button>
    </div>
</template>

<script>
import {Button, Notification} from 'element-ui';
import qs from 'qs';
import {errorResponseToString} from '@/core/helper';

export default {
    name: 'ModuleName',
    components: {
        ElButton: Button
    },

    data() {
        return {
            number: 0
        }
    },

    methods: {
        calc() {
            this.axios
                .get('/module-name', {
                    params: {
                        number: this.number,
                    },
                    paramsSerializer: params => {
                        return qs.stringify(params)
                    }
                })
                .then((response) => {
                    Notification.success({
                        title: 'Success',
                        message: response.data.data.message,
                        type: 'success'
                    });
                }).catch((e) => {
                    Notification.error({
                        title: 'Error',
                        message: errorResponseToString(e),
                    });
                });
        }
    }
}
</script>

<style scoped>

</style>
