import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import axios from "axios";

axios.defaults.withCredentials = false;
axios.defaults.baseURL = "http://127.0.0.1:8000/api/";
axios.interceptors.request.use((config) => {
  const user = store.state.auth.user;
  if (user) {
    config.headers.Authorization = `Bearer ${user.token}`
  }
  return config
})
axios.interceptors.response.use(undefined, function(error) {
  if (error) {
    const originalRequest = error.config;
    if (error.response.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;
      store.dispatch("LogOut");
      return router.push("/login");
    }
    throw error;
  }
});

Vue.config.productionTip = false;

new Vue({
  store,
  router,
  render: (h) => h(App),
}).$mount("#app");
