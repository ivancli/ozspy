<template>
    <div class="abs-center wd-xl">
        <!-- START panel-->
        <div class="p">
            <img src="img/user/02.jpg" alt="Avatar" width="60" height="60" class="img-thumbnail img-circle center-block">
        </div>
        <div class="panel widget b0">
            <div class="panel-body">
                <p class="text-center">Please enter your email address to reset password</p>
                <form role="form">
                    <div class="form-group has-feedback">
                        <input id="exampleInputPassword1" type="password" placeholder="Password" class="form-control">
                        <span class="fa fa-lock form-control-feedback text-muted"></span>
                    </div>
                    <div class=clearfix>
                        <a href="dashboard.html" class="btn btn-block btn-sm btn-primary">Reset Password</a>
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
        <!-- END panel-->
        <div class="p-lg text-center">
            <span>&copy;</span>
            <span>2017</span>
            <span>-</span>
            <span>Angle</span>
            <br>
            <span>Bootstrap Admin Template</span>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.info('Forgot component mounted.');
        }
    }
</script>

<style>

</style>