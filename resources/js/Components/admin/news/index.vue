<template>
  <div :class="`row admin-${Ctrl}`">
    <div class="col-12 text-left main-actions">
      <admin-share-head-edit
        class=""
        title="Bài viết"
        :count-display="countDisplay"
        :is-edit="isEdit"
        @getList="
          countDisplay = $event;
          getList();
        "
        @addItem="addItem"
        @closeEdit="closeEdit"
        @save="save"
        top
      >
        <v-btn text title="Reset Cache" @click="resetCacheNews()">
          Reset Cache
        </v-btn>
      </admin-share-head-edit>
      <v-snackbar
        v-model="snackbar"
        :timeout="snackbarTimeout"
        color="rgba(0, 0, 0, 0.5)"
        app
      >
        {{ snackbarText }}
      </v-snackbar>
    </div>
    <div class="col-12 list-data" v-if="!isEdit">
      <div class="text-center" v-if="isLoading">
        <v-progress-circular indeterminate color="green"></v-progress-circular>
      </div>
      <v-simple-table>
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
              <td
                @click="editAt(index)"
                class="pointer img-contain"
                width="100px"
              >
                <div class="img-news" :style="getClassBG(item)"></div>
              </td>
              <td @click="editAt(index)" class="pointer news-title">
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
                  <admin-topic-menu
                    :topics="topics"
                    @onSelect="updateTopic(item, $event)"
                  >
                    <p v-html="item.topic_name"></p>
                  </admin-topic-menu>
                </div>
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
      <admin-share-foot-table
        :curentPage="curentPage"
        :totalPage="totalPage"
        :startItemView="startItemView"
        :endItemView="endItemView"
        :totalItem="totalItem"
        @curentPage="curentPage = $event"
        @nextPage="nextPage"
      ></admin-share-foot-table>
    </div>
    <v-col cols="12" class="from-edit" :class="isEdit ? '' : 'd-none'">
      <v-row>
        <v-col class="text-editer-constainer">
          <!-- <lazy-hydrate when-visible>
          </lazy-hydrate> -->
            <editor
              id="d1_news"
              ref="tm_news"
              class="news-editor"
              v-model="itemDetail.content"
              :other_options="tinymceOptions"
              @selectionChange="onChangeContent"
            ></editor>
        </v-col>
        <v-col cols="12" lg="4" md="4">
          <v-row>
            <v-col cols="12">
              <v-text-field
                class="img-thumb-src"
                v-model="itemDetail.img_path"
                hint="src"
                absolute
                flat
              ></v-text-field>
              <div
                class="img-news pointer"
                :style="getClassBG(itemDetail)"
                @click="select_file()"
              ></div>
              <div class="more-btns text-center">
                <v-btn @click="browser_server()"> Server file... </v-btn>
              </div>
            </v-col>
            <v-col cols="12">
              <admin-topic-menu
                :topics="topics"
                @onSelect="selectedTopic($event)"
              >
                <v-text-field
                  v-model="itemDetail.topic_name"
                  hint="Chủ đề"
                  label="Chủ đề*"
                  readonly
                ></v-text-field>
              </admin-topic-menu>
              <v-text-field
                v-model="itemDetail.title"
                hint="Tiêu đề"
                label="Tiêu đề"
              ></v-text-field>
              <v-textarea
                solo
                label="Mô tả"
                v-model="itemDetail.description"
              ></v-textarea>
              <v-textarea
                solo
                label="Từ khóa SEO"
                v-model="itemDetail.key_words"
              ></v-textarea>
              <v-row align="start">
                <v-col>
                  <v-switch
                    v-model="itemDetail.is_pin"
                    label="Trang chủ"
                    color="success"
                    :true-value="1"
                    :false-value="0"
                    :input-value="0"
                  ></v-switch>
                </v-col>
                <v-col>
                  <v-switch
                    v-model="itemDetail.is_hot"
                    label="Tin hot"
                    color="success"
                    :true-value="1"
                    :false-value="0"
                    :input-value="0"
                  ></v-switch>
                </v-col>
                <v-col>
                  <v-switch
                    v-model="itemDetail.is_new"
                    label="Tin mới"
                    color="success"
                    :true-value="1"
                    :false-value="0"
                    :input-value="0"
                  ></v-switch>
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="12 pb-7">
              <p>
                <v-btn @click="onChangeContent">
                  <span>Word Count: </span>
                </v-btn>
                <b class="mx-2" v-html="countWord"></b>
              </p>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
      <!-- <admin-share-edit-footer @save="save"></admin-share-edit-footer> -->
      <v-footer class="footer-actions">
        <v-spacer />
        <v-btn v-if="isEdit" @click="save" title="Save" text>
          <v-icon>mdi-content-save</v-icon>
        </v-btn>
        <v-btn v-if="isEdit" @click="closeEdit" red text>
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-footer>
    </v-col>
  </div>
</template>
<style >
.admin-new .img-news {
  width: 50%;
  padding-top: 50%;
  margin: auto;
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
  border-radius: 10px;
}
.admin-new .from-edit .img-news {
  padding-top: 30%;
}
.admin-new .from-edit .img-news {
  min-width: 50%;
  background-size: contain;
}
.admin-new .from-edit .news-editor .mce-top-part {
  position: fixed;
  top: 0px;
  z-index: 100;
  max-width: calc(70vw);
}
.text-editer-constainer .mce-edit-area,
.text-editer-constainer {
  min-height: 50vh;
  height: 100%;
}
.text-editer-constainer {
  padding-top: 15px;
}
.mce-menubar {
  background-color: #fff;
}

.actions-head,
.actions-footer {
  background-color: rgba(0, 0, 0, 0) !important;
}
.actions-head {
  margin-left: 50px;
}
.footer-actions {
  display: flex !important;
  position: fixed !important;
  width: 100%;
  bottom: 0px;
  left: 0px;
}
.list-data {
  padding-bottom: 25px;
}
@media (max-width: 1200px) {
  .admin-new .from-edit .news-editor .mce-top-part {
    /* position: relative; */
  }
}
</style>
<script>
import newsService from "./news.service";

export default {
  mixins: [newsService],
};
</script>
