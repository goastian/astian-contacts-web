<template>
    <div class="row p-0 m-2">
        <v-contact @is-created="isRegistered"></v-contact>
        
        <v-sites v-show="registered" class="my-4 pt-4 border-top"></v-sites>

        <v-phone v-show="registered" class="my-4 pt-4 border-top"></v-phone>

        <v-email v-show="registered" class="my-4 pt-4 border-top"></v-email>
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
            contact: {},
            contacts: {},
            errors: {},
            registered: false,
        };
    },

    created() {
        this.registered = false;
        if (this.$route.params.id) {
            this.registered = true;
            this.showContact(this.$route.params.id);
        }
    },

    watch: {
        $route(to, from) {
            if (!to.params.id) {
                this.contact = {};
                this.errors = {};
                this.registered = false;
            }
        },
    },

    methods: {
        showContact(id) {
            this.$host
                .get("/api/contacts/" + id)
                .then((res) => {
                    this.contact = res.data.data;
                    this.getEmails();
                })
                .catch((err) => {
                    if (err.response && err.response.status) {
                        this.errors = err.response.data.errors;
                    }
                });
        },

        createContact() {
            this.$host
                .post("/api/contacts", this.contact)
                .then((res) => {
                    this.contact = res.data.data;
                    this.$router.push({
                        name: "contacts",
                        params: { id: this.contact.id },
                    });

                    this.registered = true;
                    this.errors = {};
                })
                .catch((err) => {
                    if (err.response && err.response.status) {
                        this.errors = err.response.data.errors;
                    }
                });
        },

        isRegistered(event){ 
            this.registered = event
        },

        updateContact(link) {
            this.$host
                .put(link, this.contact)
                .then((res) => {
                    this.registered = true;
                    this.errors = {};
                })
                .catch((err) => {
                    if (err.response && err.response.status) {
                        this.errors = err.response.data.errors;
                    }
                });
        },

        destroyContact() {
            this.$host
                .delete(link)
                .then((res) => {
                    this.registered = false;
                    this.errors = {};
                    this.$router.push({ name: "contacts" });
                })
                .catch((err) => {
                    if (err.response && err.response.status) {
                        console.log(err.response);
                    }
                });
        },

        listenEvents() {},
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
