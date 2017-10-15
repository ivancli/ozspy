<template>
    <section>
        <div class="content-wrapper">
            <slot name="page-content"></slot>
        </div>
        <alert-modal></alert-modal>
    </section>
</template>

<script>
    import alertModal from './modals/Alert.vue';

    export default {
        components:{
            alertModal
        },
        mounted() {
            console.info("Content component mounted.");
        }
    }
</script>

<style>

</style>