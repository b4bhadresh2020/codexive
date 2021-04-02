<template v-slot:top>
  <v-card elevation="20" outlined tile class="mr-4 ml-4 mt-4">
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
        ></v-select>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" md="6">
        <v-dialog
          ref="dialog"
          v-model="modal"
          :return-value.sync="dates"
          persistent
          width="290px"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              v-model="dateRangeText"
              label="Select date range"
              prepend-icon="mdi-calendar"
              readonly
              v-bind="attrs"
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker v-model="dates" range scrollable>
            <v-spacer></v-spacer>
            <v-btn text color="primary" @click="modal = false"> Cancel </v-btn>
            <v-btn text color="primary" @click="$refs.dialog.save(dates)">
              OK
            </v-btn>
          </v-date-picker>
        </v-dialog>
      </v-col>
      <v-col cols="12" md="6">
        <v-btn
          class="ma-2"
          :loading="loading4"
          :disabled="disableSearch"
          color="info"
          @click="fetchAccountData(account)"
        >
          Search
          <template v-slot:loader>
            <span class="custom-loader">
              <v-icon light>mdi-cached</v-icon>
            </span>
          </template>
        </v-btn>
      </v-col>
    </v-row>
    <h3 align="center" class="mt-4">Balance Sheet</h3>
    <v-row class="pa-3">
      <v-col cols="12" md="6">
        <p class="text-center text-decoration-underline h4">Credits</p>
        <v-data-table :headers="toHeaders" :items="toTransaction">
          <template slot="body.append">
            <tr>
              <th>Total Credits</th>
              <td>
                <v-chip color="green" dark>
                  {{ sumToField("amount") }}
                </v-chip>
              </td>
            </tr>
          </template>
          <template slot="body.append">
            <tr>
              <th>Carry Forward</th>
              <td>{{ carryFwd }}</td>
            </tr>
          </template>
          <template slot="body.append">
            <tr>
              <th>Total</th>
              <td>{{ totalFiels("amount") }}</td>
            </tr>
          </template>
        </v-data-table>
      </v-col>
      <v-col cols="12" md="6">
        <p class="text-center text-decoration-underline h4 mt-4">Debits</p>
        <v-data-table :headers="fromheaders" :items="fromTransaction">
          <template slot="body.append">
            <tr>
              <th>Total Debits</th>
              <td>
                <v-chip color="red" dark>
                  {{ sumFromField("amount") }}
                </v-chip>
              </td>
            </tr>
          </template>
        </v-data-table>
      </v-col>
    </v-row>
    <hr />
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
    loading: true,
    toHeaders: [
      {
        text: "Debited from",
        align: "start",
        filterable: false,
        value: "from_account.master_account.name",
      },
      { text: "Amount", value: "amount" },
    ],
    fromheaders: [
      {
        text: "Credited To",
        align: "start",
        filterable: false,
        value: "to_account.master_account.name",
      },
      { text: "Amount", value: "amount" },
    ],
    toTransaction: [],
    fromTransaction: [],
    expense: [],
    carryFwd: 0,
    dates: [],
    modal: false,
    menu2: false,
    loader: null,
    loading4: false,
  }),
  computed: {
    dateRangeText() {
      return this.dates.join(" ~ ");
    },
    disableSearch() {
      if (
        this.accountType != null &&
        this.account != null &&
        this.dates.length == 2
      ) {
        return false;
      }
      return true;
    },
  },
  watch: {
    loader() {
      const l = this.loader;
      this[l] = !this[l];

      setTimeout(() => (this[l] = false), 300);

      this.loader = null;
    },
  },
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
    fetchAccountData(id) {
        this.loader = "loading4"
      this.$http
        .get("analytics/getAccountData/" + id, {
          params: {
            0: this.dates,
          },
        })
        .then((response) => {
          this.fromTransaction = response.data.data.from_transaction;
          this.toTransaction = response.data.data.to_transaction;
          if (response.data.data.expense) {
            response.data.data.expense.forEach((element) => {
              this.fromTransaction.push({
                amount: element.amount,
                to_account: {
                  master_account: { name: element.notes + " (expense)" },
                },
              });
            });
          }
          this.carryFwd = response.data.data.carryFwd;
          this.expense = response.data.data.expense;
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
      return this.toTransaction.reduce((a, b) => a + (b[key] || 0), 0);
    },
    sumFromField(key) {
      // sum data in give key (property)
      return this.fromTransaction.reduce((a, b) => a + (b[key] || 0), 0);
    },
    totalFiels(key) {
      let total = this.sumToField(key) - this.sumFromField(key);
      total += this.carryFwd;
      // sum data in give key (property)
      return total;
    },
  },
  created() {
    this.loading = false;
  },
};
</script>

<style>
.custom-loader {
  animation: loader 1s infinite;
  display: flex;
}
@-moz-keyframes loader {
  from {
    transform: rotate(0);
  }
  to {
    transform: rotate(360deg);
  }
}
@-webkit-keyframes loader {
  from {
    transform: rotate(0);
  }
  to {
    transform: rotate(360deg);
  }
}
@-o-keyframes loader {
  from {
    transform: rotate(0);
  }
  to {
    transform: rotate(360deg);
  }
}
@keyframes loader {
  from {
    transform: rotate(0);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
