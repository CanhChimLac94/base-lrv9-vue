const _ = require("lodash");
import $ from "axios";
import managerService from "../share/manager.service";

const data = ()=>({
  selected_topic_name: "",
  topics: [],
  contentHolder: "",
  content: "",
  countWord: 0,
});

export default {
  mixins: [managerService],
  data,
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
        img_path: "/upload/icons/ic-img.svg",
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
      this.upload_local_file((localtion) => {
        this.itemDetail.img_path = localtion;
      });
    },
    browser_server() {
      this.browserFileServer((url) => {
        this.itemDetail.img_path = url;
      });
    },
    getListDone() {},
    showTest() {},
    isValid(item, msg) {
      if (_.isEmpty(item.title) || !item.topic_id || item.topic_id === 0)
        return true;
      return false;
    },
    selectedTopic(topic) {
      this.itemDetail = {
        ...this.itemDetail,
        topic_id: topic.id,
        topic_name: topic.name,
      };
    },
    updateTopic(item, topic) {
      item.topic_id = topic.id;
      item.topic_name = topic.name;
      this.saveI(item, -1);
    },
    bindDataUpdate(resData) {
      // this.getList();
    },
    resetCacheNews(){
      $.post(`/api/new/resetCache`, {}).then((res)=>{});  
    },
    onChangeContent(){
      let wordcount = tinymce.activeEditor.plugins.wordcount;
      this.countWord = wordcount.getCount();
    },
  },
  mounted() {
    this.init();
  },
  watch: {
    itemDetail() {},
  },
};