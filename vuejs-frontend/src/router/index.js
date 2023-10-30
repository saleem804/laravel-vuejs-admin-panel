import Vue from "vue";
import VueRouter from "vue-router";
import store from "../store";
import Employees from "../views/Employees";
import Login from "../views/Login";
import Companies from "../views/Companies";

Vue.use(VueRouter);

const routes = [
  {
    path: "/",
    name: "Companies",
    component: Companies,
    meta: { requiresAuth: true },
  },
  {
    path: "/login",
    name: "Login",
    component: Login,
    meta: { guest: true },
  },
  {
    path: "/employees",
    name: "Employees",
    component: Employees,
    meta: { requiresAuth: true },
  },
];

const router = new VueRouter({
  mode: "history",
  base: process.env.BASE_URL,
  routes,
});

router.beforeEach((to, from, next) => {
  if (to.matched.some((record) => record.meta.requiresAuth)) {
    if (store.getters.isAuthenticated) {
      next();
      return;
    }
    next("/login");
  } else {
    next();
  }
});

router.beforeEach((to, from, next) => {
  if (to.matched.some((record) => record.meta.guest)) {
    if (store.getters.isAuthenticated) {
      next("/companies");
      return;
    }
    next();
  } else {
    next();
  }
});

export default router;
