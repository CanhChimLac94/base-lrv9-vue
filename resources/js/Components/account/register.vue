<template>
  <v-container>
    <v-row>
      <v-col cols="12" sm="6" lg="5" class="d-none d-sm-flex">
        <v-img
          src="http://mydev.ddns.net/wui/images/login-s6-img-dark.svg"
          alt=""
        ></v-img>
      </v-col>
      <v-col
        cols="12"
        sm="6"
        md="5"
        lg="3"
        offset-lg="3"
        offset-md="1"
        class="content-login my-auto"
      >
        <v-form
          class="from-login"
          :disabled="isLoadding"
          :readonly="isLoadding"
          ref="formRegister"
          v-model="valid"
          lazy-validation
        >
          <v-text-field
            v-model="register.txtName"
            :rules="nameRules"
            label="User Name"
            required
          ></v-text-field>
          <v-text-field
            v-model="register.txtFullName"
            :rules="nameRules"
            label="Full Name"
            required
          ></v-text-field>
          <v-text-field
            v-model="register.txtEmail"
            :rules="nameRules"
            :type="'email'"
            label="Email"
            required
          ></v-text-field>
          <v-text-field
            v-model="register.txtPass"
            :type="'password'"
            label="Password"
            required
            @keyup.enter="submitLogin"
          ></v-text-field>
          <div class="d-flex">
            <v-btn
              class="grow"
              color="primary"
              router
              :to="`/account/login`"
              text
            >
              Đăng nhập
            </v-btn>
            <div class="grow">
              <v-btn
                color="success"
                @click="submitRegister"
                :loading="isLoadding"
              >
                <!-- :disabled="!valid || isLoadding" -->
                Đăng ký
              </v-btn>
            </div>
          </div>
        </v-form>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
import accountService from "./account.service";
export default {
  mixins: [accountService],
  mounted() {
    this.$refs.formRegister.validate();
  },
};
</script>