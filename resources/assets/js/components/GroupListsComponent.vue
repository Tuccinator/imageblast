<template>
    <div class="groups-list">
        <div class="group-row columns" v-for="group in groups">
            <div class="group-name column is-one-third">
                <span>{{ group.name }}</span>
            </div>
            <div class="column is-one-third"></div>
            <div class="group-join column is-one-third">
            <a :href="'/groups/' + group.id" class="button is-small is-warning">View Group</a>
            </div>
        </div>
    </div>
</template>

<script>

const axios = require('axios');

export default {
    data: function() {
        return {
            groups: []
        };
    },

    mounted: function() {
        axios.get('/graphql?query=query+groups{ groups(order: "desc"){ id, name }}')
            .then(response => {
                const result = response.data;

                this.groups = result.data.groups;
            })
    }
}

</script>
