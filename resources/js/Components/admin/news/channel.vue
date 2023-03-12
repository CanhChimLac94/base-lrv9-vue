<template>
  <v-row :class="`row admin-${Ctrl}`">
    <v-col cols="12" class="text-left main-actions">
      <v-app-bar class="actions-head" app flat>
        <v-col cols="6" sm="3" md="2" lg="2">
          <v-autocomplete
            v-if="!isEdit"
            :items="channelSources"
            :hint="selectedChannel.title"
            label="Nguồn"
            single-line
            item-text="title"
            v-model="selectedChannel"
            return-object
            @change="
              selectedPage = '/';
              getList();
            "
          ></v-autocomplete>
        </v-col>
        <v-col cols="6" sm="3" md="2" lg="2">
          <v-autocomplete
            v-if="!isEdit"
            :items="selectedChannel.pages"
            :hint="selectedPage === '/' ? 'Home' : selectedPage"
            v-model="selectedPage"
            label="Trang"
            single-line
            return-object
            @change="getList"
          ></v-autocomplete>
        </v-col>

        <v-spacer />
        <v-btn text title="Reset Cache" @click="resetCacheNews()">
          Reset Cache
        </v-btn>
        <v-btn v-if="!isEdit" text fab @click="getList">
          <v-icon>mdi-refresh</v-icon>
        </v-btn>
        <v-btn v-if="!isEdit" router fab :to="'/'" text>
          <v-icon>mdi-home</v-icon>
        </v-btn>
        <v-btn v-if="isEdit" fab @click="save" title="Save" text>
          <v-icon>mdi-content-save</v-icon>
        </v-btn>
        <v-btn v-if="isEdit" fab @click="closeEdit" red text>
          <v-icon>mdi-close</v-icon>
        </v-btn>
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
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(item, index) in dataList"
              :key="index"
              :title="item.title"
            >
              <td>
                <span v-html="index + 1"></span>
                <!-- <v-checkbox /> -->
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
                <admin-share-edit-more-action
                  class=""
                  @editAt="editAt(index)"
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
    </v-col>
    <v-footer cols="12" class="footer-actions view-page" v-if="!isEdit">
      <v-spacer />
      <v-btn text fab @click="getList">
        <v-icon>mdi-refresh</v-icon>
      </v-btn>
      <v-btn router fab :to="'/'" text>
        <v-icon>mdi-home</v-icon>
      </v-btn>
    </v-footer>
    <v-col cols="12" class="from-edit" :class="isEdit ? '' : 'd-none'">
      <v-row>
        <v-col class="text-editer-constainer">
          <editor
            id="d22"
            class="news-editor"
            v-model="itemDetail.content"
            :other_options="tinymceOptions"
            @change="onChangeContent"
            :height="'50vh'"
            ref="tm"
          ></editor>
        </v-col>

        <v-col cols="12" lg="4" md="4">
          <v-row>
            <v-col cols="12">
              <v-text-field
                v-model="itemDetail.img_path"
                :hint="itemDetail.img_path"
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
  </v-row>
</template>
<style>
.footer-actions {
  display: flex !important;
  position: fixed !important;
  width: 100%;
  bottom: 0px;
  left: 0px;
}
.footer-actions.view-page {
  display: none !important;
}
.img-contain{
  padding: 0px !important;
}
</style>
<style >
</style>
<script>
import channelService from "./channel.service";
export default {
  mixins: [channelService],
};
</script>
