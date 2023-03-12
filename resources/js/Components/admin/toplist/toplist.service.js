// import draggable from "vuedraggable";
import managerService from "../share/manager.service";
import tinymceOptions from '../share/tinymce.options';

const ACTION_ADD = 'addNew';
const ACTION_EDIT = 'edit';

const types = [
  'du lich', 'van hoa', 'the thao', 'cong ty', 'thuc pham', 'my pham',

];

const data = () => ({
  Ctrl: "toplist",
  crrStep: 1,
  maxStep: 3,
  types,
  crrTop: {},
  tinymceOptions: {
    ...tinymceOptions,
    menubar: false,
  },
  show: false,
  overviewComponents: [],

});

export default {
  mixins: [managerService],
  components: {
    draggable: () => import('vuedraggable'),
  },
  data,
  methods: {
    init() {
      this.getList();
    },
    newEntity() {
      const top = 2;
      this.crrStep = 1;
      return {
        top,
        type: [],
        title: "",
        thumb: '/img/icon/ic-img.png',
        key_words: "",
        description: "",
        content: {
          list: []
        },
      };
    },
    getClassBG(o) {
      const img_path = _.get(o, 'thumb', '/img/icon/ic-img.png').replace(["public/"], "");
      return {
        "background-image": `url('${img_path}')`,
      };
    },
    resetDefault() {
      this.crrStep = 1;
    },
    closeEdit() {
      this.status = '';
      this.getList();
      this.isEdit = false;
    },
    async editAt(_index) {
      let item = this.dataList[_index];
      this.editItem({
        ...item,
        content: {
          ...item.content,
          list: []
        }
      });
      this.crIndex = _index;
      await this.changeTop();
      setTimeout(() => {
        this.itemDetail = _.cloneDeep(item);
      }, 200);
    },
    editItem(item) {
      this.isEdit = true;
      this.crIndex = - 1;
      this.status = ACTION_EDIT;
      this.itemDetail = _.cloneDeep(item);
      this.resetDefault();
    },
    select_file() {
      this.upload_local_file((localtion) => {
        this.itemDetail.thumb = localtion;
      });
    },
    browser_server() {
      this.browserFileServer((url) => {
        this.itemDetail.thumb = url;
      });
    },
    beforSave(params) {
      // params.type = JSON.stringify(params.type);
      // params.content = JSON.stringify(params.content);
      return params;
    },
    bindDataCreate(data) {
      this.closeEdit();
    },
    bindDataUpdate(data) {
      this.closeEdit();
    },
    removeSelectedType(item) {
      const index = this.itemDetail.type.indexOf(item)
      if (index >= 0) this.itemDetail.type.splice(index, 1)
    },
    async changeTop() {
      return new Promise(async (resolve, reject) => {
        let top = parseInt(this.itemDetail.top) || 0;
        const { list } = this.itemDetail.content;
        const crrNumTop = list.length || 0;
        if (top < crrNumTop) {
          top = crrNumTop;
        }
        this.itemDetail.top = top;
        if (top > crrNumTop) {
          const diff = top - crrNumTop;
          const fns = [];
          for (let index = 0; index <= diff; index++) {
            this.addTop();
          }
        }
        return resolve();
      });
    },
    toStep(step) {
      this.crrStep = step;
      if (step >= this.maxStep) {
        this.overviewComponents = this.getContentList();
      }
    },
    nextStep() {
      this.changeTop();
      this.toStep(this.crrStep + 1);
    },
    previousStep() {
      this.toStep(this.crrStep - 1);
    },
    removeTop(item, index) {
      const { list } = this.itemDetail.content;
      if (list.length <= 0) {
        return;
      }
      list.splice(index, 1);
      this.itemDetail = {
        ...this.itemDetail,
        content: {
          ...this.itemDetail.content,
          list,
        },
        // top: list.length,
      };
    },
    async addTop() {
      return await new Promise((res, ject) => {
        const { top } = this.itemDetail;
        const { list } = this.itemDetail.content;
        if (list.length >= top) {
          return;
        }
        list.push({
          title: '',
          content: '',
          order: 0,
          liked: 0,
        });
        this.itemDetail = {
          ...this.itemDetail,
          content: {
            ...this.itemDetail.content,
            list,
          }
        };
        return res();
      });
    },
    getContentList() {
      return _.get(this.itemDetail, 'content.list', []);
    },
    //------tesst--------------
    onOverView() {
      // console.log('item detail', this.itemDetail);
    },
  },
  mounted() {
    this.init();
  },
  watch: {
  },

};