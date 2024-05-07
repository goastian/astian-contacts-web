<template>
    <div class="phone-info">
        <p class="fw-bold text-color m-0">Add new phone number</p>

        <div class="inputs">
            <div class="items">
                <input
                    type="text"
                    class="input-theme"
                    placeholder="Name"
                    v-model="phone.name"
                />
                <v-error :error="errors.name"></v-error>
            </div>
            <div class="items">
                <div class="group">
                    <div>
                        <v-select-search
                            class="label text-color"
                            :items="countries"
                            param="name_en"
                            text="Dial code"
                            @selected="dialCode"
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
                        type="email"
                        class="input-theme"
                        placeholder="Phone"
                        v-model="phone.number"
                    />
                </div>
                <v-error :error="errors.dial_code"></v-error>
                <v-error :error="errors.number"></v-error>
            </div>
            <div class="items">
                <button
                    class="btn btn-primary btn-sm"
                    v-show="!phone_update"
                    @click="addPhone"
                >
                    Add phone
                    <i class="bi bi-telephone-plus-fill mx-2"></i>
                </button>

                <button
                    class="btn btn-ternary btn-sm"
                    v-show="phone_update"
                    @click="updatePhone"
                >
                    Update phone
                    <i class="bi bi-telephone-plus-fill mx-2"></i>
                </button>
            </div>
        </div>
        <ul class="list" v-for="(item, index) in phones" :key="index">
            <li class="text-color fw-bold">
                {{ item.name }}
            </li>
            <li>
                {{ item.full_number }}
            </li>
            <li>
                <button
                    class="btn btn-ternary btn-sm mx-4 btn-sm"
                    @click="update(item)"
                >
                    <i class="bi bi-pencil-square text-color"></i>
                </button>
            </li>
            <li>
                <button
                    class="btn btn-sm btn-secondary btn-sm"
                    @click="destroyPhone(item.links.destroy, $event)"
                >
                    <i class="bi bi-trash text-color"></i>
                </button>
            </li>
        </ul>
    </div>
</template>
<script>
export default {
    data() {
        return {
            phone: {},
            phones: {},
            errors: {},
            phone_update: false,
            phone_request: null,
            countries: {},
        };
    },

    created() {
        this.getUri();
    },

    mounted() {
        if (this.$route.params.id) {
            this.getPhones();
        }
        this.getCountries();
        this.listenEvents();
    },

    watch: {
        $route(to, from) {
            if (to.params.id) {
                this.registered = true;
                this.getUri();
                this.getPhones();
            }
        },
    },

    methods: {
        getCountries() {
            this.$server
                .get("api/locations/countries")
                .then((res) => {
                    this.countries = res.data;
                })
                .catch((err) => {});
        },

        dialCode(event) {
            this.phone.dial_code = event.dial_code;
        },
        /**
         * get the actual uri
         */
        getUri() {
            if (this.$route.params.id) {
                this.phone_request =
                    "/api/contacts/" + this.$route.params.id + "/phones";
            }
        },

        update(item) {
            this.phone_update = true;
            this.phone = item;
            this.errors = {};
        },

        getPhones() {
            this.$host
                .get(this.phone_request)
                .then((res) => {
                    this.phones = res.data.data;
                })
                .catch((err) => {
                    if (err.response && err.response.status) {
                        console.log(err.response);
                    }
                });
        },

        addPhone(event) {
            this.button = event.target;
            this.button.disabled = true;

            this.getUri();

            this.$host
                .post(this.phone_request, this.phone)
                .then((res) => {
                    this.errors = {};
                    this.phone = {};
                    this.getPhones();
                    this.button.disabled = false;
                })
                .catch((err) => {
                    this.button.disabled = false;
                    if (err.response && err.response.status) {
                        this.errors = err.response.data.errors;
                    }
                });
        },

        showPhones(link) {
            this.$host
                .get(link)
                .then((res) => {
                    this.phone = res.data.data;
                })
                .catch((err) => {
                    if (err.response && err.response.status) {
                        console.log(err.response);
                    }
                });
        },

        updatePhone() {
            this.button = event.target;
            this.button.disabled = true;

            this.$host
                .put(this.phone.links.update, this.phone)
                .then((res) => {
                    this.phone_update = false;
                    this.getPhones();
                    this.errors = {};
                    this.phone = {};
                    this.button.disabled = false;
                })
                .catch((err) => {
                    this.button.disabled = false;
                    if (err.response && err.response.status) {
                        this.errors = err.response.data.errors;
                    }
                });
        },

        destroyPhone(link, event) {
            this.button = event.target;
            this.button.disabled = true;

            this.$host
                .delete(link)
                .then((res) => {
                    this.button.disabled = false;
                    this.errors = {};
                    this.phone_update = false;
                    this.getPhones();
                })
                .catch((err) => {
                    this.button.disabled = false;
                    if (err.response && err.response.status) {
                        console.log(err.response);
                    }
                });
        },

        listenEvents() {
            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("StorePhoneEvent", (res) => {
                    this.getPhones();
                });

            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("UpdatePhoneEvent", (res) => {
                    this.getPhones();
                });

            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("DestroyPhoneEvent", (res) => {
                    this.getPhones();
                });
        },
    },
};
</script>
<style lang="scss" scoped>
.phone-info {
    padding: 0.5em;
    font-size: 0.8em;
    background-color: var(--light);
    
    @media (min-width: 800px) {
        font-size: 0.9em;
    }
    .inputs {
        display: flex;
        flex-wrap: wrap;

        .items {
            flex: 1 0 100%;
            margin: 0.3em 0;
            @media (min-width: 800px) {
                flex: 1 1 calc(100% / 3);
                padding-right: 0.2em;
            }
        }
    }

    .list {
        display: flex;
        list-style: none;
        padding: 0;
        li {
            flex: 0 1 auto;
            padding: 0.1em;
            @media (min-width: 800px) {
                padding-right: 2em;
                padding-top: 0.5em;
            }
        }
    }
}
</style>
