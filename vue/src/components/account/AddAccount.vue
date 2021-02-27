<template>
  <form>
    <p class="font-weight-black">
        Add Account
    </p>
    <v-text-field
      v-model.trim="name"
      :error-messages="nameErrors"
      :counter="20"
      label="Name"
      required
      @input="$v.name.$touch()"
      @blur="$v.name.$touch()"
    ></v-text-field>
    <v-select
      v-model="accountType"
      :items="items"
      :error-messages="selectErrors"
      label="Select Type"
      required
      @change="$v.accountType.$touch()"
      @blur="$v.accountType.$touch()"
    ></v-select>

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
  import { required, maxLength, email } from 'vuelidate/lib/validators'

  export default {
    beforeCreate(){
        this.$http.get("account/types").then((response)=>{
            const accountType = response.data.data.accountType
            accountType.forEach(element => {
                this.items.push({ value: element.id, text: element.name})
            });
        }).catch((error)=>{
            console.log("error",error.response);
        })
    },
    mixins: [validationMixin],

    validations: {
      name: { required, maxLength: maxLength(20) },
      accountType: { required },

    },

    data: () => ({
      name: '',
      accountType: null,
      items: [
      ],
    }),
    emits: ['update-list'],

    computed: {
      selectErrors () {
        const errors = []
        if (!this.$v.accountType.$dirty) return errors
        !this.$v.accountType.required && errors.push('Item is required')
        return errors
      },
      nameErrors () {
        const errors = []
        if (!this.$v.name.$dirty) return errors
        !this.$v.name.maxLength && errors.push('Name must be at most 20 characters long')
        !this.$v.name.required && errors.push('Name is required.')
        return errors
      }
    },

    methods: {
      submit () {
        this.$v.$touch()
        if(this.name === '' || this.accountType === '') return;

        this.$http.post('accounts',{type: this.accountType, name: this.name}).then((response)=>{
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
        this.name = ''
        this.accountType = null
      },
    },
  }
</script>
