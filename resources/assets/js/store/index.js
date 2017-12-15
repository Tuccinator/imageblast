const Vue = require('vue');
const Vuex = require('vuex');
const user = require('./modules/user');
const upload = require('./modules/upload');
const feed = require('./modules/feed');

Vue.use(Vuex);

const store = new Vuex.Store({
    modules: {
        user,
        upload,
        feed
    }
});

module.exports = store;
