<template>
    <ul class="group-members-list">
        <li v-for="member in members">
            <img :src="'/' + member.avatar" />
        </li>
    </ul>
</template>

<script>
export default {
    props: ['members']
}
</script>
