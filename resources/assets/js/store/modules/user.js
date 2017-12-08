const user = {
    state: {
        email: '',
        username: '',
        password: '',
        confirmPassword: '',
        emailValid: true,
        passwordValid: true,
        usernameValid: true,
        confirmPasswordValid: true,
        loggingIn: false
    },
    mutations: {
        setUsername: (state, username) => {
            state.username = username;

            if(username.length > 0 && !state.usernameValid) {
                state.usernameValid = true;
            }
        },

        setEmail: (state, email) => {
            state.email = email;

            if(email.length > 0 && !state.emailValid) {
                state.emailValid = true;
            }
        },

        setPassword: (state, password) => {
            state.password = password;

            if(password.length > 0 && !state.passwordValid) {
                state.passwordValid = true;
            }
        },

        setConfirmPassword: (state, password) => {
            state.confirmPassword = password;

            if(password.length > 0 && !state.confirmPasswordValid) {
                state.confirmPasswordValid = true;
            }
        },

        setUsernameValid: (state, validity) => {
            state.usernameValid = validity;
        },

        setEmailValid: (state, validity) => {
            state.emailValid = validity;
        },

        setPasswordValid: (state, validity) => {
            state.passwordValid = validity;
        },

        setConfirmPasswordValid: (state, validity) => {
            state.confirmPasswordValid = validity;
        },

        loggingIn: (state) => {
            state.loggingIn = !state.loggingIn;
        }
    }
}

module.exports = user;
