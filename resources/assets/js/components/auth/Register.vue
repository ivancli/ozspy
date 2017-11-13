<template>
    <div class="abs-center wd-xl mx-wd-100">
        <!-- START panel-->
        <div class="p">
            <logo></logo>
        </div>
        <div class="panel widget b0">
            <div class="panel-body">
                <p class="text-center">Sign up for new account</p>
                <form role="form" novalidate @input="form.errors.clear($event.target.name)" @submit.prevent="onSubmit">
                    <div class="form-group has-feedback" :class="errorClass('first_name')">
                        <input id="first-name" type="text" name="first_name" placeholder="First name" class="form-control" v-model="form.first_name">
                        <span class="fa fa-tag form-control-feedback text-muted"></span>
                        <span class="help text-danger" v-if="form.errors.has('first_name')" v-text="form.errors.getError('first_name')"></span>
                    </div>
                    <div class="form-group has-feedback" :class="errorClass('last_name')">
                        <input id="last-name" type="text" name="last_name" placeholder="Last name" class="form-control" v-model="form.last_name">
                        <span class="fa fa-tag form-control-feedback text-muted"></span>
                        <span class="help text-danger" v-if="form.errors.has('last_name')" v-text="form.errors.getError('last_name')"></span>
                    </div>
                    <div class="form-group has-feedback" :class="errorClass('email')">
                        <input id="email" type="email" name="email" placeholder="Email" class="form-control" v-model="form.email">
                        <span class="fa fa-envelope-o form-control-feedback text-muted"></span>
                        <span class="help text-danger" v-if="form.errors.has('email')" v-text="form.errors.getError('email')"></span>
                    </div>
                    <div class="form-group has-feedback" :class="errorClass('password')">
                        <input id="password" type="password" name="password" placeholder="Password" class="form-control" v-model="form.password">
                        <span class="fa fa-lock form-control-feedback text-muted"></span>
                        <span class="help text-danger" v-if="form.errors.has('password')" v-text="form.errors.getError('password')"></span>
                    </div>
                    <div class="form-group has-feedback" :class="errorClass('password_confirmation')">
                        <input id="password-confirmation" name="password_confirmation" type="password" placeholder="Confirm password" class="form-control" v-model="form.password_confirmation">
                        <span class="fa fa-lock form-control-feedback text-muted"></span>
                        <span class="help text-danger" v-if="form.errors.has('password_confirmation')" v-text="form.errors.getError('password_confirmation')"></span>
                    </div>
                    <div class=clearfix>
                        <button type="submit" class="btn btn-block btn-sm btn-primary">Sign Up</button>
                    </div>
                    <div class="clearfix">
                        <div class="text-center mt-sm">
                            <a href="/auth/login" class="text-muted">
                                <small>Already have an account?</small>
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
            console.info('Index component mounted.');
        },
        data() {
            return {
                form: new Form({
                    first_name: null,
                    last_name: null,
                    email: null,
                    password: null,
                    password_confirmation: null,
                }),
                loading: new Loading(this.$store)
            }
        },
        methods: {
            onSubmit() {
                this.loading.setLoadingPromise();
                this.form.post('/auth/register')
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