const $ = require('axios');

const OpenServerBrowser = (url, width, height, _oft) => {
  const iLeft = (screen.width - width) / 2;
  const iTop = (screen.height - height) / 2;
  let sOptions = "toolbar=no,status=no,resizable=yes,dependent=yes";
  sOptions += ",width=" + width;
  sOptions += ",height=" + height;
  sOptions += ",left=" + iLeft;
  sOptions += ",top=" + iTop;
  window.SetUrl = (p, v, w) => {
    try { _oft(p); }
    catch (er) { }
  };
  window.open(url, "BrowseWindow", sOptions);
}

const uploadLocalFile = (oft = () => { }) => {
  const fileSelector = document.createElement('input');
  fileSelector.setAttribute('type', 'file');
  fileSelector.addEventListener('change', (evt) => {
    const file = fileSelector.files[0];
    uploadFile(file, file.name, (location) => {
      oft(location);
    });
  })
  fileSelector.click();
}

const uploadFile = (file, fileName, success, failure) => {
  const formData = new FormData();
  formData.append('file', file, fileName);
  $.post('/api/file/upload', formData).then((res) => {
    const { data } = res;
    success(data.location);
  }).catch((err) => {
    failure('HTTP Error: ' + err.status);
  });
}

export default {
  OpenServerBrowser,
  uploadLocalFile,
  uploadFile,
};