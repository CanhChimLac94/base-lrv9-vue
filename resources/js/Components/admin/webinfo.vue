<template>
  <div class="row">
    <v-col cols="12" class="main-actions">
      <v-app-bar class="actions-head" app flat>
        <v-col cols="1" md="2"> </v-col>
        <v-spacer />
        <v-btn v-if="!isEdit" @click="isEdit = true" title="Edit" text fab>
          <v-icon>mdi-pencil-outline</v-icon>
        </v-btn>
        <v-btn v-if="!isEdit" @click="getInfo" text fab>
          <v-icon>mdi-refresh</v-icon>
        </v-btn>
        <v-btn v-if="!isEdit" router :to="'/'" text fab>
          <v-icon>mdi-home</v-icon>
        </v-btn>
        <v-btn v-if="isEdit" @click="update()" title="Save" text fab>
          <v-icon>mdi-content-save</v-icon>
        </v-btn>
        <v-btn v-if="isEdit" @click="isEdit = false" red text fab>
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-app-bar>
    </v-col>
    <v-col cols="12">
      <div class="text-center" v-if="isLoading">
        <v-progress-circular indeterminate color="green"></v-progress-circular>
      </div>
    </v-col>
    <v-col cols="6">
      <v-text-field
        label="Tên Website"
        v-model="itemDetail.Name"
        :readonly="!isEdit"
      ></v-text-field>
    </v-col>
    <v-col cols="6">
      <v-text-field
        label="Địa chỉ"
        v-model="itemDetail.Address"
        :readonly="!isEdit"
      ></v-text-field>
    </v-col>
    <v-col cols="6">
      <v-text-field
        label="Logo"
        v-model="itemDetail.LogoPath"
        :readonly="!isEdit"
      ></v-text-field>
    </v-col>
    <v-col cols="6">
      <v-text-field
        label="Icon"
        v-model="itemDetail.IconPath"
        :readonly="!isEdit"
      ></v-text-field>
    </v-col>
    <v-col cols="6">
      <v-text-field
        label="Phone"
        v-model="itemDetail.Phone"
        :readonly="!isEdit"
      ></v-text-field>
    </v-col>
    <v-col cols="6">
      <v-text-field
        label="Email"
        v-model="itemDetail.Email"
        :readonly="!isEdit"
      ></v-text-field>
    </v-col>
    <v-col cols="6">
      <v-text-field
        label="Facebook"
        v-model="itemDetail.Facebook"
        :readonly="!isEdit"
      ></v-text-field>
    </v-col>
    <v-col cols="6">
      <v-text-field
        label="Youtube"
        v-model="itemDetail.Youtube"
        :readonly="!isEdit"
      ></v-text-field>
    </v-col>
    <v-col cols="6">
      <v-text-field
        label="Hotline"
        v-model="itemDetail.Hotline"
        :readonly="!isEdit"
      ></v-text-field>
    </v-col>
    <v-col cols="6">
      <v-text-field
        label="Fax"
        v-model="itemDetail.Fax"
        :readonly="!isEdit"
      ></v-text-field>
    </v-col>
    <v-col cols="12">
      <!-- <lazy-hydrate when-visible>
      </lazy-hydrate> -->
        <editor
          id="d1"
          v-model="itemDetail.Introduct"
          :other_options="tinymceOptions"
          :height="'50vh'"
          :readonly="!isEdit"
          ref="tm"
        ></editor>
    </v-col>
  </div>
</template>
<style>
.actions-head .v-btn {
  color: #888888;
}
</style>
<style >
</style>
<script>
const $ = require("axios");
import managerService from "./share/manager.service";

export default {
  mixins: [managerService],
  methods: {
    init() {
      this.initData({
        Ctrl: "Webinfor",
      });
      this.getInfo();
    },
    newEntity() {
      return {};
    },
    enableEdit: function (bol) {
      this.isEdit = bol;
      if (bol) this.status = "edit";
      else this.status = "";
    },
    update: function () {
      const url = "/api/WebInfor/Update";
      $.post(url, this.itemDetail).then((result) => {
        this.enableEdit(false);
      });
    },
    getInfo: function () {
      const url = "/api/WebInfor/GetInfo";
      $.get(url).then((res) => {
        const data = res.data;
        this.itemDetail = data;
      });
    },
  },
  mounted() {
    this.init();
  },
};
</script>
