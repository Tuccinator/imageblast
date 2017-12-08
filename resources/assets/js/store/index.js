const Vue = require('vue');
const Vuex = require('vuex');
const user = require('./modules/user');

Vue.use(Vuex);

const store = new Vuex.Store({
    modules: {
        user
    }
});

module.exports = store;
