<template>
    <v-container>
            <v-flex xs4 lg12 class="grey lighten-4">
                <v-row align="center" justify="center">
                    <img src="@/assets/codexive.png" >
                </v-row>
                <v-card elevation="2"  shaped >
                    <v-card-title primary-title>
                        <h4>Login</h4>
                    </v-card-title>

                    <v-alert
                    v-if="message"
                    border="right"
                    color="red"
                    class="mx-4"
                    elevation="4"
                    text
                    type="success"
                    >{{message}}</v-alert>

                    <v-form class="pr-4 pl-4">
                        <v-text-field
                            v-model.trim="email"
                            :error-messages="emailErrors"
                            label="E-mail"
                            required
                            @input="$v.email.$touch()"
                            @blur="$v.email.$touch()"
                        ></v-text-field>
                        <v-text-field
                            type="password"
                            v-model="password"
                            :error-messages="passwordErrors"
                            :counter="10"
                            label="Password"
                            required
                            @input="$v.password.$touch()"
                            @blur="$v.password.$touch()"
                        ></v-text-field>
                        <v-card-actions>
                            <v-btn class="mr-4" @click="submit">
                                submit
                            </v-btn>
                            <v-btn @click="clear">
                                clear
                            </v-btn>
                        </v-card-actions>
                    </v-form>
                </v-card>
            </v-flex>
    </v-container>
</template>

<script>
import { validationMixin } from "vuelidate";
import { required, maxLength, email } from "vuelidate/lib/validators";
import axios from 'axios';

export default {
    mixins: [validationMixin],

    validations: {
        password: { required, maxLength: maxLength(10) },
        email: { required, email },
        select: { required },
        checkbox: {
            checked(val) {
                return val;
            }
        }
    },

    data: () => ({
        password: "",
        email: "",
        message:""
    }),
    computed: {
        passwordErrors() {
            const errors = [];
            if (!this.$v.password.$dirty) return errors;
            !this.$v.password.maxLength &&
                errors.push("password must be at most 10 characters long");
            !this.$v.password.required && errors.push("password is required.");
            return errors;
        },
        emailErrors() {
            const errors = [];
            if (!this.$v.email.$dirty) return errors;
            !this.$v.email.email && errors.push("Must be valid e-mail");
            !this.$v.email.required && errors.push("E-mail is required");
            return errors;
        }
    },

    methods: {
        submit() {
            if(this.email!='' && this.password!=''){
                this.$v.$touch();
                axios.post("auth/login",{email: this.email,password:this.password}).then((response)=>{
                    console.log("response.status"+response.status);
                    console.log("response.access_token"+response.data.access_token);
                    if(response.status==200){
                        localStorage.setItem('access_token',response.data.access_token);
                    }

                }).catch((error)=>{
                    console.log("error",error.response);
                    if(error.response.status == 422){
                        this.message=error.message;
                    }
                    if(error.response.status == 401){
                        this.message=error.response.data.message;
                    }
                });
            }else{
                this.message="please enter email and password";
            }
        },
        clear() {
            this.$v.$reset();
            this.password = "";
            this.email = "";
        }
    }
};
</script>
