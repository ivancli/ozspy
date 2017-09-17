<template>
    <div class="wrapper">
        <slot name="header"></slot>
        <slot name="sidebar"></slot>
        <slot name="content"></slot>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.info("Angle component mounted.");
        }
    }
</script>

<style>

</style>