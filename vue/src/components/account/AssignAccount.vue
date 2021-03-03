<template>
  <v-data-table
    :headers="headers"
    :items="accounts"
    sort-by="type"
    class="elevation-1"
  >
    <template v-slot:top>
      <v-toolbar flat>
        <v-toolbar-title>Assign Account</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <v-dialog v-model="dialog" max-width="600px">
          <template v-slot:activator="{ on, attrs }">
            <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on">
              Assign
            </v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="headline">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-row>
                  <v-col cols="12" sm="6" md="6">
                    <v-select
                      v-model.number="masterAccount"
                      :items="masterAccounts"
                      item-value="value"
                      label="Select Account"
                      :disabled="isDisabled"
                      required
                    ></v-select>
                  </v-col>
                  <v-col cols="12" sm="6" md="6">
                    <v-select
                      v-model.number="branch"
                      :items="branches"
                      item-value="value"
                      label="Select Branch"
                      required
                    ></v-select>
                  </v-col>
                </v-row>
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
      <p>No data found.</p>
    </template>
  </v-data-table>
</template>

<script>
export default {
  data: () => ({
    dialog: false,
    dialogDelete: false,
    isDisabled: false,
    headers: [
      {
        text: "Account Name",
        align: "start",
        sortable: false,
        value: "name",
      },
      { text: "Type", value: "type" },
      { text: "Branch", value: "branch" },
      { text: "Actions", value: "actions", sortable: false },
    ],
    accounts: [],
    masterAccounts: [],
    masterAccount: null,
    branches: [],
    branch: null,
    editedIndex: -1,
    editedItem: {
      name: "",
      type: null,
      branch: null,
    },
    defaultItem: {
      name: "",
      type: null,
      branch: null,
    },
  }),
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New Item" : "Edit Item";
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
    this.fetchMasterAccounts();
    this.fetchBranches();
    this.fetchAccounts();
  },
  methods: {
    fetchMasterAccounts() {
      this.$http
        .get("masteraccounts")
        .then((response) => {
          const masterAccount = response.data.data.masterAccounts;
          masterAccount.forEach((element) => {
            this.masterAccounts.push({
              value: element.id,
              text: this.getItemName(element),
              type: element.account_type.name,
            });
          });
        })
        .catch((error) => {
          console.log("error", error.response);
        });
    },
    fetchBranches() {
      this.$http.get("branches").then((response) => {
        const branches = response.data.data.branches;
        branches.forEach((element) => {
          this.branches.push({ value: element.id, text: element.name });
        });
      });
    },
    fetchAccounts() {
      this.$http.get("accounts").then((response) => {
        this.accounts = [];
        const accounts = response.data.data.accounts;
        accounts.forEach((element) => {
          this.accounts.push({
            id: element.id,
            name: this.getItemName(element.master_account),
            type: element.account_type.name,
            branch: element.branch.name,
          });
        });
      });
    },

     getItemName(item){
        if(item.account_type_id == 1 && item.acc_number != null) return item.name + ' (' + item.acc_number.substring(item.acc_number.length - 4)  + ')';
        return item.name;
    },

    editItem(item) {
      this.isDisabled = true;
      this.masterAccount = this.masterAccounts.find(
        (type) => type.text === item.name
      ).value;
      this.branch = this.branches.find(
        (type) => type.text === item.branch
      ).value;
      this.editedIndex = item.id;
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },
    deleteItem(item) {
      this.editedIndex = item.id;
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },
    deleteItemConfirm() {
      this.deleteAssignedAccount(this.editedIndex);
      console.log(this.editedIndex);
      this.accounts.splice(this.editedIndex, 1);
      this.closeDelete();
    },
    close() {
      this.dialog = false;
      this.branch = null;
      this.isDisabled = false;
      this.masterAccount = null;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
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
      if (
        this.branch == "" ||
        this.branch == null ||
        this.masterAccount == "" ||
        this.masterAccount == null
      ) {
        return;
      }
      const data = {
        branch_id: this.branch,
        master_account_id: this.masterAccount,
      };
      if (this.editedIndex > -1) {
        this.updateAccount(data);
      } else {
        this.$http
          .post("accounts", data)
          .then((response) => {
            if (response.data.status === 200) {
              console.log(response);
              this.fetchAccounts();
              this.close();
            }
            this.$toast.success(response.data.data.message[0]);
          })
          .catch((error) => {
            this.$toast.error("Something went wrong");
            console.log(error);
          });
      }
    },

    updateAccount(data) {
      this.$http
        .put("accounts/" + this.editedIndex, data)
        .then((response) => {
          if (response.data.status === 200) {
            this.close();
            this.fetchAccounts();
          }
        })
        .catch((error) => {
          this.$toast.error("Something went wrong");
          console.log(error);
        });
    },

    deleteAssignedAccount(id) {
      this.$http
        .delete("accounts/" + id)
        .then((response) => {
          this.fetchAccounts();
        })
        .catch((error) => {
          this.$toast.error("Something went wrong");
          console.log(error);
        });
    },
  },
};
</script>
