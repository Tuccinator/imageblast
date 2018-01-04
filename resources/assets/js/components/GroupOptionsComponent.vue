<template>
    <div class="group-options-all">
        <div class="control">
            <label class="checkbox">
                <input type="checkbox" name="privacy" :checked="privacy === 0" @change="updatePrivacy" />
                Invite-Only
            </label>
        </div>
        <div class="field" v-if="privacy === 0">
            <input type="text" class="input" :value="inviteCode" @input="updateInviteCode" @keyup.enter="changeInviteCode" placeholder="Enter invite code..."/>
        </div>
    </div>
</template>

<script>
export default {
    props: ['privacy', 'updatePrivacy', 'inviteCode', 'updateInviteCode', 'changeInviteCode'],

};
</script>
