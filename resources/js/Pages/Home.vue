<template>
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

    <v-pagination :pages="pages" @changed-page="getContacts"></v-pagination>
</template>
<script>
export default {
    data() {
        return {
            contacts: {},
            form: {
                per_page: 50,
                page: 1,
            },
            pages: {},
        };
    },

    created() {
        this.getContacts(1);
    },

    methods: {
        showContact(id) {
            this.$router.push({ name: "contacts", params: { id: id } });
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
                        console.log(err.response);
                    }
                });
        },
    },
};
</script>
<style lang="scss" scoped>
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
