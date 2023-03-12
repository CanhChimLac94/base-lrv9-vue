<template>
  <div class="row">
    <v-col cols="12" class="main-actions">
      <admin-share-head-edit
        class=""
        title="Danh Mục Bài viết"
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
    </v-col>
    <v-col cols="12" class="list-data" v-if="!isEdit">
      <v-simple-table >
        <template v-slot:default>
          <thead>
            <tr>
              <th style="max-width: 30px" class="sorting">
                <v-checkbox />
              </th>
              <th class="sorting text-left">Tên chủ đề</th>
              <th class="sorting text-center">Mã chủ đề</th>
              <th class="sorting text-left">Ghi chú</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in dataList" :key="index">
              <td>
                <v-checkbox :label="`(${item.subs.length})`" />
              </td>
              <td @click="editAt(index)">
                <span v-html="item.name"></span>
              </td>
              <td @click="editAt(index)" class="text-center">
                <span v-html="item.code"></span>
              </td>
              <td @click="editAt(index)">
                <p v-html="item.notes"></p>
              </td>
              <td>
                <admin-share-edit-more-action
                  class=""
                  @editAt="editAt(index)"
                  @deleteAt="deleteAt(index)"
                >
                  <v-list-item class="pointer" @click="addSubFor(item)">
                    <v-list-item-title>+ Mục con</v-list-item-title>
                  </v-list-item>
                </admin-share-edit-more-action>
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
      <admin-share-foot-table
        :curentPage="curentPage"
        :totalPage="totalPage"
        :startItemView="startItemView"
        :endItemView="endItemView"
        :totalItem="totalItem"
        @curentPage="curentPage = $event"
        @nextPage="nextPage"
      ></admin-share-foot-table>
    </v-col>

    <v-col cols="12" class="from-edit" :class="isEdit ? '' : 'd-none'">
      <v-row>
        <v-col cols="12" v-if="itemDetail.parent_id > 0 && status == 'addNew'">
          <p>
            Thêm danh mục con cho danh mục <b v-html="parentTopic.name"></b>
          </p>
        </v-col>
        <v-col cols="6">
          <v-text-field
            v-model="itemDetail.name"
            hint="Tên chủ đề"
            label="Tên chủ đề"
          ></v-text-field>
        </v-col>
        <v-col cols="6">
          <v-text-field
            v-model="itemDetail.code"
            hint="Mã chủ đề"
            label="Mã chủ đề"
          ></v-text-field>
        </v-col>
        <v-col cols="6">
          <v-textarea
            v-model="itemDetail.notes"
            hint="Ghi chú"
            label="Ghi chú"
          ></v-textarea>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12">
          <v-tabs>
            <v-tab>Chủ đề con</v-tab>
          </v-tabs>
        </v-col>
        <v-col cols="12 text-right">
          <v-btn color="primary" @click="addItemSubFast" :disabled="addingSub">
            Add
          </v-btn>
          <v-btn
            color="default"
            @click="cancelAddItemSubFast"
            :disabled="!addingSub"
          >
            Cancel
          </v-btn>
          <v-btn
            color="success"
            @click="saveItemSubFast"
            :disabled="!addingSub"
          >
            Save
          </v-btn>
        </v-col>
        <v-col cols="12" v-if="itemDetail.has_sub > 0">
          <v-simple-table>
            <template v-slot:default>
              <thead>
                <tr>
                  <th style="max-width: 30px" class="sorting">
                    <v-checkbox />
                  </th>
                  <th class="sorting text-left">Tên chủ đề</th>
                  <th class="sorting text-center">Mã chủ đề</th>
                  <th class="sorting text-left">Ghi chú</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="addingSub">
                  <td>
                    <v-checkbox :label="`(0)`" />
                  </td>
                  <td>
                    <v-text-field v-model="subItem.name" label="Tên chủ đề" />
                  </td>
                  <td>
                    <v-text-field v-model="subItem.code" label="Mã" />
                  </td>
                  <td>
                    <v-text-field v-model="subItem.notes" label="Ghi chú" />
                  </td>
                  <td></td>
                </tr>
                <tr v-for="(item, index) in itemDetail.subs || []" :key="index">
                  <td>
                    <v-checkbox :label="`(${item.subs.length})`" />
                  </td>
                  <td>
                    <span v-html="item.name"></span>
                  </td>
                  <td class="text-center">
                    <span v-html="item.code"></span>
                  </td>
                  <td>
                    <p v-html="item.notes"></p>
                  </td>
                  <td>
                    <admin-share-edit-more-action
                      class=""
                      @editAt="editItem(item)"
                      @deleteAt="deleteSubFast(item, index)"
                    >
                      <v-list-item class="pointer" @click="addSubFor(item)">
                        <v-list-item-title>+ Mục con</v-list-item-title>
                      </v-list-item>
                    </admin-share-edit-more-action>
                  </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-col>
      </v-row>
      <!-- <admin-share-edit-footer @save="save"></admin-share-edit-footer> -->
    </v-col>
  </div>
</template>
<style >
</style>
<script>
import managerService from "./share/manager.service";

export default {
  mixins: [managerService],
  data() {
    return {
      parentTopic: {},
      subItem: {},
      addingSub: false,
    };
  },
  methods: {
    init() {
      this.initData({
        Ctrl: "newtopic",
      });
      this.getList();
    },
    newEntity() {
      return {
        name: "",
        code: "",
        notes: "",
        parent_id: -1,
        type: "text",
        has_sub: 0,
      };
    },
    addSubFor(parentTopic) {
      this.addItem();
      this.itemDetail.parent_id = parentTopic.id;
      this.parentTopic = parentTopic;
    },
    addItemSubFast() {
      if (this.addingSub) {
        return;
      }
      this.subItem = this.newEntity();
      this.subItem.parent_id = this.itemDetail.id;
      this.itemDetail.has_sub = 1;
      this.addingSub = true;
    },
    saveItemSubFast() {
      if (!this.addingSub || this.subItem.name === "") {
        return;
      }
      this.submitCreate(this.subItem, -1).then(() => {
        this.getList();
        this.addingSub = false;
        this.itemDetail.subs = [
          { ...this.subItem, subs: [] },
          ...this.itemDetail.subs,
        ];
        this.subItem = this.newEntity();
      });
    },
    cancelAddItemSubFast() {
      this.subItem = this.newEntity();
      this.addingSub = false;
    },
    deleteSubFast(item, index) {
      this.deleteItem(item).then(() => {
        this.itemDetail.subs.splice(index, 1);
      });
    },
    beforSave(param) {
      param.subs = null;
      delete param.subs;
    },
    bindDataUpdate(data) {},
    bindDataCreate(data) {},
  },
  mounted() {
    this.init();
  },
};
</script>
