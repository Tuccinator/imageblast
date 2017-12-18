<template>
    <div class="login-form">
        <div class="field">
            <label class="label">E-mail Address</label>
            <div class="control is-medium">
                <input type="email" class="input is-medium" v-bind:class="{'is-danger': !emailValid}" :value="email" @input="updateEmail" @keyup.enter="login" placeholder="Enter e-mail address" />
            </div>
        </div>
        <div class="field">
            <label class="label">Password</label>
            <div class="control is-medium">
                <input type="password" class="input is-medium" v-bind:class="{'is-danger': !passwordValid}" :value="password" @keyup="updatePassword" @keyup.enter="login" placeholder="Enter password" />
            </div>
        </div>
        <div class="field">
            <a href="#">Forgot Password?</a>
        </div>
        <div class="submit-button">
            <button class="button is-danger is-pulled-right" v-bind:class="{'is-loading': loggingIn}" @click="login">Login</button>
        </div>
    </div>
</template>

<script>
const axios = require('axios');
const upperCaseFirst = require('upper-case-first');

import { mapState, mapGetters } from 'vuex';

export default {
    computed: {
        ...mapState({
            email: state => state.user.email,
            password: state => state.user.password,
            loggingIn: state => state.user.loggingIn,
            emailValid: state => state.user.emailValid,
            passwordValid: state => state.user.passwordValid
        })
    },

    methods: {
        updateEmail: function(e) {
            this.$store.commit('setEmail', e.target.value);
        },

        updatePassword: function(e) {
            this.$store.commit('setPassword', e.target.value);
        },

        login: function() {
            this.$store.commit('loggingIn');

            if(this.email.length === 0) {
                this.$store.commit('setEmailValid', false);
            }

            if(this.password.length === 0) {
                this.$store.commit('setPasswordValid', false);
            }

            if(!this.email || !this.password) {
                this.$store.commit('loggingIn');
                return;
            }

            axios.post('/login', { email: this.email, password: this.password })
                .then(response => {
                    this.$store.commit('loggingIn');

                    const result = response.data;

                    if(result.errors) {

                        // iterate through validation errors and set the specific fields to invalid
                        Object.keys(result.errors).forEach(errKey => {
                            this.$store.commit('set' + upperCaseFirst(errKey) + 'Valid', false);
                        });

                        return;
                    }

                    // if response was unsuccessful without a specific error, automatically make password invalid
                    if(!result.success) {
                        this.$store.commit('setPasswordValid', false);
                        return;
                    }

                    window.location.href = '/account';
                })
        }
    }
}
</script>
