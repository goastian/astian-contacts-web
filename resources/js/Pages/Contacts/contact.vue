<template>
    <div class="row">
        <div class="col-12" v-show="registered">
            <button
                class="btn btn-secondary btn-sm float-end"
                @click="destroyContact"
            >
                Delete Contact <i class="bi bi-person-fill-x mx-2"></i>
            </button>
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
        <div class="col" v-show="!registered">
            <input
                type="text"
                class="form-control form-control-sm text-color"
                placeholder="Email Address"
                v-model="contact.correo"
            />
            <v-error :error="errors.correo"></v-error>
        </div>

        <div class="col" v-show="!registered">
            <input
                type="text"
                class="form-control form-control-sm text-color"
                placeholder="Number phone"
                v-model="contact.telefono"
            />
            <v-error :error="errors.telefono"></v-error>
        </div>

        <div class="col">
            <input
                type="text"
                class="form-control form-control-sm text-color"
                placeholder="Home Address"
                v-model="contact.direccion"
            />
            <v-error :error="errors.direccion"></v-error>
        </div>
        <div class="col">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="favorito" />
                <label class="form-check-label text-color" for="favorite">
                    Mark as favorite
                </label>
            </div>

            <v-error :error="errors.favorite"></v-error>
        </div>
        <div class="col">
            <label class="text-color">Choose group ...</label>
            <select
                class="form-select form-select-sm text-color"
                aria-label="Choose group"
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

        <div class="col-12 mt-3">
            <button
                class="btn btn-sm btn-ternary mx-2"
                v-show="registered"
                @click="updateContact"
            >
                Update Contact
                <i class="bi bi-person-circle"></i>
            </button>
            <button
                href="#"
                class="btn btn-primary btn-sm"
                @click="createContact"
            >
                New Contact

                <i class="bi bi-person-fill-add mx-2"></i>
            </button>
        </div>
        <div class="col-12 mt-2">
            <span v-show="message" class="mx-2 text-color">{{ message }}</span>
        </div>
    </div>
</template>
<script>
import { Button } from "bootstrap";

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
        /**
         * Show info about the contact
         *
         * @param {*} id
         */
        showContact(id) {
            
            this.$host
                .get("/api/contacts/" + id)
                .then((res) => {
                    this.contact = res.data.data;
                    this.errors = {}

                    document.getElementById("favorito").checked =
                        this.contact.favorito == 1 ? true : false;
                })
                .catch((err) => {
                    if (err.response && err.response.status) {
                        this.errors = err.response.data.errors;
                    }
                });
        },

        /**
         * get groups belongs to the user authenticable
         */
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

        /**
         * create a new contanct
         */
        createContact(event) {
            this.button = event.target;
            this.button.disabled = true;

            //reset varaible message
            this.message = false;

            if (this.registered) {
                this.button.disabled = false;
                this.$router.push({
                    name: "contacts",
                });
            } else {
                this.contact.favorito = document.getElementById("favorito")
                    .checked
                    ? 1
                    : 0;

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
                        if (err.response && err.response.status) {
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

                    this.showContact(this.$route.params.id);

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
