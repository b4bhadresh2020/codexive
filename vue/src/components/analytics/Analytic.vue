<template>
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
        const data = {
            id: id
        }
        this.$http
        .get("analytics/getAccountData", data)
        .then((response) => {
        //   if (response.data.status === 200) {
        //     this.close();
        //     this.initialize();
        //   }
        //   console.log(response);
            // this.$toast.success(response.data.data.message[0]);
        })
        .catch((error) => {
          this.$toast.error("Something went wrong");
          console.log("error", error.response);
        });

    },
  },
};
</script>

<style>
</style>
