<template>
    <section>
        <div class="content-wrapper">
            <slot name="page-content"></slot>
        </div>
    </section>
</template>

<script>
    export default {
        mounted() {
            console.info("Content component mounted.");
        }
    }
</script>

<style>

</style>