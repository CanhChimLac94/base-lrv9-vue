<template>
  <div class="row">
    <div class="col-12 text-left main-actions">
      <admin-share-head-edit
        class=""
        :count-display="countDisplay"
        :is-edit="isEdit"
        @getList="
          countDisplay = $event;
          getList();
        "
        @addItem="addItem"
        @closeEdit="closeEdit"
        @save="save"
      ></admin-share-head-edit>
    </div>
    <div class="col-12 list-data" v-if="!isEdit">
      <div class="text-center" v-if="isLoading">
        <v-progress-circular indeterminate color="green"></v-progress-circular>
      </div>
      <v-simple-table v-else>
        <template v-slot:default>
          <thead>
            <tr>
              <th style="max-width: 30px">
                <v-checkbox />
              </th>
              <th class="sorting">Tên quyền</th>
              <th class="sorting">Ghi chú</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in dataList" :key="index">
              <td>
                <v-checkbox />
              </td>
              <td>{{ item.name }}</td>
              <td>
                <p>{{ item.note }}</p>
              </td>
              <td>
                <v-menu offset-y>
                  <template v-slot:activator="{ on, attrs }">
                    <span v-bind="attrs" v-on="on">...</span>
                  </template>
                  <v-list>
                    <v-list-item class="pointer" @click="editAt(index)">
                      <v-list-item-title>Edit</v-list-item-title>
                    </v-list-item>
                    <v-list-item class="pointer" @click="deleteAt(index)">
                      <v-list-item-title>Delete</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
      <div class="text-center">
        <v-app-bar>
          <p>
            Hiển thị {{ startItemView }} tới {{ endItemView }} của
            {{ totalItem }} mục
          </p>
          <v-spacer />
          <v-pagination
            v-model="curentPage"
            :length="totalPage"
            :total-visible="4"
            @input="nextPage"
          ></v-pagination>
        </v-app-bar>
      </div>
    </div>
    <div class="col-12 from-edit" :class="isEdit ? '' : 'd-none'">
      <v-row>
        <v-col cols="12">
          <v-text-field
            v-model="itemDetail.name"
            hint="Tên quyền hạn (không chứa dấu cách)"
            label="Tên quyền hạn (không chứa dấu cách)"
          ></v-text-field>
        </v-col>
        <v-col cols="12">
          <v-text-field
            v-model="itemDetail.note"
            hint="Ghi chú"
            label="Ghi chú"
          ></v-text-field>
        </v-col>
      </v-row>

      <div class="row">
        <div class="col-12">
          <v-app-bar class="actions-footer">
            <v-spacer />
            <v-btn @click="save()"> Save </v-btn>
          </v-app-bar>
        </div>
      </div>
    </div>
  </div>
</template>
<style >
.table-added-role {
  padding-bottom: 3px;
}
</style>
<script>
import managerService from "./share/manager.service";
import $ from "axios";

export default {
  mixins: [managerService],
  data() {
    return {
      all_permissions: [],
      permission_in_role: [],
      permission_out_role: [],
      selected_permissions: [],
      isCheckAll: false,
      isShowAddPer: false,
    };
  },
  methods: {
    init() {
      this.initData({
        Ctrl: "Permission",
        Title: "Quyền hạn",
        TableName: "Quyền hạn",
      });
      this.getList();
    },
    newEntity() {
      return {
        name: "",
        note: "",
        value: 0,
      };
    },
    BeforSave: function (data) {
      data.name = data.name.trim();
      data.name = data.name.replace(/ /g, "_");
    },
  },
  mounted() {
    this.init();
  },
  watch: {},
};
</script>
