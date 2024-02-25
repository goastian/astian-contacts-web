import { createRouter, createWebHistory } from "vue-router";

import Home from "../Pages/Home.vue";
import VContact from "../Pages/Contacts/Index.vue";
import VGroup from "../Pages/Group.vue";

const routes = [
    { path: "/:group?", name: "home", component: Home, meta: { auth: true } },
    {
        path: "/contacts/:id?",
        name: "contacts",
        component: VContact,
        meta: { auth: true },
    },
    {
        path: "/groups/:id?",
        name: "groups",
        component: VGroup,
        meta: { auth: true },
    },
    ,
];

export const router = createRouter({
    history: createWebHistory(),
    routes,
});
