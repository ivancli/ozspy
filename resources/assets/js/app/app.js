/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('../bootstrap');

window.Vue = require('vue');


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

/*partial*/
import angleHeader from '../components/partials/Header.vue';
import angleSidebar from '../components/partials/Sidebar.vue';
import angleContent from '../components/partials/Content.vue';

/*content*/
import index from '../components/app/Index.vue';

const ozspy = new Vue({
    el: '#ozspy',
    components: {
        /*partial*/
        angleHeader,
        angleSidebar,
        angleContent,
        /*content*/
        index
    }
});
