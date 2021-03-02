<template>
  <v-data-table
    :headers="headers"
    :items="branches"
    sort-by="email"
    class="elevation-1 pa-4"
  >
    <template v-slot:top>
      <v-toolbar flat>
        <v-toolbar-title>Branch</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <v-dialog v-model="dialog" max-width="500px">
          <template v-slot:activator="{ on, attrs }">
            <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on">
              New Branch
            </v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="headline">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-text-field
                  v-model="name"
                  :error-messages="nameErrors"
                  label="Branch name"
                  required
                  @input="$v.name.$touch()"
                  @blur="$v.name.$touch()"
                ></v-text-field>
                <v-text-field
                  v-model="email"
                  :error-messages="emailErrors"
                  label="E-mail"
                  required
                  @input="$v.email.$touch()"
                  @blur="$v.email.$touch()"
                ></v-text-field>
                <v-text-field
                  v-model="password"
                  :error-messages="passwordErrors"
                  :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                  :type="showPassword ? 'text' : 'password'"
                  label="Password"
                  class="input-group--focused"
                  @click:append="showPassword = !showPassword"
                  required
                  @input="$v.password.$touch()"
                  @blur="$v.password.$touch()"
                ></v-text-field>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="close"> Cancel </v-btn>
              <v-btn color="blue darken-1" text @click="save"> Save </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="dialogDelete" max-width="500px">
          <v-card>
            <v-card-title class="headline"
              >Are you sure you want to delete this item?</v-card-title
            >
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="closeDelete"
                >Cancel</v-btn
              >
              <v-btn color="blue darken-1" text @click="deleteItemConfirm"
                >OK</v-btn
              >
              <v-spacer></v-spacer>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar>
    </template>
    <template v-slot:[`item.actions`]="{ item }">
      <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
      <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
    </template>
    <template v-slot:no-data>
      <p class="ma-2">No data found.</p>
    </template>
  </v-data-table>
</template>

<script>
import { validationMixin } from "vuelidate";
import { required, maxLength, email } from "vuelidate/lib/validators";
export default {
  mixins: [validationMixin],

  validations: {
    name: { required, maxLength: maxLength(20) },
    email: { required, email },
    accountType: { required },
    password: { required },
  },

  data: () => ({
    dialog: false,
    dialogDelete: false,
    headers: [
      {
        text: "Name",
        align: "start",
        sortable: true,
        value: "name",
      },
      { text: "Email", value: "email", sortable: true },
      { text: "Actions", value: "actions", sortable: false },
    ],
    branches: [],
    editedIndex: -1,
    editedItem: {
      name: "",
      email: "",
    },
    defaultItem: {
      name: "",
      email: "",
      password: "",
    },
    editId: null,
    name: "",
    email: "",
    password: "",
    showPassword: false,
  }),
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New Branch" : "Edit Branch";
    },
    nameErrors() {
      const errors = [];
      if (!this.$v.name.$dirty) return errors;
      !this.$v.name.maxLength &&
        errors.push("Name must be at most 10 characters long");
      !this.$v.name.required && errors.push("Name is required.");
      return errors;
    },
    passwordErrors() {
      const errors = [];
      if (!this.$v.password.$dirty) return errors;
      !this.$v.password.required && errors.push("Password is required.");
      return errors;
    },
    emailErrors() {
      const errors = [];
      if (!this.$v.email.$dirty) return errors;
      !this.$v.email.email && errors.push("Must be valid e-mail");
      !this.$v.email.required && errors.push("E-mail is required");
      return errors;
    },
  },
  watch: {
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
  },
  created() {
    this.initialize();
  },
  methods: {
    initialize() {
      this.$http
        .get("branches")
        .then((response) => {
          if (response.data.status === 200) {
            const branches = response.data.data.branches;
            this.branches = [];
            branches.forEach((element) => {
              this.branches.push({
                id: element.id,
                name: element.name,
                email: element.user.email,
              });
            });
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    editItem(item) {
      this.editedIndex = this.branches.indexOf(item);
      this.name = item.name;
      this.email = item.email;
      this.editId = item.id;
      //   this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },
    deleteItem(item) {
      this.editId = item.id;
      this.editedIndex = this.branches.indexOf(item);
      this.dialogDelete = true;
    },
    deleteItemConfirm() {
      this.$http
        .delete("branches/" + this.editId)
        .then((response) => {
          this.$toast.success(response.data.data.message[0]);
          this.initialize();
        })
        .catch((error) => {
          this.$toast.error("Something went wrong");
          console.log(error);
        });
      this.branches.splice(this.editedIndex, 1);
      this.closeDelete();
    },
    close() {
      this.dialog = false;
      this.$v.$reset();
      this.$nextTick(() => {
        this.name = this.defaultItem.name;
        this.email = this.defaultItem.email;
        this.password = this.defaultItem.password;
        this.editedIndex = -1;
      });
    },
    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
    save() {
      this.$v.$touch();
      if (this.name === "" || this.email === "" || this.password === "") return;
      if (this.editedIndex > -1) {
        this.$http
          .put("branches/" + this.editId, {
            name: this.name,
            email: this.email,
            password: this.password,
          })
          .then((response) => {
            this.$toast.success(response.data.data.message[0]);
            this.initialize();
          })
          .catch((error) => {
            this.$toast.error("Something went wrong");
            console.log(error);
          });
      } else {
        this.$http
          .post("branches", {
            name: this.name,
            email: this.email,
            password: this.password,
          })
          .then((response) => {
            this.$toast.success(response.data.data.message[0]);
            this.initialize();
          })
          .catch((error) => {
            this.$toast.error("Something went wrong");
            console.log(error);
          });
      }
      this.close();
    },
  },
};
</script>
