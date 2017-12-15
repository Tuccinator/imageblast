/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

const Vue = require('vue');
const Vuex = require('vuex');
const Login = require('./components/LoginComponent.vue');
const Signup = require('./components/SignupComponent.vue');
const AvatarForm = require('./components/AvatarFormComponent.vue');
const UploadForm = require('./components/UploadFormComponent.vue');

window.Vue = Vue;

const store = require('./store/index');
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',

    store,

    // Components
    components: {
        'login-form': Login,
        'signup-form': Signup,
        'avatar-form': AvatarForm,
        'upload-form': UploadForm
    }
});
