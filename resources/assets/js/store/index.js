const Vue = require('vue');
const Vuex = require('vuex');
const user = require('./modules/user');
const upload = require('./modules/upload');
const feed = require('./modules/feed');
const group = require('./modules/group');
const search = require('./modules/search');

Vue.use(Vuex);

const store = new Vuex.Store({
    modules: {
        user,
        upload,
        feed,
        group,
        search
    }
});

module.exports = store;
