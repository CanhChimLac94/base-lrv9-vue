<template>
  <v-row>
    <v-col cols="12" class="text-left main-actions">
      <v-app-bar class="actions-head" app flat>
        <v-col cols="1" md="2">
          <v-select
            v-if="!isEdit"
            :items="[10, 25, 50, 100]"
            :hint="'records'"
            label="Hiển thị"
            single-line
            :value="countDisplay"
            @input="getList"
          ></v-select>
        </v-col>
        <v-spacer />
        <span>Top bình chọn</span>
        <v-spacer />
        <div v-if="!isEdit">
          <v-btn @click="addItem()" title="Add" text fab>
            <v-icon>mdi-plus-thick</v-icon>
          </v-btn>
          <v-btn @click="getList(countDisplay)" text fab>
            <v-icon>mdi-refresh</v-icon>
          </v-btn>
          <v-btn router :to="'/'" text fab>
            <v-icon>mdi-home</v-icon>
          </v-btn>
        </div>
        <div v-if="isEdit">
          <v-btn
            color="blue lighten-2"
            text
            @click="previousStep"
            v-if="crrStep > 1"
            fab
          >
            <v-icon dark center>mdi-arrow-left-bold</v-icon>
          </v-btn>
          <v-btn
            color="grey darken-1"
            text
            @click="save"
            v-if="crrStep >= maxStep"
            fab
          >
            <v-icon dark center>mdi-content-save</v-icon>
          </v-btn>
          <v-btn color="#6dbd63" text v-if="crrStep == 2" @click="addTop()" fab>
            <v-icon drak center>mdi-plus-thick</v-icon>
          </v-btn>
          <v-btn
            color="blue lighten-2"
            text
            @click="nextStep"
            v-if="crrStep < maxStep"
            fab
          >
            <v-icon dark center>mdi-arrow-right-bold</v-icon>
          </v-btn>
          <v-btn color="#BDBDBD" text @click="closeEdit()" title="Hủy" fab>
            <v-icon dark center>mdi-close</v-icon>
          </v-btn>
        </div>
      </v-app-bar>
    </v-col>
    <v-col cols="12" class="list-data" v-if="!isEdit">
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
              <th class="sorting">Ảnh</th>
              <th class="sorting">Tiêu đề</th>
              <th>Trang chủ</th>
              <th>Tin hot</th>
              <th>Tin mới</th>
              <th class="sorting">Chủ đề</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in dataList" :key="index">
              <td>
                <v-checkbox />
              </td>
              <td @click="editAt(index)" class="pointer" width="100px">
                <div class="img-news" :style="getClassBG(item)"></div>
              </td>
              <td @click="editAt(index)" class="pointer">
                <span class="top-number" v-html="item.top"></span>
                <span v-html="item.title"></span>
              </td>
              <td>
                <v-switch
                  v-model="item.is_pin"
                  label="Trang chủ"
                  color="success"
                  :true-value="1"
                  :false-value="0"
                  :input-value="0"
                  @change="saveI(item, index)"
                ></v-switch>
              </td>
              <td>
                <v-switch
                  v-model="item.is_hot"
                  label="Tin hot"
                  color="success"
                  :true-value="1"
                  :false-value="0"
                  :input-value="0"
                  @change="saveI(item, index)"
                ></v-switch>
              </td>
              <td>
                <v-switch
                  v-model="item.is_new"
                  label="Tin mới"
                  color="success"
                  :true-value="1"
                  :false-value="0"
                  :input-value="0"
                  @change="saveI(item, index)"
                ></v-switch>
              </td>
              <td>
                <div style="overflow: hidden; white-space: normal">
                  chủ đề ...
                </div>
              </td>
              <td>
                <admin-share-edit-more-action
                  class=""
                  @editAt="editAt(index)"
                  @deleteAt="deleteAt(index)"
                >
                  <v-btn text @click="clone(item)"> Copy </v-btn>
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
    <v-col
      cols="12"
      class="from-edit"
      justify="center"
      :class="isEdit ? '' : 'd-none'"
      v-if="isEdit"
    >
      <v-card>
        <v-card-title>
          <p class="text-h6">
            Top
            <template v-if="crrStep <= 1">
              <span v-html="'List'"></span>
            </template>
            <template v-else>
              <span class="top-number">{{ itemDetail.top }}</span>
              <span class="top-title">{{ itemDetail.title }}</span>
            </template>
          </p>
          <v-spacer></v-spacer>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row class="step-start" v-if="crrStep == 1">
              <v-col cols="12" sm="2">
                <v-text-field
                  class="text-center"
                  v-model="itemDetail.top"
                  @change="changeTop"
                  label="Top*"
                  hint="Top"
                  color="warning"
                  type="number"
                  required
                ></v-text-field>
                <!-- solo -->
              </v-col>
              <v-col cols="12" sm="10">
                <v-text-field
                  v-model="itemDetail.title"
                  label="Tiêu đề"
                  hint="title"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <div
                  class="img-thumb pointer"
                  :style="getClassBG(itemDetail)"
                  @click="select_file()"
                ></div>
                <div class="more-btns text-center">
                  <v-btn @click="browser_server()"> Server file... </v-btn>
                </div>
              </v-col>
              <v-col cols="12" sm="6">
                <v-combobox
                  color="blue-grey lighten-2"
                  v-model="itemDetail.type"
                  :items="types"
                  label="Chủ đề"
                  multiple
                  :attach="true"
                  chips
                >
                  <template v-slot:selection="data">
                    <v-chip
                      v-bind="data.attrs"
                      :input-value="data.selected"
                      close
                      @click="data.select"
                      @click:close="removeSelectedType(data.item)"
                    >
                      {{ data.item }}
                    </v-chip>
                  </template>
                  <template v-slot:item="data">
                    <v-list-item-content>
                      <v-list-item-title v-html="data.item"></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-combobox>
                <v-textarea
                  label="Mô tả"
                  v-model="itemDetail.description"
                  solo
                ></v-textarea>
                <v-textarea
                  label="Từ khóa SEO"
                  v-model="itemDetail.key_words"
                  solo
                ></v-textarea>
              </v-col>
            </v-row>
            <v-row
              :class="`steps-next ${
                crrStep > 1 && crrStep < maxStep ? '' : 'd-none'
              }`"
              v-if="itemDetail.content"
            >
              <v-col
                cols="6"
                v-for="(item, index) in itemDetail.content.list || []"
                :key="index"
              >
                <v-card>
                  <v-card-text>
                    <v-row>
                      <v-col cols="12" sm="2">
                        <v-text-field
                          label="Top"
                          readonly
                          :value="`${index + 1}`"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="10">
                        <v-text-field
                          v-model="item.title"
                          label="Tên đơn vị"
                          hint="title"
                          required
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12">
                        <!-- <lazy-hydrate when-visible>
                        </lazy-hydrate> -->
                          <editor
                            :other_options="tinymceOptions"
                            v-model="item.content"
                            :id="`edit_${index + 1}`"
                            :ref="`tm_${index + 1}`"
                          >
                          </editor>
                      </v-col>
                    </v-row>
                  </v-card-text>

                  <v-card-actions>
                    <v-btn
                      @click="removeTop(item, index)"
                      color="red lighten-2"
                      title="Xóa top"
                      text
                      fab
                    >
                      <v-icon dark>mdi-close</v-icon>
                    </v-btn>
                    <v-spacer></v-spacer>
                  </v-card-actions>
                </v-card>
              </v-col>
            </v-row>
            <draggable
              v-if="crrStep === maxStep"
              class="row step-end overview"
              v-model="itemDetail.content.list"
            >
              <template v-for="(item, index) in getContentList()">
                <v-col cols="12" sm="4" :key="index">
                  <v-card :key="index">
                    <v-card-title class="top-title">
                      Top
                      <span class="top-number">{{ index + 1 }}</span>
                      {{ item.title }}
                    </v-card-title>
                    <v-card-actions>
                      <v-btn color="grey lighten-2" icon sm fab>
                        <v-icon>mdi-arrow-all</v-icon>
                      </v-btn>
                      <v-spacer></v-spacer>
                      <v-btn
                        color="grey lighten-2"
                        @click="show = !show"
                        icon
                        sm
                        fab
                      >
                        <v-icon>
                          {{ show ? "mdi-chevron-up" : "mdi-chevron-down" }}
                        </v-icon>
                      </v-btn>
                    </v-card-actions>
                    <v-expand-transition>
                      <div v-show="show">
                        <v-divider></v-divider>
                        <v-card-text v-html="item.content"></v-card-text>
                      </div>
                    </v-expand-transition>
                  </v-card>
                </v-col>
              </template>
            </draggable>
            <!-- </v-row> -->
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="blue darken-1"
            v-if="crrStep > 1"
            @click="previousStep"
            text
          >
            <v-icon dark left>mdi-arrow-left</v-icon>
            Previous
          </v-btn>

          <v-btn
            color="#6dbd63"
            v-if="crrStep === 2"
            @click="addTop()"
            text
            fab
          >
            <v-icon drak center>mdi-plus-thick</v-icon>
          </v-btn>

          <v-btn
            color="grey darken-1"
            v-if="crrStep >= maxStep"
            @click="save"
            text
          >
            <v-icon dark left>mdi-content-save</v-icon>
            Save
          </v-btn>

          <v-btn
            color="blue darken-1"
            text
            @click="nextStep"
            v-if="crrStep < maxStep"
          >
            Next
            <v-icon dark right>mdi-arrow-right</v-icon>
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
  </v-row>
</template>
<style>
.img-thumb {
  width: 50%;
  padding-top: 50%;
  margin: auto;
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
  border-radius: 10px;
}
.overview .top-number {
  color: #f57c00;
  padding: 0px 5px;
}
.overview .top-title {
  color: #757575;
}
</style>
<script>
import toplistService from "./toplist.service";
export default {
  mixins: [toplistService],
};
</script>