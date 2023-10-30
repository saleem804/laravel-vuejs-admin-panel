<template>
  <div class="container">
    <div v-if="User">
      <p>Hi {{ User.name }}</p>
    </div>
    <button class="sm-bt" @click="createEmployee">Create Employee</button>
    <!-- use the modal component, pass in the prop -->
    <modal v-if="showModal" @close="showModal = false">
      <div slot="header">
        <h2>{{ form.id > 0 ? "Upate" : "Create" }} Employee</h2>
      </div>
      <div slot="body">
        <form @submit.prevent="submit">
          <div>
            <label for="company_id">Company:</label>
            <select name="company_id" id="" v-model="form.company_id">
              <option
                :value="company.id"
                v-for="company in companies.data"
                :key="company.id"
                >{{ company.name }}</option
              >
            </select>
          </div>
          <div>
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" v-model="form.first_name" />
          </div>
          <div>
            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" v-model="form.last_name" />
          </div>
          <div>
            <label for="email">Email:</label>
            <input type="text" name="email" v-model="form.email" />
          </div>
          <div>
            <label for="phone">Phone:</label>
            <input type="tel" name="phone" v-model="form.phone" />
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
    <div id="employee-div">
      <div v-if="employees.data">
        <table>
          <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Company Name</th>
            <th>Actions</th>
          </tr>
          <tr v-for="(employee, key) in employees.data" :key="employee.id">
            <td>{{ employee.first_name }}</td>
            <td>{{ employee.last_name }}</td>
            <td>{{ employee.email }}</td>
            <td>{{ employee.phone }}</td>
            <td>{{ employee.company.name }}</td>
            <td>
              <button class="sm-bt" @click="editRow(employee)">Edit</button>
              <button
                class="sm-bt delete-btn"
                @click="deleteRow(key, employee.id)"
              >
                Delete
              </button>
            </td>
          </tr>
        </table>

        <Pagination
          :totalPages="employees.meta.last_page"
          :perPage="employees.meta.per_page"
          :currentPage="employees.meta.current_page"
          @pagechanged="onPageChange"
        />
      </div>

      <div v-else>We have no employees</div>
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
  name: "Employees",
  components: { Modal, Pagination },
  data() {
    return {
      showModal: false,
      form: {
        id: 0,
        company_id: 0,
        first_name: "",
        last_name: "",
        email: "",
        phone: "",
      },
      errors: false,
      loading: false,
    };
  },
  created: function() {
    // a function to call getEmployees action
    this.GetEmployees();
  },
  computed: {
    ...mapGetters({ companies: "StateCompanies", employees: "StateEmployees", User: "StateUser" }),
  },
  methods: {
    ...mapActions(["CreateEmployee", "GetEmployees"]),
    async submit() {
      try {
        let formData = new FormData();
        if (this.form.first_name) formData.append("first_name", this.form.first_name);
        if (this.form.last_name) formData.append("last_name", this.form.last_name);
        if (this.form.company_id) formData.append("company_id", this.form.company_id);
        if (this.form.email) formData.append("email", this.form.email);
        if (this.form.phone) formData.append("phone", this.form.phone);
        this.errors = false;
        this.loading = true;
        let post_url = null;
        if (this.form.id > 0) {
          formData.append("_method", "PUT");
          post_url = "employees/" + this.form.id;
        } else {
          post_url = "employees";
        }
        await axios
          .post(post_url, formData)
          .then((response) => {
            console.log(response);
            this.form = {};
            this.showModal = false;
            store.dispatch("GetEmployees");
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
        throw "Sorry you can't create a employee now!";
      }
    },
    createEmployee() {
      this.form = {};
      this.showModal = true;
      this.form.company_id = this.companies.data[0] ? this.companies.data[0].id : '';
    },
    editRow(employee) {
      this.form = employee;
      this.showModal = true;
    },
    async deleteRow(index, id) {
      await axios.delete("employees/" + id).then((response) => {
        if (response.status == 204) {
          this.employees.data.splice(index, 1);
        }
      });
    },
    onPageChange(pageNo) {
      store.dispatch("GetEmployees", pageNo);
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
  width: 100px;
}

input,
select {
  width: 60%;
  margin: 15px;
  border: 1px solid black;
  padding: 10px;
  border-radius: 0px;
}

#employee-div {
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
