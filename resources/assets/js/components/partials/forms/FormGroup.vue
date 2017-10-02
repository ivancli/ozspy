<template>
    <div class="form-group">
        <input :id="name" :type="inputType" :placeholder="placeholder" class="form-control" v-if="isInput">
        <select name="" id="" v-if="isSelect">
            <option value="">{{ placeholder }}</option>
            <slot name="options"></slot>
        </select>
        <span v-if="hasFeedback" :class="feedbackClass" class="form-control-feedback"></span>
    </div>
</template>

<script>
    export default {
        props: {
            type: {
                default: 'input',
                type: String,
            },
            name: {
                default: null,
                type: String,
            },
            inputType: {
                default: 'text',
                type: String,
            },
            placeholder: {
                default: null,
                type: String,
            },
            feedbackClass: {
                default: null,
                type: String
            }
        },
        mounted() {
            console.info('FormGroup component mounted.');
        },
        computed: {
            isInput() {
                return this.type === 'input';
            },
            isSelect() {
                return this.type === 'select';
            },
            hasFeedback() {
                return this.feedbackClass !== null;
            }
        }
    }
</script>

<style>

</style>