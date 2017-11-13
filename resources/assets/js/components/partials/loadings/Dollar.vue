<template>
    <transition name="fade">
        <div v-if="promise > 0">
            <div class="dollar-loading-backdrop"></div>
            <div class="dollar-loading-spinner"></div>
            <div class="dollar-loading-text" v-if="text" v-text="text"></div>
        </div>
    </transition>
</template>

<script>

    export default {
        mounted() {
            console.info('Dollar component mounted.');
        },
        computed: {
            promise() {
                return this.$store.getters['loading/promise'];
            },
            text() {
                return this.$store.getters['loading/text'];
            }
        }
    }
</script>

<style>
    .dollar-loading-backdrop {
        background-color: #000;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";
        filter: alpha(opacity=50);
        -moz-opacity: 0.5;
        -khtml-opacity: 0.5;
        opacity: 0.5;
        z-index: 1999;
    }

    .dollar-loading-spinner {
        position: fixed;
        top: 50%;
        left: 50%;
        margin-left: -50px;
        margin-top: -50px;
        width: 70px;
        height: 70px;
        background-image: url('../../../../images/loading/loading.gif');
        background-size: 2100px 70px;
        -webkit-animation: loading-h 1s steps(30) infinite;
        z-index: 2000;
    }

    .dollar-loading-text {
        z-index: 2000;
        position: fixed;
        top: 50%;
        text-align: center;
        margin: 50px auto 0;
        color: #fff;
        font-size: 20px;
        font-weight: bold;
        width: 100%;
        left: 0;
    }

    @-webkit-keyframes loading-h {
        0% {
            background-position-x: 0;
        }
        100% {
            background-position-x: -2100px;
        }
    }

    @keyframes loading-h {
        0% {
            background-position-x: 0;
        }
        100% {
            background-position-x: -2100px;
        }
    }

    .fade-enter-active, .fade-leave-active {
        transition: opacity .3s
    }

    .fade-enter, .fade-leave-to {
        opacity: 0
    }
</style>