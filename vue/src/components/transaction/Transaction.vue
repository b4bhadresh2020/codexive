<template>
  <div class="mr-4 ml-4 mt-4">
    <v-toolbar  color="white">
      <v-toolbar-title>My CRUD</v-toolbar-title>
      <v-divider
        class="mx-2"
        inset
        vertical
      ></v-divider>
      <v-spacer></v-spacer>
      <v-dialog v-model="dialog" max-width="50%">
        <template v-slot:activator="{ on }">
          <v-btn color="primary" dark class="mb-2" v-on="on">New Transaction</v-btn>
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
                                <v-select
                                v-model="account"
                                :items="accounts"
                                label="Select Account Type"
                                :error-messages="selectErrors"
                                @change="$v.account.$touch()"
                                @blur="$v.account.$touch()"
                                required

                                ></v-select>
                                <v-select
                                v-model="account"
                                :items="accounts"
                                label="Select Account"
                                required

                                ></v-select>
                            </v-col>

                            <v-col cols="12" md="6">
                                <v-select
                                v-model="account"
                                :items="accounts"
                                label="Select Account Type"
                                required

                                ></v-select>
                                <v-select
                                v-model="account"
                                :items="accounts"
                                label="Select Account"
                                required

                                ></v-select>
                            </v-col>
                        </v-row>

                        <!-- RADIO -->
                        <v-row>
                            <v-col cols="12" md="12">
                                    <v-radio-group v-model="radioList" :mandatory="false" class="ml-4 mr-4" >
                                        <v-layout>
                                        <v-flex>
                                        <v-radio label="Cash" :value="0" @change="caseShowDiv()" color="blue"></v-radio>
                                        </v-flex>
                                        <v-flex>
                                        <v-radio label="cheque" :value="1" @change="chequeShowDiv()" color="blue"></v-radio>
                                        </v-flex>
                                        <v-flex>
                                        <v-radio label="TF" :value="3" @change="TFshowDiv()" color="blue"></v-radio>
                                        </v-flex>
                                        <v-flex>
                                        <v-radio label="Forex" :value="4" @change="ForexShowDiv()" color="blue"></v-radio>
                                        </v-flex>
                                            </v-layout>
                                    </v-radio-group>
                            </v-col>
                        </v-row>

                        <!-- chequeShow -->
                        <v-row v-if="chequeShow">
                                <v-col cols="12" md="4">
                                        <v-text-field v-model="editedItem.name" label="Cheque No"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="4">

                                    <v-text-field v-model="editedItem.name" label="bank Name"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="4">
                                    <v-menu
                                        ref="menu"
                                        v-model="menu"
                                        :close-on-content-click="false"
                                        :return-value.sync="date"
                                        transition="scale-transition"
                                        offset-y
                                        min-width="auto"
                                    >
                                        <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                            v-model="date"
                                            label="Picker in menu"
                                            prepend-icon="mdi-calendar"
                                            readonly
                                            v-bind="attrs"
                                            v-on="on"
                                        ></v-text-field>
                                        </template>
                                        <v-date-picker
                                        v-model="date"
                                        no-title
                                        scrollable
                                        >
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            text
                                            color="primary"
                                            @click="menu = false"
                                        >
                                            Cancel
                                        </v-btn>
                                        <v-btn
                                            text
                                            color="primary"
                                            @click="$refs.menu.save(date)"
                                        >
                                            OK
                                        </v-btn>
                                        </v-date-picker>
                                    </v-menu>
                                </v-col>
                        </v-row>

                        <!-- tfShow -->
                        <v-row  v-if="tfShow">
                                <v-col cols="12" md="6">
                                    <v-text-field v-model="editedItem.name" label="Transfer No"></v-text-field>
                                </v-col>
                                 <v-col cols="12" md="6">
                                    <v-text-field v-model="editedItem.name" label="Bank Name"></v-text-field>
                                </v-col>
                        </v-row>

                        <!-- forexShow -->
                        <v-row  v-if="forexShow">
                                <v-col cols="12" md="6">
                                    <v-text-field v-model="editedItem.name" label="forex TF No"></v-text-field>
                                </v-col>
                                 <v-col cols="12" md="6">
                                    <v-text-field v-model="editedItem.name" label="Bank Name"></v-text-field>
                                </v-col>
                        </v-row>

                        <v-row >
                                <v-col cols="12" md="4">
                                    <v-text-field    label="Amount"
                                    v-model.number="amount"
                                    prefix="₹"
                                    type="number"
                                    min="0"
                                    :error-messages="amountErrors"
                                    @change="$v.amount.$touch()"
                                    @blur="$v.amount.$touch()" v-model="editedItem.amount"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="4">
                                     <v-file-input
                                    counter
                                    truncate-length="1"
                                    ></v-file-input>
                                </v-col>
                                <v-col cols="12" md="4">
                                    <v-menu
                                        ref="menu"
                                        v-model="menu"
                                        :close-on-content-click="false"
                                        :return-value.sync="date"
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
                                        no-title
                                        scrollable
                                        >
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            text
                                            color="primary"
                                            @click="menu = false"
                                        >
                                            Cancel
                                        </v-btn>
                                        <v-btn
                                            text
                                            color="primary"
                                            @click="$refs.menu.save(date)"
                                        >
                                            OK
                                        </v-btn>
                                        </v-date-picker>
                                    </v-menu>
                                </v-col>

                                <v-row >
                                    <v-col cols="12" md="12">
                                            <v-textarea
                                                outlined
                                                name="note"
                                                label="note"
                                                value=""
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
    </v-toolbar>
    <v-data-table
      :headers="headers"
      :items="desserts"
      class="elevation-1"
    >
      <template v-slot:items="props">
        <td class="text-xs-right">{{ props.item.account}}</td>
        <td class="text-xs-right">{{ props.item.type}}</td>
        <td class="text-xs-right">{{ props.item.amount}}</td>
        <td class="text-xs-right">{{ props.item.notes}}</td>
        <td class="justify-center layout px-0">
          <v-icon
            small
            class="mr-2"
            @click="editItem(props.item)"
          >
            edit
          </v-icon>
          <v-icon
            small
            @click="deleteItem(props.item)"
          >
            delete
          </v-icon>
        </td>
      </template>
      <template v-slot:no-data>
        <v-btn color="primary" @click="initialize">Reset</v-btn>
      </template>
    </v-data-table>
  </div>
</template>
<script>
  import { validationMixin } from 'vuelidate'
  import { required } from 'vuelidate/lib/validators'
  export default {
      mixins: [validationMixin],
       validations: {
        account: { required },
        transactionType: { required },
        amount: { required },
    },
    beforeMount(){
        this.$http.get("accounts").then((response) => {
            console.log(response);
            const accounts = response.data.data.accounts;
            this.accounts = [];
            accounts.forEach((element) => {
            this.accounts.push({ text: element.name, value: element.id });
            });
            console.log("accounts",this.accounts);
        })
        .catch((error) => {
            console.log("error", error.response);
        });
    },
    data: () => ({
      dialog: false,
      headers: [
        {
            text: "Account",
            align: "start",
            value: "account",
        },
        { text: "Type", value: "type" },
        { text: "Amount", value: "amount"},
        { text: "Notes", value: "notes" },
        { text: 'Actions', value: 'name', sortable: false }
      ],
      desserts: [],
      editedIndex: -1,
      editedItem: {
        name: '',
        calories: 0,
        fat: 0,
        carbs: 0,
        protein: 0
      },
      defaultItem: {
        name: '',
        calories: 0,
        fat: 0,
        carbs: 0,
        protein: 0
      },
      accounts: [],
      account: null,
      amount: null,
      transactions :{

            account: '',
            name: '',
            type: '',
            amount: '',
            notes: '',
            invoice_img:'',
            transfer_n0:'',
            bank_name:'',


      },
      chequeShow:false,
      tfShow:false,
      forexShow:false,
      radioList:0,
      debitRadioList:0,
      date: new Date().toISOString().substr(0, 10),
      menu: false,
      modal: false,
      menu2: false,
    }),


    computed: {
      selectErrors () {
        const errors = []
        if (!this.$v.account.$dirty) return errors
        !this.$v.account.required && errors.push('Account is required')
        return errors
      },
     transactionErrors () {
        const errors = []
        if (!this.$v.transactionType.$dirty) return errors
        !this.$v.transactionType.required && errors.push('Transaction Type is required')
        return errors
      },
      amountErrors () {
        const errors = []
        if (!this.$v.amount.$dirty) return errors
        !this.$v.amount.required && errors.push('Amount must be greater than 0')
        return errors
      },
      formTitle () {
        return this.editedIndex === -1 ? 'Add Transaction' : 'Edit Transaction'
      }
    },

    watch: {
      dialog (val) {
        val || this.close()
      }
    },

    created () {
      this.initialize()
    },

    methods: {
        initialize(){
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
        },
      editItem (item) {
        this.editedIndex = this.desserts.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },
      deleteItem (item) {
        const index = this.desserts.indexOf(item)
        confirm('Are you sure you want to delete this item?') && this.desserts.splice(index, 1)
      },
      close () {
        this.dialog = false
        setTimeout(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        }, 300)
      },
      save () {
        if (this.editedIndex > -1) {
          Object.assign(this.desserts[this.editedIndex], this.editedItem)
        } else {
          this.desserts.push(this.editedItem)
        }
        this.close()
      },
      chequeShowDiv(){
            this.chequeShow=true;
      },
      TFshowDiv(){
            this.tfShow=true;
            this.chequeShow=false;
            this.forexShow=false;
      },
      ForexShowDiv(){
          console.log("chequeShowForex");
            this.forexShow=true;
            this.tfShow=false;
            this.chequeShow=false;

      },
      caseShowDiv(){
          this.forexShow=false;
            this.tfShow=false;
            this.chequeShow=false;
      },
    }
  }
</script>
