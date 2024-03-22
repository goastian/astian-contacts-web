import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/login",
        name: "login",
        component: () => import("../Login/Login.vue"),
        meta: { auth: false },
    },
    {
        path: "/:group?",
        name: "home",
        component: () => import("../Pages/Home.vue"),
        meta: { auth: true },
    },
    {
        path: "/contacts/:id?",
        name: "contacts",
        component: () => import("../Pages/Contacts/Index.vue"),
        meta: { auth: true },
    },
    {
        path: "/groups/:id?",
        name: "groups",
        component: () => import("../Pages/Group.vue"),
        meta: { auth: true },
    },
    ,
];

export const router = createRouter({
    history: createWebHistory(),
    routes,
});
