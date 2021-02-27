<template>
  <v-card class="pa-2">
    <v-card-title>
      Transactions
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
      :items="transactions"
      :items-per-page="5"
      :search="search"
      class="elevation-1"

    ></v-data-table>
    <v-card class="mx-auto pa-3 my-12" max-width="500">
        <add-transaction />
    </v-card>
  </v-card>
</template>

<script>
import AddTransaction from './AddTransaction'
export default {
    components: {
        AddTransaction
    },
    data() {
        return {
            search: "",
            headers: [
            {
                text: "Account",
                align: "start",
                value: "account",
            },
            { text: "Type", value: "type" },
            { text: "Amount", value: "amount"},
            { text: "Notes", value: "notes" },
            { text: "Action", value: "" },
            ],
            accounts: [],
            transactions: []
        };
    },
    methods: {
        getTransactions(){
            this.$http.get("transactions").then((response) => {
            console.log(response);
            const transactions = response.data.data.transactions;
            this.transactions = [];
            transactions.forEach((element) => {
                this.transactions.push({ account: element.account.name, type: element.type, amount: element.amount, notes: element.notes });
                });
            })
            .catch((error) => {
                console.log("error", error.response);
            });
        }
    },
    beforeMount(){
        this.getTransactions();
    }
}
</script>

<style>

</style>
