<template>
    <div class="abs-center wd-xl mx-wd-100">
        <!-- START panel-->
        <div class="p">
            <logo></logo>
        </div>
        <div class="panel widget b0">
            <div class="panel-body">
                <p class="text-center">Please enter your email address to reset password</p>
                <form role="form" @submit.prevent="onSubmit">
                    <div class="form-group has-feedback" :class="errorClass('email')">
                        <input type="email" placeholder="Email address" class="form-control" v-model="form.email">
                        <span class="fa fa-envelope-o form-control-feedback text-muted"></span>
                        <span class="help text-danger" v-if="form.errors.has('email')" v-text="form.errors.getError('email')"></span>
                    </div>
                    <div class=clearfix>
                        <button type="submit" class="btn btn-block btn-sm btn-primary">Reset Password</button>
                    </div>
                    <div class="clearfix">
                        <div class="text-center mt-sm">
                            <a class="text-muted" href="/auth/login">
                                <small>Remember your password?</small>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <alert-modal></alert-modal>
        <loading></loading>
    </div>
</template>

<script>
    import logo from '../partials/branding/Logo.vue';

    import Form from '../../classes/form';
    import Loading from '../../classes/loadings/loading';

    import alertModal from '../partials/modals/Alert.vue';
    import loading from '../partials/loadings/Dollar.vue';


    import {
        SET_ALERT_MODAL,
    } from '../../actions/action-types';

    export default {
        components: {
            logo,
            alertModal,
            loading,
        },
        mounted() {
            console.info('Forgot component mounted.');
        },
        data() {
            return {
                form: new Form({
                    email: null,
                }),
                loading: new Loading(this.$store),
            }
        },
        methods: {
            onSubmit() {
                this.loading.setLoadingPromise();
                this.form.post('/auth/forgot')
                    .then(data => {
                        this.loading.unsetLoadingPromise();
                        this.showSuccessMessage();
                        console.info('data', data);
                    })
                    .catch(errors => {
                        this.loading.unsetLoadingPromise();
                        console.info('errors', errors);
                    });
            },
            errorClass(field) {
                if (this.form.errors.has(field)) {
                    return 'has-error';
                }
                return null;
            },
            showSuccessMessage() {
                this.$store.dispatch(SET_ALERT_MODAL, {
                    title: 'Reset Password Email Sent',
                    content: 'An email with reset password link has been sent to your inbox. Please follow instructions to reset your password.',
                });
            },
        }
    }
</script>

<style>

</style>