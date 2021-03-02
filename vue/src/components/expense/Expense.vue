<template>
  <div class="mr-4 ml-4 mt-4">
    <v-data-table :headers="headers" :items="expenses" class="elevation-1">
      <template v-slot:top>
        <v-toolbar color="white">
          <v-toolbar-title>Expenses</v-toolbar-title>
          <v-divider class="mx-2" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialog" max-width="50%">
            <template v-slot:activator="{ on }">
              <v-btn color="primary" dark class="mb-2" v-on="on"
                >Add Expense</v-btn
              >
            </template>
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-form>
                  <v-container grid-list-md>
                    <p class="font-weight-bold">Expense from</p>
                    <v-select
                      v-model="accountType"
                      :items="accountTypes"
                      :error-messages="accountTypeErrors"
                      label="Select Account Type"
                      @input="fetchTypedAccounts(accountType)"
                      required
                    ></v-select>
                    <v-select
                      v-model="account"
                      :items="accounts"
                      label="Select Account"
                      required
                    ></v-select>
                    <v-select
                      v-model="expenseType"
                      :items="expenseTypes"
                      label="Select Expense Type"
                      required
                    ></v-select>

                    <!-- RADIO -->
                    <v-row>
                      <v-col cols="12" md="12">
                        <v-radio-group
                          v-model="transactionType"
                          :mandatory="false"
                          class="ml-4 mr-4"
                          @change="clearRadioInputs"
                        >
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
      <template v-slot:[`item.expenseType`]="{ item }">
        {{ getExpenseType(item.expenseType) }}
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
    accountType: { required },
    transactionType: { required },
    amount: { required },
  },
  beforeMount() {
    this.fetchAccountTypes();
    this.fetchExpenseTypes();
  },
  data: () => ({
    dialog: false,
    headers: [
      {
        text: "From Account",
        align: "start",
        value: "account",
      },
      { text: "Transaction Type", value: "type" },
      { text: "Expense Type", value: "expenseType" },
      { text: "Amount", value: "amount" },
      { text: "Notes", value: "notes" },
      { text: "Actions", value: "actions", sortable: false },
    ],
    editedIndex: -1,
    accountTypes: [],
    accountType: null,
    accounts: [],
    account: null,
    expenseTypes: [],
    expenseType: null,
    chequeNo: "",
    bankName: "",
    chequeMenu: false,
    depositedDate: "",
    transferNo: "",
    amount: null,
    invoiceImg: null,
    note: "",
    expenses: [],
    chequeShow: false,
    tfShow: false,
    forexShow: false,
    transactionType: 0,
    date: new Date().toISOString().substr(0, 10),
    menu: false,
    modal: false,
    menu2: false,
    dialogDelete: false,
    deleteAccount: null,
  }),

  computed: {
    accountTypeErrors() {
      const errors = [];
      if (!this.$v.accountType.$dirty) return errors;
      !this.$v.accountType.required && errors.push("Account is required");
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
      return this.editedIndex === -1 ? "Add Expense" : "Edit Expense";
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
        .get("expenses")
        .then((response) => {
          const expenses = response.data.data.expenses;
          this.expenses = [];
          expenses.forEach((element) => {
            this.expenses.push({
              id: element.id,
              account: element.account.master_account.name,
              accountType: element.account.account_type_id,
              expenseType: element.expense_type_id,
              type: element.transaction_type,
              amount: element.amount,
              notes: element.notes,
              bankName: element.bank_name || "",
              chequeNo: element.cheque_no || "",
              transferNo: element.transfer_no || "",
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

    fetchExpenseTypes() {
      this.$http
        .get("expense/types")
        .then((response) => {
          this.expenseTypes = [];
          const expenseTypes = response.data.data.expenseType;
          expenseTypes.forEach((element) => {
            this.expenseTypes.push({ value: element.id, text: element.name });
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
              text: element.master_account.name,
            });
          });
        })
        .catch((error) => {
          console.log("error", error.response);
        });
    },

    editItem(item) {
      console.log(item);
      this.editedIndex = item.id;
      this.accountType = this.accountTypes.find(
        (type) => type.value === item.accountType
      );

      this.expenseType = this.expenseTypes.find(
        (type) => type.value === item.expenseType
      );
      this.amount = item.amount;
      this.note = item.notes;
      this.transactionType = item.type;
      if (item.chequeNo != "") this.chequeNo = item.chequeNo;
      if (item.transferNo != "") this.transferNo = item.transferNo;
      if (item.bankName != "") this.bankName = item.bankName;
      this.dialog = true;
    },
    deleteItem(item) {
      this.editedIndex = item.id;
      this.deleteAccount = item;
      this.dialogDelete = true;
    },
    close() {
      this.dialog = false;
      this.clear();
      this.$nextTick(() => {
        this.editedIndex = -1;
      });
    },
    closeDelete() {
      this.dialogDelete = false;
      this.deleteAccount = null;
      this.$nextTick(() => {
        this.editedIndex = -1;
      });
    },
    deleteItemConfirm() {
      this.deleteTransaction(this.deleteAccount);
      this.closeDelete();
    },
    save() {
      var data = new FormData();
      data.append("account_id", this.account);
      data.append("expense_type_id", this.expenseType);
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
        .post("expenses", data)
        .then((response) => {
          if (response.data.status === 200) {
            this.close();
            this.initialize();
          }
        })
        .catch((error) => {
          console.log("error", error.response);
        });
    },
    updateTransaction(data) {
      data.append("_method", "PUT");
      this.$http
        .post("expenses/" + this.editedIndex, data)
        .then((response) => {
          if (response.data.status === 200) {
            this.close();
            this.clear();
            this.initialize();
          }
        })
        .catch((error) => {
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
      this.$http
        .delete("expenses/" + item.id)
        .then((response) => {
          this.initialize();
        })
        .catch((error) => {
          console.log(error);
        });
    },

    getTransactionType(id) {
      if (id === 0) return "Cash";
      if (id === 1) return "Cheque";
      if (id === 2) return "Transfer";
      if (id === 3) return "Forex";
    },
    getExpenseType(id) {
        const type = this.expenseTypes.find((type) => type.value == id);
      return (type) ? type.text : '';
    },

    clear() {
      (this.accountType = null),
        (this.expenseType = null),
        (this.account = null);
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
