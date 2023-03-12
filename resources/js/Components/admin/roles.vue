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
              <th class="sorting">Tên nhóm</th>
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
                    <!-- <v-btn color="primary" dark v-bind="attrs" v-on="on">
                      ...
                    </v-btn> -->
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
      <div class="row">
        <div class="col-6">
          <v-text-field
            v-model="itemDetail.name"
            hint="Tên vai trò (không chứa dấu cách)"
            label="Tên vai trò (không chứa dấu cách)"
          ></v-text-field>
        </div>
        <v-col cols="6">
          <v-textarea
            solo
            label="Ghi chú"
            v-model="itemDetail.note"
          ></v-textarea>
        </v-col>
      </div>
      <div class="set-position dataTables_wrapper" v-if="status == 'edit'">
        <v-simple-table class="table-added-role">
          <template v-slot:default>
            <thead>
              <tr style="font-weight: bold">
                <th>Quyền</th>
                <th>Ghi chú</th>
                <th title="Add to this role">Kích hoạt</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(p, pindex) in permission_in_role" :key="pindex">
                <td>
                  <span v-html="p.name"></span>
                </td>
                <td>
                  <p v-html="p.note"></p>
                </td>
                <td>
                  <i
                    :class="`fa pointer fa-toggle-on ${
                      p.is_enable === 1 ? 'success' : 'danger'
                    }`"
                    v-if="p.is_enable == 1"
                    @click="update_enable(p)"
                  ></i>
                </td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
        <div class="row">
          <div class="col-12">
            <div class="pull-right">
              <v-btn @click="isShowAddPer = !isShowAddPer"
                >Add permission to role</v-btn
              >
              <v-btn @click="isShowAddPer = !isShowAddPer">
                Add new permission</v-btn
              >
            </div>
          </div>
          <div class="col-12" v-if="isShowAddPer">
            <v-simple-table class="table-added-role">
              <template v-slot:default>
                <tbody>
                  <tr>
                    <td colspan="2">Add Permision To Role</td>
                    <td width="10px">
                      <v-checkbox
                        @change="check_all_per()"
                        v-model="isCheckAll"
                      />
                    </td>
                  </tr>
                  <tr v-for="(p, pindex) in permission_out_role" :key="pindex">
                    <td style="width: 50%">
                      <span v-html="p.name"></span>
                    </td>
                    <td style="width: 40%">
                      <span v-html="p.note"></span>
                    </td>
                    <td style="width: 10px">
                      <v-checkbox
                        @change="updateCheckall()"
                        v-model="selected_permissions"
                        :value="p.id"
                      />
                    </td>
                  </tr>
                  <tr>
                    <td
                      colspan="3"
                      @click="submit_add_permission()"
                      class="text-center"
                    >
                      <v-btn> Submit </v-btn>
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </div>
        </div>
      </div>

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
        Ctrl: "Role",
        Title: "Nhóm chức năng",
        TableName: "Nhóm chức năng",
      });
      // this.load_topics();
      this.getList();
    },
    newEntity() {
      return {
        name: "",
        value: 0,
        note: "",
      };
    },
    load_permission() {
      const url = "/api/role/get_permissions";
      const param = {
        role_id: this.itemDetail.id,
      };
      $.post(url, param).then((res) => {
        this.permission_in_role = res.data.permission_in;
        this.permission_out_role = res.data.permission_out;
      });
    },
    submit_add_permission() {
      const url = "/api/role/add_permissions";
      const param = {
        role_id: this.itemDetail.id,
        permission_ids: this.selected_permissions,
      };
      $.post(url, param).then((res) => {
        this.load_permission();
      });
    },
    update_enable(p) {
      const url = "/api/role/update_enable";
      p.is_enable = 1 - p.is_enable;
      const param = {
        role_id: this.itemDetail.id,
        permission_id: p.id,
        is_enable: p.is_enable,
      };
      $.post(url, param).then((res) => {
        $this.load_permission();
      });
    },
    check_all_per() {
      this.selected_permissions = [];
      if (this.isCheckAll == true)
        this.permission_out_role.forEach((p) => {
          this.selected_permissions.push(p.id);
        });
    },
    updateCheckall: function () {
      if (this.selected_permissions.length == this.permission_out_role.length) {
        this.isCheckAll = true;
      } else {
        this.isCheckAll = false;
      }
    },
  },
  mounted() {
    this.init();
  },
  watch: {
    status: function () {
      if (this.status == "edit") this.load_permission();
    },
  },
};
</script>
