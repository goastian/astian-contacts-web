<template>
    <div class="row">
        <div class="col">
            <input
                type="text"
                class="form-control form-control-sm text-color"
                placeholder="Name"
                v-model="phone.nombre"
            />
            <v-error :error="errors.nombre"></v-error>
        </div>
        <div class="col">
            <input
                type="email"
                class="form-control form-control-sm text-color"
                placeholder="Phone"
                v-model="phone.numero"
            />
            <v-error :error="errors.numero"></v-error>
        </div>
        <div class="col">
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

        <div class="col-12 pt-2">
            <p v-for="(item, index) in phones" :key="index">
                <strong class="text-color">
                    {{ item.nombre }}
                </strong>
                <span class="text-color mx-4">
                    {{ item.numero }}
                </span>
                <button
                    class="btn btn-ternary btn-sm mx-4 btn-sm"
                    @click="update(item)"
                >
                    <i class="bi bi-pencil-square text-color"></i>
                </button>
                <button
                    class="btn btn-sm btn-secondary btn-sm"
                    @click="destroyPhone(item.links.destroy, $event)"
                >
                    <i class="bi bi-trash text-color"></i>
                </button>
            </p>
        </div>
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
        };
    },

    created() {
        this.getUri();
    },

    mounted() {
        if (this.$route.params.id) {
            this.getPhones();
        }
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
                .private(this.$channels.ch_1(window.$id))
                .listen("StorePhoneEvent", (res) => {
                    this.getPhones();
                });

            this.$echo
                .private(this.$channels.ch_1(window.$id))
                .listen("UpdatePhoneEvent", (res) => {
                    this.getPhones();
                });

            this.$echo
                .private(this.$channels.ch_1(window.$id))
                .listen("DestroyPhoneEvent", (res) => {
                    this.getPhones();
                });
        },
    },
};
</script>
<style lang="scss" scoped>
.col {
    flex: 0 0 auto;

    @media (min-width: 320px) {
        width: 98%;
        margin-top: 2%;
    }

    @media (min-width: 940px) {
        width: 30%;
    }
}
</style>
