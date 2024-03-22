<template>
    <div class="row p-0 m-2">
        <v-contact @is-created="isRegistered"></v-contact>

        <v-email v-show="registered" class="my-4 pt-4 border-top"></v-email>

        <v-phone v-show="registered" class="my-4 pt-4 border-top"></v-phone>

        <v-sites v-show="registered" class="my-4 pt-4 border-top"></v-sites>
    </div>
</template>
<script>
import VContact from "./contact.vue";
import VEmail from "./email.vue";
import VPhone from "./phone.vue";
import VSites from "./sites.vue";

export default {
    components: {
        VEmail,
        VPhone,
        VSites,
        VContact,
    },

    data() {
        return {
            registered: false,
        };
    },

    created() {
        this.registered = false;
        if (this.$route.params.id) {
            this.registered = true;
        }
    },

    mounted() {
        this.listenEvents();
    },

    watch: {
        $route(to, from) {
            if (!to.params.id) {
                this.registered = false;
            }
        },
    },

    methods: {
        isRegistered(event) {
            this.registered = event;
        },

        listenEvents() {
            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("StoreContactEvent", (e) => {
                    this.getContacts();
                });

            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("UpdateContactEvent", (e) => {
                    this.getContacts();
                });

            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("DestroyContactEvent", (e) => {
                    this.getContacts();
                });
        },
    },
};
</script>
<style lang="scss" scoped>
.col {
    flex: 0 0 auto;
    padding-top: 2%;

    @media (min-width: 840px) {
        width: 32%;
    }
}
</style>
