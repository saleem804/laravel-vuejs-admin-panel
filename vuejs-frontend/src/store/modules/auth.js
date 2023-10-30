import axios from "axios";

const state = {
  user: null,
  companies: null,
};

const getters = {
  isAuthenticated: (state) => !!state.user,
  StateCompanies: (state) => state.companies,
  StateEmployees: (state) => state.employees,
  StateUser: (state) => state.user,
};

const actions = {
 async LogIn({commit}, user) {
  let response = await axios.post("login", user);
    await commit("setUser", response.data);
  },

  async GetCompanies({ commit }, pageNo = 1) {
    let response = await axios.get("companies?page="+pageNo);
    commit("setCompanies", response.data);
  },

  async GetEmployees({ commit }, pageNo = 1) {
    let response = await axios.get("employees?page="+pageNo);
    commit("setEmployees", response.data);
  },

  async LogOut({ commit }) {
    let user = null;
    commit("logout", user);
  },
};

const mutations = {
  setUser(state, user) {
    state.user = user;
  },

  setCompanies(state, companies) {
    state.companies = companies;
  },
  setEmployees(state, employees) {
    state.employees = employees;
  },
  logout(state, user) {
    state.user = user;
  },
};

export default {
  state,
  getters,
  actions,
  mutations,
};
