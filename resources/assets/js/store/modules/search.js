const search = {
    state: {
        query: ''
    },

    mutations: {
        setSearchQuery: (state, query) => {
            state.query = query;
        }
    }
}

module.exports = search;
