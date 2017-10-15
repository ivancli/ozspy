<template>
    <modal cancel-text="OK" :value="show" :effect="effect" :small="small" :title="title" @ok="onClickOk">
        {{ content }}
    </modal>
</template>

<script>

    //    import {
    //        modal
    //    } from 'vue-strap';
    import modal from './Modal.vue';

    import {
        CLEAR_ALERT_MODAL
    } from '../../../actions/action-types';

    export default {
        mounted() {
            console.info('Alert component mounted.');
        },
        components: {
            modal
        },
        methods: {
            onClickOk() {
                this.$store.dispatch(CLEAR_ALERT_MODAL);
            }
        },
        computed: {
            title() {
                return this.$store.getters['alert/title'];
            },
            content() {
                return this.$store.getters['alert/content'];
            },
            effect() {
                return this.$store.getters['alert/effect'];
            },
            small() {
                return this.$store.getters['alert/small'];
            },
            show() {
                return this.content !== null;
            }
        }
    }
</script>

<style>

</style>