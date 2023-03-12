// const $ = require('axios');
// import tinymce from 'vue-tinymce-editor';
import fileManager from './file.manager';

// import highcharts from 'highcharts';
// import HighchartsVue from 'highcharts-vue'
// import chartEditor from '../../libs/chart.editor.tinymce';

const FILE_MANAGER_URL = '/rfm';

const fontsize_formats = "8px 9px 10px 11px 12px 14px 18px 24px 30px 36px 48px 60px 72px 96px";
const lineheight_formats = '1 1.1 1.2 1.3 1.4 1.5 2';
const font_formats =
  "Roboto = Roboto; Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats";

const tinymcePlugins = 
  'table code lists template image media anchor hr link autoresize preview searchreplace visualblocks spellchecker fullscreen paste imagetools contextmenu importcss textcolor textpattern colorpicker insertdatetime wordcount'
  // 'print preview fullpage powerpaste searchreplace autolink directionality advcode visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists textcolor wordcount tinymcespellchecker a11ychecker imagetools mediaembed  linkchecker contextmenu colorpicker textpattern help',
;

const menu = {
  file: { title: 'File', items: 'newdocument restoredraft | print ' },
  edit: { title: 'Edit', items: 'undo redo | cut copy paste | selectall | searchreplace' },
  view: { title: 'View', items: 'code | visualaid visualchars visualblocks | preview fullscreen' },
  insert: { title: 'Insert', items: 'image link media template codesample inserttable | charmap emoticons hr | pagebreak nonbreaking anchor toc | insertdatetime' },
  format: { title: 'Format', items: 'bold italic underline strikethrough superscript subscript codeformat | formats blockformats fontformats fontsizes align lineheight | forecolor backcolor | removeformat | importcss' },
  tools: { title: 'Tools', items: 'spellchecker spellcheckerlanguage wordcount' },
  table: { title: 'Table', items: 'inserttable | cell row column | tableprops deletetable' },
  // help: { title: 'Help', items: 'help' }
};

const contextmenu = 'link image imagetools table spellchecker lists | cut copy paste | inserttable | cell row column | tableprops deletetable';

const toolbar = ["formatselect | styleselect  | fontselect | fontsizeselect lineheight | bold italic | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | removeformat | link | emoticons | forecolor backcolor wordcount"];

// const toolbar = "formatgroup | paragraphgroup | insertgroup";

// const toolbar_groups = {
//   formatgroup: {
//     icon: 'format',
//     tooltip: 'Formatting',
//     items: 'bold italic underline strikethrough | forecolor backcolor | superscript subscript | removeformat'
//   },
//   paragraphgroup: {
//     icon: 'paragraph',
//     tooltip: 'Paragraph format',
//     items: 'h1 h2 h3 | bullist numlist | alignleft aligncenter alignright | indent outdent'
//   },
//   insertgroup: {
//     icon: 'plus',
//     tooltip: 'Insert',
//     items: 'link image emoticons hr'
//   },

// };

const textcolor_map = [
  "010101", "black",
  "252525", "grey-darken-1",
  "4c4948", "grey-darken-2",
  "686868", "grey-darken-3",
  "8b8b8b", "grey-darken-4",

  "9c9c9c", "grey",
  "c5c5c5", "grey-lighten-1",
  "fafafa", "grey-lighten-2",
  "fcfcfc", "grey-lighten-3",


  "0381fe", "blue-accent-1",
  "458bff", "blue-accent-2",
  "3e91ff", "blue-accent-3",
  "8bb0fc", "blue-accent-4",

  "53a4ff", "blue-lighten-1",
  "69a9f1", "blue-lighten-2",
  "0381fe40", "blue-lighten-3",

  "ffa746", "orange-lighten-1",
  "fdba44", "orange-lighten-2",
  "f7b68e", "deep-orange-lighten-1",

  "6dbd63", "green-lighten-1",
  "82d28c", "green-lighten-2",
  "9dd31d", "light-green-lighten-1",

  "e17282", "red-lighten-1",
  "ff8d93", "red-lighten-2",
  "f2a1a1", "red-lighten-3",
  "f28072", "red-accent-1",
  "e87092", "pink-lighten-1",

  "c17adf", "purple-lighten-1",
  "8c84f3", "deep-purple-darken-1",
  "7882ff", "deep-purple-darken-2",

  "000000", "Black",
  "993300", "Burnt orange",
  "333300", "Dark olive",
  "003300", "Dark green",
  "003366", "Dark azure",
  "000080", "Navy Blue",
  "333399", "Indigo",
  "333333", "Very dark gray",
  "800000", "Maroon",
  "FF6600", "Orange",
  "808000", "Olive",
  "008000", "Green",
  "008080", "Teal",
  "0000FF", "Blue",
  "666699", "Grayish blue",
  "808080", "Gray",
  "FF0000", "Red",
  "FF9900", "Amber",
  "99CC00", "Yellow green",
  "339966", "Sea green",
  "33CCCC", "Turquoise",
  "3366FF", "Royal blue",
  "800080", "Purple",
  "999999", "Medium gray",
  "FF00FF", "Magenta",
  "FFCC00", "Gold",
  "FFFF00", "Yellow",
  "00FF00", "Lime",
  "00FFFF", "Aqua",
  "00CCFF", "Sky blue",
  "993366", "Red violet",
  "FFFFFF", "White",
  "FF99CC", "Pink",
  "FFCC99", "Peach",
  "FFFF99", "Light yellow",
  "CCFFCC", "Pale green",
  "CCFFFF", "Pale cyan",
  "99CCFF", "Light sky blue",
  "CC99FF", "Plum",
];

const file_browser_callback = (field_name, url, type, win) => {
  const w = window,
    d = document,
    e = d.documentElement,
    g = d.getElementsByTagName('body')[0],
    x = w.innerWidth || e.clientWidth || g.clientWidth,
    y = w.innerHeight || e.clientHeight || g.clientHeight;
  let cmsURL = `${FILE_MANAGER_URL}?&field_name=${field_name}`;
  tinyMCE.activeEditor.windowManager.open({
    file: cmsURL,
    title: 'Filemanager',
    width: x * 0.8,
    height: y * 0.8,
    resizable: "yes",
    close_previous: "yes"
  }, {
    window: win,
    input: field_name,
    resizable: "yes",
    inline: "yes",
  });
}

const images_upload_handler = (blobInfo, success, failure) => {
  fileManager.uploadFile(blobInfo.blob(), blobInfo.filename(), success, failure);
};

const tinySetup = (editor) => {
  editor.on('ExecCommand', function (e) {
    // console.log('The CMD: ' + e.command, e );
  });
}

const tinymceOptions = {
  // plugins: [
  //   ...tinymcePlugins,
  // ],
  plugins: tinymcePlugins,
  setup: tinySetup,
  themes: "inlite",
  paste_webkit_styles: 'all',
  paste_retain_style_properties: 'all',
  paste_remove_styles_if_webkit: false,
  // powerpaste_html_import: "merge",
  // paste_text_sticky: true,
  // paste_text_sticky_default: true,
  // paste_data_images: true,
  // paste_filter_drop: false,
  // paste_as_text: true,

  // language_url: 'https://dyonir.github.io/vue-tinymce-editor//static/langs/vi_VN.js',
  // language: 'vi_VN',
  height: 'calc(50vh)',
  min_height: '50vh',
  autoresize_bottom_margin: 50,
  autoresize_on_init: true,
  importcss_append: true,
  textcolor_cols: "10",
  textcolor_rows: "10",
  visualblocks_default_state: true,
  file_browser_callback,
  relative_urls: false,
  remove_script_host: true,
  automatic_uploads: true,
  images_reuse_filename: true,
  images_upload_handler,
  menu,
  contextmenu,
  toolbar1: '',
  toolbar,
  // toolbar_groups,
  // toolbar_drawer: 'sliding',
  // toolbar_item_size: "small",
  // skin: 'outside',
  // toolbar_location: 'bottom',
  lineheight_formats,
  fontsize_formats,
  font_formats,
  textcolor_map,
  statusbar: false,
  // document_base_url: '/',
  // images_upload_url: '/api/file/upload',
  // external_filemanager_path: FILE_MANAGER_URL,
  // images_upload_base_path: '/',
  // filemanager_title: "Responsive Filemanager",

};

export default tinymceOptions;