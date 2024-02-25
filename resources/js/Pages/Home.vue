<template>
    <div class="add text-color" v-show="!pages.total">
        <div class="add-head">Do you want to keep save your contacts?</div>
        <div class="add-body">
            <button class="btn btn-lg" @click="addContacts">
                <i class="bi bi-journal-plus"></i>
            </button>
        </div>
        <div class="add-footer text-center">
            Keep your contacts saved and get it them any moment and anywhere.
        </div>
    </div>

    <div class="row p-0 m-2">
        <div
            class="card text-color text-center"
            v-for="(item, index) in contacts"
            :key="index"
        >
            <div class="card-head text-uppercase border-bottom">
                {{ item.nombre }} {{ item.apellido }}
            </div>
            <div class="card-body">
                <p>
                    <strong>Group</strong>
                    {{ item.grupo ? item.grupo : "Unknow" }}
                    <i
                        v-show="item.favorito"
                        class="bi bi-star-fill text-secondary mx-2"
                    ></i>
                </p>
            </div>
            <div class="card-footer">
                <button class="btn disabled btn-sm bg-ternary float-start">
                    <i class="bi bi-three-dots"></i>
                </button>
                <button
                    class="btn btn-sm bg-primary float-end"
                    @click="showContact(item.id)"
                >
                    <i class="bi bi-eye-fill"></i>
                </button>
            </div>
        </div>
    </div>
    <v-pagination
        v-show="pages.total > pages.per_page"
        :pages="pages"
        @changed-page="getContacts"
    ></v-pagination>
</template>
<script>
export default {
    data() {
        return {
            contacts: {},
            form: {
                per_page: 25,
                page: 1,
            },
            pages: {},
        };
    },

    created() {
        this.getContacts(1);
    },

    methods: {
        addContacts() {
            this.$router.push({ name: "contacts" });
        },

        showContact(id) {
            this.$router.push({
                name: "contacts",
                params: { id: id },
                replace: true,
            });
        },

        getContacts(id) {
            this.form.page = id;

            this.$host
                .get("/api/contacts", { params: this.form })
                .then((res) => {
                    this.contacts = res.data.data;
                    this.pages = res.data.meta.pagination;
                })
                .catch((err) => {
                    if (err.response) {
                    }
                });
        },
    },
};
</script>
<style lang="scss" scoped>
.add {
    width: 50% !important;
    margin: 5% auto;
}

.add-head {
    text-align: center;
}

.add-body {
    font-weight: bold;
    text-align: center;
}

.add-body .btn:hover {
    font-weight: bold;
    color: var(--seconday);
}

.add-body .bi {
    font-size: 5rem;
}

.card {
    flex: 0 0 auto;
    margin-right: 1%;

    @media (min-width: 320px) {
        width: 98%;
        margin-top: 2%;
    }

    @media (min-width: 940px) {
        width: 18%;
        margin-top: 1%;
    }
}
</style>
