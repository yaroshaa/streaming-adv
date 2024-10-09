<!-- eslint-disable -->
<template>
    <div>
        <app-header/>
        <div>
            <h1 id="access-denied-title">Account is not verified, please contact someone to fix that</h1>
        </div>
    </div>
</template>

<!-- eslint-disable -->
<script>
import AppHeader from "./AppHeader";

export default {
    name: "AccessDenied",
    components: {
        AppHeader
    }
};
</script>

<style>
#access-denied-title {
    margin-top: 150px;
}
</style>
