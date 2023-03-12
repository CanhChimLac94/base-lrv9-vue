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
              <th class="sorting text-center">User name</th>
              <th class="sorting text-center">Họ tên</th>
              <th class="sorting text-center">Email</th>
              <th class="sorting text-center">SĐT</th>
              <th class="sorting text-center">Role</th>
              <th></th>
            </tr>
          </thead>
          <tbody v-if="true">
            <tr v-for="(item, index) in dataList" :key="index" >
              <td>
                <v-checkbox />
              </td>
              <td class="text-center">
                <span v-html="item.user_name"></span>
              </td>
              <td class="text-center">
                <span v-html="item.full_name"></span>
              </td>
              <td class="text-center">
                <span v-html="item.email"></span>
              </td>
              <td class="text-center">
                <p v-html="item.phone"></p>
              </td>
              <td class="text-center" style="overflow: visible">
                <v-autocomplete
                  v-model="item.role_id"
                  :items="lstRoles"
                  label="Role"
                  item-text="name"
                  item-value="id"
                  single-line
                  :disabled="item.is_edit == 0"
                  @change="submit_update_role(item)"
                ></v-autocomplete>
              </td>
              <td>
                <admin-share-edit-more-action
                  class=""
                  @editAt="editAt(index)"
                  @deleteAt="deleteAt(index)"
                ></admin-share-edit-more-action>
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
    <!-- <div class="col-12 from-edit" :class="isEdit ? '' : 'd-none'">
      <admin-share-edit-footer @save="save"></admin-share-edit-footer>
    </div> -->
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
      lstRoles: [],
    };
  },
  methods: {
    init() {
      this.initData({
        Ctrl: "User",
        title: "Users",
        tableName: "Thành Viên",
      });
      this.getList();
      this.loadRoles();
    },
    newEntity() {
      return {
        name: "",
        note: "",
        type: "",
      };
    },
    loadRoles() {
      $.post("/api/Role/GetList", {}).then((res) => {
        this.lstRoles = res.data.list || [];
      });
    },
    submit_update_role(u) {
      $.post(`/api/${this.Ctrl}/update_role`, {
        user_id: u.id,
        role_id: u.role_id,
      }).then((res) => {
        // displaySuccess("Change role for member is sueccess!");
      });
    },
  },
  mounted() {
    this.init();
  },
};
</script>
