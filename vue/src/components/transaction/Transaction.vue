<template>
  <div class="mr-4 ml-4 mt-4">
    <v-data-table :headers="headers" :items="transactions" class="elevation-1">
      <template v-slot:top>
        <v-toolbar color="white">
          <v-toolbar-title>Transactions</v-toolbar-title>
          <v-divider class="mx-2" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialog" max-width="50%">
            <template v-slot:activator="{ on }">
              <v-btn color="primary" dark class="mb-2" v-on="on"
                >New Transaction</v-btn
              >
            </template>
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-form>
                  <v-container grid-list-md>
                    <v-row>
                      <v-col cols="12" md="6">
                        <p class="font-weight-bold">Debit from</p>
                        <v-select
                          v-model="fromAccountType"
                          :items="accountTypes"
                          label="Select Account Type"
                          @input="fetchTypedAccounts(fromAccountType, 'from')"
                          required
                        ></v-select>
                        <v-select
                          v-model="fromAccount"
                          :items="fromAccounts"
                          label="Select Account"
                          required
                        ></v-select>
                      </v-col>
                      <v-col cols="12" md="6">
                        <p class="font-weight-bold">Credit to</p>
                        <v-select
                          v-model="toAccountType"
                          :items="accountTypes"
                          label="Select Account Type"
                          :error-messages="toAccountTypeErrors"
                          @input="fetchTypedAccounts(toAccountType, 'to')"
                          @change="$v.toAccountType.$touch()"
                          @blur="$v.toAccountType.$touch()"
                          required
                        ></v-select>
                        <v-select
                          v-model="toAccount"
                          :items="toAccounts"
                          label="Select Account"
                          required
                        ></v-select>
                      </v-col>
                    </v-row>

                    <!-- RADIO PURPOSE TYPE-->
                    <v-row>
                      <v-col cols="12" md="12">
                        <v-radio-group
                          v-model="purposeType"
                          :mandatory="false"
                          class="ml-4 mr-4"
                        >
                          <template v-slot:label>
                            <div>
                              <strong>Transaction Purpose</strong>
                            </div>
                          </template>
                          <v-layout>
                            <v-flex>
                              <v-radio
                                label="Normal"
                                :value="0"
                                color="blue"
                              ></v-radio>
                            </v-flex>
                            <v-flex>
                              <v-radio
                                label="Salary"
                                :value="1"
                                color="blue"
                              ></v-radio>
                            </v-flex>
                            <v-flex>
                              <v-radio
                                label="Upad"
                                :value="2"
                                color="blue"
                              ></v-radio>
                            </v-flex>
                          </v-layout>
                        </v-radio-group>
                      </v-col>
                    </v-row>

                    <!-- RADIO TRANSACTION TYPE-->
                    <v-row>
                      <v-col cols="12" md="12">
                        <v-radio-group
                          v-model="transactionType"
                          :mandatory="false"
                          class="ml-4 mr-4"
                          @change="clearRadioInputs"
                        >
                        <template v-slot:label>
                            <div>
                              <strong>Transaction Type</strong>
                            </div>
                          </template>
                          <v-layout>
                            <v-flex>
                              <v-radio
                                label="Cash"
                                :value="0"
                                color="blue"
                              ></v-radio>
                            </v-flex>
                            <v-flex>
                              <v-radio
                                label="cheque"
                                :value="1"
                                color="blue"
                              ></v-radio>
                            </v-flex>
                            <v-flex>
                              <v-radio
                                label="Transfer"
                                :value="2"
                                color="blue"
                              ></v-radio>
                            </v-flex>
                            <v-flex>
                              <v-radio
                                label="Forex"
                                :value="3"
                                color="blue"
                              ></v-radio>
                            </v-flex>
                          </v-layout>
                        </v-radio-group>
                      </v-col>
                    </v-row>

                    <!-- chequeShow -->
                    <v-row v-if="transactionType == 1">
                      <v-col cols="12" md="4">
                        <v-text-field
                          v-model="chequeNo"
                          label="Cheque No"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="4">
                        <v-text-field
                          v-model="bankName"
                          label="Bank Name"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="4">
                        <v-menu
                          v-model="chequeMenu"
                          :close-on-content-click="false"
                          :nudge-right="40"
                          transition="scale-transition"
                          offset-y
                          min-width="auto"
                        >
                          <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                              v-model="depositedDate"
                              label="Deposited Date"
                              prepend-icon="mdi-calendar"
                              readonly
                              v-bind="attrs"
                              v-on="on"
                            ></v-text-field>
                          </template>
                          <v-date-picker
                            v-model="depositedDate"
                            @input="chequeMenu = false"
                          ></v-date-picker>
                        </v-menu>
                      </v-col>
                    </v-row>

                    <!-- tfShow -->
                    <v-row v-else-if="transactionType == 2">
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model="transferNo"
                          label="Transfer No"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model="bankName"
                          label="Bank Name"
                        ></v-text-field>
                      </v-col>
                    </v-row>

                    <!-- forexShow -->
                    <v-row v-else-if="transactionType == 3">
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model="transferNo"
                          label="Forex Transfer No"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model="bankName"
                          label="Bank Name"
                        ></v-text-field>
                      </v-col>
                    </v-row>

                    <v-row>
                      <v-col cols="12" md="4">
                        <v-text-field
                          label="Amount"
                          v-model.number="amount"
                          prefix="₹"
                          type="number"
                          min="0"
                          :error-messages="amountErrors"
                          @change="$v.amount.$touch()"
                          @blur="$v.amount.$touch()"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="4">
                        <v-file-input
                          accept="image/png, image/jpeg, image/bmp, application/pdf"
                          v-model="invoiceImg"
                          label="Invoice Image"
                          counter
                          truncate-length="25"
                        ></v-file-input>
                      </v-col>
                      <v-col cols="12" md="4">
                        <v-menu
                          v-model="menu"
                          :close-on-content-click="false"
                          :nudge-right="40"
                          transition="scale-transition"
                          offset-y
                          min-width="auto"
                        >
                          <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                              v-model="date"
                              label="Date"
                              prepend-icon="mdi-calendar"
                              readonly
                              v-bind="attrs"
                              v-on="on"
                            ></v-text-field>
                          </template>
                          <v-date-picker
                            v-model="date"
                            @input="menu = false"
                          ></v-date-picker>
                        </v-menu>
                      </v-col>

                      <v-row>
                        <v-col cols="12" md="12">
                          <v-textarea
                            v-model="note"
                            outlined
                            name="note"
                            label="note"
                            clearable
                          ></v-textarea>
                        </v-col>
                      </v-row>
                    </v-row>
                  </v-container>
                </v-form>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                <v-btn color="blue darken-1" text @click="save">Save</v-btn>
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
      <template v-slot:[`item.type`]="{ item }">
        {{ getTransactionType(item.type) }}
      </template>
      <template v-slot:[`item.actions`]="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
        <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
      </template>
      <template v-slot:no-data>
        <p>No data available.</p>
      </template>
    </v-data-table>
  </div>
</template>
<script>
import { validationMixin } from "vuelidate";
import { required } from "vuelidate/lib/validators";
export default {
  mixins: [validationMixin],
  validations: {
    account: { required },
    toAccountType: { required },
    transactionType: { required },
    amount: { required },
  },
  beforeMount() {
    this.fetchAccountTypes();
  },
  data: () => ({
    dialog: false,
    headers: [
      {
            text: "From Account",
            align: "start",
            value: "fromAccount",
      },
      {
        text: "To Account",
        value: "toAccount",
      },
      { text: "Type", value: "type" },
      { text: "Amount", value: "amount" },
      { text: "Notes", value: "notes" },
      { text: "Actions", value: "actions", sortable: false },
    ],
    desserts: [],
    editedIndex: -1,
    editedItem: {
      name: "",
      calories: 0,
      fat: 0,
      carbs: 0,
      protein: 0,
    },
    defaultItem: {
      name: "",
      calories: 0,
      fat: 0,
      carbs: 0,
      protein: 0,
    },
    accountTypes: [],
    toAccountType: null,
    fromAccountType: null,
    toAccounts: [],
    toAccount: null,
    fromAccounts: [],
    fromAccount: null,
    account: null,
    chequeNo: "",
    bankName: "",
    chequeMenu: false,
    depositedDate: "",
    transferNo: "",
    amount: null,
    invoiceImg: null,
    note: "",
    transactions: [],
    chequeShow: false,
    tfShow: false,
    forexShow: false,
    purposeType: 0,
    transactionType: 0,
    date: new Date().toISOString().substr(0, 10),
    menu: false,
    modal: false,
    menu2: false,
    dialogDelete: false,
    deleteAccount: null,
  }),

  computed: {
    toAccountTypeErrors() {
      const errors = [];
      if (!this.$v.toAccountType.$dirty) return errors;
      !this.$v.toAccountType.required && errors.push("Account is required");
      return errors;
    },
    transactionErrors() {
      const errors = [];
      if (!this.$v.transactionType.$dirty) return errors;
      !this.$v.transactionType.required &&
        errors.push("Transaction Type is required");
      return errors;
    },
    amountErrors() {
      const errors = [];
      if (!this.$v.amount.$dirty) return errors;
      !this.$v.amount.required && errors.push("Amount must be greater than 0");
      return errors;
    },
    formTitle() {
      return this.editedIndex === -1 ? "Add Transaction" : "Edit Transaction";
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
        .get("transactions")
        .then((response) => {
          console.log(response);
          const transactions = response.data.data.transactions;
          this.transactions = [];
          transactions.forEach((element) => {
            this.transactions.push({
              id: element.id,
              toAccount: element.to_account.master_account.name,
              toAccountTypeId: element.to_account.account_type_id,
              fromAccount: element.from_account.master_account.name,
              fromAccountTypeId: element.from_account.account_type_id,
              type: element.transaction_type,
              amount: element.amount,
              notes: element.notes,
              bankName: element.bank_name || "",
              chequeNo: element.cheque_no || "",
              transferNo: element.transfer_no || "",
              purposeType: element.purpose_type,
            });
          });
        })
        .catch((error) => {
          console.log("error", error.response);
        });
    },

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

    fetchTypedAccounts(type, flag) {
      const data = {
        accountTypeId: type,
      };
      this.$http
        .post("typed/accounts", data)
        .then((response) => {
          const accountType = response.data.data.accounts;
          if (flag == "to") {
            this.toAccounts = [];
            accountType.forEach((element) => {
              this.toAccounts.push({
                value: element.id,
                text: this.getItemName(element.master_account),
              });
            });
          } else {
            this.fromAccounts = [];
            accountType.forEach((element) => {
              this.fromAccounts.push({
                value: element.id,
                text: this.getItemName(element.master_account),
              });
            });
          }
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

    editItem(item) {
      console.log(item);
      this.editedIndex = item.id;
      this.fromAccountType = this.accountTypes.find(
        (type) => type.value === item.fromAccountTypeId
      );
      this.fetchTypedAccounts(this.fromAccountType.value, "from");

      this.toAccountType = this.accountTypes.find(
        (type) => type.value === item.toAccountTypeId
      );
      this.fetchTypedAccounts(this.toAccountType.value, "to");
      this.amount = item.amount;
      this.note = item.notes;
      this.transactionType = item.type;
      this.purposeType = item.purposeType;
      if (item.chequeNo != "") this.chequeNo = item.chequeNo;
      if (item.transferNo != "") this.transferNo = item.transferNo;
      if (item.bankName != "") this.bankName = item.bankName;
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },
    deleteItem(item) {
      this.editedIndex = item.id;
      this.deleteAccount = item;
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },
    close() {
      this.dialog = false;
      this.clear();
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
    closeDelete() {
      this.dialogDelete = false;
      this.deleteAccount = null;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
    deleteItemConfirm() {
      this.deleteTransaction(this.deleteAccount);
      //   this.masterAccounts.splice(this.editedIndex, 1);
      this.closeDelete();
    },
    save() {
      var data = new FormData();
      data.append("from_account_id", this.fromAccount);
      data.append("to_account_id", this.toAccount);
      data.append("purpose_type", this.purposeType);
      data.append("transaction_type", this.transactionType);
      data.append("amount", this.amount);
      if (this.invoiceImg != null) data.append("invoice_img", this.invoiceImg);
      data.append("date", this.date);
      data.append("notes", this.note);
      if (this.chequeNo != "") data.append("cheque_no", this.chequeNo);
      if (this.bankName != "") data.append("bank_name", this.bankName);
      if (this.depositedDate != "")
        data.append("cheque_deposited_date", this.depositedDate);
      if (this.transferNo != "") data.append("transfer_no", this.transferNo);

      if (this.editedIndex > -1) {
        this.updateTransaction(data);
      } else {
        this.saveTransaction(data);
      }
    },
    saveTransaction(data) {
      this.$http
        .post("transactions", data)
        .then((response) => {
          if (response.data.status === 200) {
            this.close();
            this.initialize();
            this.$toast.success(response.data.data.message[0]);
          }
          console.log(response);
        })
        .catch((error) => {
          this.$toast.error("Something went wrong");
          console.log("error", error.response);
        });
    },
    updateTransaction(data) {
      console.log(this.editedIndex);
      data.append("_method", "PUT");
      this.$http
        .post("transactions/" + this.editedIndex, data)
        .then((response) => {
          console.log(response);
          if (response.data.status === 200) {
            this.close();
            this.clear();
            this.initialize();
            this.$toast.success(response.data.data.message[0]);
          }
        })
        .catch((error) => {
          this.$toast.error("Something went wrong");
          console.log(error);
        });
    },
    clearRadioInputs() {
      this.chequeNo = "";
      this.bankName = "";
      this.depositedDate = "";
      this.transferNo = "";
    },
    deleteTransaction(item) {
      console.log(item);
      this.$http
        .delete("transactions/" + item.id)
        .then((response) => {
          console.log(response);
          this.initialize();
          this.$toast.success(response.data.data.message[0]);
        })
        .catch((error) => {
          this.$toast.error("Something went wrong");
          console.log(error);
        });
    },
    getTransactionType(id) {
      if (id === 0) return "Cash";
      if (id === 1) return "Cheque";
      if (id === 2) return "Transfer";
      if (id === 3) return "Forex";
    },
    clear() {
      this.toAccountType = null;
      this.fromAccountType = null;
      this.toAccounts = [];
      this.toAccount = null;
      this.fromAccounts = [];
      this.fromAccount = null;
      this.account = null;
      this.chequeNo = "";
      this.bankName = "";
      this.chequeMenu = false;
      this.depositedDate = "";
      this.transferNo = "";
      this.amount = null;
      this.invoiceImg = null;
      this.note = "";
      this.$v.$reset();
    },
  },
};
</script>
