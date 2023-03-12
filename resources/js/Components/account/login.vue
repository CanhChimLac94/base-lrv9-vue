<template>
  <v-container>
    <v-row>
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
          ref="form"
          :disabled="isLoadding"
          :readonly="isLoadding"
          class="from-login"
          v-model="valid"
          lazy-validation
        >
          <v-text-field
            v-model="user_name"
            :rules="nameRules"
            label="User Name"
            required
          ></v-text-field>
          <v-text-field
            v-model="password"
            :type="'password'"
            label="Password"
            required
            @keyup.enter="submitLogin"
          ></v-text-field>
          <div class="d-flex text-center">
            <v-btn color="primary" :to="`/account/register`" text class="grow">
              Đăng Ký
            </v-btn>
            <div class="grow d-flex">
              <v-btn
                class="mx-auto"
                color="success"
                @click="submitLogin"
                :loading="isLoadding"
              >
                Đăng nhập
              </v-btn>
            </div>
          </div>
        </v-form>
      </v-col>
      <v-col cols="12" sm="6" lg="5" class="d-none d-sm-flex">
        <v-img
          src="http://mydev.ddns.net/wui/images/login-s6-img-dark.svg"
          alt="login - topneww.top"
        ></v-img>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
import loginService from "./account.service";
export default {
  mixins: [loginService],
  mounted() {
    this.init();
  },
};
</script>