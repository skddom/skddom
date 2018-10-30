urlDispatcher.addRoutes({
  'module.sitesecure': NETCAT_PATH + 'modules/sitesecure/admin.php?view=main'
})
.addPrefixRouter('module.sitesecure.', function(path, params) {
  var url = NETCAT_PATH + "modules/sitesecure/admin.php?view=" + path.substr(18);
  if (params) { url += "&id=" + params; }
  mainView.loadIframe(url);
});
