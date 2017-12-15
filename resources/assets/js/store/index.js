const Vue = require('vue');
const Vuex = require('vuex');
const user = require('./modules/user');
const upload = require('./modules/upload');

Vue.use(Vuex);

const store = new Vuex.Store({
    modules: {
        user,
        upload
    }
});

module.exports = store;
