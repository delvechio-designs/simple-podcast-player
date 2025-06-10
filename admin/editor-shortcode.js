(function () {
  /* TinyMCE toolbar (Classic / Classic block) */
  if (typeof tinymce !== "undefined") {
    tinymce.PluginManager.add("spp_mce", function (editor) {
      editor.ui.registry.addButton("spp_mce", {
        text: "Podcast Player",
        icon: "audio",
        tooltip: "Insert podcast player shortcode",
        onAction() {
          editor.insertContent('[podcast_player id=""]');
        },
      });
    });
  }

  /* QuickTags button (Text tab) */
  if (typeof QTags !== "undefined") {
    QTags.addButton("spp_qt", "Podcast Player", '[podcast_player id=""]');
  }
})();
