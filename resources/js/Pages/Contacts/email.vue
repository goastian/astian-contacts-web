<template>
    <div class="email-info">
        <p class="fw-bold text-color m-0">Add new email</p>
        <div class="inputs">
            <div class="items">
                <input
                    type="text"
                    class="input-theme"
                    placeholder="Work or personal ,etc"
                    v-model="email.name"
                />
                <v-error :error="errors.name"></v-error>
            </div>
            <div class="items">
                <input
                    type="email"
                    class="input-theme"
                    placeholder="Email"
                    v-model="email.email"
                />
                <v-error :error="errors.email"></v-error>
            </div>
            <div class="items">
                <button
                    class="btn btn-primary btn-sm"
                    @click="addEmail"
                    v-show="!email_update"
                >
                    Add email <i class="bi bi-envelope-at mx-2"></i>
                </button>

                <button
                    class="btn btn-ternary btn-sm"
                    @click="updateEmail"
                    v-show="email_update"
                >
                    Update email <i class="bi bi-envelope-at mx-2"></i>
                </button>
            </div>
        </div>
        <ul class="list" v-for="(item, index) in emails" :key="index">
            <li class="fw-bold text-color">{{ item.name }}</li>
            <li class="text-color">
                <a :href="'mailto:' + item.email" target="_blank">
                    {{ item.email }}
                </a>
            </li>
            <li>
                <button
                    class="btn btn-ternary btn-sm mx-4"
                    @click="update(item)"
                >
                    <i class="bi bi-pencil-square text-color"></i>
                </button>
            </li>
            <li>
                <button
                    class="btn btn btn-sm btn-secondary"
                    @click="destroyEmail(item.links.destroy, $event)"
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
            email: {},
            emails: {},
            errors: {},
            email_update: false,
            email_request: null,
            button: null,
        };
    },

    created() {
        this.getUri();
    },

    mounted() {
        if (this.$route.params.id) {
            this.getEmails();
        }
        this.listenEvents();
    },

    watch: {
        $route(to, from) {
            if (to.params.id) {
                this.registered = true;
                this.getUri();
                this.getEmails();
            }
        },
    },

    methods: {
        /**
         * get the actual uri
         */
        getUri() {
            if (this.$route.params.id) {
                this.email_request =
                    "/api/contacts/" + this.$route.params.id + "/emails";
            }
        },

        /**
         * get all emails from the contact
         */
        getEmails() {
            this.$host
                .get(this.email_request)
                .then((res) => {
                    this.emails = res.data.data;
                })
                .catch((err) => {
                    if (err.response && err.response.status) {
                        console.log(err.response);
                    }
                });
        },

        /**
         * create a new email to the current contact
         */
        addEmail(event) {
            this.button = event.target;
            this.button.disabled = true;

            this.getUri();

            this.$host
                .post(this.email_request, this.email)
                .then((res) => {
                    this.errors = {};
                    this.email = {};
                    this.getEmails();
                    this.button.disabled = false;
                })
                .catch((err) => {
                    if (err.response && err.response.status == 422) {
                        this.errors = err.response.data.errors;
                        this.button.disabled = false;
                    }
                });
        },

        /**
         * get data from the current contact
         */
        showEmail(link) {
            this.$host
                .get(link)
                .then((res) => {
                    this.email = res.data.data;
                })
                .catch((err) => {
                    if (err.response && err.response.status) {
                        console.log(err.response);
                    }
                });
        },

        /**
         * change form register to update
         */
        update(item) {
            this.email_update = true;
            this.email = item;
            this.errors = {};
        },

        /**
         * update the current email user
         */
        updateEmail(event) {
            this.button = event.target;
            this.button.disabled = true;

            this.$host
                .put(this.email.links.update, this.email)
                .then((res) => {
                    this.email_update = false;
                    this.errors = {};
                    this.getEmails();
                    this.email = {};
                    this.button.disabled = false;
                })
                .catch((err) => {
                    this.button.disabled = false;
                    if (err.response && err.response.status) {
                        this.errors = err.response.data.errors;
                    }
                });
        },

        /**
         * destroy de current email user
         * @param {*} link
         */
        destroyEmail(link, event) {
            this.button = event.target;
            this.button.disabled = true;

            this.$host
                .delete(link)
                .then((res) => {
                    this.getEmails();
                    this.errors = {};
                    this.email = {};
                    this.email_update = false;
                    this.button.disabled = false;
                })
                .catch((err) => {
                    this.button.disabled = false;
                    if (err.response && err.response.status) {
                        console.log(err.response);
                    }
                });
        },

        /**
         * listening all events
         */
        listenEvents() {
            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("StoreEmailEvent", (res) => {
                    this.getEmails();
                });

            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("UpdateEmailEvent", (res) => {
                    this.getEmails();
                });

            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("DestroyEmailEvent", (res) => {
                    this.getEmails();
                });
        },
    },
};
</script>

<style lang="scss" scoped>
.email-info {
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
            flex: 1 1 100%;
            margin: 0.3em 0;

            @media (min-width: 800px) {
                flex: 1 1 calc(100% / 3);
            }
            padding-right: 0.5em;
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
