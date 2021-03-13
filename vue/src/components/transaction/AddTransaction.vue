<template>
  <form>
      <p class="font-weight-black">
        Add Transaction
        </p>
      <v-select
      v-model="account"
      :items="accounts"
      :error-messages="selectErrors"
      label="Select Account"
      required
      @change="$v.account.$touch()"
      @blur="$v.account.$touch()"
    ></v-select>
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
    <v-select
      v-model="transactionType"
      :items="transactionTypes"
      :error-messages="transactionErrors"
      label="Select Type"
      required
      @change="$v.transactionType.$touch()"
      @blur="$v.transactionType.$touch()"
    ></v-select>
    <v-text-field
      v-model="notes"
      label="Notes"
    ></v-text-field>

    <v-btn
      class="mr-4"
      @click="submit"
    >
      submit
    </v-btn>
    <v-btn @click="clear">
      clear
    </v-btn>
  </form>
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
        })
        .catch((error) => {
            console.log("error", error.response);
        });
    },

    data: () => ({
      notes: '',
      accounts: [],
      account: null,
      amount: null,
      transactionType: null,
      transactionTypes: [
          { text: 'Credit', value: 0 },
          { text: 'Debit', value: 1 },
      ]
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
      }
    },

    methods: {
      submit () {
        this.$v.$touch();
        console.log({account_id: this.account, type: this.transactionType, amount: this.amount, notes: this.notes});
        this.$http.post('transactions',{account_id: this.account, type: this.transactionType, amount: this.amount, notes: this.notes}).then((response)=>{
            if(response.data.status === 200){
                this.clear();
            }
            this.$emit('update-list');
            console.log(response);
        }).catch((error)=>{
            console.log(error);
        })
      },
      clear () {
        this.$v.$reset()
        this.notes = ''
        this.account = null
        this.transactionType = null
        this.amount = null
      },
    },
  }
</script>
