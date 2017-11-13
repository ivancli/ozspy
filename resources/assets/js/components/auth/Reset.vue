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
                        <input id="email" type="email" name="email" placeholder="Email" class="form-control" v-model="form.email">
                        <span class="fa fa-envelope-o form-control-feedback text-muted"></span>
                        <span class="help text-danger" v-if="form.errors.has('email')" v-text="form.errors.getError('email')"></span>
                    </div>
                    <div class="form-group has-feedback" :class="errorClass('password')">
                        <input type="password" placeholder="New password" class="form-control">
                        <span class="fa fa-lock form-control-feedback text-muted"></span>
                        <span class="help text-danger" v-if="form.errors.has('password')" v-text="form.errors.getError('password')"></span>
                    </div>
                    <div class="form-group has-feedback" :class="errorClass('password_confirmation')">
                        <input type="password" placeholder="Confirm new password" class="form-control">
                        <span class="fa fa-lock form-control-feedback text-muted"></span>
                        <span class="help text-danger" v-if="form.errors.has('password_confirmation')" v-text="form.errors.getError('password_confirmation')"></span>
                    </div>
                    <div class=clearfix>
                        <button type="submit" class="btn btn-block btn-sm btn-primary">Reset Password</button>
                    </div>
                    <div class="clearfix">
                        <div class="pull-left mt-sm">
                            <a class="text-muted" href="/auth">
                                <small>Remember your password?</small>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <loading></loading>
    </div>
</template>

<script>
    import logo from '../partials/branding/Logo.vue';
    import loading from '../partials/loadings/Dollar.vue';

    import Form from '../../classes/form';
    import Loading from '../../classes/loadings/loading';

    export default {
        components: {
            logo,
            loading,
        },
        mounted() {
            console.info('Reset component mounted.');
        },
        data() {
            return {
                form: new Form({
                    password: null,
                    password_confirmation: null,
                    email: email,
                    token: token,
                }),
                loading: new Loading(this.$store),
            };
        },
        methods: {
            onSubmit() {
                this.loading.setLoadingPromise();
                this.form.post('/auth/reset')
                    .then(data => {
                        this.loading.unsetLoadingPromise();
                        if (data.redirect_path) {
                            window.location.href = data.redirect_path;
                        }
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
            }
        }
    }
</script>

<style>

</style>