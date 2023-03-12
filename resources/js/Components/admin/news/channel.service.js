const _ = require("lodash");
import $ from "axios";
import managerService from "../share/manager.service";

const CHANEL_TYPE = {
  VNEXPRESS: 'vnexpress',
  VTV: 'vtv-news',
  CAFEF: 'cafef',
  GENK: 'genk',
  VIETNAMBIZ: 'vietnambiz',
  CAFEBIZ: 'cafebiz',
  SUCKHOEDOISONG: 'suckhoedoisong',
};

const channelSources = [
  {
    title: 'VnExpress',
    type: CHANEL_TYPE.VNEXPRESS,
    pages: [
      '/', 'thoi-su', 'kinh-doanh', 'goc-nhin', 'the-gioi', 'kinh-doanh', 'khoa-hoc',
      'giai-tri', 'the-thao', 'phap-luat', 'giao-duc', 'suc-khoe', 'doi-song', 'du-lich',
      'so-hoa', 'oto-xe-may', 'hai'
    ],
  },
  {
    title: 'VTV-News',
    type: CHANEL_TYPE.VTV,
    pages: ['/', 'van-hoa-giai-tri.htm', 'doi-song.htm', 'cong-nghe.htm', 'xa-hoi.htm'],
  },
  {
    title: 'CaFef',
    type: CHANEL_TYPE.CAFEF,
    pages: ['/', 'thi-truong-chung-khoan.chn', 'bat-dong-san.chn', 'doanh-nghiep.chn', 
    'tai-chinh-ngan-hang.chn', 'tai-chinh-quoc-te.chn', 'vi-mo-dau-tu.chn', 'song.chn',
    'thi-truong.chn', ],
  },
  {
    title: 'GenK',
    type: CHANEL_TYPE.GENK,
    pages: ['/', 'mobile.chn', 'tin-ict.chn', 'internet.chn', 'kham-pha.chn', 'thu-thuat.chn'],
  },
  {
    title: 'Vitet Nam Biz',
    type: CHANEL_TYPE.VIETNAMBIZ,
    pages: ['/', 'thoi-su.htm', 'hang-hoa.htm', 'tai-chinh.htm', 'nha-dat.htm', 
    'chung-khoan.htm', 'doanh-nghiep.htm', 'kinh-doanh.htm'],
  },
  {
    title: 'Cafe Biz',
    type: CHANEL_TYPE.CAFEBIZ,
    pages: ['/', 'thoi-su.chn', 'vi-mo.chn', 'cau-chuyen-kinh-doanh.chn', 
    'cong-nghe.chn', 'song.chn'],
  },
  {
    title: 'Sức khỏe & Đời Sống',
    type: CHANEL_TYPE.SUCKHOEDOISONG,
    pages: ['/', 'thoi-su.htm', 'y-te.htm', 'suc-khoe-tv.htm', 'duoc.htm', 
    'y-hoc-co-truyen.htm', 'y-hoc-360.htm', 'phong-mach-online.htm', 'khoe-dep.htm',
  'dinh-duong.htm', 'gioi-tinh.htm', 'thi-truong.htm', 'nhip-cau-nhan-ai.htm'],
  },

];

const data = () => ({
  selectedChannel: channelSources[0],
  channelSources,
  selected_topic_name: "",
  selectedPage: '/',
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
      // this.getList(this.getListDone);
      this.loadFromChannel();
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
    addItem() {
      return;
    },
    editAt(_index) {
      this.isEdit = true;
      this.isChanged = true;
      this.status = "addNew";
      this.crIndex = _index;
      this.$emit("BEFORE_EDIT_ITEM");
      this.$set(this, "itemDetail", _.cloneDeep(this.dataList[_index]));
      this.loadContent();
      this.$emit("EDIT_ITEM");
    },
    bindDataCreate(data) {
      return;
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
    getListDone() { },
    showTest() { },
    isValid(item, msg) {
      if (_.isEmpty(item.title) || !item.topic_id || item.topic_id === 0)
       {
        this.toast("Bạn chưa chọn chủ đề");
        return true;
       }
      return false;
    },
    beforSave(params){
      delete params['link'];
    },
    selectedTopic(topic) {
      this.itemDetail = {
        ...this.itemDetail,
        topic_id: topic.id,
        topic_name: topic.name,
      };
    },
    bindDataUpdate(resData) {

    },
    closeEdit() {
      this.isEdit = false;
      this.isLoading = false;
    },
    async getList() {
      return this.loadFromChannel();
    },
    async loadFromChannel() {
      this.isLoading = true;
      $.post(`/api/new/getFromChannel`, {
        channel: this.selectedChannel.type,
        page: this.selectedPage,
      }).then((res) => {
        const rawDatas = res.data;
        const datas = [];
        _.each(rawDatas, (item) => {
          datas.push({
            ...this.newEntity(),
            title: item.title,
            img_path: item.thumb,
            description: item.description,
            content: item.content,
            key_words: item.key_words,
            link: item.link,
          });
        });
        this.dataList = datas;
        this.isLoading = false;
      });
    },
    async loadContent() {
      this.isLoading = true;
      $.post(`/api/new/getChannelContentItem`, {
        channel: this.selectedChannel.type,
        link: this.itemDetail.link,
      }).then((res) => {
        this.itemDetail.content = res.data.content + '<p class="Normal" style="text-align: right;" data-mce-style="text-align: right;">Nguồn: '+this.selectedChannel.title+'</p>';
        this.itemDetail.key_words = res.data.key_words;
        this.editorExecCommand();
        this.isLoading = false;
      });
    },
    async editorExecCommand(){
      setTimeout(() => {
        tinymce.activeEditor.execCommand('SelectAll');
        tinymce.activeEditor.execCommand('Unlink');
        tinymce.activeEditor.execCommand('FontName', false, 'arial,helvetica,sans-serif');
        tinymce.activeEditor.execCommand('FontSize', false, '1rem');
        tinymce.activeEditor.execCommand('JustifyFull');
        tinymce.activeEditor.selection.collapse();
        // tinymce.activeEditor.execCommand('ToggleToolbarDrawer');
        this.tinymceOptions.toolbar = false;
      }, 100);
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
    itemDetail() { },
  },
};