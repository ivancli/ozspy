<template>
    <div class="abs-center wd-xl mx-wd-100">
        <!-- START panel-->
        <div class="p">
            <logo></logo>
        </div>
        <div class="panel widget b0">
            <div class="panel-body">
                <p class="text-center"><strong>Welcome to OzSpy</strong></p>
                <p class="text-center">Please login for better experience</p>
                <form role="form" @submit.prevent="onSubmit" @input="form.errors.clear($event.target.name)" novalidate>
                    <div class="form-group has-feedback" :class="errorClass('email')">
                        <input id="email" type="email" name="email" placeholder="Email" class="form-control" v-model="form.email">
                        <span class="fa fa-envelope-o form-control-feedback text-muted"></span>
                        <span class="help text-danger" v-if="form.errors.has('email')" v-text="form.errors.getError('email')"></span>
                    </div>
                    <div class="form-group has-feedback" :class="errorClass('password')">
                        <input id="exampleInputPassword1" name="password" type="password" placeholder="Password" class="form-control" v-model="form.password">
                        <span class="fa fa-lock form-control-feedback text-muted"></span>
                        <span class="help text-danger" v-if="form.errors.has('password')" v-text="form.errors.getError('password')"></span>
                    </div>
                    <div class=clearfix>
                        <button href="dashboard.html" class="btn btn-block btn-sm btn-primary">Login</button>
                    </div>
                    <div class="clearfix">
                        <div class="pull-left mt-sm">
                            <a href="/auth/register" class="text-muted">
                                <small>Don't have an account?</small>
                            </a>
                        </div>
                        <div class="pull-right mt-sm">
                            <a href="/auth/forgot" class="text-muted">
                                <small>Forgot your password?</small>
                            </a>
                        </div>
                    </div>
                    <hr>
                    <div class="clearfix">
                        <a href="/auth/facebook" class="btn btn-block btn-primary btn-facebook">
                            <i class="fa fa-facebook-square"></i>
                            &nbsp;
                            Login with Facebook
                        </a>
                        <a href="/auth/twitter" class="btn btn-block btn-info btn-twitter">
                            <i class="fa fa-twitter-square"></i>
                            &nbsp;
                            Login with Twitter
                        </a>
                        <a href="/auth/google" class="btn btn-block btn-danger btn-google-plus">
                            <i class="fa fa-google-plus-square"></i>
                            &nbsp;
                            Login with Google +
                        </a>
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
            loading
        },
        mounted() {
            console.info('Index component mounted.');
        },
        data() {
            return {
                form: new Form({
                    email: null,
                    password: null,
                }),
                loading: new Loading(this.$store),
            };
        },
        methods: {
            onSubmit() {
                this.loading.setLoadingPromise();
                this.form.post('/auth/login')
                    .then(data => {
                        this.loading.unsetLoadingPromise();
                        if(data.redirect_path){
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
        },
        computed: {}
    }
</script>

<style>

</style>