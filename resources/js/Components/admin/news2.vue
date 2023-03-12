<template>
  <admin-share-edit-item v-model="dataList" :columns="columns">
    <template v-slot:data_table>
      <v-simple-table>
        <template v-slot:default>
          <thead>
            <tr>
              <td style="max-width: 30px">
                <v-checkbox />
              </td>
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
              <td width="100px">
                <div class="img-news" :style="getClassBG(item)"></div>
              </td>
              <td>
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
                  @change="saveI(item)"
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
                  @change="saveI(item)"
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
                  @change="saveI(item)"
                ></v-switch>
              </td>
              <td>
                <div style="overflow: hidden; white-space: normal">
                  <p v-html="get_topic_byId(item.topic_id).name"></p>
                </div>
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
    </template>

    <template v-slot:from_edit>
      <div class="row">
        <div class="col text-center flex">
          <div
            class="img-news pointer"
            :style="getClassBG(itemDetail)"
            @click="select_file()"
          ></div>
        </div>
        <div class="col-6">
          <v-autocomplete
            v-model="itemDetail.topic_id"
            :items="topics"
            label="Chủ đề"
            item-text="name"
            item-value="id"
            single-line
          ></v-autocomplete>
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
        </div>
      </div>
      <div class="row">
        <div class="col">
          <!-- <lazy-hydrate when-visible>
          </lazy-hydrate> -->
            <editor
              id="d1"
              class="text-editer-constainer"
              v-model="itemDetail.content"
              :other_options="tinymceOptions"
              ref="tm"
            ></editor>
        </div>
      </div>
    </template>
  </admin-share-edit-item>
</template>
<style >
.fa_icons {
  position: absolute;
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  background: #fff;
  width: 100%;
  height: 100vh;
  top: 0px;
  z-index: 100;
  overflow: auto;
}
.img-news {
  width: 50%;
  /* padding-top: 50%; */
  max-width: 100px;
  max-height: 100px;
  margin: auto;
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
  border-radius: 10px;
  /* border: 1px solid #2c454a; */
}
.text-editer-constainer {
  color: #555555;
  min-height: 50vh;
}
.text-editer-constainer .mce-container {
  min-height: inherit;
}
</style>
<script>
import managerService from "./share/manager.service";
import $ from "axios";

export default {
  mixins: [managerService],
  data() {
    return {
      topics: [],
      contentHolder: "",
      content: "",
      columns: [
        {
          title: "Ảnh",
          name: "img_path",
        },
        {
          title: "Tiêu đề",
          name: "title",
        },
        {
          title: "Trang chủ",
          name: "is_pin",
        },
        {
          title: "Tin hot",
          name: "is_hot",
        },
        {
          title: "Tin mới",
          name: "is_new",
        },
        {
          title: "Chủ đề",
          name: "topic_id",
        },
      ],
    };
  },
  methods: {
    init() {
      this.initData({
        Ctrl: "new",
      });
      this.load_topics();
      this.getList(this.getListDone);
    },
    newEntity() {
      return {
        topic_id: 0,
        title: "",
        img_path: "/img/icon/ic-img.png",
        description: "",
        content: "",
        key_words: "",
        is_pin: false,
        is_hot: false,
        is_new: true,
      };
    },
    getClassBG(o) {
      const img_path = (o.img_path || "").replace(["public/"], "");
      return {
        "background-image": `url('${img_path}')`,
      };
    },
    get_topic_byId(topic_id) {
      if (this.topics.length <= 0) return {};
      let topic = { name: "Other" };
      this.topics.forEach((tp) => {
        if (tp.id === topic_id) {
          return (topic = tp);
        }
      });
      return topic;
    },
    load_topics() {
      $.post("/api/new/getAllTopics", {}).then((res) => {
        this.topics = res.data.datas.topics;
      });
    },
    select_file() {
      this.browserFileServer((url) => {
        this.itemDetail.img_path = url;
      });
    },
    getListDone() {
      // if (this.dataList.length > 0) {
      //   this.itemDetail = this.dataList[0];
      // }
    },
    showTest() {},
    // saveI(item)
    change(e) {
      console.log("CHNAGE", { e });
    },
  },
  mounted() {
    this.init();
  },
};
</script>
