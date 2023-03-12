const _ = require('lodash');
const $ = require('axios');
// import { mapState, mapMutations } from 'vuex'
// import LazyHydrate from 'vue-lazy-hydration';
import tinymceOptions from './tinymce.options';
import fileManager from './file.manager';

const FILE_MANAGER_URL = '/rfm';
const ACTION_ADD = 'addNew';
const ACTION_EDIT = 'edit';

const {OpenServerBrowser, uploadLocalFile} = fileManager;

const data = () => ({
  Ctrl: 'admin',
  title: 'admin',
  menus: [],
  url: "/api/",
  status: "",
  tableName: 'admin',
  dataList: [], //-----------------
  itemDetail: {},
  dataListChange: [],
  crIndex: 0, // current index
  countDisplay: 10,
  enableSearch: false,
  searchKey: "",
  startItemView: 0,
  endItemView: 0,
  totalItem: 0,
  curentPage: 1,
  totalPage: 1,
  isEdit: false,
  isChanged: false,
  isLoading: false,
  isCreatingItem: false,
  isSubmitingItem: false,
  snackbar: false,
  snackbarText: '',
  snackbarTimeout: 1500,
  tinymceOptions,
})

export default {
  data,
  components: {
    Editor: () => import('vue-tinymce-editor'),
  },
  computed: {
  },
  methods: {
    disableEvent(e) {
      e.stopPropagation();
    },
    disableClick(e) {
      this.disableEvent(e);
    },
    getDateTime(value) {
      try {
        if (value != null) {
          return value = new Date(parseInt(value.replace("/Date(", "").replace(")/", ""), 10));
        } else {
          return new Date();
        }
      } catch (err) {
        return new Date();
      }
    },
    toast(txtMessage) {
      this.snackbarText = txtMessage;
      this.snackbar = true;
    },
    setStore(dataAdmin) {
      this.$store.commit('updateAdminData', dataAdmin);
    },
    setAdminStore(field, value) {
      const data = {};
      data[field] = value;
      this.setStore(data);
    },
    getAdminStore(feild) {
      return this.$store.state.admin[feild];
    },
    initData(data) {
      for (const [key, value] of Object.entries(data)) {
        this.$set(this, key, value);
      }
    },
    resetDefault(){},
    nextPage(page) {
      this.curentPage = page;
      this.getList();
    },
    closeEdit() {
      this.isEdit = false;
      this.status = '';
      this.getList();
    },
    browserFileServer($oft) {
      const url = FILE_MANAGER_URL;
      OpenServerBrowser(url, 800, 600, (file_link) => {
        try { $oft(file_link); } catch (er) { }
      });
    },
    upload_local_file(oft) {
      uploadLocalFile(oft);
    },
    newEntity() {
      return {};
    },
    addItem(obj) {
      this.isCreatingItem = true;
      this.status = ACTION_ADD;
      this.itemDetail = this.newEntity();
      this.isEdit = true;
      this.$emit("ADD_ITEM");
      this.isCreatingItem = false;
      this.resetDefault();
    },
    clone(item){
      const newItem = {
        ...item,
        id: null,
      };
      delete newItem.id;
      this.editItem(newItem);
      this.status = ACTION_ADD;
    },
    editAt(_index) {
      this.editItem(this.dataList[_index]);
      this.crIndex = _index;
      this.isEdit = true;
    },
    editItem(item) {
      this.isChanged = true;
      this.crIndex = - 1;
      this.status = ACTION_EDIT;
      this.$emit("BEFORE_EDIT_ITEM");
      this.$set(this, 'itemDetail', _.cloneDeep(item));
      this.$emit("EDIT_ITEM");
      this.resetDefault();
    },
    viewDetailAt(_index) {
      this.IsEdit = false;
      this.crIndex = _index;
      this.status = "detail";
      this.$emit("BEFORE_VIEW_ITEM", this.dataList[this.crIndex]);
      this.itemDetail = this.dataList[this.crIndex];
      this.$emit("VIEW_ITEM", this.dataList[this.crIndex]);
      this.isChanged = false;
      // ShowForm(".create-edit-detail");
      // HiddenForm(".dataTables_wrapper");
    },
    
    deleteAll() {
      // to do delete all item selected
      this.$emit("DELETE_ITEMS");
      // if (confirm("Bạn thực sự muốn xóa những bản nghi đã chọn")) {
      //   $.post(this.url + this.Ctrl + "/Deletes", { ids: values }).then((result) => {
      //     this.bindingDelete(result.data)
      //   });
      // }
    },
    deleteAt(index) {
      if (confirm("Bạn thực sự muốn xóa bản ghi này")) {
        this.crIndex = index;
        let values = ";";
        values += this.dataList[index].id;
        $.post(`${this.url}${this.Ctrl}/Deletes`, { ids: values }).then((result) => {
          this.bindingDelete(result.data);
        });
        this.$emit("DELETE_ITEM", { ids: values });
      }
    },
    async deleteItem(item) {
      if (confirm("Bạn thực sự muốn xóa bản ghi này")) {
        this.crIndex = -1;
        let values = `;${item.id}`;
        return $.post(`${this.url}${this.Ctrl}/Deletes`, { ids: values }).then((result) => {
          this.bindingDelete(result.data);
          this.$emit("DELETE_ITEM", { ids: values });
        });
      }
      return Promise.reject();
    },
    async getList(callBack = null) {
      if (_.isEmpty(this.Ctrl)) {
        return;
      }
      this.$emit('BEFORE_GET_LIST');
      this.isLoading = true;
      return $.post(`${this.url}${this.Ctrl}/GetList`, {
        page: this.curentPage,
        size: this.countDisplay || 10
      }).then((result) => {
        this.bindDataGet(result.data, callBack);
        this.isLoading = false;
      }).catch((error) => { this.isLoading = false; });
    },
    getDetail(index) {
      this.crIndex = index;
      this.$emit("BEFORE_GET_DETAIL");
      this.itemDetail = copyOBJ(this.dataList[index]);
    },
    bindDataGet(data, callBack = null) {
      this.$emit("BIND_DATA_GET", data);
      data = this._handlerRawData(data);
      this.totalItem = data.total;
      this.totalPage = Math.floor(this.totalItem / this.countDisplay + 1);
      this.startItemView = (this.curentPage - 1) * this.countDisplay + 1;
      this.endItemView = this.curentPage * this.countDisplay;
      if (this.countDisplay > this.totalItem)
        this.endItemView = this.totalItem;
      if (data.list != undefined && data.list.length > 0) {
        this.dataList = data.list;
      } else {
        this.dataList = [];
      }
      if (callBack) {
        callBack();
      }
      this.$emit("BIND_DATA_GET_SUCCESS", data);
    },
    bindDataUpdate(data) {
      if (!_.isEmpty(data.item)) {
        this.itemDetail = data.item;
      }
      this.$emit('BIND_DATA_UPDATE', data);
      const datas = this.dataList;
      datas[this.crIndex] = this.itemDetail;
      this.dataList = datas;
      this.isEdit = false;
    },
    bindDataCreate(data) {
      this.$emit('BIND_DATA_CREATE', data);
      if (typeof data.item !== undefined && data.item !== null) {
        const data = this.dataList;
        this.$emit('BIND_DATA_CREATE_SUCCESS', data);
        this.isEdit = false;
      }
    },
    bindingDelete(data) {
      this.$emit('BIND_DATA_DELETE', data);
      this.getList();
    },
    _handlerRawData(data) {
      return data;
    },
    beforSave(params) {
      return params;
    },
    isValid(param, msg) {
      return false;
    },

    async submitUpdate(param) {
      return $.post(`${this.url}${this.Ctrl}/Update`, param).then((result) => {
        this.isSubmitingItem = false;
        if (!_.isEmpty(result.data) &&
          result.data.status != "ok") {
          this.toast(result.msg);
          return;
        }
        this.isChanged = false;
        this.toast('saved data');
        this.bindDataUpdate(result.data);
        this.closeEdit();
      }).catch((er) => {
        this.toast('save error!');
        this.isSubmitingItem = false;
      });
    },
    async submitCreate(param) {
      return $.post(`${this.url}${this.Ctrl}/Create`, param).then((result) => {
        this.isSubmitingItem = false;
        if (!_.isEmpty(result.data) &&
          result.data.status != "ok") {
          this.toast(result.msg);

          return;
        }
        this.isChanged = false;
        this.toast('added success');
        this.bindDataCreate(result.data);
        this.closeEdit();
      }).catch((er) => {
        // this.ErrorShow(er);
        this.isSubmitingItem = false;
      });
    },

    async save() {
      const param = Object.assign({}, this.itemDetail);
      this.beforSave(param);
      let msg = {
        text: "You need enter value obligatory"
      };
      if (this.isValid(param, msg) || _.isEmpty(this.Ctrl)) {
        this.toast(msg.text);
        return;
      }
      this.isSubmitingItem = true;
      // this.isEdit = false;
      if (this.status === ACTION_EDIT) {
        return this.submitUpdate(param);
      } else if (this.status === ACTION_ADD) {
        return this.submitCreate(param);
      }
      return new Promise((resolve, reject) => {
        this.$emit("SAVED_ITEM");
      });
    },
    async saveI(item, index) {
      // this.isEdit = true;
      this.crIndex = index;
      this.status = ACTION_EDIT;
      this.itemDetail = item;
      return this.save();
    },
    async saveAt(index) {
      return this.saveI(this.dataList[index], index);
    },
    OnKeyMaps() {
      var $this = this;
      document.onkeydown = ($e) => {
        var $e = window.event ? event : $e;
        if ($e.keyCode == 83 && $e.ctrlKey) { // Ctrl + s
          $e.preventDefault();
          if (this.status === ACTION_ADD || this.status === ACTION_EDIT)
            this.Save();
        } else if ($e.keyCode == 68 && $e.ctrlKey) { // Ctrl + d
          $e.preventDefault();
          if (this.status != "")
            this.closeEdit();
          else
            LoadView('Home', './admin/home');
        } else if ($e.keyCode == 72 && $e.ctrlKey) { // Ctrl + h
          $e.preventDefault();
          window.location = "./"; // goto home
        } else if ($e.keyCode == 90 && $e.ctrlKey) { // Ctrl + z
          var m = $(".sidebar-menu").css("margin-top");
          // trace(m, 'margin:');
        } else if ($e.keyCode == 69 && $e.ctrlKey) { // Ctrl + e
          $e.preventDefault();
          this.status = ACTION_EDIT;
          try { this.EnableEdit(true); } catch (e) { }
        } else if ($e.keyCode == 116) { // Ctrl + e
          $e.preventDefault();
          LoadView(crView, crLink);
        }
      }
    },

    init() {
      const user = this.$auth.user;
      if (!user) {
        this.$router.push('/account/login');
        return;
      }
    },

    loadMenus() {
      $.post("/api/admin/GetMenus", { type: 0 }).then((result) => {
        this.menus = result.data;
      }).catch((er) => {
      });
    },

    checkUser() {
      const user = this.$auth.user;
    },
  },
  async mounted() {
    // const _ = await import('lodash');
    // const $ = await import('axios');
  },
}