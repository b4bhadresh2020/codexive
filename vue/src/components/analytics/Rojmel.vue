<template v-slot:top>
  <v-card elevation="20" outlined tile class="mr-4 ml-4 mt-4">
    <v-row class="pa-5">
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
          @click="fetchRojmelData()"
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
    <h3 align="center" class="mt-4">Rojmel</h3>
    <hr>
    <h4 class="ma-3">Carry Forward   <v-chip color="gray" dark>{{ carryFwd }}</v-chip></h4>
    <v-row class="pa-3">
      <v-col cols="12" md="6">
        <p class="text-center text-decoration-underline h4">Credits</p>
        <v-data-table :headers="creditHeaders" :items="toTransaction">
            <template v-slot:item="{ item }" >
                <tr :style="{'background-color': item.color}">
                    <td>{{ item.amount }}</td>
                    <td>{{ item.from_account.master_account.name }}</td>
                </tr>
            </template>
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
              <th>Total</th>
              <td>{{ totalFiels("amount") }}</td>
            </tr>
          </template>
        </v-data-table>
      </v-col>
      <v-col cols="12" md="6">
        <p class="text-center text-decoration-underline h4">Debits</p>
        <v-data-table :headers="debitheaders" :items="fromTransaction">
            <template v-slot:item="{ item }" >
                <tr :style="{'background-color': item.color}">
                    <td>{{ item.amount }}</td>
                    <td>{{ item.to_account.master_account.name }}</td>
                </tr>
            </template>
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
    // this.fetchAccountTypes();
  },
  data: () => ({
    accountType: null,
    accountTypes: [],
    accounts: [],
    account: null,
    loading: true,
    expanded: [],
    headers: [
      {
        text: "Name",
        align: "start",
        filterable: false,
        value: "name",
      },
      { text: "Amount", value: "amount" },
    ],
    rojmelData:[],
    dates: [],
    modal: false,
    menu2: false,
    loader: null,
    loading4: false,
    creditHeaders: [
        { text: "Amount", value: "amount" },
        {
            text: "Debited from",
            align: "start",
            filterable: false,
            value: "from_account.master_account.name",
        },
    ],
    debitheaders: [
        { text: "Amount", value: "amount" },
        {
            text: "Credited To",
            align: "start",
            filterable: false,
            value: "to_account.master_account.name",
        },
    ],
    fromTransaction: [],
    toTransaction: [],
    carryFwd: 0,
    expense: 0,
    total:0
  }),
  computed: {
    dateRangeText() {
      return this.dates.join(" ~ ");
    },
    disableSearch() {
        console.log(this.dates);
      if (
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
    fetchRojmelData() {
        this.loader = "loading4"
    this.resetData();
      this.$http
        .get("analytics/getRojmelData/", {
          params: {
            0: this.dates,
          },
          headers: {"Authorization": 'Bearer '+localStorage.getItem('access_token')}
        })
        .then((response) => {
            this.rojmelData = response.data.data.rojmelData;
            response.data.data.from_transaction.forEach((ele)=>{
                ele.color = this.intToColour(ele.id);
                this.fromTransaction.push(ele);
            });
            response.data.data.to_transaction.forEach((ele)=>{
                ele.color = this.intToColour(ele.id);
                this.toTransaction.push(ele);
            });
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
    intToColour(num) {
        var hash = 0;
        for (var i = 0; i < num; i++) {
            hash = num + ((hash << 5) - hash);
        }
        var colour = '#';
        for (var j = 0; j < 3; j++) {
            var value = (hash >> (j * 8)) & 0xFF;
            colour += ('00' + value.toString(16)).substr(-2);
        }
        return colour+'5f';
    },
    resetData(){
        this.rojmelData = [];
        this.fromTransaction = [];
        this.toTransaction = [];
        this.carryFwd = [];
        this.expense = [];
    }
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
