<template>
    <div class="signup-form">
        <div class="field">
            <label class="label">Username</label>
            <div class="control is-medium">
                <input type="text" class="input is-medium" :class="{'is-danger': !usernameValid}" :value="username" @input="updateUsername" placeholder="Enter username" />
            </div>
        </div>
        <div class="field">
            <label class="label">E-mail Address</label>
            <div class="control is-medium">
                <input type="email" class="input is-medium" :class="{'is-danger': !emailValid}" :value="email" @input="updateEmail" placeholder="Enter e-mail address" />
            </div>
        </div>
        <div class="field">
            <label class="label">Password</label>
            <div class="control is-medium">
                <input type="password" class="input is-medium" :class="{'is-danger': !passwordValid}" :value="password" @input="updatePassword" placeholder="Enter password" />
            </div>
        </div>
        <div class="field">
            <label class="label">Password Again</label>
            <div class="control is-medium">
                <input type="password" class="input is-medium" :class="{'is-danger': !confirmPasswordValid}" :value="confirmPassword" @input="updateConfirmPassword" placeholder="Enter password again" />
            </div>
        </div>
        <div class="submit-button">
            <button class="button is-danger is-pulled-right" :class="{'is-loading': loggingIn}" @click="createAccount">Create Account</button>
        </div>
    </div>
</template>

<script>
const axios = require('axios');
const upperCaseFirst = require('upper-case-first');

import { mapState } from 'vuex';

export default {
    computed: {
        ...mapState({
            email: state => state.user.email,
            username: state => state.user.username,
            password: state => state.user.password,
            confirmPassword: state => state.user.confirmPassword,
            emailValid: state => state.user.emailValid,
            usernameValid: state => state.user.usernameValid,
            passwordValid: state => state.user.passwordValid,
            confirmPasswordValid: state => state.user.confirmPasswordValid,
            loggingIn: state => state.user.loggingIn
        })
    },

    methods: {
        updateUsername: function(e) {
            this.$store.commit('setUsername', e.target.value);
        },

        updateEmail: function(e) {
            this.$store.commit('setEmail', e.target.value);
        },

        updatePassword: function(e) {
            this.$store.commit('setPassword', e.target.value);
        },

        updateConfirmPassword: function(e) {
            this.$store.commit('setConfirmPassword', e.target.value);
        },

        createAccount: function() {
            this.$store.commit('loggingIn');

            if(this.username.length === 0) {
                this.$store.commit('setUsernameValid', false);
            }

            if(this.email.length === 0) {
                this.$store.commit('setEmailValid', false);
            }

            if(this.password.length === 0) {
                this.$store.commit('setPasswordValid', false);
                this.$store.commit('setConfirmPasswordValid', false);
            }

            if(this.confirmPassword.length === 0 || this.password !== this.confirmPassword) {
                this.$store.commit('setPasswordValid', false);
                this.$store.commit('setConfirmPasswordValid', false);
            }

            if(!this.usernameValid, !this.emailValid || !this.passwordValid || !this.passwordValid) {
                this.$store.commit('loggingIn', false);
                return;
            }

            // send a mutation request to create user
            axios.post(`/graphql?query=mutation+users{createUser(username: "${this.username}", email: "${this.email}", password: "${this.password}"){ id, username, email} }`)
                .then(response => {
                    this.$store.commit('loggingIn', false);

                    const result = response.data;

                    if(result.errors) {
                        const errors = result.errors[0].validation;

                        // iterate through validation errors and set the specific fields to invalid
                        Object.keys(errors).forEach(errKey => {
                            this.$store.commit('set' + upperCaseFirst(errKey) + 'Valid', false);
                        });

                        return;
                    }

                    // if the user was created, redirect to account page
                    if(result.data.createUser.id) {
                        window.location.href = '/account';
                    }
                });
        }
    }
}
</script>
