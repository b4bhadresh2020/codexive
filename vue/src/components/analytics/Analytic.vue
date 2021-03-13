<template v-slot:top>
    <v-card
    elevation="20"
    outlined
    tile
    class="mr-4 ml-4 mt-4"
    >
        <v-row class="pa-3">
        <v-col cols="12" md="6">
            <v-select
                v-model="accountType"
                :items="accountTypes"
                label="Select Account Type"
                @input="fetchTypedAccounts(accountType)"
                required
            ></v-select>
        </v-col>
        <v-col cols="12" md="6">
            <v-select
                v-model="account"
                :items="accounts"
                label="Select Account"
                required
                @input="fetchAccountData(account)"
            ></v-select>
        </v-col>
        </v-row>
        <h3 align="center" class="mt-4">Balance Sheet</h3>

        <v-row class="pa-3">
            <v-col cols="12" md="6">
                <v-data-table
                    :headers="toHeaders"
                    :items="toTransaction"
                 >

                    <template  slot="body.append">
                        <tr>
                            <th>Total Credits</th>
                            <td>{{ sumToField('amount') }}</td>
                        </tr>
                    </template>
                    <template  slot="body.append">
                        <tr>
                            <th>Total</th>
                            <td>{{ totalFiels('amount') }}</td>
                        </tr>
                    </template>

                 </v-data-table>
            </v-col>
                 <v-col cols="12" md="6">
                <v-data-table
                    :headers="fromheaders"
                    :items="fromTransaction"
                 >

                    <template  slot="body.append">
                        <tr>
                            <th>Total Debits</th>
                            <td>{{ sumFromField('amount') }}</td>
                        </tr>
                    </template>
                 </v-data-table>
            </v-col>
        </v-row>
        <hr>

    </v-card>

</template>

<script>
export default {
  beforeMount() {
    this.fetchAccountTypes();
  },
  data: () => ({
    accountType: null,
    accountTypes: [],
    accounts: [],
    account: null,
    loading:true,
    toHeaders: [
          {
            text: 'Title (Credits)',
            align: 'start',
            filterable: false,
            value: 'to_account.master_account.name',
          },
          { text: 'Amount', value: 'amount' },

    ],
    fromheaders:[
          {
            text: 'Title (Debits)',
            align: 'start',
            filterable: false,
            value: 'from_account.master_account.name',
          },
          { text: 'Amount', value: 'amount' },
    ],
    toTransaction:[],
    fromTransaction:[]

  }),
  methods: {
    fetchAccountTypes() {
      this.$http
        .get("account/types")
        .then((response) => {
          this.accountTypes = [];
          const accountType = response.data.data.accountType;
          accountType.forEach((element) => {
            this.accountTypes.push({ value: element.id, text: element.name });
          });
        })
        .catch((error) => {
          console.log("error", error.response);
        });
    },
    fetchTypedAccounts(type) {
      const data = {
        accountTypeId: type,
      };
      this.$http
        .post("typed/accounts", data)
        .then((response) => {
          const accountType = response.data.data.accounts;
          this.accounts = [];
          accountType.forEach((element) => {
            this.accounts.push({
              value: element.id,
              text: this.getItemName(element.master_account),
            });
          });
        })
        .catch((error) => {
          console.log("error", error.response);
        });
    },
    getItemName(item) {
      if (item.account_type_id == 1 && item.acc_number != null)
        return (
          item.name +
          " (" +
          item.acc_number.substring(item.acc_number.length - 4) +
          ")"
        );
      return item.name;
    },
    fetchAccountData(id){
        this.$http
        .get("analytics/getAccountData/"+id)
        .then((response) => {
            this.fromTransaction=response.data.data.from_transaction;
            this.toTransaction=response.data.data.to_transaction;

        })
        .catch((error) => {
          this.$toast.error("Something went wrong");
          console.log("error", error.response);
        });

    },
    getTransactionPerpose(id) {
      if (id === 0) return "Normal";
      if (id === 1) return "Salary";
      if (id === 2) return "Withdrawal";
    },
    sumToField(key) {
        // sum data in give key (property)
        return this.toTransaction.reduce((a, b) => a + (b[key] || 0), 0)
    },
    sumFromField(key) {
        // sum data in give key (property)
        return this.fromTransaction.reduce((a, b) => a + (b[key] || 0), 0)
    },
    totalFiels(key) {
        const total = this.sumToField(key) - this.sumFromField(key);
        // sum data in give key (property)
        return total;
    },
  },
  created(){
      this.loading=false;
  }
};
</script>

<style>
</style>
