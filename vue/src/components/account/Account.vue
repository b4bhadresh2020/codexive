<template>
  <div class="mr-4 ml-4 mt-4">
    <v-data-table
      :headers="headers"
      :items="masterAccounts"
      sort-by="id"
      class="elevation-1"
      :loading="isLoading"
    >
      <template v-slot:top>
        <v-toolbar>
          <v-toolbar-title>Master Accounts</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialog" max-width="900px">
            <template v-slot:activator="{ on, attrs }">
              <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on">
                New Account
              </v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-select
                    v-model.number="accountType"
                    :items="accountTypes"
                    item-value="value"
                    :error-messages="selectErrors"
                    label="Select Type"
                    required
                    @change="
                      $v.accountType.$touch();
                      clear();
                    "
                    @blur="$v.accountType.$touch()"
                  ></v-select>
                </v-container>
                <v-container v-if="accountType === 1">
                  <v-text-field v-model.trim="bank.acc_number" label="Bank Account Number">
                    <v-icon slot="append" color="black"> mdi-pencil </v-icon>
                  </v-text-field>
                  <v-text-field v-model.trim="bank.name" label="Bank Name">
                    <v-icon slot="append" color="black"> mdi-pencil </v-icon>
                  </v-text-field>
                  <v-text-field v-model.trim="bank.branch" label="Branch Name">
                    <v-icon slot="append" color="black"> mdi-pencil </v-icon>
                  </v-text-field>
                  <v-text-field v-model.trim="bank.ifsc" label="IFSC">
                    <v-icon slot="append" color="black"> mdi-pencil </v-icon>
                  </v-text-field>
                </v-container>
                <v-container v-if="accountType === 2">
                  <v-text-field v-model.trim="cash.name" label="Account Name">
                    <v-icon slot="append" color="black"> mdi-pencil </v-icon>
                  </v-text-field>
                </v-container>
                <v-container
                  v-if="
                    accountType === 3 || accountType === 4 || accountType === 5
                  "
                >
                  <v-row>
                    <v-col cols="12" md="4">
                      <v-text-field
                        v-model.trim="party.name"
                        label="First name"
                      >
                        <v-icon slot="append" color="black">
                          mdi-pencil
                        </v-icon>
                      </v-text-field>
                    </v-col>

                    <v-col cols="12" md="4">
                      <v-text-field
                        v-model.trim="party.middle_name"
                        label="Middle name"
                      ></v-text-field>
                    </v-col>

                    <v-col cols="12" md="4">
                      <v-text-field
                        v-model.trim="party.last_name"
                        label="Last Name"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                  <v-text-field v-model.trim="party.email" label="E-mail">
                    <v-icon slot="append" color="black"> mdi-pencil </v-icon>
                  </v-text-field>

                  <v-text-field
                    v-model.trim="party.pan_no"
                    label="PAN"
                  ></v-text-field>
                  <v-text-field
                    v-model.trim="party.gst_no"
                    label="GST No."
                  ></v-text-field>
                </v-container>
                <v-container v-if="accountType === 6">
                  <v-row>
                    <v-col cols="12" md="3">
                      <v-text-field
                        v-model.trim="employee.name"
                        label="First name"
                      >
                        <v-icon slot="append" color="black">
                          mdi-pencil
                        </v-icon>
                      </v-text-field>
                    </v-col>

                    <v-col cols="12" md="3">
                      <v-text-field
                        v-model.trim="employee.middle_name"
                        label="Middle name"
                      ></v-text-field>
                    </v-col>

                    <v-col cols="12" md="3">
                      <v-text-field
                        v-model.trim="employee.last_name"
                        label="Last Name"
                      ></v-text-field>
                    </v-col>

                    <v-col cols="12" md="3">
                      <v-file-input
                        ref="file"
                        v-model="profile_img"
                        accept="image/png, image/jpeg, image/bmp"
                        label="Profile Image"
                        truncate-length="25"
                      ></v-file-input>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model.trim="employee.email"
                        label="E-mail"
                      >
                        <v-icon slot="append" color="black">
                          mdi-pencil
                        </v-icon>
                      </v-text-field>
                    </v-col>
                    <v-col cols="12" md="3">
                      <v-menu
                        v-model="dobMenu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-text-field
                            v-model="employee.dob"
                            label="Date of Birth"
                            prepend-icon="mdi-calendar"
                            readonly
                            v-bind="attrs"
                            v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker
                          v-model="employee.dob"
                          @input="dobMenu = false"
                        ></v-date-picker>
                      </v-menu>
                    </v-col>
                    <v-col cols="12" md="3">
                      <v-menu
                        v-model="dojMenu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-text-field
                            v-model="employee.doj"
                            label="Date of Joining"
                            prepend-icon="mdi-calendar"
                            readonly
                            v-bind="attrs"
                            v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker
                          v-model="employee.doj"
                          @input="dojMenu = false"
                        ></v-date-picker>
                      </v-menu>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model.trim="employee.mobile_no"
                        label="Mobile No."
                      >
                        <v-icon slot="append" color="black">
                          mdi-pencil
                        </v-icon>
                      </v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model.trim="employee.emergency_no"
                        label="Emergency Mobile No."
                      ></v-text-field>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model.trim="employee.address"
                        label="Address"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="3">
                      <v-text-field
                        v-model.trim="employee.ctc"
                        label="CTC"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="3">
                      <v-text-field
                        v-model.trim="employee.position"
                        label="Position"
                      ></v-text-field>
                    </v-col>
                  </v-row>

                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model.trim="employee.pan_no"
                        label="PAN"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-file-input
                        accept="image/png, image/jpeg, image/bmp"
                        v-model="pan_img"
                        label="PAN image"
                        truncate-length="25"
                      ></v-file-input>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model.trim="employee.aadhar_no"
                        label="Aadhar No."
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-file-input
                        accept="image/png, image/jpeg, image/bmp"
                        v-model="aadhar_img"
                        label="Aadhar image"
                        truncate-length="25"
                      ></v-file-input>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="close">
                  Cancel
                </v-btn>
                <v-btn color="blue darken-1" text @click="save"> Save </v-btn>
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
      <template v-slot:[`item.name`]="{ item }">
        {{ getItemName(item) }}
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
import { required, maxLength, email } from "vuelidate/lib/validators";
export default {
  mixins: [validationMixin],

  beforeMount() {
    this.fetchAccountTypes();
    this.fetchMasterAccounts();
  },

  validations: {
    name: { required, maxLength: maxLength(20) },
    email: { required, email },
    accountType: { required },
    password: { required },
    checkbox: {
      checked(val) {
        return val;
      },
    },
  },

  data: () => ({
    dialog: false,
    dialogDelete: false,
    isLoading: true,
    accountType: null,
    headers: [
      {
        text: "Account Name",
        align: "start",
        value: "name",
      },
      { text: "Type", value: "type" },
      { text: "Actions", value: "actions", sortable: false },
    ],
    masterAccounts: [],
    name: "",
    accountTypes: [],
    email: "",
    editedIndex: -1,
    editedItem: {
      name: "",
      type: 0,
      email: "",
    },
    defaultItem: {
      name: "",
      type: 0,
      email: "",
    },
    showPassword: false,
    password: "",
    dobMenu: false,
    dojMenu: false,
    defaultBank: {
      name: "",
      branch: "",
      ifsc: "",
      acc_number: ""
    },
    bank: {
      name: "",
      branch: "",
      ifsc: "",
      acc_number: ""
    },
    defaultCash: {
      name: "",
    },
    cash: {
      name: "",
    },
    defaultParty: {
      name: "",
      middle_name: "",
      last_name: "",
      email: "",
      pan_no: "",
      gst_no: "",
    },
    party: {
      name: "",
      middle_name: "",
      last_name: "",
      email: "",
      pan_no: "",
      gst_no: "",
    },
    defaultEmployee: {
      name: "",
      middle_name: "",
      last_name: "",
      email: "",
      dob: null,
      doj: null,
      mobile_no: "",
      emergency_no: "",
      address: "",
      ctc: "",
      position: "",
      pan_no: "",
      aadhar_no: "",
    },
    employee: {
      name: "",
      middle_name: "",
      last_name: "",
      email: "",
      dob: null,
      doj: null,
      mobile_no: "",
      emergency_no: "",
      address: "",
      ctc: "",
      position: "",
      pan_no: "",
      aadhar_no: "",
    },
    profile_img: null,
    pan_img: null,
    aadhar_img: null,

    deleteAccount: null,
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New Account" : "Edit Account";
    },
    passwordErrors() {
      const errors = [];
      if (!this.$v.password.$dirty) return errors;
      !this.$v.password.required && errors.push("Password is required.");
      return errors;
    },

    checkboxErrors() {
      const errors = [];
      if (!this.$v.checkbox.$dirty) return errors;
      !this.$v.checkbox.checked && errors.push("You must agree to continue!");
      return errors;
    },
    selectErrors() {
      const errors = [];
      if (!this.$v.accountType.$dirty) return errors;
      !this.$v.accountType.required && errors.push("Item is required.");
      return errors;
    },
    nameErrors() {
      const errors = [];
      if (!this.$v.name.$dirty) return errors;
      !this.$v.name.maxLength &&
        errors.push("Name must be at most 10 characters long");
      !this.$v.name.required && errors.push("Name is required.");
      return errors;
    },
    emailErrors() {
      const errors = [];
      if (!this.$v.email.$dirty) return errors;
      !this.$v.email.email && errors.push("Must be valid e-mail");
      !this.$v.email.required && errors.push("E-mail is required");
      return errors;
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
  methods: {
    fetchAccountTypes() {
      this.$http
        .get("account/types")
        .then((response) => {
          const accountType = response.data.data.accountType;
          accountType.forEach((element) => {
            this.accountTypes.push({ value: element.id, text: element.name });
          });
        })
        .catch((error) => {
          console.log("error", error.response);
        });
    },

    fetchMasterAccounts() {
      this.$http
        .get("masteraccounts")
        .then((response) => {
          const masterAccounts = response.data.data.masterAccounts;
          this.masterAccounts = [];
          masterAccounts.forEach((element) => {
            this.masterAccounts.push({
              id: element.id,
              name: element.name,
              type: element.account_type.name,
              typeId: element.account_type_id,
              acc_number: element.acc_number,
            });
          });
          this.isLoading = false;
        })
        .catch((error) => {
          console.log("error", error);
          this.isLoading = false;
        });
    },

    fetchEditItem(id) {
      this.$http
        .get("masteraccounts/" + id + "/edit")
        .then((response) => {
          const editAccount = response.data.data.masterAccount;
          switch (this.accountType) {
            case 1: {
              this.bank = JSON.parse(JSON.stringify(editAccount));
              break;
            }
            case 2:
              this.cash = JSON.parse(JSON.stringify(editAccount));
              break;
            case 3:
            case 4:
            case 5: {
              this.party = JSON.parse(JSON.stringify(editAccount));
              break;
            }
            case 6: {
              this.employee = JSON.parse(JSON.stringify(editAccount));
              break;
            }
            default:
              break;
          }
          this.isLoading = false;
        })
        .catch((error) => {
          console.log("error", error);
          this.isLoading = false;
        });
    },

    getItemName(item){
        if(item.typeId == 1 && item.acc_number != null) return item.name + ' (' + item.acc_number.substring(item.acc_number.length - 4)  + ')';
        return item.name;
    },

    editItem(item) {
      this.fetchEditItem(item.id);
      this.accountType = this.accountTypes.find(
        (type) => type.text === item.type
      );
      this.accountType = this.accountType.value;
      this.editedIndex = item.id;
      this.name = item.name;
      this.email = item.email;
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      this.editedIndex = item.id;
      this.deleteAccount = item;
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },

    deleteItemConfirm() {
      this.deleteMasterAccount(this.deleteAccount);
      this.masterAccounts.splice(this.editedIndex, 1);
      this.closeDelete();
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

    prepareFormData() {
      var data = new FormData();
      switch (this.accountType) {
        case 1: {
          const bankDetails = this.bank;
          if (
            bankDetails.acc_number == "" ||
            bankDetails.name == "" ||
            bankDetails.branch == "" ||
            bankDetails.ifsc == ""
          ) {
            return;
          }
          data.append("name", bankDetails.name);
          if (bankDetails.branch) data.append("branch", bankDetails.branch);
          if (bankDetails.ifsc) data.append("ifsc", bankDetails.ifsc);
          if (bankDetails.acc_number) data.append("acc_number", bankDetails.acc_number);

          break;
        }
        case 2:
          if (this.cash.name == "") {
            return;
          }
          data.append("name", this.cash.name);
          break;
        case 3:
        case 4:
        case 5: {
          var partyDetails = this.party;
          if (partyDetails.name == "" || partyDetails.email == "") {
            return;
          }
          partyDetails = this.cleanData(partyDetails);

          data.append("name", partyDetails.name);
          data.append("middle_name", partyDetails.middle_name);
          data.append("last_name", partyDetails.last_name);
          data.append("email", partyDetails.email);
          data.append("pan_no", partyDetails.pan_no);
          data.append("gst_no", partyDetails.gst_no);
          break;
        }
        case 6: {
          const employeeDetails = this.employee;
          if (
            employeeDetails.name === "" ||
            employeeDetails.email === "" ||
            employeeDetails.mobile_no === ""
          ) {
            return;
          }
          data.append("profile_img", this.profile_img);
          data.append("name", employeeDetails.name);
          data.append("middle_name", employeeDetails.middle_name);
          data.append("last_name", employeeDetails.last_name);
          data.append("email", employeeDetails.email);
          if (employeeDetails.dob) data.append("dob", employeeDetails.dob);
          if (employeeDetails.doj) data.append("doj", employeeDetails.doj);
          data.append("mobile_no", employeeDetails.mobile_no);
          data.append("emergency_no", employeeDetails.emergency_no);
          data.append("address", employeeDetails.address);
          if (employeeDetails.ctc) data.append("ctc", employeeDetails.ctc);
          data.append("position", employeeDetails.position);
          data.append("pan_no", employeeDetails.pan_no);
          data.append("aadhar_no", employeeDetails.aadhar_no);
          data.append("pan_img", this.pan_img);
          data.append("aadhar_img", this.aadhar_img);

          break;
        }
        default:
          break;
      }
      data.append("account_type_id", this.accountType);
      return data;
    },

    cleanData(data, exceptkeys = {}) {
      for (const property in data) {
        data[property] =
          data[property] != null && data[property] != "" ? data[property] : "";
      }
      return data;
    },

    save() {
      this.$v.$touch();
      var data = this.prepareFormData();
      if (this.editedIndex > -1) {
        this.updateAccount(data);
      } else {
        this.saveAccount(data);
      }
    },

    saveAccount(data) {
      this.$http
        .post("masteraccounts", data)
        .then((response) => {
          if (response.data.status === 200) {
            this.close();
            this.clear();
            this.fetchMasterAccounts();
          }
          this.$toast.success(response.data.data.message[0]);
        })
        .catch((error) => {
          this.$toast.error("Something went wrong");
          console.log(error);
        });
    },

    updateAccount(data) {
      data.append("_method", "PUT");
      this.$http
        .post("masteraccounts/" + this.editedIndex, data)
        .then((response) => {
          if (response.data.status === 200) {
            this.close();
            this.clear();
            this.fetchMasterAccounts();
          }
          this.$toast.success(response.data.data.message[0]);
        })
        .catch((error) => {
          this.$toast.error("Something went wrong");
          console.log(error);
        });
    },

    deleteMasterAccount(item) {
      this.$http
        .delete("masteraccounts/" + item.id)
        .then((response) => {
          this.fetchMasterAccounts();
          this.$toast.success(response.data.data.message[0]);
        })
        .catch((error) => {
          this.$toast.error("Something went wrong");
          console.log(error);
        });
    },
    clear() {
      this.bank = JSON.parse(JSON.stringify(this.defaultBank));
      this.cash = JSON.parse(JSON.stringify(this.defaultCash));
      this.party = JSON.parse(JSON.stringify(this.defaultParty));
      this.employee = JSON.parse(JSON.stringify(this.defaultEmployee));
      this.profile_img = null;
      this.pan_img = null;
      this.aadhar_img = null;
      this.$v.$reset();
    },
  },
};
</script>
