<template>
    <div class="row">
        <div class="col-12" v-show="registered">
            <a
                href="#"
                class="btn btn-secondary btn-sm float-end"
                @click="destroyContact"
            >
                Delete Contact <i class="bi bi-person-fill-x mx-2"></i>
            </a>
        </div>
        <div class="col">
            <input
                type="text"
                class="form-control form-control-sm text-color"
                placeholder="First Name"
                v-model="contact.nombre"
            />
            <v-error :error="errors.nombre"></v-error>
        </div>
        <div class="col">
            <input
                type="text"
                class="form-control form-control-sm text-color"
                placeholder="Last Name"
                v-model="contact.apellido"
            />
            <v-error :error="errors.apellido"></v-error>
        </div>
        <div class="col">
            <input
                type="text"
                class="form-control form-control-sm text-color"
                placeholder="Address"
                v-model="contact.direccion"
            />
            <v-error :error="errors.direccion"></v-error>
        </div>
        <div class="col">
            <label class="text-color">Group</label>
            {{ contact.grupo_id }}
            <select
                class="form-control form-control-sm text-color"
                v-model="contact.grupo_id"
            >
                <option
                    v-for="(item, index) in groups"
                    :key="index"
                    :value="item.id"
                >
                    {{ item.grupo }}
                </option>
            </select>
            <v-error :error="errors.grupo_id"></v-error>
        </div>
        <div class="col">
            <label class="text-color" for="">Favorite</label>
            <select
                class="form-control form-control-sm text-color"
                v-model="contact.favorito"
            >
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
            <v-error :error="errors.favorite"></v-error>
        </div>
        <div class="col-12 mt-3">
            <a
                href="#"
                class="btn btn-sm btn-ternary mx-2"
                v-show="registered"
                @click="updateContact"
                >Update Contact
                <i class="bi bi-person-circle"></i>
            </a>
            <a href="#" class="btn btn-primary btn-sm" @click="createContact"
                >New Contact

                <i class="bi bi-person-fill-add mx-2"></i
            ></a>
        </div>
        <div class="col-12 mt-2">
            <span v-show="message" class="mx-2 text-color">{{ message }}</span>
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
        };
    },

    created() {
        if (this.$route.params.id) {
            this.registered = true;
            this.showContact(this.$route.params.id);
        }
        this.getGroups();
    },

    mounted() {
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
        showContact(id) {
            this.$host
                .get("/api/contacts/" + id)
                .then((res) => {
                    this.contact = res.data.data;
                })
                .catch((err) => {
                    if (err.response && err.response.status) {
                        this.errors = err.response.data.errors;
                    }
                });
        },

        getGroups() {
            this.$host
                .get("/api/groups", {
                    params: {
                        per_page: 100,
                        order_by: "nombre",
                    },
                })
                .then((res) => {
                    this.groups = res.data.data;
                })
                .catch((err) => {
                    if (err.response) {
                        console.log(err.response);
                    }
                });
        },

        createContact() {
            this.message = false;

            if (this.registered) {
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

                        this.$emit("isCreated", true);
                    })
                    .catch((err) => {
                        if (err.response && err.response.status) {
                            this.errors = err.response.data.errors;
                        }
                    });
            }
        },

        updateContact() {
            this.message = false;

            this.$host
                .put(this.contact.links.update, this.contact)
                .then((res) => {
                    this.registered = true;
                    this.errors = {};

                    this.showContact(this.$route.params.id);

                    this.message = "The contact information has been updated.";
                })
                .catch((err) => {
                    if (err.response && err.response.status) {
                        this.errors = err.response.data.errors;
                    }
                });
        },

        destroyContact() {
            this.$host
                .delete(this.contact.links.destroy)
                .then((res) => {
                    this.registered = false;
                    this.errors = {};
                    this.contact = {};
                    this.$router.push({ name: "contacts" });
                })
                .catch((err) => {
                    if (err.response && err.response.status) {
                        console.log(err.response);
                    }
                });
        },

        listenEvents() {
            /* this.$echo
                .private(this.$channels.ch_1(window.$id))
                .listen("StoreContactEvent", (e) => {
                    this.getContacts();
                });

            this.$echo
                .private(this.$channels.ch_1(window.$id))
                .listen("UpdateContactEvent", (e) => {
                    this.getContacts();
                });

            this.$echo
                .private(this.$channels.ch_1(window.$id))
                .listen("DestroyContactEvent", (e) => {
                    this.getContacts();
                });*/

            this.$echo
                .private(this.$channels.ch_1(window.$id))
                .listen("StoreGroupEvent", (res) => {
                    this.getGroups();
                });

            this.$echo
                .private(this.$channels.ch_1(window.$id))
                .listen("UpdateGroupEvent", (res) => {
                    this.getGroups();
                });

            this.$echo
                .private(this.$channels.ch_1(window.$id))
                .listen("DestroyGroupEvent", (res) => {
                    this.getGroups();
                });
        },
    },
};
</script>
<style lang="scss" scoped>
.col {
    flex: 0 0 auto;
    padding-top: 2%;

    @media (min-width: 240px) {
        padding-left: 0;
        padding-right: 0;
        margin: 0;
    }

    @media (min-width: 840px) {
        width: 30%;
        margin-right: 2%;
    }
}
</style>
