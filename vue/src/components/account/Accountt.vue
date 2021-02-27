<template>
  <v-card class="pa-2">
    <v-card-title>
      Account List
      <v-spacer></v-spacer>
      <v-text-field
        v-model="search"
        append-icon="mdi-magnify"
        label="Search"
        single-line
        hide-details
      ></v-text-field>
    </v-card-title>
    <v-data-table
      :headers="headers"
      :items="accounts"
      :items-per-page="5"
      :search="search"
      class="elevation-1"

    >
    </v-data-table>
    <v-card class="mx-auto pa-3 my-12" max-width="500">
      <add-account @update-list="getAccounts"></add-account>
    </v-card>
    <account/>
  </v-card>
</template>

<script>
import AddAccount from "./AddAccount.vue";
import account from './account'
export default {
  components: {
      account,
    AddAccount,
  },
  data() {
    return {
      search: "",
      headers: [
        {
          text: "Account",
          align: "start",
          value: "name",
        },
        { text: "Type", value: "type" },
        { text: "Action", value: "" },
      ],
      accounts: [],
    };
  },
  beforeMount() {
      this.getAccounts();
  },
  methods: {
    getAccounts(){
        this.$http.get("accounts").then((response) => {
            console.log(response);
            const accounts = response.data.data.accounts;
            this.accounts = [];
            accounts.forEach((element) => {
            this.accounts.push({ name: element.name, type: element.account_type.name });
            });
        })
        .catch((error) => {
            console.log("error", error.response);
        });
    }
  }
};
</script>

<style>
</style>
