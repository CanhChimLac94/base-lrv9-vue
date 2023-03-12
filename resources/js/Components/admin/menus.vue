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
              <th class="sorting text-center">Order</th>
              <th class="sorting">Tên hiển thị</th>
              <th class="sorting">Trang hiển thị</th>
              <th class="sorting">Đường dẫn</th>
              <th class="sorting text-center">Icon</th>
              <th class="sorting text-center">Menu cha</th>
              <th class="sorting text-center">Active</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in dataList" :key="index">
              <td>
                <i
                  class="fa fa-plus-circle pointer"
                  @click="addSubMenu(item)"
                ></i>
                <input
                  style="width: inherit; border: none"
                  class="order-input"
                  type="number"
                  v-model="item.order"
                />
              </td>
              <td>{{ item.name }}</td>
              <td>
                <p v-if="item.type == 0">Admin</p>
                <p v-if="item.type == 1">Trang chủ</p>
              </td>
              <td class="">
                <a :href="item.url">{{ item.url }}</a>
              </td>
              <td class="text-center">
                <v-icon>{{ item.icon }}</v-icon>
              </td>
              <td>{{ item.parent_name }}</td>
              <td class="text-center">
                <v-icon
                  title="Change to disable"
                  v-text="'mdi-check-circle-outline'"
                  :color="item.enable ? 'green' : 'orange darken-4'"
                  @click="enable(item, index)"
                ></v-icon>
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
    </div>

    <v-col
      cols="12"
      class="from-edit"
      justify="center"
      :class="isEdit ? '' : 'd-none'"
    >
      <v-row>
        <v-col cols="6">
          <v-text-field
            v-model="itemDetail.name"
            hint="Tên hiển thị"
            label="Tên hiển thị"
          ></v-text-field>
        </v-col>
        <v-col cols="6">
          <v-autocomplete
            v-model="itemDetail.type"
            :items="[
              { id: 0, name: 'Admin' },
              { id: 1, name: 'Trang chủ' },
              { id: 2, name: 'Khác' },
            ]"
            label="Trang hiển thị"
            item-text="name"
            item-value="id"
            single-line
          ></v-autocomplete>
        </v-col>
        <v-col cols="6">
          <v-text-field
            v-model="itemDetail.url"
            hint="/admin/menu"
            label="Đường dẫn"
          ></v-text-field>
        </v-col>
        <v-col cols="6">
          <v-text-field
            v-model="itemDetail.icon"
            hint="Icon"
            label="Icon"
          ></v-text-field>
        </v-col>
      </v-row>
    </v-col>
  </div>
</template>
<script>
import managerService from "./share/manager.service";
export default {
  mixins: [managerService],
  data() {
    return {
      Ctrl: "menu",
    };
  },
  methods: {
    init() {
      this.getList();
    },
    newEntity() {
      return {
        name: "",
        type: "",
        url: "",
        icon: "",
        order: 1,
      };
    },
    addSubMenu(ParentMenu) {
      this.addItem(ParentMenu);
      this.itemDetail.parent_id = ParentMenu.id;
    },
    enable: function (im, i) {
      this.status = "edit";
      this.crIndex = i;
      im.enable = !im.enable;
      this.itemDetail = im;
      this.save();
      this.status = "";
    },
  },
  mounted() {
    this.init();
  },
};
</script>