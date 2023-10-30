<template>
  <div class="container">
    <div v-if="User">
      <p>Hi {{ User.name }}</p>
    </div>
    <button class="sm-bt" @click="createCompany">Create Company</button>
    <!-- use the modal component, pass in the prop -->
    <modal v-if="showModal" @close="showModal = false">
      <div slot="header">
        <h2>{{ form.id > 0 ? "Upate" : "Create" }} Company</h2>
      </div>
      <div slot="body">
        <form @submit.prevent="submit">
          <div>
            <label for="name">Name:</label>
            <input type="text" name="name" v-model="form.name" />
          </div>
          <div>
            <label for="email">Email:</label>
            <input type="text" name="email" v-model="form.email" />
          </div>
          <div>
            <label for="logo">Logo:</label>
            <input type="file" name="logo" @change="onFileChange" />
          </div>
          <div>
            <label for="website">Website:</label>
            <input type="text" name="website" v-model="form.website" />
          </div>
          <button class="sm-bt" type="submit">
            {{ loading ? "Loading..." : form.id > 0 ? "Upate" : "Create" }}
          </button>
          <div v-if="errors" id="errors">
            <ul>
              <li v-for="(error, key) in errors" :key="key">
                {{ error[0] }}
              </li>
            </ul>
          </div>
        </form>
      </div>
    </modal>
    <div></div>
    <div id="company-div">
      <div v-if="companies.data">
        <table>
          <tr>
            <th>Company Name</th>
            <th>Email</th>
            <th>Logo</th>
            <th>Website</th>
            <th>Actions</th>
          </tr>
          <tr v-for="(company, key) in companies.data" :key="company.id">
            <td>{{ company.name }}</td>
            <td>{{ company.email }}</td>
            <td><img class="logo" :src="company.logo" /></td>
            <td>{{ company.website }}</td>
            <td>
              <button class="sm-bt" @click="editRow(company)">Edit</button>
              <button
                class="sm-bt delete-btn"
                @click="deleteRow(key, company.id)"
              >
                Delete
              </button>
            </td>
          </tr>
        </table>

        <Pagination
          :totalPages="companies.meta.last_page"
          :perPage="companies.meta.per_page"
          :currentPage="companies.meta.current_page"
          @pagechanged="onPageChange"
        />
      </div>

      <div v-else>We have no companies</div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import axios from "axios";
import store from "../store";
import Modal from "../components/Modal.vue";
import Pagination from "../components/Pagination.vue";
export default {
  name: "Companies",
  components: { Modal, Pagination },
  data() {
    return {
      showModal: false,
      form: {
        id: 0,
        name: "",
        email: "",
        website: "",
        logo_file: null,
      },
      errors: false,
      loading: false,
    };
  },
  created: function() {
    // a function to call getCompanies action
    this.GetCompanies();
  },
  computed: {
    ...mapGetters({ companies: "StateCompanies", User: "StateUser" }),
  },
  methods: {
    ...mapActions(["CreateCompany", "GetCompanies"]),
    async submit() {
      try {
        let formData = new FormData();
        if (this.form.name) formData.append("name", this.form.name);
        if (this.form.email) formData.append("email", this.form.email);
        if (this.form.logo_file) formData.append("logo", this.form.logo_file);
        if (this.form.website) formData.append("website", this.form.website);
        this.errors = false;
        this.loading = true;
        let post_url = null;
        if (this.form.id > 0) {
          formData.append("_method", "PUT");
          post_url = "companies/" + this.form.id;
        } else {
          post_url = "companies";
        }
        await axios
          .post(post_url, formData)
          .then((response) => {
            console.log(response);
            this.form = {};
            this.showModal = false;
            store.dispatch("GetCompanies");
          })
          .catch((error) => {
            if (
              error.response &&
              error.response.status == 422 &&
              error.response.data.errors
            ) {
              this.errors = error.response.data.errors;
            }
          })
          .finally(() => (this.loading = false));
      } catch (error) {
        throw "Sorry you can't create a company now!";
      }
    },
    onFileChange(event) {
      this.form.logo_file = event.target.files[0];
    },
    createCompany() {
      this.form = {};
      this.showModal = true;
    },
    editRow(company) {
      this.form = company;
      this.showModal = true;
    },
    async deleteRow(index, id) {
      await axios.delete("companies/" + id).then((response) => {
        if (response.status == 204) {
          this.companies.data.splice(index, 1);
        }
      });
    },
    onPageChange(pageNo) {
      store.dispatch("GetCompanies", pageNo);
    },
  },
};
</script>
<style scoped>
* {
  box-sizing: border-box;
}

label {
  padding: 12px 12px 12px 0;
  display: inline-block;
  width: 55px;
}

input {
  width: 60%;
  margin: 15px;
  border: 1px solid black;
  padding: 10px;
  border-radius: 0px;
}

#company-div {
  width: 60%;
  margin: auto;
  margin-bottom: 5px;
}
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
  margin-bottom: 50px;
  margin-top: 30px;
}

td,
th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
.logo {
  max-width: 100px;
  max-height: 100px;
}
.sm-bt {
  background-color: #42b983;
  color: white;
  padding: 10px;
  cursor: pointer;
  border-radius: 0px;
  margin: 5px;
}
.delete-btn {
  background-color: red;
}
#errors {
  color: red;
}
ul li {
  list-style: none;
}
</style>
