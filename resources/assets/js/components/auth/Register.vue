<template>
    <div class="abs-center wd-xl">
        <!-- START panel-->
        <div class="p">
            <img src="img/user/02.jpg" alt="Avatar" width="60" height="60" class="img-thumbnail img-circle center-block">
        </div>
        <div class="panel widget b0">
            <div class="panel-body">
                <p class="text-center">Sign up for new account</p>
                <form role="form">
                    <div class="form-group has-feedback">
                        <input id="exampleInputPassword1" type="password" placeholder="Password" class="form-control">
                        <span class="fa fa-lock form-control-feedback text-muted"></span>
                    </div>
                    <div class=clearfix>
                        <a href="/auth" class="btn btn-block btn-sm btn-primary">Sign Up</a>
                    </div>
                    <div class="clearfix">
                        <div class="pull-left mt-sm">
                            <a href="/auth/login" class="text-muted">
                                <small>Already have an account?</small>
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
            console.info('Index component mounted.');
        }
    }
</script>

<style>

</style>