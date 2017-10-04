<template>
    <div class="abs-center wd-xl">
        <!-- START panel-->
        <div class="p">
            <logo></logo>
        </div>
        <div class="panel widget b0">
            <div class="panel-body">
                <p class="text-center"><strong>Welcome to OzSpy</strong></p>
                <p class="text-center">Please login for better experience</p>
                <form role="form" @submit.prevent="onSubmit">
                    <div class="form-group has-feedback">
                        <input id="email" type="email" placeholder="Email" class="form-control" v-model="form.email">
                        <span class="fa fa-envelope-o form-control-feedback text-muted"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input id="exampleInputPassword1" type="password" placeholder="Password" class="form-control" v-model="form.password">
                        <span class="fa fa-lock form-control-feedback text-muted"></span>
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
    </div>
</template>

<script>
    import logo from '../partials/branding/Logo.vue';
    import loginForm from '../../classes/forms/auth/login';

    export default {
        components: {
            logo
        },
        mounted() {
            console.info('Index component mounted.');
        },
        data() {
            return {
                form: new loginForm({
                    email: null,
                    password: null,
                })
            };
        },
        methods: {
            onSubmit() {
                this.form.submit('post', '/auth/login');
            },
        },
        computed: {}
    }
</script>

<style>

</style>