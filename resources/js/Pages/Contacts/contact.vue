<template>
    <div>
        <p class="fw-bold text-sm text-color d-block m-0">
            Update user information
        </p>
        <div class="contact-form">
            <div class="item">
                <div class="group">
                    <div>
                        <label for="" class="label text-color"
                            >First Name</label
                        >
                    </div>
                    <input
                        type="text"
                        placeholder="First Name"
                        v-model="contact.name"
                    />
                </div>
                <v-error :error="errors.name"></v-error>
            </div>
            <div class="item">
                <div class="group">
                    <div>
                        <label for="" class="label text-color">Last Name</label>
                    </div>
                    <input
                        type="text"
                        placeholder="Last Name"
                        v-model="contact.last_name"
                    />
                </div>
                <v-error :error="errors.last_name"></v-error>
            </div>
            <div class="item" v-show="!registered">
                <div class="group">
                    <div>
                        <label for="" class="label text-color"
                            >Email Address</label
                        >
                    </div>
                    <input
                        type="text"
                        placeholder="Email Address"
                        v-model="contact.email"
                    />
                </div>
                <v-error :error="errors.email"></v-error>
            </div>

            <div class="item" v-show="!registered">
                <div class="group">
                    <div>
                        <v-select-search
                            class="label text-color"
                            :items="countries"
                            param="name_en"
                            text="Dial code"
                            @selected="setDialCode"
                        >
                            <template #title="slotProps">
                                {{
                                    slotProps.item.emoji
                                        ? slotProps.item.emoji +
                                          " " +
                                          slotProps.item.name_en +
                                          " " +
                                          slotProps.item.dial_code
                                        : slotProps.text
                                }}
                            </template>

                            <template #options="slotProps">
                                {{ slotProps.items.emoji }}
                                {{ slotProps.items.name_en }}
                                {{ slotProps.items.dial_code }}
                            </template>
                        </v-select-search>
                    </div>
                    <input
                        type="text"
                        placeholder="Number phone"
                        v-model="contact.phone"
                    />
                </div>
                <v-error :error="errors.dial_code"></v-error>
                <v-error :error="errors.phone"></v-error>
            </div>

            <div class="item">
                <div class="group">
                    <div>
                        <label for="" class="label text-color"
                            >Home address</label
                        >
                    </div>
                    <input
                        type="text"
                        placeholder="Home Address"
                        v-model="contact.address"
                    />
                </div>
                <v-error :error="errors.address"></v-error>
            </div>
            <div class="item">
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="favorite"
                        v-model="contact.favorite"
                    />
                    <label class="form-check-label text-color" for="favorite">
                        Mark as favorite
                    </label>
                </div>

                <v-error :error="errors.favorite"></v-error>
            </div>
            <div class="item">
                <div class="group">
                    <div>
                        <v-select-search
                            class="label text-color"
                            :items="groups"
                            param="group"
                            text="Choose Group"
                            @selected="setGroup"
                        >
                            <template #title="slotProps">
                                {{ slotProps.text }}
                            </template>

                            <template #options="slotProps">
                                {{ slotProps.items.group }}
                            </template>
                        </v-select-search>
                    </div>
                    <input
                        type="text"
                        placeholder="Group name"
                        v-model="contact.group_name"
                    />
                </div>

                <v-error :error="errors.grupo_id"></v-error>
            </div>

            <div class="item">
                <button
                    class="btn btn-sm btn-ternary"
                    v-show="registered"
                    @click="updateContact"
                >
                    Update Contact
                    <i class="bi bi-person-circle mx-1"></i>
                </button>
                <button class="btn btn-primary btn-sm" @click="createContact">
                    New Contact
                    <i class="bi bi-person-fill-add mx-1"></i>
                </button>
                <button
                    v-show="registered"
                    class="btn btn-secondary btn-sm"
                    @click="destroyContact"
                >
                    Delete Contact <i class="bi bi-trash3-fill mx-1"></i>
                </button>
            </div>
        </div>
        <div class="item">
            <span v-show="message" class="mx-2 text-primary">{{
                message
            }}</span>
        </div>
    </div>
</template>
<script>
export default {
    emits: ["isCreated"],

    data() {
        return {
            contact: {},
            contacts: {},
            errors: {},
            groups: {},
            registered: false,
            message: false,
            button: null,
            countries: {},
        };
    },

    created() {
        if (this.$route.params.id) {
            this.registered = true;
            this.showContact(this.$route.params.id);
        }
    },

    mounted() {
        this.getGroups();
        this.getCountries();
        this.listenEvents();
    },

    watch: {
        $route(to, from) {
            if (!to.params.id) {
                this.contact = {};
                this.errors = {};
                this.registered = false;
            }

            if (to.params.id) {
                this.registered = true;
                this.showContact(to.params.id);
                this.$emit("isCreated", true);
            }
        },
    },

    methods: {
        getCountries() {
            this.$server
                .get("/api/locations/countries")
                .then((res) => {
                    this.countries = res.data;
                })
                .catch((err) => {});
        },

        setDialCode(event) {
            this.contact.dial_code = event.dial_code;
        },

        setGroup(event) {
            this.contact.group_id = event.id;
            this.contact.group_name = event.group;
        },

        /**
         * Show info about the contact
         *
         * @param {*} id
         */
        showContact() {
            const id = this.$route.params.id;
            if (id) {
                this.contact = {};
                this.errors = {};
                this.$host
                    .get("/api/contacts/" + id)
                    .then((res) => {
                        this.contact = res.data.data;
                    })
                    .catch((err) => {});
            }
        },

        /**
         * get groups belongs to the user authenticable
         */
        getGroups() {
            this.$host
                .get("/api/groups", {
                    params: {
                        per_page: 100,
                        order_by: "name",
                    },
                })
                .then((res) => {
                    this.groups = res.data.data;
                })
                .catch((err) => {});
        },

        /**
         * create a new contanct
         */
        createContact(event) {
            this.button = event.target;
            this.button.disabled = true;

            //reset varaible message
            this.message = null;

            /**
             * Send user to register a new user view
             */
            if (this.registered) {
                this.button.disabled = false;
                this.$router.push({
                    name: "contacts",
                });
            } else {
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
                        this.button.disabled = false;
                        this.$emit("isCreated", true);
                    })
                    .catch((err) => {
                        this.button.disabled = false;
                        if (err.response && err.response.status == 422) {
                            this.errors = err.response.data.errors;
                        }
                    });
            }
        },

        /**
         * update contact
         */
        updateContact(event) {
            this.button = event.target;
            this.button.disabled = true;

            this.message = false;

            this.$host
                .put(this.contact.links.update, this.contact)
                .then((res) => {
                    this.registered = true;
                    this.errors = {};
                    this.message = "The contact information has been updated.";
                    this.button.disabled = false;
                })
                .catch((err) => {
                    this.button.disabled = false;
                    if (err.response && err.response.status) {
                        this.errors = err.response.data.errors;
                    }
                });
        },

        destroyContact(event) {
            this.button = event.target;
            this.button.disabled = true;

            this.$host
                .delete(this.contact.links.destroy)
                .then((res) => {
                    this.registered = false;
                    this.errors = {};
                    this.contact = {};
                    this.$router.push({ name: "contacts" });
                    this.button.disabled = false;
                })
                .catch((err) => {
                    this.button.disabled = false;
                });
        },

        listenEvents() {
            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("StoreGroupEvent", (res) => {
                    this.getGroups();
                    this.showContact();
                });

            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("UpdateGroupEvent", (res) => {
                    this.showContact();
                    this.getGroups();
                });

            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("DestroyGroupEvent", (res) => {
                    this.showContact();
                    this.getGroups();
                });
        },
    },
};
</script>
<style lang="scss" scoped>
.btn {
    margin-right: 0.3em;
    margin-top: 0.2em;
}

.btn-remove {
    position: absolute !important;
    right: 1em;
}

.contact-form {
    display: flex;
    flex-wrap: wrap;
    padding: 0;
    margin: 0;
    font-size: 0.8em;
    @media (min-width: 800px) {
        font-size: 0.9em;
    }
    .item {
        flex: 1 1 calc(100% / 2);
        margin-bottom: 0.1em;
        padding: 0.1em;
    }
}
</style>
